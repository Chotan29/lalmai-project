<?php
namespace App\Http\Controllers\Account\Fees\Payment;

use App\Http\Controllers\CollegeBaseController;
use App\Services\SslCommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\OnlinePayment;
use App\Models\FeeMaster;
use Carbon\Carbon;
use App\Traits\SmsEmailScope;

class SslCommerzPaymentController extends CollegeBaseController
{
    use SmsEmailScope;

    /** @var SslCommerzService */
    protected $sslCommerzService;

    public function __construct(SslCommerzService $SslCommerzService)
    {
        $this->sslCommerzService = $SslCommerzService;
    }

    public function index()
    {
        return view('account.fees.payment.sslcommerz.payment');
    }

    public function pay(Request $request)
    {
        try {
            $student     = $this->getStudentInfo(auth()->user()->hook_id);
            $installment = $this->resolveInstallmentForPayment($student);

            if (!$installment || !isset($installment['installmentAmount']) || (float) $installment['installmentAmount'] <= 0) {
                throw new \Exception("No unpaid installment found");
            }

            $paymentData = $this->preparePaymentData($student);

            // Keep a pending reference so success callback can recover student context even
            // if passthrough values (value_a/value_b) are missing in gateway callback.
            OnlinePayment::updateOrCreate(
                ['ref_no' => $paymentData['orderId']],
                [
                    'students_id' => $student->id,
                    'date' => Carbon::now(),
                    'amount' => (float) $paymentData['amount'],
                    'payment_gateway' => 'SSLCommerz',
                    'payment_status' => 'pending',
                    'invoice_id' => $student->reg_no,
                    'created_by' => auth()->id(),
                ]
            );

            Log::info('SSLCommerz callback URLs prepared', [
                'success_url' => $paymentData['successUrl'] ?? null,
                'fail_url' => $paymentData['failUrl'] ?? null,
                'cancel_url' => $paymentData['cancelUrl'] ?? null,
                'ipn_url' => $paymentData['ipnUrl'] ?? null,
                'app_url' => config('app.url'),
                'request_host' => request()->getHost(),
            ]);

            $response    = $this->sslCommerzService->initiatePayment($paymentData);

            if (isset($response['error'])) {
                throw new \Exception($response['error']);
            }

            if (empty($response['GatewayPageURL'])) {
                $rawStatus = strtoupper((string) ($response['raw']['status'] ?? ''));
                $rawReason = (string) ($response['raw']['failedreason'] ?? '');
                $message = 'No payment gateway URL received';

                if ($rawStatus !== '') {
                    $message .= ' (status: ' . $rawStatus . ')';
                }

                if ($rawReason !== '') {
                    $message .= ' - ' . $rawReason;
                }

                throw new \Exception($message);
            }

            // Do NOT create any OnlinePayment record here.
            return redirect()->away($response['GatewayPageURL']);

        } catch (\Exception $e) {
            Log::error("Payment Initiation Error: " . $e->getMessage());
            return redirect()->back()
                ->with($this->message_warning, 'Payment initiation failed: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        try {
            $callbackData  = $request->all();
            $transactionId = $callbackData['tran_id'] ?? null;

            if (!$transactionId) {
                throw new \Exception("Transaction ID missing");
            }

            // Validate against SSLCommerz validator API
            $validated = $this->sslCommerzService->validateCallback($callbackData, $transactionId);
            if ($validated === false) {
                throw new \Exception("Payment validation failed");
            }

            // Resolve student with fallback paths for callback payload inconsistencies.
            $student = $this->resolveStudentFromCallback($callbackData, $transactionId);
            if (!$student) {
                throw new \Exception("Student not found");
            }

            $regNo = $student->reg_no;
            $studentId = $student->id;

            $this->restoreStudentSession($student);

            // Prefer amount/currency from validator payload
            $amount   = (float) ($validated['amount'] ?? $callbackData['currency_amount'] ?? 0);
            $currency = (string) ($validated['currency_type'] ?? $callbackData['currency'] ?? 'BDT');

            // Create/update the OnlinePayment ONLY on success here
            $payment = OnlinePayment::updateOrCreate(
                ['ref_no' => $transactionId], // unique key by gateway transaction id
                [
                    'students_id'     => $studentId,
                    'date'            => Carbon::now(),
                    'amount'          => $amount,
                    'payment_gateway' => 'SSLCommerz',
                    'payment_status'  => 'completed',
                    'invoice_id'      => $regNo, // your column name
                    'ref_text'        => json_encode([
                        'callback'  => $callbackData,
                        'validated' => $validated
                    ]),
                    'created_by'      => $callbackData['value_c'] ?? (auth()->id() ?: null),
                ]
            );

            if ($payment) {
                // SmsEmailScope trait method (your implementation)
                $this->sendPaymentReceipt($payment);
            }

            $collectionApply = $this->applyGatewayPaymentToFeeCollection($student, $payment, $amount, $transactionId);

            $student->refresh();

            $statusMessage = 'Payment completed successfully.';
            if (!$collectionApply['applied']) {
                $statusMessage = 'Payment completed, but fee posting is pending: ' . $collectionApply['message'];
            }

            return view('account.fees.payment.sslcommerz.payment_status', [
                'status'        => 'success',
                'transactionId' => $transactionId,
                'message'       => $statusMessage,
                'amount'        => $amount,
                'currency'      => $currency,
                'payment'       => $payment,
                'dueAmount'     => (float) ($student->balance ?? 0),
            ]);

        } catch (\Exception $e) {
            Log::error("Payment Success Error: " . $e->getMessage());
            return view('account.fees.payment.sslcommerz.payment_status', [
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $callbackData  = $request->all();
            $transactionId = $callbackData['tran_id'] ?? null;

            if (!$transactionId) {
                throw new \Exception("Transaction ID missing");
            }

            $this->restoreStudentSessionFromCallback($callbackData, $transactionId);

            $student = $this->resolveStudentFromCallback($callbackData, $transactionId);

            // NOTE: Do NOT create any OnlinePayment record on cancel
            Log::warning("SSLCommerz payment cancelled", [
                'transaction_id' => $transactionId,
                'callback'       => $callbackData,
            ]);

            return view('account.fees.payment.sslcommerz.payment_status', [
                'status'        => 'cancelled',
                'transactionId' => $transactionId,
                'message'       => 'Payment was cancelled',
                'dueAmount'     => (float) ($student->balance ?? 0),
            ]);

        } catch (\Exception $e) {
            Log::error("Cancel Callback Error: " . $e->getMessage());
            return view('account.fees.payment.sslcommerz.payment_status', [
                'status'  => 'error',
                'message' => 'Payment processing error: ' . $e->getMessage()
            ]);
        }
    }

    public function fail(Request $request)
    {
        try {
            $callbackData  = $request->all();
            $transactionId = $callbackData['tran_id'] ?? null;

            if (!$transactionId) {
                throw new \Exception("Transaction ID missing");
            }

            $this->restoreStudentSessionFromCallback($callbackData, $transactionId);

            $student = $this->resolveStudentFromCallback($callbackData, $transactionId);

            // NOTE: Do NOT create any OnlinePayment record on failure
            Log::warning("SSLCommerz payment failed", [
                'transaction_id' => $transactionId,
                'callback'       => $callbackData,
            ]);

            return view('account.fees.payment.sslcommerz.payment_status', [
                'status'        => 'failed',
                'transactionId' => $transactionId,
                'message'       => 'Payment failed. Please try again.',
                'dueAmount'     => (float) ($student->balance ?? 0),
            ]);

        } catch (\Exception $e) {
            Log::error("Fail Callback Error: " . $e->getMessage());
            return view('account.fees.payment.sslcommerz.payment_status', [
                'status'  => 'error',
                'message' => 'Payment processing error: ' . $e->getMessage()
            ]);
        }
    }

    public function ipn(Request $request)
    {
        try {
            $callbackData  = $request->all();
            $transactionId = $callbackData['tran_id'] ?? null;

            if (!$transactionId) {
                throw new \Exception("Transaction ID missing");
            }

            // Optional: validate and just log. We DO NOT write DB here.
            $validated = $this->sslCommerzService->validateCallback($callbackData, $transactionId);
            Log::info("SSLCommerz IPN received", [
                'transaction_id' => $transactionId,
                'validated'      => (bool) $validated,
                'callback'       => $callbackData,
            ]);

            return response()->json(['status' => 'acknowledged']);

        } catch (\Exception $e) {
            Log::error("IPN Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // ----------------- Helper methods -----------------

    protected function getStudentInfo($studentId)
    {
        return Student::with('address')->findOrFail($studentId);
    }

    protected function preparePaymentData($student)
    {
        $installment = $this->resolveInstallmentForPayment($student);

        $studentName = trim(implode(' ', array_filter([
            $student->first_name ?? '',
            $student->middle_name ?? '',
            $student->last_name ?? ''
        ])));

        $address = $student->address;

        return [
            'amount'          => $installment['installmentAmount'],
            'currency'        => 'BDT',
            'orderId'         => 'SSL-' . time() . '-' . $student->reg_no,
            'studentName'     => $studentName,
            'studentEmail'    => $student->email,
            'studentPhone'    => $student->mobile_1,
            'studentAddress'  => $address->address ?? 'Not Provided',
            'studentCity'     => $address->state ?? 'Not Provided',
            'studentPostcode' => $address->postal_code ?? '0000',
            'studentCountry'  => $address->country ?? 'Bangladesh',

            'successUrl'      => $this->buildPublicCallbackUrl('account.fees.sslcommerz.success'),
            'failUrl'         => $this->buildPublicCallbackUrl('account.fees.sslcommerz.fail'),
            'cancelUrl'       => $this->buildPublicCallbackUrl('account.fees.sslcommerz.cancel'),
            'ipnUrl'          => $this->buildPublicCallbackUrl('account.fees.sslcommerz.ipn'),

            'product_name'     => 'Fee Payment',
            'product_category' => 'Education',

            // Pass-through values
            'value_a' => $student->reg_no, // Registration number
            'value_b' => $student->id,     // Student ID
            'value_c' => auth()->id(),     // User ID for created_by
        ];
    }

    protected function buildPublicCallbackUrl(string $routeName): string
    {
        $publicBase = rtrim((string) config('app.url'), '/');

        $currentHost = request()->getHost();
        if (!empty($currentHost) && !in_array($currentHost, ['127.0.0.1', 'localhost'], true)) {
            $publicBase = 'https://' . $currentHost;
        }

        if (strpos($publicBase, 'http://') === 0) {
            $publicBase = 'https://' . ltrim(substr($publicBase, 7), '/');
        }

        $path = route($routeName, [], false);

        // Ensure callback URLs always use the configured public domain, not localhost request host.
        return $publicBase . '/' . ltrim($path, '/');
    }

    /**
     * Kept for compatibility if you ever need it again.
     * Not used in this controller anymore (we only write on success()).
     */
    protected function createOnlinePaymentRecord(array $data)
    {
        $createdBy = $data['value_c'] ?? (auth()->check() ? auth()->user()->id : null);

        $paymentData = [
            'created_by'     => $createdBy,
            'students_id'    => Student::where('reg_no', $data['student_reg_no'])->value('id'),
            'date'           => Carbon::now(),
            'amount'         => $data['amount'],
            'payment_gateway'=> $data['payment_gateway'],
            'payment_status' => $data['status'],
            'ref_no'         => $data['ref_no'] ?? null,
            'ref_text'       => $data['ref_text'] ?? null,
            'invoice_id'     => $data['student_reg_no'],
        ];

        if (empty($paymentData['students_id'])) {
            Log::warning("Student not found for reg_no: " . $data['student_reg_no']);
            $paymentData['students_id'] = null;
        }

        return OnlinePayment::create($paymentData);
    }

    protected function getUserDashboardRoute()
    {
        if (auth()->check()) {
            return $this->getRoleByUserId(auth()->user()->id) == 'Student'
                ? route('user-student')
                : route('user-guardian');
        }
        return route('login');
    }

    protected function getPaymentStatusFromCallback(array $callbackData)
    {
        $status = strtolower($callbackData['status'] ?? 'failed');

        if (in_array($status, ['valid', 'validated'])) {
            return 'completed';
        }

        if (in_array($status, ['failed', 'cancel'])) {
            return $status;
        }

        return 'pending';
    }
    protected function resolveInstallmentForPayment($student)
    {
        // Use calculateInstallments() — returns full due as single payable amount (no installment split)
        $feeMaster = FeeMaster::where('students_id', $student->id)
            ->whereNotIn('fee_head', config('api.excluded_heads', []))
            ->latest()
            ->first();

        $studentName = trim(implode(' ', array_filter([
            $student->first_name ?? '',
            $student->middle_name ?? '',
            $student->last_name ?? '',
        ])));

        if ($feeMaster) {
            $installmentDetails = $feeMaster->calculateInstallments();
            $currentPayable = (float) ($installmentDetails['current_payable_amount'] ?? 0);

            if ($currentPayable > 0) {
                return [
                    'studentId'         => $student->reg_no,
                    'studentName'       => $studentName,
                    'installmentAmount' => $currentPayable,
                ];
            }
        }

        return [
            'studentId'         => $student->reg_no,
            'studentName'       => $studentName,
            'installmentAmount' => 0,
        ];
    }

    /*
    // OLD resolveInstallmentForPayment — kept for reference (do not delete)
    protected function resolveInstallmentForPayment_old($student)
    {
        $totalDue = $this->resolveTotalDueAmount($student);

        if ($totalDue > 0) {
            return [
                'studentId' => $student->reg_no,
                'studentName' => trim(implode(' ', array_filter([
                    $student->first_name ?? '',
                    $student->middle_name ?? '',
                    $student->last_name ?? '',
                ]))),
                'installmentAmount' => $totalDue,
            ];
        }

        $installment = $this->currentUnpaidInstallment($student->reg_no);

        if ($installment && isset($installment['installmentAmount']) && (float) $installment['installmentAmount'] > 0) {
            return $installment;
        }

        $fallback = $this->buildInstallmentFallback($student);

        return [
            'studentId' => $student->reg_no,
            'studentName' => trim(implode(' ', array_filter([
                $student->first_name ?? '',
                $student->middle_name ?? '',
                $student->last_name ?? '',
            ]))),
            'installmentAmount' => $fallback['current_payable_amount'] ?? 0,
        ];
    }
    */

    protected function resolveTotalDueAmount($student)
    {
        $feeAmount = round($student->feeMaster()->sum('fee_amount'), 2);
        $paidAmount = round($student->feeCollect()->sum('paid_amount'), 2);
        $discountAmount = round($student->feeCollect()->sum('discount'), 2);
        $fineAmount = round($student->feeCollect()->sum('fine'), 2);

        return max(0, round(($feeAmount + $fineAmount) - ($paidAmount + $discountAmount), 2));
    }

    protected function buildInstallmentFallback($student)
    {
        $feeMasters = FeeMaster::where('students_id', $student->id)
            ->where('semester', $student->semester)
            ->orderBy('id', 'desc')
            ->get();

        if ($feeMasters->isEmpty()) {
            $feeMasters = FeeMaster::where('students_id', $student->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        if ($feeMasters->isEmpty()) {
            return ['current_payable_amount' => 0];
        }

        $totalAmount = round($feeMasters->sum('fee_amount'), 2);
        $installmentPercentages = [1 => 30, 2 => 40, 3 => 30];
        $currentPayableAmount = 0;

        foreach ($installmentPercentages as $number => $percentage) {
            $initialAmount = round(($totalAmount * $percentage) / 100, 2);
            $paidAmount = round($feeMasters->sum(function ($feeMaster) use ($number) {
                return $feeMaster->collections()
                    ->where('status', 1)
                    ->where('installment_number', $number)
                    ->sum('paid_amount');
            }), 2);
            $discountAmount = round($feeMasters->sum(function ($feeMaster) use ($number) {
                return $feeMaster->collections()
                    ->where('status', 1)
                    ->where('installment_number', $number)
                    ->sum('discount');
            }), 2);
            $fineAmount = round($feeMasters->sum(function ($feeMaster) use ($number) {
                return $feeMaster->collections()
                    ->where('status', 1)
                    ->where('installment_number', $number)
                    ->sum('fine');
            }), 2);

            $dueAmount = max(0, round($initialAmount - ($paidAmount + $discountAmount), 2));
            $finalDueAmount = round($dueAmount + $fineAmount, 2);

            if ($currentPayableAmount <= 0 && $finalDueAmount > 0) {
                $currentPayableAmount = $finalDueAmount;
            }
        }

        return ['current_payable_amount' => $currentPayableAmount];
    }

    protected function restoreStudentSessionFromCallback(array $callbackData, ?string $transactionId = null)
    {
        $student = $this->resolveStudentFromCallback($callbackData, $transactionId);

        if ($student) {
            $this->restoreStudentSession($student);
        }
    }

    protected function resolveStudentFromCallback(array $callbackData, ?string $transactionId = null): ?Student
    {
        $studentId = $callbackData['value_b'] ?? null;
        $regNo = $callbackData['value_a'] ?? null;

        if ($studentId) {
            $student = Student::find($studentId);
            if ($student) {
                return $student;
            }
        }

        if ($regNo) {
            $student = Student::where('reg_no', $regNo)->first();
            if ($student) {
                return $student;
            }
        }

        $tranId = $transactionId ?: ($callbackData['tran_id'] ?? null);

        if ($tranId) {
            $payment = OnlinePayment::where('ref_no', $tranId)->first();
            if ($payment && !empty($payment->students_id)) {
                $student = Student::find($payment->students_id);
                if ($student) {
                    return $student;
                }
            }
        }

        if ($tranId && preg_match('/^SSL-\d+-(.+)$/', (string) $tranId, $matches)) {
            $tranRegNo = trim((string) ($matches[1] ?? ''));
            if ($tranRegNo !== '') {
                $student = Student::where('reg_no', $tranRegNo)->first();
                if ($student) {
                    return $student;
                }
            }
        }

        if (auth()->check()) {
            $student = Student::find(auth()->user()->hook_id);
            if ($student) {
                return $student;
            }
        }

        return null;
    }

    protected function applyGatewayPaymentToFeeCollection(Student $student, OnlinePayment $payment, float $amount, string $transactionId): array
    {
        if ($amount <= 0) {
            return ['applied' => false, 'message' => 'Invalid amount'];
        }

        $alreadyApplied = DB::table('fee_collections')
            ->where('external_ref_no', $transactionId)
            ->exists();

        if ($alreadyApplied) {
            $payment->update(['status' => 'active']);
            return ['applied' => true, 'message' => 'Already applied'];
        }

        $payload = [
            'studentId' => $student->reg_no,
            'amountPaid' => round($amount, 2),
            'bankRef' => $transactionId,
            'paymentMethod' => 'Online',
            'gatewayName' => 'SSLCommerz',
        ];

        try {
            $response = app('App\\Http\\Controllers\\API\\PaymentController')
                ->confirmPayment(new Request($payload));

            $responseData = method_exists($response, 'getData') ? $response->getData() : null;
            $success = isset($responseData->success) && $responseData->success;

            if ($success) {
                $payment->update(['status' => 'active']);
                return ['applied' => true, 'message' => 'Applied'];
            }

            $message = isset($responseData->message) ? (string) $responseData->message : 'Fee posting failed';

            if (stripos($message, 'already') !== false && stripos($message, 'reference') !== false) {
                $payment->update(['status' => 'active']);
                return ['applied' => true, 'message' => 'Already applied'];
            }

            Log::warning('SSLCommerz fee posting failed', [
                'transaction_id' => $transactionId,
                'student_id' => $student->id,
                'message' => $message,
                'payload' => $payload,
            ]);

            return ['applied' => false, 'message' => $message];
        } catch (\Throwable $e) {
            Log::error('SSLCommerz fee posting exception', [
                'transaction_id' => $transactionId,
                'student_id' => $student->id,
                'message' => $e->getMessage(),
            ]);

            return ['applied' => false, 'message' => $e->getMessage()];
        }
    }

    protected function restoreStudentSession(Student $student)
    {
        if (!auth()->check() && $student->user) {
            auth()->login($student->user);
            session()->regenerate();
        }
    }
}
