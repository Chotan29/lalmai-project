<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\OnlinePayment;
use App\Models\Student;
use App\Services\SslCommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Student self-service recovery.
 *
 * If a student paid and never received the registration form or receipt, they can fix
 * it themselves here: enter the e-mail used on the form (or the transaction id), and
 * the system asks SSLCommerz whether the payment is real. When it is, the registration
 * is completed on the spot and the form and receipt open - no office visit, no phone
 * call, no second payment.
 *
 * Nothing is created unless SSLCommerz confirms the payment, and a transaction that was
 * already processed simply returns its existing receipt.
 */
class RegistrationSelfRecoveryController extends Controller
{
    protected $sslCommerz;

    public function __construct(SslCommerzService $sslCommerz)
    {
        $this->sslCommerz = $sslCommerz;
    }

    /** The public page. */
    public function index()
    {
        return view('student.online-registration.self-recovery');
    }

    /**
     * Find the student's unfinished application, verify the payment and finish it.
     */
    public function recover(Request $request)
    {
        $response = ['error' => true, 'message' => ''];

        $email  = strtolower(trim((string) $request->get('email')));
        $tranId = trim((string) $request->get('tran_id'));

        if ($email === '' && $tranId === '') {
            $response['message'] = 'Please enter the e-mail you used on the application form, or your transaction ID.';
            return response()->json($response);
        }

        /* Rate limit so this page cannot be used to probe the gateway. */
        $throttleKey = 'selfrec:' . $request->ip();
        $attempts = (int) Cache::get($throttleKey, 0);
        if ($attempts > 20) {
            $response['message'] = 'Too many attempts. Please try again after an hour, or contact the college office.';
            return response()->json($response);
        }
        Cache::put($throttleKey, $attempts + 1, now()->addHour());

        /* 1. Already registered with this e-mail? Then nothing is stuck. */
        if ($email !== '') {
            $already = Student::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();
            if ($already) {
                $payment = OnlinePayment::where('students_id', $already->id)->latest()->first();
                $response['error'] = false;
                $response['message'] = 'Good news - your registration is already complete.';
                $response['form_url'] = route('online-registration.print', encrypt($already->id));
                $response['receipt_url'] = $payment
                    ? route('print-out.fees.online-payment-receipt', ['id' => encrypt($payment->id)])
                    : null;
                return response()->json($response);
            }
        }

        /* 2. Find the unfinished application. */
        $match = $tranId !== ''
            ? $this->payloadByRef($tranId)
            : $this->payloadByEmail($email);

        if (!$match) {
            $response['message'] = 'We could not find an unfinished application for this '
                . ($tranId !== '' ? 'transaction ID' : 'e-mail')
                . '. If you paid, please contact the college office with your transaction ID.';
            return response()->json($response);
        }

        $ref = $match['ref'];
        $payload = $match['data'];

        /* 3. Already completed under this reference? Hand back the receipt. */
        $existing = OnlinePayment::where('ref_no', $ref)
            ->where('payment_gateway', 'SSLCommerz')->latest()->first();
        if ($existing && $existing->students_id) {
            $student = Student::find($existing->students_id);
            $response['error'] = false;
            $response['message'] = 'Your registration is already complete.';
            $response['receipt_url'] = route('print-out.fees.online-payment-receipt', ['id' => encrypt($existing->id)]);
            $response['form_url'] = $student ? route('online-registration.print', encrypt($student->id)) : null;
            return response()->json($response);
        }

        /* 4. Ask the gateway whether the money was really taken. */
        $gateway = $this->sslCommerz->queryByTransactionId($ref);

        if (!$gateway['valid']) {
            $response['message'] = 'We found your application, but SSLCommerz has not confirmed a completed '
                . 'payment for it (status: ' . ($gateway['status'] ?: 'not found') . '). '
                . 'If money was deducted, please wait a few minutes and try again, or contact the college office.';
            return response()->json($response);
        }

        /* 5. Complete it, charging exactly what the gateway took. */
        if ($gateway['amount'] > 0) {
            $payload['amount'] = (float) $gateway['amount'];
        }

        try {
            $paymentController = app(RegistrationPaymentController::class);
            $result = $paymentController->createStudentAndFeeRecord($payload, $ref, 'SSLCommerz');
        } catch (\Exception $e) {
            Log::error('[SELF_RECOVERY] Creation failed', ['ref' => $ref, 'error' => $e->getMessage()]);
            $response['message'] = 'Your payment is confirmed, but the registration could not be completed '
                . 'automatically. Please contact the college office with transaction ID: ' . $ref;
            return response()->json($response);
        }

        if (empty($result['success'])) {
            Log::error('[SELF_RECOVERY] Creation unsuccessful', ['ref' => $ref, 'message' => $result['message'] ?? null]);
            $response['message'] = 'Your payment is confirmed, but the registration could not be completed '
                . 'automatically. Please contact the college office with transaction ID: ' . $ref;
            return response()->json($response);
        }

        /* 6. Clean up and hand over the documents. */
        Cache::forget('registration_payment_data:' . $ref);
        Cache::forget('reg_recovery_verify:' . $ref);
        $file = storage_path('app/pending_payments/' . $ref . '.json');
        if (is_file($file)) {
            @unlink($file);
        }

        Log::info('[SELF_RECOVERY] Student recovered their own registration', [
            'ref' => $ref, 'student_reg_no' => $result['student_id'] ?? null,
        ]);

        $payment = OnlinePayment::where('ref_no', $ref)
            ->where('payment_gateway', 'SSLCommerz')->latest()->first();
        $student = !empty($result['student_id'])
            ? Student::where('reg_no', $result['student_id'])->first()
            : null;

        $response['error'] = false;
        $response['message'] = 'Your registration is complete. Please print your form and receipt.';
        $response['receipt_url'] = $payment
            ? route('print-out.fees.online-payment-receipt', ['id' => encrypt($payment->id)])
            : null;
        $response['form_url'] = $student
            ? route('online-registration.print', encrypt($student->id))
            : null;

        return response()->json($response);
    }

    /* ------------------------------------------------------------- helpers */

    protected function payloadByRef($ref)
    {
        $file = storage_path('app/pending_payments/' . $ref . '.json');
        if (is_file($file)) {
            $data = json_decode(@file_get_contents($file), true);
            if (is_array($data)) {
                return ['ref' => $ref, 'data' => $data];
            }
        }

        $cached = Cache::get('registration_payment_data:' . $ref);
        return $cached ? ['ref' => $ref, 'data' => $cached] : null;
    }

    /** Newest unfinished application for this e-mail. */
    protected function payloadByEmail($email)
    {
        $dir = storage_path('app/pending_payments');
        if (!is_dir($dir)) {
            return null;
        }

        $files = glob($dir . DIRECTORY_SEPARATOR . '*.json');
        usort($files, function ($a, $b) { return filemtime($b) - filemtime($a); });

        foreach ($files as $file) {
            $data = json_decode(@file_get_contents($file), true);
            if (!is_array($data)) {
                continue;
            }
            $rowEmail = strtolower(trim((string) ($data['registration_data']['email'] ?? '')));
            if ($rowEmail !== '' && $rowEmail === $email) {
                return ['ref' => basename($file, '.json'), 'data' => $data];
            }
        }

        return null;
    }
}
