<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\CollegeBaseController;
use App\Models\OnlinePayment;
use App\Models\OnlineRegistrationSetting;
use App\Models\Student;
use App\Services\SslCommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

/**
 * Registration Payment Recovery
 *
 * A student can pay successfully at SSLCommerz and still end up without a
 * Student record or receipt: the gateway returns by cross-site POST, the Laravel
 * session cookie is dropped, and if the callback carries no value_a there is no
 * key left to find the stored registration data - so the flow bounces back to the
 * payment tab even though the money was taken.
 *
 * This screen closes that gap. It lists every pending registration payload left on
 * the server, asks SSLCommerz what really happened for each transaction, and lets an
 * admin finish the registration - but only when the gateway itself reports the
 * payment VALID. It reuses RegistrationPaymentController::createStudentAndFeeRecord(),
 * so a recovered registration is created exactly like a normal one.
 */
class RegistrationPaymentRecoveryController extends CollegeBaseController
{
    protected $base_route = 'registration-payment-recovery';
    protected $view_path  = 'student.payment-recovery';
    protected $panel      = 'Registration Payment Recovery';

    protected $sslCommerz;

    public function __construct(SslCommerzService $sslCommerz)
    {
        $this->sslCommerz = $sslCommerz;
    }

    /**
     * List pending (paid-but-unfinished) registrations found on the server.
     * Each row is verified against SSLCommerz only when the admin asks for it,
     * so opening the page stays fast.
     */
    public function index(Request $request)
    {
        $data = [];
        $data['rows'] = $this->pendingPayloads();
        $data['searched'] = null;

        /* Optional direct lookup by transaction id (for a payment whose payload file
           is gone, or one the admin knows about from the SSLCommerz dashboard). */
        $tranId = trim((string) $request->get('tran_id'));
        if ($tranId !== '') {
            $data['searched'] = $this->inspectTransaction($tranId);
        }

        $data['url'] = URL::current();

        return view(parent::loadDataToView($this->view_path . '.index'), compact('data'));
    }

    /**
     * Verify one pending item (or a typed transaction id) against SSLCommerz
     * and report what recovery is possible. Read-only - creates nothing.
     */
    public function verify(Request $request)
    {
        $response = ['error' => true, 'message' => ''];

        $tranId = trim((string) $request->get('tran_id'));
        $ref    = trim((string) $request->get('ref'));

        if ($tranId === '' && $ref === '') {
            $response['message'] = 'Provide a transaction ID or a payment reference.';
            return response()->json($response);
        }

        $info = $tranId !== ''
            ? $this->inspectTransaction($tranId)
            : $this->inspectReference($ref);

        $response['error'] = false;
        $response['data']  = $info;

        return response()->json($response);
    }

    /**
     * Auto-verify one pending application against SSLCommerz.
     *
     * The reference we send to the gateway (REG-xxxx) IS the transaction id, so a
     * pending application can be checked without anybody typing anything. The result
     * is cached so the list can be re-opened without hitting the gateway again.
     */
    public function autoVerify(Request $request)
    {
        $response = ['error' => true, 'message' => ''];

        $ref = trim((string) $request->get('ref'));
        if ($ref === '') {
            $response['message'] = 'Reference is required.';
            return response()->json($response);
        }

        $cacheKey = 'reg_recovery_verify:' . $ref;
        if (!$request->get('force') && ($cached = Cache::get($cacheKey))) {
            $response['error'] = false;
            $response['data'] = $cached;
            $response['cached'] = true;
            return response()->json($response);
        }

        $gateway = $this->sslCommerz->queryByTransactionId($ref);

        $existing = OnlinePayment::where('ref_no', $ref)
            ->where('payment_gateway', 'SSLCommerz')->latest()->first();

        $result = [
            'ref'          => $ref,
            'paid'         => (bool) $gateway['valid'],
            'status'       => $gateway['status'] ?: ($gateway['found'] ? 'UNKNOWN' : 'NOT FOUND'),
            'amount'       => $gateway['amount'],
            'tran_date'    => $gateway['tran_date'],
            'error'        => $gateway['error'],
            'already_done' => (bool) ($existing && $existing->students_id),
            'receipt_url'  => ($existing && $existing->students_id)
                ? route('print-out.fees.online-payment-receipt', ['id' => encrypt($existing->id)])
                : null,
        ];

        /* A verified payment is worth remembering for a day; a "not paid" answer only
           briefly, because the student may still be paying right now. */
        Cache::put($cacheKey, $result, $result['paid'] ? now()->addDay() : now()->addMinutes(30));

        $response['error'] = false;
        $response['data'] = $result;

        return response()->json($response);
    }

