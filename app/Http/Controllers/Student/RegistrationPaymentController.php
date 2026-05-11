<?php

namespace App\Http\Controllers\Student;

use App\Models\OnlinePayment;
use App\Models\Student;
use App\Models\StudentBatch;
use App\Models\Addressinfo;
use App\Models\Role;
use App\Models\OnlineRegistrationSetting;
use App\Models\FeeCollection;
use App\Models\FeeMaster;
use App\Http\Controllers\Controller;
use App\Services\SslCommerzService;
use App\Services\UnitedCommercialBankLimitedService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\User;

class RegistrationPaymentController extends Controller
{
    protected $sslCommerz;
    protected $ucb;

    public function __construct(SslCommerzService $sslCommerz, UnitedCommercialBankLimitedService $ucb)
    {
        $this->sslCommerz = $sslCommerz;
        $this->ucb = $ucb;
    }

    /**
     * Initiate registration payment
     */
    public function pay(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'student_type' => 'required|in:new,old',
                'payment_method' => 'required|in:ssl,ucb',
                'amount' => 'required|numeric|min:1',
                'registration_data' => 'required|json'
            ]);

            // Store registration data and payment info in session
            $request->session()->put('registration_payment_data', [
                'student_type' => $validated['student_type'],
                'amount' => $validated['amount'],
                'registration_data' => json_decode($validated['registration_data'], true),
                'payment_method' => $validated['payment_method'],
                'initiated_at' => Carbon::now()->toDateTimeString()
            ]);
            $request->session()->forget([
                'registration_payment_completed',
                'registration_payment_gateway',
                'registration_payment_transaction'
            ]);

            // Get the amount from settings
            $registrationSetting = OnlineRegistrationSetting::where('status', 'active')
                ->orWhere('status', 1)
                ->first() ?? OnlineRegistrationSetting::first();

            if (!$registrationSetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration settings not found. Please contact admin.'
                ], 422);
            }
            $registrationFee = $validated['student_type'] === 'new' 
                ? $registrationSetting->new_student_registration_fee 
                : $registrationSetting->old_student_registration_fee;

            if ($registrationFee <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration fee is not configured for this student type.'
                ], 422);
            }

            // Create temporary payment reference
            $tempPaymentRef = 'REG-' . Str::random(10) . '-' . time();
            $request->session()->put('registration_payment_ref', $tempPaymentRef);
            Cache::put('registration_payment_data:' . $tempPaymentRef, [
                'student_type' => $validated['student_type'],
                'amount' => $validated['amount'],
                'registration_data' => json_decode($validated['registration_data'], true),
                'payment_method' => $validated['payment_method'],
                'initiated_at' => Carbon::now()->toDateTimeString()
            ], now()->addHours(6));

            // Initiate payment based on selected method
            if ($validated['payment_method'] === 'ssl') {
                $paymentResponse = $this->initiateSslPayment($registrationFee, $tempPaymentRef, $validated['student_type']);
            } else {
                $paymentResponse = $this->initiateUcbPayment($registrationFee, $tempPaymentRef, $validated['student_type']);
            }

            return response()->json($paymentResponse);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * SSL Commerz payment success callback
     */
    public function sslSuccess(Request $request)
    {
        try {
            $tempPaymentRef = $request->session()->get('registration_payment_ref');
            $tranId = $request->input('tran_id', $tempPaymentRef);

            $paymentData = $request->session()->get('registration_payment_data');
            if (!$paymentData && $tranId) {
                $paymentData = Cache::get('registration_payment_data:' . $tranId);
            }

            if (!$paymentData) {
                return $this->redirectToRegistrationPaymentTab(
                    'message_danger',
                    'Payment received but registration data expired. Please contact admin with transaction ID: ' . ($tranId ?? 'N/A')
                );
            }

            // Validate SSL Commerz callback
            // NOTE: Callback validation skipped for development.
            // In production with public URL, SSL Commerz can deliver callbacks.
            // For localhost development, callbacks cannot reach local machine,
            // so we skip validation and rely on form submission validation instead.
            if (!app()->isLocal()) {
                $sslValidation = $this->sslCommerz->validateCallback($request->all(), $tranId);
                
                if (!$sslValidation) {
                    return $this->redirectToRegistrationPaymentTab(
                        'message_danger',
                        'Payment validation failed. Please try again.'
                    );
                }
            }

            $result = $this->createStudentAndFeeRecord($paymentData, $tranId, 'SSLCommerz');

            if (!$result['success']) {
                return $this->redirectToRegistrationPaymentTab('message_danger', $result['message']);
            }

            $request->session()->forget([
                'registration_payment_data',
                'registration_payment_ref',
                'registration_payment_completed',
                'registration_payment_gateway',
                'registration_payment_transaction'
            ]);
            if ($tranId) {
                Cache::forget('registration_payment_data:' . $tranId);
            }

            $onlinePayment = OnlinePayment::where('ref_no', $tranId)
                ->where('payment_gateway', 'SSLCommerz')
                ->latest()
                ->first();
            if ($onlinePayment) {
                return redirect()->route('print-out.fees.online-payment-receipt', ['id' => encrypt($onlinePayment->id)])
                    ->with('message_success', 'Registration completed successfully!');
            }

            $student = Student::where('reg_no', $result['student_id'])->first();
            if ($student) {
                return redirect()->route('online-registration.print', encrypt($student->id))
                    ->with('message_success', 'Registration completed successfully!');
            }

            return redirect()->route('online-registration.find')
                ->with('message_success', 'Registration completed successfully!');

        } catch (\Exception $e) {
            return $this->redirectToRegistrationPaymentTab(
                'message_danger',
                'Payment processing failed: ' . $e->getMessage()
            );
        }
    }

    /**
     * SSL Commerz payment fail callback
     */
    public function sslFail(Request $request)
    {
        return $this->redirectToRegistrationPaymentTab('message_danger', 'Payment failed. Please try again.');
    }

    /**
     * SSL Commerz payment cancel callback
     */
    public function sslCancel(Request $request)
    {
        return $this->redirectToRegistrationPaymentTab(
            'message_warning',
            'Payment cancelled. Please try again if you want to continue.'
        );
    }

    /**
     * UCB payment confirmation callback
     */
    public function ucbSuccess(Request $request)
    {
        try {
            $refId = $request->input('ref_id') ?: $request->input('reference') ?: $request->session()->get('registration_payment_ref');
            $paymentData = $request->session()->get('registration_payment_data');
            if (!$paymentData && $refId) {
                $paymentData = Cache::get('registration_payment_data:' . $refId);
            }

            if (!$paymentData) {
                return $this->redirectToRegistrationPaymentTab(
                    'message_danger',
                    'Payment received but registration data expired. Please contact admin with transaction ID: ' . ($refId ?? 'N/A')
                );
            }

            $result = $this->createStudentAndFeeRecord($paymentData, $refId, 'UCB');

            if (!$result['success']) {
                return $this->redirectToRegistrationPaymentTab('message_danger', $result['message']);
            }

            $request->session()->forget([
                'registration_payment_data',
                'registration_payment_ref',
                'registration_payment_completed',
                'registration_payment_gateway',
                'registration_payment_transaction'
            ]);
            if ($refId) {
                Cache::forget('registration_payment_data:' . $refId);
            }

            $onlinePayment = OnlinePayment::where('ref_no', $refId)
                ->where('payment_gateway', 'UCB')
                ->latest()
                ->first();
            if ($onlinePayment) {
                return redirect()->route('print-out.fees.online-payment-receipt', ['id' => encrypt($onlinePayment->id)])
                    ->with('message_success', 'Registration completed successfully!');
            }

            $student = Student::where('reg_no', $result['student_id'])->first();
            if ($student) {
                return redirect()->route('online-registration.print', encrypt($student->id))
                    ->with('message_success', 'Registration completed successfully!');
            }

            return redirect()->route('online-registration.find')
                ->with('message_success', 'Registration completed successfully!');

        } catch (\Exception $e) {
            return $this->redirectToRegistrationPaymentTab(
                'message_danger',
                'Payment processing failed: ' . $e->getMessage()
            );
        }
    }

    private function redirectToRegistrationPaymentTab($messageKey, $message)
    {
        return redirect()->route('online-registration.registration', [
            'tab' => 'payment',
            'retry_payment' => 1,
        ])
            ->with($messageKey, $message);
    }

    /**
     * Create student record and fee collection entry
     */
    private function createStudentAndFeeRecord($paymentData, $transactionRef, $gateway)
    {
        DB::beginTransaction();
        try {
            if ($transactionRef) {
                $existingPayment = OnlinePayment::where('ref_no', $transactionRef)
                    ->where('payment_gateway', $gateway)
                    ->first();
                if ($existingPayment && $existingPayment->students_id) {
                    $existingStudent = Student::find($existingPayment->students_id);
                    DB::commit();
                    return [
                        'success' => true,
                        'student_id' => $existingStudent ? $existingStudent->reg_no : null,
                        'message' => 'Registration already completed'
                    ];
                }
            }

            $regData = $paymentData['registration_data'];
            
            // Generate registration number
            $oldStudent = Student::where('batch', $regData['batch'])->orderBy('id', 'desc')->first();
            if (!$oldStudent) {
                $sn = 1;
            } else {
                $oldReg = intval(substr($oldStudent->reg_no, -4));
                $sn = $oldReg + 1;
            }

            $batchTitle = StudentBatch::find($regData['batch'])->title;
            $sn = substr("00000{$sn}", -4);
            $regNum = $batchTitle . '/' . $sn;

            // Handle student image
            $student_image_name = "";
            if (isset($regData['student_main_image'])) {
                $student_image_name = $regData['student_main_image'];
            }

            // Create student record
            $student = Student::create([
                'created_by' => 0,
                'reg_no' => Str::slug($regNum),
                'reg_date' => Carbon::today()->toDateString(),
                'faculty' => $regData['faculty'] ?? ($regData['faculty_id'] ?? null),
                'semester' => $regData['semester'] ?? ($regData['semester_id'] ?? null),
                'batch' => $regData['batch'] ?? ($regData['batch_id'] ?? null),
                'academic_status' => 8,
                'student_type' => $paymentData['student_type'],
                'registration_payment_status' => 'completed',
                'first_name' => $regData['first_name'],
                'middle_name' => $regData['middle_name'] ?? null,
                'last_name' => $regData['last_name'] ?? null,
                'date_of_birth' => $regData['date_of_birth'],
                'gender' => $regData['gender'] ?? null,
                'mobile_1' => $regData['mobile_1'] ?? ($regData['phone'] ?? null),
                'email' => $regData['email'] ?? ($regData['student_email'] ?? null),
                'student_image' => $student_image_name,
                'status' => 1,
                'national_id_1' => $regData['national_id'] ?? null,
            ]);

            // Create address row so admin filtered listing (which joins addressinfos) can include online-registered students.
            Addressinfo::create([
                'students_id' => $student->id,
                'address' => $regData['address'] ?? null,
                'state' => $regData['state'] ?? null,
                'country' => $regData['country'] ?? null,
                'postal_code' => $regData['postal_code'] ?? null,
                'mobile_1' => $regData['mobile_1'] ?? ($regData['phone'] ?? null),
                'mobile_2' => $regData['mobile_2'] ?? null,
                'created_by' => 0,
                'status' => 1,
            ]);

            // Create user account
            $rolesId = Role::where('name', 'student')->first()->id;
            $password = Str::random(10);
            $emailIds = $regData['email'];

            $user = User::create([
                'role_id' => $rolesId,
                'hook_id' => $student->id,
                'name' => $regData['first_name'] . ' ' . ($regData['last_name'] ?? ''),
                'email' => $emailIds,
                'password' => Hash::make($password),
                'status' => 'active'
            ]);

            $user->userRole()->sync([['user_id' => $user->id, 'role_id' => $rolesId]]);

            // Create fee master for registration/admission fee so fee_collection has a valid foreign key.
            $admissionFeeHeadId = DB::table('fee_heads')
                ->where('status', 1)
                ->where('fee_head_title', 'like', '%ADMISSION%')
                ->value('id');

            if (!$admissionFeeHeadId) {
                $admissionFeeHeadId = DB::table('fee_heads')
                    ->where('status', 1)
                    ->orderBy('id', 'asc')
                    ->value('id');
            }

            $feeMaster = FeeMaster::create([
                'students_id' => $student->id,
                'semester' => $student->semester ?? ($regData['semester'] ?? 1),
                'fee_head' => $admissionFeeHeadId,
                'fee_due_date' => Carbon::today()->toDateString(),
                'fee_due_date2' => Carbon::today()->toDateString(),
                'fee_due_date3' => Carbon::today()->toDateString(),
                'fee_amount' => $paymentData['amount'],
                'created_by' => 0,
                'status' => 1,
            ]);

            // Create fee collection record for registration payment (admission fee)
            $feeCollection = FeeCollection::create([
                'students_id' => $student->id,
                'fee_masters_id' => $feeMaster->id,
                'date' => Carbon::now(),
                'paid_amount' => $paymentData['amount'],
                'discount' => 0,
                'fine' => 0,
                'payment_method' => 'Online',
                'external_ref_no' => $transactionRef,
                'ref_no' => 'REG-' . $student->reg_no . '-' . Carbon::now()->timestamp,
                'note' => 'Registration/Admission Fee via ' . $gateway,
                'status' => 1,
                'verified_at' => Carbon::now(),
                'created_by' => 0
            ]);

            // Create online payment record
            OnlinePayment::create([
                'students_id' => $student->id,
                'date' => Carbon::now(),
                'amount' => $paymentData['amount'],
                'payment_gateway' => $gateway,
                'ref_no' => $transactionRef,
                'invoice_id' => $feeCollection->id,
                'payment_status' => 'completed',
                'status' => 'active',
                'note' => 'Online Registration Payment',
                'created_by' => 0
            ]);

            DB::commit();

            return [
                'success' => true,
                'student_id' => $student->reg_no,
                'message' => 'Student registered successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to create student record: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Initiate SSL Commerz payment
     */
    private function initiateSslPayment($amount, $ref, $studentType)
    {
        try {
            $paymentData = [
                'amount' => $amount,
                'currency' => 'BDT',
                'orderId' => $ref,
                'product_name' => 'Online Registration - ' . ucfirst($studentType) . ' Student',
                'product_category' => 'Registration',
                'successUrl' => route('registration-payment.ssl-success'),
                'failUrl' => route('registration-payment.ssl-fail'),
                'cancelUrl' => route('registration-payment.ssl-cancel'),
                'ipnUrl' => route('registration-payment.ssl-ipn'),
                'studentName' => 'Student Registration',
                'studentEmail' => 'registration@college.edu',
                'studentPhone' => '01000000000',
                'studentAddress' => 'N/A',
                'studentCity' => 'Cumilla',
                'studentPostcode' => '3500',
                'studentCountry' => 'Bangladesh',
            ];

            $response = $this->sslCommerz->initiatePayment($paymentData);

            if (!empty($response['error'])) {
                return [
                    'success' => false,
                    'message' => 'SSL Commerz initialization failed: ' . $response['error']
                ];
            }
            
            return [
                'success' => true,
                'gateway_url' => $response['GatewayPageURL'] ?? null,
                'message' => 'Redirecting to payment gateway...'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'SSL Commerz initialization failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Initiate UCB payment
     */
    private function initiateUcbPayment($amount, $ref, $studentType)
    {
        try {
            $paymentData = [
                'amount' => $amount,
                'reference' => $ref,
                'description' => 'Online Registration - ' . ucfirst($studentType) . ' Student',
                'return_url' => route('registration-payment.ucb-success'),
                'cancel_url' => route('registration-payment.ucb-cancel'),
            ];

            $response = $this->ucb->initiatePayment($paymentData);

            return [
                'success' => true,
                'gateway_url' => $response['payment_url'] ?? null,
                'message' => 'Redirecting to payment gateway...'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'UCB initialization failed: ' . $e->getMessage()
            ];
        }
    }
}
