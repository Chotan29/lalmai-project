<?php
namespace App\Http\Controllers\Account\Fees\Payment;

use App\Http\Controllers\CollegeBaseController;
use App\Services\SslCommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\Student;
use App\Models\OnlinePayment;
use Carbon\Carbon;
use App\Traits\SmsEmailScope;

class SslCommerzPaymentController extends CollegeBaseController
{
    use SmsEmailScope;
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
            $student = $this->getStudentInfo(auth()->user()->hook_id);
            $installment = $this->currentUnpaidInstallment($student->reg_no);
            
            if (!$installment || !isset($installment['installmentAmount'])) {
                throw new \Exception("No unpaid installment found");
            }
           // dd($student,$installment);
            $paymentData = $this->preparePaymentData($student);
            
            $response = $this->sslCommerzService->initiatePayment($paymentData);

            if (isset($response['error'])) {
                throw new \Exception($response['error']);
            }

            if (empty($response['GatewayPageURL'])) {
                throw new \Exception("No payment gateway URL received");
            }

            // Store initial payment record with transaction ID
            // $this->createOnlinePaymentRecord([
            //     'student_reg_no' => $student->reg_no,
            //     'amount' => $paymentData['amount'],
            //     'payment_gateway' => 'SSLCommerz',
            //     'status' => 'initiated',
            //     'ref_no' => $paymentData['orderId'], // Store the order ID as ref_no initially
            //     'ref_text' => json_encode($response)
            // ]);

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
            $callbackData = $request->all();
            $transactionId = $callbackData['tran_id'] ?? null;
            
            // Validate transaction
            if (!$transactionId) throw new \Exception("Transaction ID missing");
            if (!$this->sslCommerzService->validateCallback($callbackData, $transactionId)) {
                throw new \Exception("Payment validation failed");
            }

            // Get student info
            $regNo = $callbackData['value_a'] ?? null;
            $studentId = $callbackData['value_b'] ?? null; // Using value_b for student ID
            
            if (!$regNo || !$studentId) {
                throw new \Exception("Student information missing");
            }

            $student = Student::find($studentId);
            if (!$student) throw new \Exception("Student not found");

            //dd($student);

            // Restore session - CRUCIAL FIX
            if (!auth()->check()) {
                if ($student->user) {
                    auth()->login($student->user);
                    session()->regenerate(); // This fixes the session persistence
                }
            }

            $amount = $callbackData['currency_amount'] ?? 0;

            // Find or create payment record
            $payment = OnlinePayment::updateOrCreate(
                        [
                            'ref_no' => $transactionId,
                            'students_id' => $studentId,
                            'amount' => $amount,
                            'payment_gateway' => 'SSLCommerz',
                            'payment_status' => 'completed',
                            'ref_text' => json_encode($callbackData),
                            'invoice_id' => $regNo,
                            'created_by' => $callbackData['value_c'] ?? null // Get user ID from callback
                        ]
                    );

            // Send payment receipt email
            if ($payment) { 
                //SmsEmailScope
                $this->sendPaymentReceipt($payment);
            }

            return view('account.fees.payment.upay.payment_status', [
                    'status' => 'success',
                    'transactionId' => $transactionId,
                    'message' => 'United Commercial Bank Ltd payment completed successfully.',
                    'payment' => $payment
                ]);

            // Prepare response with secure session cookie
            // $response = view('account.fees.payment.sslcommerz.payment_status', [
            //     'status' => 'success',
            //     'transactionId' => $transactionId,
            //     'message' => 'Payment successful'
            // ]);