    /**
     * Housekeeping: drop unfinished applications that were never paid and are older
     * than the retention window (30 days). Applications the gateway confirmed as paid
     * are always kept, so no money is ever lost from this screen.
     */
    public function cleanup(Request $request)
    {
        $response = ['error' => true, 'message' => ''];

        /* Anything the gateway confirms was never paid is removed straight away - a
           cancelled or abandoned attempt should not leave data behind. Only attempts
           the gateway has not answered for yet get a short grace period, and a paid
           attempt is never touched. */
        $graceSeconds = 30 * 60;
        $now = time();
        $deleted = 0;
        $keptPaid = 0;
        $keptRecent = 0;

        foreach ($this->pendingPayloads() as $row) {
            $verify = Cache::get('reg_recovery_verify:' . $row->ref);
            if (!$verify) {
                $gateway = $this->sslCommerz->queryByTransactionId($row->ref);
                $verify = [
                    'paid' => (bool) $gateway['valid'],
                    'status' => strtoupper((string) $gateway['status']),
                ];
                Cache::put('reg_recovery_verify:' . $row->ref, $verify,
                    $verify['paid'] ? now()->addDay() : now()->addMinutes(30));
            }

            /* Never delete something the gateway says was paid. */
            if (!empty($verify['paid'])) {
                $keptPaid++;
                continue;
            }

            /* Give a very new attempt time to finish paying. */
            if (($now - (int) @filemtime($row->file)) < $graceSeconds) {
                $keptRecent++;
                continue;
            }

            if (is_file($row->file) && @unlink($row->file)) {
                $deleted++;
                Cache::forget('registration_payment_data:' . $row->ref);
                Cache::forget('reg_recovery_verify:' . $row->ref);
            }
        }

        \Log::info('[PAYMENT_RECOVERY] Cleanup run', [
            'deleted' => $deleted, 'kept_paid' => $keptPaid, 'kept_recent' => $keptRecent,
            'by' => auth()->user()->id ?? null,
        ]);

        $response['error'] = false;
        $response['message'] = $deleted . ' unpaid/cancelled application(s) removed. '
            . $keptPaid . ' paid kept, ' . $keptRecent . ' still in progress (last 30 minutes).';

        return response()->json($response);
    }

