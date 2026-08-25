<?php

namespace App\Services\Admission;

use App\Models\OnlinePayment;
use App\Models\PaymentRefund;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Someone paid, then did not take admission.
 *
 * Three things have to happen and they are easy to do only two of. The student stops being
 * counted; the money is recorded as having gone back; and the face comes off the attendance
 * device, because a device that still knows them will open the gate for somebody who is not a
 * student here. That last one is the one that gets forgotten, so it lives in here with the rest
 * rather than being left to whoever remembers.
 *
 * Nothing is deleted anywhere. Cancelling is a mark that can be lifted, and a refund is a new row
 * beside the payment rather than a change to it.
 */
class AdmissionCancellation
{
    /**
     * Mark that the admission did not go ahead.
     *
     * @param  string $note  why - free text, kept with the mark so nobody has to remember
     * @param  int|null $userId  who decided
     */
    public function cancel(Student $student, $note = '', $userId = null)
    {
        if ($student->admission_cancelled_at !== null) {
            return false;   // already cancelled; do not overwrite the original date and reason
        }

        /*
         * Written with the query builder rather than save(). These three columns are not in the
         * model's fillable list and should not be - they are not part of filling in a student -
         * and a mass assignment would silently drop them.
         */
        DB::table('students')->where('id', $student->id)->update([
            'admission_cancelled_at' => now(),
            'admission_cancel_note'  => mb_substr(trim((string) $note), 0, 255),
            'admission_cancelled_by' => $userId,
            'updated_at'             => now(),
        ]);

        $this->takeOffDevices($student);

        return true;
    }

    /** Lift the mark. The student returns to the counts exactly as they were. */
    public function restore(Student $student, $userId = null)
    {
        if ($student->admission_cancelled_at === null) {
            return false;
        }

        DB::table('students')->where('id', $student->id)->update([
            'admission_cancelled_at' => null,
            'admission_cancel_note'  => null,
            'admission_cancelled_by' => null,
            'updated_at'             => now(),
        ]);

        /* Put the face back, so the gate knows them again from tomorrow morning. */
        try {
            $queue = app(\App\Services\Attendance\EnrolmentQueue::class);
            if ($queue->hasTargets()) {
                $queue->enrol($student->fresh());
            }
        } catch (\Throwable $e) {
            Log::warning('Restored the admission but could not put the student back on the device', [
                'student' => $student->id, 'err' => $e->getMessage(),
            ]);
        }

        return true;
    }

    /**
     * Record money handed back.
     *
     * The payment row is not touched. It says money came in on a day, and that remains true; this
     * says money went out on another. Anything else would quietly change a month that has already
     * been reported.
     *
     * @param  array $data  amount, date, method, ref_no, note, online_payment_id
     */
    public function refund(Student $student, array $data, $userId = null)
    {
        $amount = round((float) ($data['amount'] ?? 0), 2);
        if ($amount <= 0) {
            return ['ok' => false, 'message' => 'Enter how much money is being returned.'];
        }

        $paymentId = (int) ($data['online_payment_id'] ?? 0) ?: null;

        /* Do not let more go out than ever came in - a typed extra zero is otherwise permanent. */
        $paid     = $this->paidTotal($student->id, $paymentId);
        $refunded = $this->refundedTotal($student->id, $paymentId);

        if ($paid > 0 && ($refunded + $amount) > $paid + 0.001) {
            return [
                'ok' => false,
                'message' => sprintf(
                    'That is more than was received. Paid %s, already refunded %s, so at most %s can be returned.',
                    number_format($paid, 2), number_format($refunded, 2), number_format($paid - $refunded, 2)
                ),
            ];
        }

        $method = (string) ($data['method'] ?? 'cash');
        if (!array_key_exists($method, PaymentRefund::methods())) {
            $method = 'other';
        }

        $refund = PaymentRefund::create([
            'online_payment_id' => $paymentId,
            'students_id'       => $student->id,
            'amount'            => $amount,
            'date'              => $data['date'] ?? now()->toDateString(),
            'method'            => $method,
            'ref_no'            => mb_substr(trim((string) ($data['ref_no'] ?? '')), 0, 100) ?: null,
            'note'              => mb_substr(trim((string) ($data['note'] ?? '')), 0, 255) ?: null,
            'voucher_no'        => $this->nextVoucherNo(),
            'created_by'        => $userId,
            'status'            => 1,
        ]);

        /* Without this the money leaves the drawer but never leaves the head-wise report. */
        $this->splitAcrossHeads($refund, $student->id, $amount);

        return ['ok' => true, 'refund' => $refund];
    }