            // return $response->withCookie(cookie(
            //     config('session.cookie'),
            //     session()->getId(),
            //     config('session.lifetime'),
            //     config('session.path'),
            //     config('session.domain'),
            //     config('session.secure'),
            //     true, // httpOnly
            //     false,
            //     config('session.same_site')
            // ));

        } catch (\Exception $e) {
            Log::error("Payment Success Error: " . $e->getMessage());
            return view('account.fees.payment.sslcommerz.payment_status', [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function cancel(Request $request)
    {
        try {
            $callbackData = $request->all();
            $transactionId = $callbackData['tran_id'] ?? null;
            
            if (!$transactionId) {
                throw new \Exception("Transaction ID missing");
            }

            // Get reg_no from callback data
            $regNo = $callbackData['value_a'] ?? null;
            if (!$regNo) {
                throw new \Exception("Student registration number not found");
            }

            // Create payment record without requiring auth
            $this->createOnlinePaymentRecord([
                'student_reg_no' => $regNo,
                'amount' => $callbackData['currency_amount'] ?? 0,
                'payment_gateway' => 'SSLCommerz',
                'status' => 'cancelled',
                'ref_no' => $transactionId,
                'ref_text' => json_encode($callbackData)
            ]);

            return view('account.fees.payment.sslcommerz.payment_status', [
                'status' => 'cancelled',
                'transactionId' => $transactionId,
                'message' => 'Payment was cancelled'
            ]);

        } catch (\Exception $e) {
            Log::error("Cancel Callback Error: " . $e->getMessage());
            return view('account.fees.payment.sslcommerz.payment_status', [
                'status' => 'error',
                'message' => 'Payment processing error: ' . $e->getMessage()
            ]);
        }
    }

    public function fail(Request $request)
    {
        try {
            $callbackData = $request->all();
            $transactionId = $callbackData['tran_id'] ?? null;
            
            if (!$transactionId) {
                throw new \Exception("Transaction ID missing");
            }

            // Get reg_no from callback data
            $regNo = $callbackData['value_a'] ?? null;
            if (!$regNo) {
                throw new \Exception("Student registration number not found");
            }

            // Log the failed attempt without storing in database
            Log::warning("Payment failed for student: $regNo", [
                'transaction_id' => $transactionId,
                'amount' => $callbackData['currency_amount'] ?? 0,
                'callback_data' => $callbackData
            ]);

            return view('account.fees.payment.sslcommerz.payment_status', [
                'status' => 'failed',
                'transactionId' => $transactionId,
                'message' => 'Payment failed. Please try again.'
            ]);

        } catch (\Exception $e) {
            Log::error("Fail Callback Error: " . $e->getMessage());
            return view('account.fees.payment.sslcommerz.payment_status', [
                'status' => 'error',
                'message' => 'Payment processing error: ' . $e->getMessage()
            ]);
        }
    }

    public function ipn(Request $request)
    {
        try {
            $callbackData = $request->all();
            $transactionId = $callbackData['tran_id'] ?? null;
            
            if (!$transactionId) {
                throw new \Exception("Transaction ID missing");
            }

            // Validate payment with SSLCommerz
            if (!$this->sslCommerzService->validateCallback($callbackData, $transactionId)) {
                throw new \Exception("IPN validation failed");
            }

            $regNo = $callbackData['value_a'] ?? null;
            if (!$regNo) {
                throw new \Exception("Student registration number not found");
            }

            $status = $this->getPaymentStatusFromCallback($callbackData);
            
            // Only store successful payments
            if ($status === 'completed') {
                $this->createOnlinePaymentRecord([
                    'student_reg_no' => $regNo,
                    'amount' => $callbackData['currency_amount'] ?? 0,
                    'payment_gateway' => 'SSLCommerz',
                    'status' => $status,
                    'ref_no' => $transactionId,
                    'ref_text' => json_encode($callbackData)
                ]);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error("IPN Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    // Helper methods
    protected function getStudentInfo($studentId)
    {
        return Student::with('address')
            ->findOrFail($studentId);
    }

    protected function preparePaymentData($student)
    {
        $installment = $this->currentUnpaidInstallment($student->reg_no);
        
        $studentName = trim(implode(' ', array_filter([
            $student->first_name ?? '',
            $student->middle_name ?? '',
            $student->last_name ?? ''
        ])));

        // Get address info through the relationship
        $address = $student->address;

        return [
            'amount' => $installment['installmentAmount'],
            'currency' => 'BDT',
            'orderId' => 'SSL-' . time() . '-' . $student->reg_no,
            'studentName' => $studentName,
            'studentEmail' => $student->email,
            'studentPhone' => $student->mobile_1,
            'studentAddress' => $address->address ?? 'Not Provided',
            'studentCity' => $address->state ?? 'Not Provided',
            'studentPostcode' => $address->postal_code ?? '0000',
            'studentCountry' => $address->country ?? 'Bangladesh',
            'successUrl' => route('account.fees.sslcommerz.success'),
            'failUrl' => route('account.fees.sslcommerz.fail'),
            'cancelUrl' => route('account.fees.sslcommerz.cancel'),
            'ipnUrl' => route('account.fees.sslcommerz.ipn'),
            'product_name' => 'Fee Payment',
            'product_category' => 'Education',
            'value_a' => $student->reg_no, // Registration number
            'value_b' => $student->id,     // Student ID
            'value_c' => auth()->id()      // User ID for created_by
        ];
    }

    protected function createOnlinePaymentRecord(array $data)
    {
        // Default values
        $createdBy = $data['value_c'] ?? (auth()->check() ? auth()->user()->id : null);

        // Default values
        $paymentData = [
            'created_by' => $createdBy,
            'students_id' => Student::where('reg_no', $data['student_reg_no'])->value('id'),
            'date' => Carbon::now(),
            'amount' => $data['amount'],
            'payment_gateway' => $data['payment_gateway'],
            'payment_status' => $data['status'],
            'ref_no' => $data['ref_no'] ?? null,
            'ref_text' => $data['ref_text'] ?? null,
            'invoice_id' => $data['student_reg_no']
        ];

        // Handle case where student ID couldn't be found
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
}