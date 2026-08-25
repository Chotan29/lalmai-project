<?php

namespace App\Http\Controllers\Account\Fees;

use App\Http\Controllers\CollegeBaseController;
use App\Models\Faculty;
use App\Models\FeeHead;
use App\Models\FeeHeadBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Where each fee head's money is transferred to.
 *
 * One screen for all of it. The office has twenty-six heads and more than thirty accounts, and
 * opening a head at a time to type one number is how half of them end up never filled in.
 *
 * The numbers themselves were read off two photographs of the college's own paperwork - a printed
 * list and a handwritten note. Nothing is written to the database from that reading: the fields
 * are offered filled in, and only what is saved on this screen counts. A bank account number is
 * not something to take on trust from optical character recognition, so somebody has to look at
 * each one.
 */
class FeeHeadBankAccountController extends CollegeBaseController
{
    protected $base_route = 'account.fees.fee-head-bank-account';
    protected $view_path  = 'account.fees.fee-head-bank-account';
    protected $panel      = 'Fee Head Bank Account';

    public function index(Request $request)
    {
        $data = [];

        $data['fee_heads'] = FeeHead::where('status', 1)->orderBy('id')->get();
        $data['faculties'] = Faculty::where('status', 1)->orderBy('faculty')->pluck('faculty', 'id');

        /* Shown in the order they appear on the letter, so the screen and the letter read the same
           way round. A zero serial is an account the college's written order never listed; it sits
           at the end rather than at the top. */
        $data['accounts'] = FeeHeadBankAccount::orderByRaw('CASE WHEN serial = 0 THEN 1 ELSE 0 END')
            ->orderBy('serial')->orderBy('account_no')->orderBy('id')->get()
            ->groupBy('fee_head_id');

        /* Religions actually in use, so the rule can be picked rather than typed. */
        $data['religions'] = DB::table('students')
            ->select('religion')->whereNotNull('religion')->where('religion', '<>', '')
            ->groupBy('religion')->orderBy('religion')->pluck('religion');

        /* The department heads need one account per department, so their suggestions are expanded
           against the faculties this college actually has. */
        $suggestions = $this->suggestions();

        foreach ($this->departmentSuggestions() as $headId => $byName) {
            foreach ($data['faculties'] as $facultyId => $facultyName) {
                foreach ($byName as $fragment => $number) {
                    if (stripos($facultyName, $fragment) === false) { continue; }

                    $suggestions[$headId][] = [
                        /* The two department fees close the college's letter: incourse, then
                           seminar. Every department's account for one of them shares that head's
                           place in the list - they are one line of the college's order that
                           happens to have several accounts under it. */
                        'serial'       => $headId === 103 ? 25 : ($headId === 104 ? 26 : 0),
                        'faculty_id'   => $facultyId,
                        'account_no'   => $number,
                        /*
                         * Which fund, then whose.
                         *
                         * A department holds two of these - a seminar fund and an internal exam
                         * fund - and named by the department alone both lines of the letter read
                         * "Department of Management" against two different account numbers, with
                         * nothing to say which is which. The college's own second-year letter
                         * names them the long way round, and so does the handwritten note.
                         */
                        'account_name' => $this->departmentFundName($headId) . ', ' . $facultyName,
                        'note'         => 'read from the handwritten note - check before use',
                    ];
                    break;
                }
            }
        }

        $data['suggestions'] = $suggestions;
        $data['has_any']     = FeeHeadBankAccount::count() > 0;

        return view(parent::loadDataToView($this->view_path . '.index'), compact('data'));
    }

