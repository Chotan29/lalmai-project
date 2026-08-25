<?php

namespace App\Services\Fees;

use App\Models\BankLetterSetting;
use App\Models\FeeHeadBankAccount;
use App\Support\BanglaNumber;
use Illuminate\Support\Facades\DB;

/**
 * The money a fee collected, arranged the way it has to be handed to the bank.
 *
 * The report already knows how much landed in each head. The bank needs something different: how
 * much goes into each account. Those are not the same list, and three things pull them apart.
 *
 *   - A head can have one account per department. Seminar money from Marketing belongs in
 *     Marketing's account, not in one pot called Seminar.
 *
 *   - A head can split by the student's religion. The religious fee arrives as one 30 taka line
 *     and leaves as two - milad and puja - decided by who paid it, not by anything on the head.
 *
 *   - Two heads can share one account. Sonali Seva has none of its own and rides in with the board
 *     fee, so on the letter they are one line of 1,135, not two lines nobody can reconcile.
 *
 * So the money is walked student by student rather than head by head, each payment sent to the
 * account that student's payment belongs in, and the result added back up by account. It costs one
 * query and it is the only way the three cases above come out right.
 */
class BankTransferLetter
{
    /* The same fee matching the report uses. Shared rather than copied - the letter and the sheet
       disagreeing about which money belongs to a fee is the one failure nobody would catch until
       the bank did. */
    use \App\Traits\FeeGroupFilter;

    /**
     * @param  int      $groupId    the Main Fee Head
     * @param  string   $start      period, already resolved by the report
     * @param  string   $end
     * @param  int|null $facultyId  one programme, or null for the whole college
     */
    public function build($groupId, $start, $end, $facultyId = null)
    {
        $settings = BankLetterSetting::current();

        $groupIds = $this->asGroupIds($groupId);

        $accounts = FeeHeadBankAccount::where('status', 1)->get()->groupBy('fee_head_id');

        /* Every payment, with the two things that decide where it goes. */
        $rows = DB::table('fee_collections as c')
            ->join('fee_masters as fm', 'fm.id', '=', 'c.fee_masters_id')
            ->join('students as s', 's.id', '=', 'c.students_id')
            ->where('c.status', 1)
            ->whereBetween('c.date', [$start, $end . ' 23:59:59']);

        $this->applyFeeGroups($rows, $groupIds);

        if ($facultyId) {
            $rows->where('s.faculty', $facultyId);
        }

        $rows = $rows->select('fm.fee_head', 's.faculty', 's.religion',
                DB::raw('SUM(c.paid_amount) as paid'), DB::raw('COUNT(DISTINCT c.students_id) as students'))
            ->groupBy('fm.fee_head', 's.faculty', 's.religion')
            ->get();

        /* Refunds come off the same way they went on, or the letter asks the bank to move money
           the college has already given back. */
        $refunds = $this->refundsByHead($groupIds, $start, $end, $facultyId);

        $lines   = [];   // account number => line
        $orphans = [];   // heads with money and no account to put it in

        foreach ($rows as $row) {
            $headAccounts = $accounts->get($row->fee_head);

            if (!$headAccounts || !$headAccounts->count()) {
                $orphans[$row->fee_head] = ($orphans[$row->fee_head] ?? 0) + $row->paid;
                continue;
            }

            $account = FeeHeadBankAccount::choose($headAccounts, $row->faculty, $row->religion);

            if (!$account) {
                $orphans[$row->fee_head] = ($orphans[$row->fee_head] ?? 0) + $row->paid;
                continue;
            }

            $key = $account->account_no;

            if (!isset($lines[$key])) {
                $lines[$key] = (object) [
                    'account_no'   => $account->account_no,
                    'account_name' => $account->account_name,
                    'collected_by' => optional($account->feeHead)->collected_by ?? 'college',
                    'serial'       => (int) ($account->getAttributes()['serial'] ?? 0),
                    'amount'       => 0.0,
                    'students'     => 0,
                    'heads'        => [],
                ];
            }

            $lines[$key]->amount += (float) $row->paid;
            $lines[$key]->students += (int) $row->students;
            $lines[$key]->heads[$row->fee_head] = true;
        }

        /* Take the refunds off the account the money would have gone to. */
        foreach ($refunds as $headId => $byFacultyReligion) {
            foreach ($byFacultyReligion as $r) {
                $headAccounts = $accounts->get($headId);
                if (!$headAccounts || !$headAccounts->count()) { continue; }

                $account = FeeHeadBankAccount::choose($headAccounts, $r->faculty, $r->religion);
                if (!$account || !isset($lines[$account->account_no])) { continue; }

                $lines[$account->account_no]->amount -= (float) $r->given;
            }
        }

        /* A line that ends at nothing is not sent to the bank. */
        $lines = array_filter($lines, function ($line) { return round($line->amount, 2) > 0; });

        $college    = array_values(array_filter($lines, function ($l) { return $l->collected_by !== 'department'; }));
        $department = array_values(array_filter($lines, function ($l) { return $l->collected_by === 'department'; }));

        /*
         * The college's own order, not the account number's.
         *
         * The bank reads each letter against the previous one, so the list has to come out in the
         * same sequence every time - and that sequence is the one on the college's letters, which
         * is not sortable from anything in the data. It is carried on the account as a serial.
         *
         * An account with no serial is one opened since that order was written down. It goes to the
         * end rather than to the front, where it would silently displace everything: appearing last
         * is noticeable, and being missed is not.
         */
        $inOrder = function ($a, $b) {
            $sa = $a->serial ?: PHP_INT_MAX;
            $sb = $b->serial ?: PHP_INT_MAX;

            return $sa === $sb ? strcmp($a->account_no, $b->account_no) : ($sa < $sb ? -1 : 1);
        };

        usort($college, $inOrder);
        usort($department, $inOrder);

        /*
         * One letter, every account on it.
         *
         * The college and department accounts are still told apart for the report, which totals
         * them separately, but the bank gets a single list: it is one transfer out of one source
         * account on one day, and splitting it across two sheets only invites half of it to be
         * actioned. The department accounts fall after the college ones on their own because their
         * serials - 25 and 26 - are where the college's order already puts them.
         */
        $all = array_merge($college, $department);
        usort($all, $inOrder);

        return (object) [
            'settings'         => $settings,
            'all'              => $all,
            'college'          => $college,
            'department'       => $department,
            'college_tail'     => $this->collegeTail($department),
            'college_total'    => array_sum(array_map(function ($l) { return $l->amount; }, $college)),
            'department_total' => array_sum(array_map(function ($l) { return $l->amount; }, $department)),
            'orphans'          => $this->nameOrphans($orphans),
        ];
    }