    /**
     * Break the refund down the way the payment was broken down.
     *
     * A fee of 7,400 is not one row. It is stored as one row per sub head - twenty-six of them for
     * this college's admission fee - because that is the only level at which anyone can say whose
     * money it is. The head-wise collection report adds those rows up, so a refund recorded as a
     * single lump can never be taken off it: nothing says which heads to take it from. The money
     * would go out of the drawer and stay on the sheet, and the department heads would keep
     * showing income the college no longer holds.
     *
     * Refunding everything gives each head back exactly what it took. Refunding part of it is
     * shared out in proportion, and the rounding remainder is put on the largest head rather than
     * dropped, so the lines always add back to the amount actually handed over.
     *
     * fee_collections is not touched. The money really was collected on that day, and a report of
     * last month must not change because of something done this month.
     */
    protected function splitAcrossHeads(PaymentRefund $refund, $studentId, $amount)
    {
        /* What each head is still holding: collected, less anything already given back. */
        $lines = DB::table('fee_collections as c')
            ->join('fee_masters as fm', 'fm.id', '=', 'c.fee_masters_id')
            ->where('c.students_id', $studentId)
            ->where('c.status', 1)
            ->select('c.id', 'c.paid_amount', 'c.date', 'fm.fee_head')
            ->orderBy('c.id')
            ->get();

        if ($lines->isEmpty()) {
            Log::warning('Refund recorded with no head breakdown - the student has no collection rows', [
                'refund' => $refund->id, 'student' => $studentId,
            ]);
            return;
        }

        $alreadyByLine = DB::table('payment_refund_items')
            ->whereIn('fee_collection_id', $lines->pluck('id')->all())
            ->select('fee_collection_id', DB::raw('SUM(amount) as given'))
            ->groupBy('fee_collection_id')
            ->pluck('given', 'fee_collection_id');

        $available = [];
        $total = 0.0;
        foreach ($lines as $line) {
            $left = round((float) $line->paid_amount - (float) ($alreadyByLine[$line->id] ?? 0), 2);
            if ($left <= 0) { continue; }
            $available[] = ['line' => $line, 'left' => $left];
            $total += $left;
        }

        if ($total <= 0) {
            Log::warning('Refund recorded but every head has already been refunded in full', [
                'refund' => $refund->id, 'student' => $studentId,
            ]);
            return;
        }

        $rows = [];
        $assigned = 0.0;
        $largest = 0;

        foreach ($available as $i => $entry) {
            /* Everything back, or a proportional share of it. */
            $share = ($amount >= $total - 0.001)
                ? $entry['left']
                : round($amount * ($entry['left'] / $total), 2);

            if ($share <= 0) { continue; }

            $rows[] = [
                'payment_refund_id' => $refund->id,
                'fee_head'          => $entry['line']->fee_head,
                'fee_collection_id' => $entry['line']->id,
                'amount'            => $share,
                'date'              => $refund->date instanceof \DateTimeInterface
                                        ? $refund->date->format('Y-m-d') : $refund->date,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

            $assigned += $share;
            if ($entry['left'] > $available[$largest]['left']) { $largest = $i; }
        }

        /* Twenty-six roundings do not add back to the total on their own. */
        $gap = round($amount - $assigned, 2);
        if (abs($gap) >= 0.01 && count($rows)) {
            foreach ($rows as $k => $row) {
                if ((int) $row['fee_collection_id'] === (int) $available[$largest]['line']->id) {
                    $rows[$k]['amount'] = round($row['amount'] + $gap, 2);
                    break;
                }
            }
        }

        if ($rows) {
            DB::table('payment_refund_items')->insert($rows);
        }
    }

    /* ---------------- figures ---------------- */

    /** What this student actually paid. */
    public function paidTotal($studentId, $paymentId = null)
    {
        $q = DB::table('online_payments')->where('students_id', $studentId);

        if ($paymentId) {
            $q->where('id', $paymentId);
        }

        /*
         * Only money that arrived. A gateway row sitting at pending or failed is an attempt, not a
         * payment, and refunding against it would send out money the college never received.
         */
        $q->where(function ($w) {
            $w->whereIn('payment_status', ['paid', 'success', 'completed', 'VALID', 'valid'])
              ->orWhereNull('payment_status');
        });

        return (float) $q->sum('amount');
    }

    /** What has already gone back. */
    public function refundedTotal($studentId, $paymentId = null)
    {
        $q = DB::table('payment_refunds')->where('students_id', $studentId)->where('status', 1);

        if ($paymentId) {
            $q->where('online_payment_id', $paymentId);
        }

        return (float) $q->sum('amount');
    }

    /* ---------------- pieces ---------------- */

    /**
     * Take the person off every attendance device that holds them.
     *
     * Queued rather than sent: the device is behind the college wifi and collects its own work.
     * If no device of our own is set up this does nothing at all, quietly, which is correct.
     */
    protected function takeOffDevices(Student $student)
    {
        try {
            $queue = app(\App\Services\Attendance\EnrolmentQueue::class);
            if ($queue->hasTargets()) {
                $queue->withdraw($student->id);
            }
        } catch (\Throwable $e) {
            Log::warning('Cancelled the admission but could not take the student off the device', [
                'student' => $student->id, 'err' => $e->getMessage(),
            ]);
        }
    }

    /** A readable receipt number, so a refund can be produced on paper and found again. */
    protected function nextVoucherNo()
    {
        $prefix = 'RF-' . date('Ym') . '-';

        $last = DB::table('payment_refunds')
            ->where('voucher_no', 'like', $prefix . '%')
            ->orderBy('id', 'desc')->value('voucher_no');

        $n = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($n, 4, '0', STR_PAD_LEFT);
    }
}