    /**
     * Save the whole screen at once.
     *
     * Rows are replaced rather than merged: what is on the screen when Save is pressed is what the
     * college means, and a half-updated set of bank accounts is worse than either version of it.
     * Existing rows are switched off rather than deleted, so an old letter can still be explained.
     */
    public function store(Request $request)
    {
        $rows = (array) $request->input('rows', []);
        $kept = [];
        $saved = 0;

        DB::transaction(function () use ($rows, &$kept, &$saved) {
            foreach ($rows as $row) {
                $headId = (int) ($row['fee_head_id'] ?? 0);
                $number = trim((string) ($row['account_no'] ?? ''));
                $name   = trim((string) ($row['account_name'] ?? ''));

                /* An empty line is somebody not filling a row in, not an instruction. */
                if (!$headId || ($number === '' && $name === '')) { continue; }

                $religions = (array) ($row['match_religion'] ?? []);
                $religions = array_values(array_filter(array_map('trim', $religions)));

                $values = [
                    'fee_head_id'     => $headId,
                    /* Where this account sits on the letter. Zero means "not in the college's
                       written order", which sorts to the end rather than to the front. */
                    'serial'          => (int) ($row['serial'] ?? 0),
                    'faculty_id'      => (int) ($row['faculty_id'] ?? 0) ?: null,
                    'account_name'    => mb_substr($name, 0, 191),
                    'account_no'      => mb_substr($number, 0, 50),
                    'match_religion'  => $religions ? implode(',', $religions) : null,
                    'is_default'      => !empty($row['is_default']),
                    'note'            => mb_substr(trim((string) ($row['note'] ?? '')), 0, 191) ?: null,
                    'last_updated_by' => auth()->id(),
                    'status'          => 1,
                ];

                $id = (int) ($row['id'] ?? 0);

                if ($id && $account = FeeHeadBankAccount::find($id)) {
                    $account->update($values);
                } else {
                    $values['created_by'] = auth()->id();
                    $account = FeeHeadBankAccount::create($values);
                }

                $kept[] = $account->id;
                $saved++;
            }

            /* Anything the screen no longer shows is switched off, not removed. */
            if ($kept) {
                FeeHeadBankAccount::whereNotIn('id', $kept)->update(['status' => 0]);
            }
        });

        $this->message = $saved . ' account(s) saved.';

        /* Warn rather than block: a head with no account cannot go in a transfer letter, and it is
           better to hear that here than when the letter is being written. */
        $missing = $this->headsWithoutAccount();
        if ($missing->count()) {
            $this->message .= ' Still without an account: ' . $missing->implode(', ') . '.';
        }

        return redirect()->route($this->base_route);
    }

    /** Live heads that no active account would catch. */
    protected function headsWithoutAccount()
    {
        $have = FeeHeadBankAccount::where('status', 1)->pluck('fee_head_id')->unique();

        return FeeHead::where('status', 1)
            ->whereNotIn('id', $have->all())
            ->pluck('fee_head_title');
    }