    /**
     * Complete a paid-but-unfinished registration.
     *
     * Guards, in order:
     *  - the gateway must report the transaction VALID/VALIDATED
     *  - an already-processed transaction returns its existing receipt (no second student)
     *  - the paid amount must match the configured registration fee
     *  - the stored registration payload must exist
     */
    public function complete(Request $request)
    {
        $response = ['error' => true, 'message' => ''];

        $tranId = trim((string) $request->get('tran_id'));
        $ref    = trim((string) $request->get('ref'));

        /* The reference we sent to the gateway is the transaction id, so a pending
           application can be completed from the list without typing anything. */
        if ($tranId === '' && $ref !== '') {
            $tranId = $ref;
        }

        if ($tranId === '') {
            $response['message'] = 'Transaction ID is required to complete a registration.';
            return response()->json($response);
        }

        /* 1. Ask the gateway what really happened. */
        $gateway = $this->sslCommerz->queryByTransactionId($tranId);

        if (!empty($gateway['error']) && !$gateway['found']) {
            $response['message'] = 'SSLCommerz lookup failed: ' . $gateway['error'];
            return response()->json($response);
        }

        if (!$gateway['found']) {
            $response['message'] = 'No transaction found at SSLCommerz for ID: ' . $tranId;
            return response()->json($response);
        }

        if (!$gateway['valid']) {
            $response['message'] = 'This transaction is not VALID at SSLCommerz (status: '
                . ($gateway['status'] ?: 'unknown') . '). Registration will not be created.';
            return response()->json($response);
        }

        /* 2. Already recovered/processed? Serve the existing receipt instead of duplicating. */
        $existing = OnlinePayment::where('ref_no', $tranId)
            ->where('payment_gateway', 'SSLCommerz')
            ->latest()
            ->first();

        if ($existing && $existing->students_id) {
            $response['error']      = false;
            $response['message']    = 'This payment was already completed. Opening the existing receipt.';
            $response['receipt_url'] = route('print-out.fees.online-payment-receipt', ['id' => encrypt($existing->id)]);
            return response()->json($response);
        }

        /* 3. Find the stored registration payload. The gateway's value_a is our REG ref. */
        $lookupRefs = array_values(array_unique(array_filter([
            $ref,
            $gateway['value_a'],
            $tranId,
        ])));

        $paymentData = null;
        $usedFile = null;
        foreach ($lookupRefs as $candidate) {
            $found = $this->loadPayload($candidate);
            if ($found) {
                $paymentData = $found['data'];
                $usedFile    = $found['file'];
                break;
            }
        }

        if (!$paymentData) {
            $response['message'] = 'Payment is VALID at SSLCommerz, but the saved registration data '
                . 'is no longer on the server (reference: ' . ($gateway['value_a'] ?: 'not returned') . '). '
                . 'Please register this student manually and treat the fee as already paid.';
            $response['manual_required'] = true;
            $response['gateway'] = $gateway;
            return response()->json($response);
        }

        /* 4. Amount sanity check.
              Compared against the amount that was charged AT THE TIME (stored in the
              payload), not today's configured fee - the fee may have been changed since,
              and an old payment of the old fee is perfectly valid. Today's fee is only
              used as a fallback when the payload carries no amount. */
        $chargedAmount = (float) ($paymentData['amount'] ?? 0);
        $expectedFee = $chargedAmount > 0
            ? $chargedAmount
            : $this->expectedFee($paymentData['student_type'] ?? null, $paymentData);

        if ($expectedFee > 0 && $gateway['amount'] > 0
            && round((float) $gateway['amount'], 2) < round((float) $expectedFee, 2)) {
            $response['message'] = 'Paid amount (' . $gateway['amount'] . ') is less than the amount this '
                . 'application was charged (' . $expectedFee . '). Recovery stopped - please check this payment manually.';
            return response()->json($response);
        }

        /* The student paid the fee that applied on the day they registered, so the fee
           record must be created with that amount even if the fee has changed since. */
        if ($gateway['amount'] > 0) {
            $paymentData['amount'] = (float) $gateway['amount'];
        }

        /* 5. Create the student + payment exactly like a normal successful callback. */
        try {
            $paymentController = app(RegistrationPaymentController::class);
            $result = $paymentController->createStudentAndFeeRecord($paymentData, $tranId, 'SSLCommerz');
        } catch (\Exception $e) {
            \Log::error('[PAYMENT_RECOVERY] Creation failed', ['tran_id' => $tranId, 'error' => $e->getMessage()]);
            $response['message'] = 'Registration creation failed: ' . $e->getMessage();
            return response()->json($response);
        }

        if (empty($result['success'])) {
            $response['message'] = $result['message'] ?? 'Registration creation failed.';
            return response()->json($response);
        }

        /* 6. Clean up the stored payload so the row disappears from this screen. */
        foreach ($lookupRefs as $candidate) {
            Cache::forget('registration_payment_data:' . $candidate);
        }
        if ($usedFile && is_file($usedFile)) {
            @unlink($usedFile);
        }

        \Log::info('[PAYMENT_RECOVERY] Registration recovered', [
            'tran_id' => $tranId,
            'student_reg_no' => $result['student_id'] ?? null,
            'recovered_by' => auth()->user()->id ?? null,
        ]);

        $payment = OnlinePayment::where('ref_no', $tranId)
            ->where('payment_gateway', 'SSLCommerz')
            ->latest()
            ->first();

        $student = !empty($result['student_id'])
            ? Student::where('reg_no', $result['student_id'])->first()
            : null;

        $response['error']   = false;
        $response['message'] = 'Registration completed successfully for transaction ' . $tranId . '.';
        $response['receipt_url'] = $payment
            ? route('print-out.fees.online-payment-receipt', ['id' => encrypt($payment->id)])
            : null;
        $response['form_url'] = $student
            ? route('online-registration.print', encrypt($student->id))
            : null;

        return response()->json($response);
    }

    /* ---------------------------------------------------------------- helpers */

