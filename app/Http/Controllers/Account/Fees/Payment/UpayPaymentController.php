<?php

namespace App\Http\Controllers\Account\Fees\Payment;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Student;
use App\Models\OnlinePayment;
use App\Services\UpayService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Traits\SmsEmailScope;

class UpayPaymentController extends CollegeBaseController
{
    use SmsEmailScope;
    protected $upayService;

    public function __construct(UpayService $upayService)
    {
        $this->upayService = $upayService;
    }

    public function index()
    {
        return view('account.fees.payment.upay.payment');
    }

    public function pay(Request $request)
    {
        try {
            // Retrieve student information
            $student = $this->getStudentInfo(auth()->user()->hook_id);
            $amount = $this->resolveTotalDueAmount($student);

            if ($amount <= 0) {
                throw new \Exception("No unpaid due found for student: " . $student->reg_no);
            }

            $invoiceId = 'INV-' . time() . '-' . Str::random(5);
            $txnId = 'TXN-' . time() . '-' . Str::random(8);

            $paymentDetails = [
                'txn_id' => $txnId,
                'invoice_id' => $invoiceId,
                'amount' => (float) $amount,
                'redirect_url' => route('fees.upay.callback'),
                'merchant_name' => config('upay.merchant_name', 'Your College Name'),
                'merchant_code' => config('upay.merchant_code'),
                'merchant_country_code' => config('upay.merchant_country_code'),
                'merchant_city' => config('upay.merchant_city'),
                'merchant_category_code' => config('upay.merchant_category_code'),
                'merchant_mobile' => config('upay.merchant_mobile'),
                'transaction_currency_code' => config('upay.transaction_currency_code'),
            ];

            // Store payment details in session instead of database
            Session::put('upay_payment_details', [
                'student_reg_no' => $student->reg_no,
                'amount' => $amount,
                'payment_gateway' => 'Upay',
                'ref_no' => $txnId,
                'invoice_id' => $invoiceId,
                'value_c' => auth()->id(),
                'payment_details' => $paymentDetails
            ]);

            // Call the Upay service to initiate the payment
            $response = $this->upayService->initPayment($paymentDetails);
            //dd($paymentDetails,$response);

            if ($response && isset($response['data']['gateway_url'])) {
                return redirect()->away($response['data']['gateway_url']);
            } else {
                Log::error('Upay Payment Initiation Failed: ' . json_encode($response));
                return redirect()->back()->with('warning', 'Payment initiation with Upay failed. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Upay Payment Error: ' . $e->getMessage());
            return redirect()->back()->with('warning', 'Payment initiation failed: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        $status = $request->input('status');
        $invoiceId = $request->input('invoice_id');

        Log::info('Upay Callback Received:', $request->all());

        try {
            if (!$invoiceId) {
                throw new \Exception('Missing invoice ID in callback.');
            }

            // Retrieve payment details from session instead of database
            $paymentDetails = Session::get('upay_payment_details');
            
            if (!$paymentDetails || $paymentDetails['invoice_id'] !== $invoiceId) {
                throw new \Exception("Payment details not found for invoice ID: {$invoiceId}");
            }

            // --- Session Restoration Logic ---
            if (!auth()->check()) {
                $student = Student::where('reg_no', $paymentDetails['student_reg_no'])->first();
                if ($student && $student->user) {
                    auth()->login($student->user);
                    Session::regenerate();
                    Log::info('Upay Callback: Session restored for user ID: ' . $student->user->id);
                }
            }
            // --- End Session Restoration Logic ---

            $paymentStatus = $this->mapUpayStatusToInternal($status);

            // Verify payment with Upay's API
            $verifiedStatus = null;
            $upayVerificationResponse = null;

            if ($paymentDetails['ref_no']) {
                $upayVerificationResponse = $this->upayService->getSinglePaymentStatus($paymentDetails['ref_no']);
                if ($upayVerificationResponse && isset($upayVerificationResponse['data']['status'])) {
                    $verifiedStatus = $this->mapUpayStatusToInternal($upayVerificationResponse['data']['status']);
                }
                Log::info('Upay Callback Verification Response:', $upayVerificationResponse);
            }

            // Only proceed with database operations if payment is verified as completed
            if ($verifiedStatus === 'completed') {
                // Create the online payment record
                $payment = $onlinePaymentRecord = OnlinePayment::create([
                    'created_by' => $paymentDetails['value_c'] ?? (auth()->check() ? auth()->id() : null),
                    'students_id' => Student::where('reg_no', $paymentDetails['student_reg_no'])->value('id'),
                    'date' => Carbon::now(),
                    'amount' => $paymentDetails['amount'],
                    'payment_gateway' => $paymentDetails['payment_gateway'],
                    'payment_status' => 'completed',
                    'ref_no' => $paymentDetails['ref_no'],
                    'ref_text' => json_encode(array_merge($request->all(), $upayVerificationResponse ?? [])),
                    'invoice_id' => $paymentDetails['invoice_id'],
                    'upay_trx_id' => $upayVerificationResponse['data']['trx_id'] ?? null,
                ]);

                // Clear the session data
                Session::forget('upay_payment_details');

                // Update installment status if needed
                // $this->updateInstallmentStatus($paymentDetails['student_reg_no'], $paymentDetails['amount']);
                // Send payment receipt email
                if ($payment) { 
                    //SmsEmailScope
                    $this->sendPaymentReceipt($payment);
                }

                return view('account.fees.payment.upay.payment_status', [
                    'status' => 'success',
                    'transactionId' => $paymentDetails['ref_no'],
                    'message' => 'Upay payment completed successfully.',
                    'payment' => $onlinePaymentRecord
                ]);
            } else {
                // Payment not completed - don't save to database
                $statusMessage = $verifiedStatus ?: $paymentStatus;
                
                return view('account.fees.payment.upay.payment_status', [
                    'status' => $statusMessage,
                    'transactionId' => $paymentDetails['ref_no'],
                    'message' => 'Upay payment ' . $statusMessage . '.',
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Upay Callback Error: ' . $e->getMessage());
            return view('account.fees.payment.upay.payment_status', [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    protected function mapUpayStatusToInternal($upayStatus)
    {
        switch (strtolower($upayStatus)) {
            case 'success':
                return 'completed';
            case 'failed':
                return 'declined';
            case 'cancelled':
                return 'cancelled';
            case 'pending':
                return 'pending';
            case 'expired':
                return 'expired';
            default:
                return 'unknown';
        }
    }

    protected function getStudentInfo($studentId)
    {
        return Student::with('address')->findOrFail($studentId);
    }

    protected function resolveTotalDueAmount($student)
    {
        $feeAmount = round($student->feeMaster()->sum('fee_amount'), 2);
        $paidAmount = round($student->feeCollect()->sum('paid_amount'), 2);
        $discountAmount = round($student->feeCollect()->sum('discount'), 2);
        $fineAmount = round($student->feeCollect()->sum('fine'), 2);

        return max(0, round(($feeAmount + $fineAmount) - ($paidAmount + $discountAmount), 2));
    }

    // protected function currentUnpaidInstallment($regNo)
    // {
    //     // Your implementation here
    // }

    // protected function updateInstallmentStatus($studentRegNo, $amount)
    // {
    //     // Your implementation here
    // }
}