    /**
     * The department fees, as they close the college's letter.
     *
     * The college's letter runs to twenty-six numbered lines, but only twenty-four of them are
     * accounts. The last two - incourse and seminar - have no single account to name, because that
     * money is split between the departments and moved by the second letter. The college still
     * lists them, so the letter plainly covers the whole fee rather than looking as though two
     * heads were forgotten.
     *
     * Named and numbered, with the account and the amount left blank exactly as the college leaves
     * them: writing a figure here that the total below does not include is how a bank comes back
     * asking which of the two numbers is the real one.
     *
     * @param  array $department  the department lines, already grouped by account
     */
    protected function collegeTail(array $department)
    {
        $tail = [];

        foreach ($department as $line) {
            /* Grouped by the serial rather than by the head: it is the serial that decides where
               the line sits on the college's list, and for these two the two agree. */
            $serial = (int) $line->serial;

            if (!isset($tail[$serial])) {
                $tail[$serial] = (object) [
                    'serial' => $serial,
                    'title'  => $this->tailTitle($line->heads),
                    'amount' => 0.0,
                ];
            }

            $tail[$serial]->amount += (float) $line->amount;
        }

        ksort($tail);

        return array_values($tail);
    }

    /**
     * What the college's own letter calls these two on its numbered list.
     *
     * Shorter than the names on the department letter - there it has to say which fund of which
     * department, here it is naming the fee itself.
     */
    protected function tailTitle(array $heads)
    {
        $names = [
            103 => 'ইনকোর্স পরীক্ষা ফি',
            104 => 'সেমিনার ফি',
        ];

        foreach (array_keys($heads) as $headId) {
            if (isset($names[$headId])) { return $names[$headId]; }
        }

        /* An unexpected department head still gets a line rather than disappearing. */
        $ids = array_keys($heads);
        return $ids
            ? (string) DB::table('fee_heads')->where('id', reset($ids))->value('fee_head_title')
            : '';
    }

    /** Words for the foot of each letter. */
    public function inWords($amount)
    {
        return BanglaNumber::taka($amount);
    }

    /* ---------------- pieces ---------------- */

    protected function refundsByHead(array $groupIds, $start, $end, $facultyId)
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('payment_refund_items')) {
            return [];
        }

        $query = DB::table('payment_refund_items as ri')
            ->join('payment_refunds as r', 'r.id', '=', 'ri.payment_refund_id')
            ->join('fee_collections as c', 'c.id', '=', 'ri.fee_collection_id')
            ->join('fee_masters as fm', 'fm.id', '=', 'c.fee_masters_id')
            ->join('students as s', 's.id', '=', 'c.students_id')
            ->where('r.status', 1)
            ->whereBetween('ri.date', [$start, $end . ' 23:59:59']);

        $this->applyFeeGroups($query, $groupIds);

        if ($facultyId) {
            $query->where('s.faculty', $facultyId);
        }

        return $query->select('ri.fee_head', 's.faculty', 's.religion', DB::raw('SUM(ri.amount) as given'))
            ->groupBy('ri.fee_head', 's.faculty', 's.religion')
            ->get()
            ->groupBy('fee_head')
            ->all();
    }

    /**
     * Heads holding money that no account would take.
     *
     * Named rather than counted, because "3 heads have no account" tells the office nothing they
     * can act on and the letter cannot go out until each one is dealt with.
     */
    protected function nameOrphans(array $orphans)
    {
        if (!$orphans) { return []; }

        $titles = DB::table('fee_heads')->whereIn('id', array_keys($orphans))
            ->pluck('fee_head_title', 'id');

        $out = [];
        foreach ($orphans as $headId => $amount) {
            $out[] = (object) [
                'title'  => $titles[$headId] ?? ('Head #' . $headId),
                'amount' => round($amount, 2),
            ];
        }

        return $out;
    }
}
