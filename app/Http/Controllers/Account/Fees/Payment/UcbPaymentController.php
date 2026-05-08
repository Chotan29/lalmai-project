<?php

namespace App\Http\Controllers\Account\Fees\Payment;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Student;
use App\Models\TemporaryPaymentReference;
use App\Models\OnlinePayment;
use Carbon\Carbon;
use App\Traits\SmsEmailScope;

class UcbPaymentController extends CollegeBaseController
{
    use SmsEmailScope;

    public function paymentForm()
    {
        try {
            $student = $this->getStudentInfo(auth()->user()->hook_id);
            $amount = $this->resolveTotalDueAmount($student);
            // Get address info through the relationship
            $address = $student->address;

            if ($amount <= 0) {
                throw new \Exception("No unpaid due found.");
            }

            $txnId = 'UCB-' . time() . '-' . Str::random(8);
            $invoiceId = 'INV-' . time();
            $uuid = Str::uuid()->toString();

            $signedFields = [
                'access_key', 'profile_id', 'transaction_uuid', 'signed_field_names',
                'signed_date_time', 'locale', 'transaction_type', 'reference_number',
                'auth_trans_ref_no', 'amount', 'currency', 'bill_to_forename',
                'bill_to_surname', 'bill_to_email', 'bill_to_address_line1',
                'bill_to_address_city', 'bill_to_address_state', 'bill_to_address_postal_code',
                'bill_to_address_country', 'override_custom_receipt_page',
                'custom_student_reg_no', 'custom_invoice_id', 'custom_amount', 'custom_user_id',
            ];

            $data = [
                'access_key' => env('UCBL_ACCESS_KEY'),
                'profile_id' => env('UCBL_PROFILE_ID'),
                'transaction_uuid' => $uuid,
                'signed_date_time' => gmdate("Y-m-d\TH:i:s\Z"),
                'locale' => 'en-us',
                'transaction_type' => 'sale',
                'reference_number' => $txnId,
                'auth_trans_ref_no' => $txnId,
                'amount' => $amount,
                'currency' => 'BDT',
                'bill_to_forename' => $student->first_name ?? 'Student',
                'bill_to_surname' => $student->last_name ?? 'User',
                'bill_to_email' => $student->email ?? 'student@example.com',
                'bill_to_address_line1' => $address->address ?? 'Dhaka',
                'bill_to_address_city' => 'Dhaka',
                'bill_to_address_state' => $address->state ?? 'Dhaka',
                'bill_to_address_postal_code' => $address->postal_code ?? '56400',
                'bill_to_address_country' => 'BD',
                'override_custom_receipt_page' => route('ucb.payment.confirmation'),
                'custom_student_reg_no' => $student->reg_no,
                'custom_invoice_id' => $invoiceId,
                'custom_amount' => $amount,
                'custom_user_id' => auth()->id(),
            ];

            $data['signed_field_names'] = implode(',', $signedFields);
            $data['signature'] = $this->sign($data);

            // Store in DB to persist across payment flow
            TemporaryPaymentReference::updateOrCreate(
                ['uuid' => $uuid],
                [
                    'student_reg_no' => $student->reg_no,
                    'invoice_id' => $invoiceId,
                    'ref_no' => $txnId,
                    'amount' => $amount,
                    'created_by' => auth()->id(),
                ]
            );

            return view('account.fees.payment.ucbl.form', compact('data', 'student', 'invoiceId', 'amount'));
        } catch (\Exception $e) {
            Log::error("UCB Payment Init Error: " . $e->getMessage());
            return redirect()->back()->with('warning', 'Unable to initiate payment: ' . $e->getMessage());
        }
    }

    public function paymentConfirmation(Request $request)
    {
        Log::info('UCBL Callback Hit:', $request->all());

        try {
            $decision = strtolower($request->input('decision'));
            $reasonCode = (int) $request->input('reason_code');
            $invoiceId = $request->input('req_reference_number');
            $uuid = $request->input('req_transaction_uuid');
            $transactionId = $request->input('transaction_id');
            $authRef = $request->input('auth_trans_ref_no');

            $paymentDetails = TemporaryPaymentReference::where('uuid', $uuid)->first();
            

            if (!$paymentDetails) {
                // Fallback from gateway custom fields
                $regNo = $request->input('custom_student_reg_no');
                if (!$regNo) {
                    throw new \Exception("Student registration number not found in session or fallback.");
                }

                $paymentDetails = (object) [
                    'student_reg_no' => $regNo,
                    'invoice_id' => $request->input('custom_invoice_id'),
                    'amount' => $request->input('custom_amount'),
                    'ref_no' => $invoiceId,
                    'value_c' => $request->input('custom_user_id'),
                ];
                Log::warning("Fallback used for UUID $uuid");
            }

            // Validate references
            if ($authRef && $invoiceId !== $authRef) {
                throw new \Exception("Reference mismatch. Ref: $invoiceId vs AuthRef: $authRef");
            }

            if ($decision !== 'accept' || $reasonCode !== 100) {
                throw new \Exception("Transaction failed. Decision: $decision, Reason: $reasonCode");
            }

            $regNo = $paymentDetails->student_reg_no ?? null;
            if (!$regNo) {
                throw new \Exception("Student registration number is empty.");
            }

            $student = Student::where('reg_no', $regNo)->first();
            if (!$student) {
                throw new \Exception("Student not found for reg_no: $regNo");
            }

            // Restore login
            if (!auth()->check() && $student->user) {
                auth()->login($student->user);
                session()->regenerate();

                Log::info("Auto-login restored for student $regNo");

                // Force browser to receive session again
                cookie()->queue(
                    cookie(
                        config('session.cookie'),
                        session()->getId(),
                        config('session.lifetime'),
                        config('session.path'),
                        config('session.domain'),
                        config('session.secure'),
                        true, // httpOnly
                        false,
                        config('session.same_site')
                    )
                );
            }

            // Prevent duplicate payment
            $existing = OnlinePayment::where('ref_no', $invoiceId)->first();
            if ($existing) {
                $payment = $existing;
                Log::info("Duplicate payment skipped for ref_no: $invoiceId");
            } else {
                $payment = OnlinePayment::create([
                    'created_by' => $paymentDetails->created_by ?? auth()->id(),
                    'students_id' => $student->id,
                    'date' => Carbon::now(),
                    'amount' => $paymentDetails->amount,
                    'payment_gateway' => 'UCBL',
                    'payment_status' => 'completed',
                    'ref_no' => $invoiceId,
                    'ref_text' => json_encode($request->all()),
                    'invoice_id' => $paymentDetails->invoice_id,
                    'transaction_id' => $transactionId,
                ]);

                $this->sendPaymentReceipt($payment);
            }

            // Cleanup temp storage
            TemporaryPaymentReference::where('uuid', $uuid)->delete();

            return view('account.fees.payment.ucbl.payment_status', [
                'status' => 'success',
                'message' => 'Payment completed successfully.',
                'payment' => $payment,
                'transactionId' => $invoiceId,
            ]);
        } catch (\Exception $e) {
            Log::error("UCBL Payment Confirmation Error: " . $e->getMessage());
            return view('account.fees.payment.ucbl.payment_status', [
                'status' => 'error',
                'message' => $e->getMessage(),
                'transactionId' => null,
            ]);
        }
    }

    protected function sign($params)
    {
        $dataToSign = [];
        foreach (explode(',', $params['signed_field_names']) as $field) {
            $dataToSign[] = $field . "=" . $params[$field];
        }

        $data = implode(",", $dataToSign);
        return base64_encode(hash_hmac('sha256', $data, env('UCBL_SECRET_KEY'), true));
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
}