    /**
     * What the college's own paperwork says, offered as a starting point.
     *
     * Read from a printed list of thirty-two college accounts and a handwritten note of the
     * department ones. Matched to the heads by arithmetic rather than by name alone: the sample
     * transfer letter covered 215 students, so every amount in it divided by 215 lands on the
     * head's own rate - transport 301,000 over 215 is 1,400, and that is what transport costs.
     *
     * Three of them are worth knowing about:
     *   - Rover and Ranger is the Ranger account. The letter's 6,450 is 30 x 215, and 30 is this
     *     head's rate; the separate Rover Scout account is not used in it.
     *   - Sonali Seva has no account of its own. The board account holds 244,025, which is 1,135
     *     x 215 against a head rate of 1,130 - the missing 5 is this fee, riding along.
     *   - The religious head has two, split by religion: 5,880 to milad and 570 to puja, which is
     *     196 students and 19 students at 30 each.
     *
     * @return array fee_head_id => list of rows
     */
    protected function suggestions()
    {
        /* Every college account is named the same way in the letter, so the prefix is written once. */
        $c = 'লালমাই সরকারি কলেজ, ';

        /* The serial is where the account sits on the college's own letter - read off that letter,
           not invented here. It is what the letter sorts by, so it travels with the suggestion. */
        return [
            80  => [
                ['serial' => 1, 'account_no' => '1335901017122', 'account_name' => $c . 'ধর্মীয় অনুষ্ঠান মিলাদ',
                 'match_religion' => [], 'is_default' => true,
                 'note' => 'takes everyone the puja account does not'],
                ['serial' => 2, 'account_no' => '1335901017125', 'account_name' => $c . 'ধর্মীয় অনুষ্ঠান পূজা',
                 'match_religion' => ['Hinduism', 'Buddhism'], 'is_default' => false,
                 'note' => 'Hindu and Buddhist students'],
            ],
            81  => [['serial' => 3,  'account_no' => '1335901017130', 'account_name' => $c . 'সাহিত্য ও সংস্কৃতি']],
            82  => [['serial' => 4,  'account_no' => '1335901017108', 'account_name' => $c . 'বহিঃক্রীড়া']],
            83  => [['serial' => 5,  'account_no' => '1335901017112', 'account_name' => $c . 'আন্তঃক্রীড়া এবং কমনরুম']],
            84  => [['serial' => 6,  'account_no' => '1335901017132', 'account_name' => $c . 'ম্যাগাজিন']],
            85  => [['serial' => 8,  'account_no' => '1335901017131', 'account_name' => $c . 'বিএনসিসি']],
            86  => [['serial' => 7,  'account_no' => '1335901017133', 'account_name' => $c . 'রেঞ্জার']],
            87  => [['serial' => 20, 'account_no' => '1335901017117', 'account_name' => $c . 'উন্নয়ন তহবিল']],
            88  => [['serial' => 9,  'account_no' => '1335901017106', 'account_name' => $c . 'লাইব্রেরি']],
            89  => [['serial' => 10, 'account_no' => '1335901017107', 'account_name' => $c . 'মসজিদ']],
            90  => [['serial' => 16, 'account_no' => '1335901017110', 'account_name' => $c . 'কলেজ পরিবহন']],
            91  => [['serial' => 11, 'account_no' => '1335901017118', 'account_name' => $c . 'অধিভুক্তি ফি']],
            92  => [['serial' => 17, 'account_no' => '1335901017109', 'account_name' => $c . 'অত্যাবশ্যকীয় কর্মচারী ও অন্যান্য']],
            93  => [['serial' => 12, 'account_no' => '1335901017124', 'account_name' => $c . 'চিকিৎসা সেবা']],
            94  => [['serial' => 13, 'account_no' => '1335901017121', 'account_name' => $c . 'শিক্ষা সফর']],
            95  => [['serial' => 18, 'account_no' => '1335901017126', 'account_name' => $c . 'ব্যবস্থাপনা']],
            96  => [['serial' => 15, 'account_no' => '1335901017129', 'account_name' => $c . 'বিবিধ']],
            97  => [['serial' => 14, 'account_no' => '1335901017116', 'account_name' => $c . 'পাঠদান উন্নয়ন / কম্পিউটার']],
            98  => [['serial' => 21, 'account_no' => '1335901017123', 'account_name' => $c . 'বোর্ড বিশ্ববিদ্যালয় ফি',
                     'note' => 'Sonali Seva fee rides along in this account']],
            99  => [['serial' => 21, 'account_no' => '1335901017123', 'account_name' => $c . 'বোর্ড বিশ্ববিদ্যালয় ফি',
                     'note' => 'no account of its own - goes with the board fee']],
            100 => [['serial' => 19, 'account_no' => '1335901017113', 'account_name' => $c . 'বিদ্যুৎ']],
            101 => [['serial' => 22, 'account_no' => '1335901017114', 'account_name' => $c . 'ভর্তি']],
            102 => [['serial' => 23, 'account_no' => '1335901017115', 'account_name' => $c . 'বেতন']],
            105 => [['serial' => 24, 'account_no' => '1335901017119', 'account_name' => $c . 'পরিচয়পত্র',
                     'note' => 'the printed list has this as a college account']],
        ];
    }

    /**
     * Department accounts, offered per department for the heads that need them.
     *
     * Read from the handwritten note, which is the least certain of the two sources - these in
     * particular want checking against the bank's own paper before any money moves.
     *
     * @return array  fee head id => [department name fragment => account number]
     */
    /**
     * What the college calls each of the two department funds.
     *
     * Taken from its own letters rather than translated from the English head title: the second
     * year letter names the incourse account "অভ্যন্তরীণ পরীক্ষা তহবিল", and the handwritten note
     * uses the same two words for both. The bank matches on the name as well as the number.
     */
    public static function departmentFundName($headId)
    {
        $names = [
            103 => 'অভ্যন্তরীণ পরীক্ষা তহবিল',
            104 => 'সেমিনার তহবিল',
        ];

        return $names[$headId] ?? '';
    }

    public function departmentSuggestions()
    {
        return [
            /* Seminar fee */
            104 => [
                'Marketing'  => '1335901017177',
                'Management' => '1335901017172',
                'English'    => '1335901017185',
                'Accounting' => '1335901017193',
            ],
            /* Incourse exam fee */
            103 => [
                'Marketing'  => '1335901017178',
                'Management' => '1335901017173',
                'English'    => '1335901017184',
                'Accounting' => '1335901017194',
            ],
        ];
    }
}