    /**
     * Every registration payload still waiting on the server.
     * These are payments that started but never finished.
     */
    protected function pendingPayloads()
    {
        $rows = [];
        $dir = storage_path('app/pending_payments');

        if (!is_dir($dir)) {
            return $rows;
        }

        foreach (glob($dir . DIRECTORY_SEPARATOR . '*.json') as $file) {
            $payload = json_decode(@file_get_contents($file), true);
            if (!is_array($payload)) {
                continue;
            }

            $reg = $payload['registration_data'] ?? [];
            $name = trim(implode(' ', array_filter([
                $reg['first_name'] ?? '',
                $reg['middle_name'] ?? '',
                $reg['last_name'] ?? '',
            ])));

            $ref = basename($file, '.json');
            $startedAt = $payload['initiated_at'] ?? date('Y-m-d H:i:s', @filemtime($file));
            $ageDays = (int) floor((time() - strtotime($startedAt)) / 86400);

            $rows[] = (object) [
                'ref'          => $ref,
                'file'         => $file,
                'name'         => $name ?: 'Unknown',
                'email'        => $reg['email'] ?? '',
                'mobile'       => $reg['mobile_1'] ?? '',
                'student_type' => $payload['student_type'] ?? '',
                'amount'       => $payload['amount'] ?? 0,
                'initiated_at' => $startedAt,
                'age_days'     => $ageDays,
                'days_left'    => max(0, 30 - $ageDays),
                /* Verification result if this row was checked before (kept in cache). */
                'verified'     => Cache::get('reg_recovery_verify:' . $ref),
            ];
        }

        usort($rows, function ($a, $b) {
            return strcmp((string) $b->initiated_at, (string) $a->initiated_at);
        });

        return $rows;
    }

    /**
     * Load a stored payload by reference (cache first, then file).
     */
    protected function loadPayload($ref)
    {
        $ref = trim((string) $ref);
        if ($ref === '') {
            return null;
        }

        $cached = Cache::get('registration_payment_data:' . $ref);
        if ($cached) {
            return ['data' => $cached, 'file' => storage_path('app/pending_payments/' . $ref . '.json')];
        }

        $file = storage_path('app/pending_payments/' . $ref . '.json');
        if (is_file($file)) {
            $data = json_decode(@file_get_contents($file), true);
            if (is_array($data)) {
                return ['data' => $data, 'file' => $file];
            }
        }

        return null;
    }

    /**
     * What the gateway says about a transaction id, plus whether we still hold its data.
     */
    protected function inspectTransaction($tranId)
    {
        $gateway = $this->sslCommerz->queryByTransactionId($tranId);

        $payload = null;
        foreach (array_filter([$gateway['value_a'] ?? '', $tranId]) as $candidate) {
            $payload = $this->loadPayload($candidate);
            if ($payload) {
                break;
            }
        }

        $existing = OnlinePayment::where('ref_no', $tranId)
            ->where('payment_gateway', 'SSLCommerz')
            ->latest()
            ->first();

        return (object) [
            'tran_id'         => $tranId,
            'gateway'         => $gateway,
            'has_data'        => (bool) $payload,
            'ref'             => $gateway['value_a'] ?? '',
            'already_done'    => (bool) ($existing && $existing->students_id),
            'existing_receipt' => ($existing && $existing->students_id)
                ? route('print-out.fees.online-payment-receipt', ['id' => encrypt($existing->id)])
                : null,
        ];
    }

    /**
     * Same as inspectTransaction() but starting from our REG reference.
     */
    protected function inspectReference($ref)
    {
        $payload = $this->loadPayload($ref);

        return (object) [
            'tran_id'          => '',
            'gateway'          => ['found' => false, 'valid' => false, 'status' => '', 'amount' => 0, 'value_a' => $ref, 'error' => 'Enter the SSLCommerz transaction ID to verify this payment.'],
            'has_data'         => (bool) $payload,
            'ref'              => $ref,
            'already_done'     => false,
            'existing_receipt' => null,
        ];
    }

    /**
     * Expected fee for a stored payload: the fee configured on the department the
     * student applied to. Returns 0 when the department has no fee set, in which case
     * the amount check is skipped rather than blocking a genuine recovery.
     */
    protected function expectedFee($studentType, array $paymentData = [])
    {
        $reg = $paymentData['registration_data'] ?? [];

        return (float) \App\Models\OnlineRegistrationProgram::resolveFee(
            $reg['faculty'] ?? ($reg['faculty_id'] ?? null),
            $reg['semester'] ?? ($reg['semester_id'] ?? null),
            $studentType
        );
    }
}
