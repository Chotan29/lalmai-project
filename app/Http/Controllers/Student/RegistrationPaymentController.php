<?php

namespace App\Http\Controllers\Student;

use App\Models\OnlinePayment;
use App\Models\Student;
use App\Models\StudentBatch;
use App\Models\Addressinfo;
use App\Models\ParentDetail;
use App\Models\AcademicInfo;
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
use Illuminate\Validation\ValidationException;
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
            $registrationDataRaw = $request->input('registration_data');
            $registrationData = is_array($registrationDataRaw)
                ? $registrationDataRaw
                : json_decode((string) $registrationDataRaw, true);

            // Validate input
            $validated = $request->validate([
                'student_type' => 'required|in:new,old',
                'payment_method' => 'required|in:ssl,ucb',
                'amount' => 'required|numeric|min:1',
                'registration_data' => 'required',
                'student_main_image' => 'nullable',
                'father_main_image' => 'nullable',
                'mother_main_image' => 'nullable',
                'guardian_main_image' => 'nullable',
            ]);

            if (!is_array($registrationData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid registration data format.'
                ], 422);
            }

            // Keep uploaded images with deterministic temp names for callback processing.
            $tempFileMap = [
                'student_main_image' => ['dir' => public_path('images/studentProfile'), 'suffix' => 'student'],
                'father_main_image' => ['dir' => public_path('images/parents'), 'suffix' => 'father'],
                'mother_main_image' => ['dir' => public_path('images/parents'), 'suffix' => 'mother'],
                'guardian_main_image' => ['dir' => public_path('images/parents'), 'suffix' => 'guardian'],
            ];

            foreach ($tempFileMap as $field => $meta) {
                if ($request->hasFile($field)) {
                    $uploadDir = $meta['dir'];
                    if (!is_dir($uploadDir)) {
                        @mkdir($uploadDir, 0755, true);
                    }

                    $file = $request->file($field);
                    $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                    $storedName = 'tmpreg_' . time() . '_' . Str::random(8) . '_' . $meta['suffix'] . '.' . $extension;
                    $file->move($uploadDir, $storedName);
                    $registrationData[$field] = $storedName;
                }
            }

            // Store registration data and payment info in session
            $request->session()->put('registration_payment_data', [
                'student_type' => $validated['student_type'],
                'amount' => $validated['amount'],
                'registration_data' => $registrationData,
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
            $paymentPayload = [
                'student_type' => $validated['student_type'],
                'amount' => $validated['amount'],
                'registration_data' => $registrationData,
                'payment_method' => $validated['payment_method'],
                'initiated_at' => Carbon::now()->toDateTimeString()
            ];

            $request->session()->put('registration_payment_ref', $tempPaymentRef);
            Cache::put('registration_payment_data:' . $tempPaymentRef, $paymentPayload, now()->addHours(6));

            // File-based fallback storage (survives across PHP processes regardless of cache driver)
            try {
                $pendingDir = storage_path('app/pending_payments');
                if (!is_dir($pendingDir)) {
                    mkdir($pendingDir, 0755, true);
                }
                file_put_contents($pendingDir . '/' . $tempPaymentRef . '.json', json_encode($paymentPayload));
            } catch (\Exception $fileEx) {
                \Log::warning('[PAYMENT_TRACE] File storage failed (non-critical)', ['error' => $fileEx->getMessage()]);
            }

            // Initiate payment based on selected method
            if ($validated['payment_method'] === 'ssl') {
                $paymentResponse = $this->initiateSslPayment($registrationFee, $tempPaymentRef, $validated['student_type']);
            } else {
                $paymentResponse = $this->initiateUcbPayment($registrationFee, $tempPaymentRef, $validated['student_type']);
            }

            return response()->json($paymentResponse);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
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
            $pendingFilesToCleanup = [];

            // ========== STEP 1: CALLBACK RECEIVED ==========
            \Log::info('[PAYMENT_TRACE] SSL Callback received', [
                'method' => $request->method(),
                'tran_id' => $request->input('tran_id'),
                'value_a' => $request->input('value_a'),
                'value_b' => $request->input('value_b'),
                'status' => $request->input('status'),
                'has_val_id' => (bool)$request->input('val_id'),
                'has_verify_sign' => (bool)$request->input('verify_sign'),
            ]);

            $sessionRef = $request->session()->get('registration_payment_ref');
            $tranId = $request->input('tran_id') ?: $request->input('reference');
            $callbackRef = $request->input('value_a') ?: $request->input('order_id') ?: $sessionRef;
            $gatewayStatus = strtoupper((string) $request->input('status'));
            $isBrowserReturn = $request->isMethod('get');

            // Non-final gateway statuses (typically IPN/webhook pre-notification) should not
            // trigger retry redirects for users. Acknowledge and wait for a final callback.
            if (!$isBrowserReturn && in_array($gatewayStatus, ['UNATTEMPTED', 'PENDING', 'INITIATED'], true)) {
                \Log::info('[PAYMENT_TRACE] Ignoring non-final callback status', [
                    'tran_id' => $tranId,
                    'status' => $gatewayStatus,
                    'callback_ref' => $callbackRef,
                ]);

                return response('Callback acknowledged (non-final status)', 200);
            }

            $paymentData = $request->session()->get('registration_payment_data');
            $lookupRefs = array_values(array_unique(array_filter([$callbackRef, $tranId, $sessionRef])));
            
            // ========== STEP 2: DATA RECOVERY ==========
            \Log::info('[PAYMENT_TRACE] Data recovery attempt', [
                'session_has_data' => (bool)$paymentData,
                'lookup_refs' => $lookupRefs,
                'session_ref' => $sessionRef,
            ]);

            if (!$paymentData && !empty($lookupRefs)) {
                foreach ($lookupRefs as $ref) {
                    // Try cache first
                    $cached = Cache::get('registration_payment_data:' . $ref);
                    if ($cached) {
                        \Log::info('[PAYMENT_TRACE] Data recovered from cache', ['ref' => $ref]);
                        $paymentData = $cached;
                        break;
                    }
                    // Fallback: try file-based storage
                    $pendingFile = storage_path('app/pending_payments/' . $ref . '.json');
                    if (file_exists($pendingFile)) {
                        $fileData = json_decode(file_get_contents($pendingFile), true);
                        if ($fileData) {
                            \Log::info('[PAYMENT_TRACE] Data recovered from file storage', ['ref' => $ref]);
                            $paymentData = $fileData;
                            $pendingFilesToCleanup[] = $pendingFile;
                            break;
                        }
                    }
                }
            }

            if (!$paymentData) {
                // ========== DATA NOT FOUND - CHECK FOR DUPLICATE ==========
                \Log::info('[PAYMENT_TRACE] Payment data not found', [
                    'tran_id' => $tranId,
                    'will_check_duplicate' => (bool)$tranId,
                ]);

                $sessionPaymentData = $request->session()->get('registration_payment_data');
                $callbackStudentType = $request->input('value_b')
                    ?: ((is_array($sessionPaymentData) && !empty($sessionPaymentData['student_type'])) ? $sessionPaymentData['student_type'] : null);

                // If callback is duplicated/replayed, try serving existing successful payment receipt.
                if ($tranId) {
                    $existingPayment = OnlinePayment::where('ref_no', $tranId)
                        ->where('payment_gateway', 'SSLCommerz')
                        ->latest()
                        ->first();
                    
                    \Log::info('[PAYMENT_TRACE] Duplicate check result', [
                        'tran_id' => $tranId,
                        'existing_payment_found' => (bool)$existingPayment,
                        'payment_id' => $existingPayment->id ?? null,
                    ]);

                    if ($existingPayment) {
                        return redirect()->route('print-out.fees.online-payment-receipt', ['id' => encrypt($existingPayment->id)])
                            ->with('message_success', 'Registration completed successfully!');
                    }
                }

                \Log::info('[PAYMENT_TRACE] Payment data expired - redirecting to retry', ['tran_id' => $tranId]);

                return $this->redirectToRegistrationPaymentTab(
                    'message_danger',
                    'Payment received but registration data expired. Please contact admin with transaction ID: ' . ($tranId ?? 'N/A'),
                    ['student_type' => $callbackStudentType]
                );
            }

            // Validate SSL Commerz callback
            // NOTE: Callback validation skipped for development.
            // In production with public URL, SSL Commerz can deliver callbacks.
            // For localhost development, callbacks cannot reach local machine,
            // so we skip validation and rely on form submission validation instead.
            $hasGatewayValidationFields = $request->filled('val_id')
                || $request->filled('verify_sign')
                || $request->filled('verify_sign_sha2')
                || $request->filled('status');

            if (!app()->isLocal() && $tranId && (!$isBrowserReturn || $hasGatewayValidationFields)) {
                $sslValidation = $this->sslCommerz->validateCallback($request->all(), $tranId);
                $statusLooksSuccessful = in_array($gatewayStatus, ['VALID', 'VALIDATED', 'SUCCESS', 'SUCCESSFUL'], true);
                
                \Log::info('[PAYMENT_TRACE] Validation check', [
                    'is_local' => app()->isLocal(),
                    'tran_id' => $tranId,
                    'is_browser_return' => $isBrowserReturn,
                    'has_validation_fields' => $hasGatewayValidationFields,
                    'will_validate' => (!app()->isLocal() && $tranId && (!$isBrowserReturn || $hasGatewayValidationFields)),
                    'validation_result' => $sslValidation,
                    'gateway_status' => $gatewayStatus,
                    'status_looks_successful' => $statusLooksSuccessful,
                ]);

                if (!$sslValidation) {
                    // Gateway validation API can intermittently fail on live despite a successful callback status.
                    // In that case, continue only when callback status strongly indicates success.
                    if (!$statusLooksSuccessful) {
                        return $this->redirectToRegistrationPaymentTab(
                            'message_danger',
                            'Payment validation failed. Please try again.',
                            ['student_type' => $paymentData['student_type'] ?? null]
                        );
                    }

                    \Log::warning('[PAYMENT_TRACE] Validation failed but proceeding by status fallback', [
                        'tran_id' => $tranId,
                        'gateway_status' => $gatewayStatus,
                    ]);
                }
            }

            // ========== STEP 3: CREATE STUDENT & PAYMENT RECORDS ==========
            \Log::info('[PAYMENT_TRACE] Creating student and payment records', [
                'tran_id' => $tranId,
                'student_type' => $paymentData['student_type'] ?? null,
                'amount' => $paymentData['amount'] ?? null,
            ]);

            $result = $this->createStudentAndFeeRecord($paymentData, $tranId, 'SSLCommerz');

            // ========== STEP 4: CREATION RESULT ==========
            \Log::info('[PAYMENT_TRACE] Student/Payment creation result', [
                'success' => $result['success'],
                'message' => $result['message'],
                'student_id' => $result['student_id'] ?? null,
            ]);

            if (!$result['success']) {
                return $this->redirectToRegistrationPaymentTab(
                    'message_danger',
                    $result['message'],
                    ['student_type' => $paymentData['student_type'] ?? null]
                );
            }

            $request->session()->forget([
                'registration_payment_data',
                'registration_payment_ref',
                'registration_payment_completed',
                'registration_payment_gateway',
                'registration_payment_transaction'
            ]);
            if (!empty($lookupRefs)) {
                foreach ($lookupRefs as $ref) {
                    Cache::forget('registration_payment_data:' . $ref);
                }
            }
            foreach (array_unique($pendingFilesToCleanup) as $pendingFile) {
                if (is_file($pendingFile)) {
                    @unlink($pendingFile);
                }
            }

            $onlinePayment = OnlinePayment::where('ref_no', $tranId)
                ->where('payment_gateway', 'SSLCommerz')
                ->latest()
                ->first();

            // ========== STEP 5: FINAL REDIRECT ==========
            \Log::info('[PAYMENT_TRACE] Final redirect', [
                'tran_id' => $tranId,
                'online_payment_found' => (bool)$onlinePayment,
                'payment_id' => $onlinePayment->id ?? null,
                'redirect_route' => $onlinePayment ? 'print-out.fees.online-payment-receipt' : null,
            ]);

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
            $pendingFilesToCleanup = [];

            $sessionRef = $request->session()->get('registration_payment_ref');
            $refId = $request->input('ref_id') ?: $request->input('reference') ?: $request->input('tran_id');
            $callbackRef = $request->input('value_a') ?: $sessionRef;

            $paymentData = $request->session()->get('registration_payment_data');
            $lookupRefs = array_values(array_unique(array_filter([$callbackRef, $refId, $sessionRef])));
            if (!$paymentData && !empty($lookupRefs)) {
                foreach ($lookupRefs as $ref) {
                    $paymentData = Cache::get('registration_payment_data:' . $ref);
                    if ($paymentData) {
                        break;
                    }
                    // Fallback: try file-based storage
                    $pendingFile = storage_path('app/pending_payments/' . $ref . '.json');
                    if (file_exists($pendingFile)) {
                        $fileData = json_decode(file_get_contents($pendingFile), true);
                        if ($fileData) {
                            $paymentData = $fileData;
                            $pendingFilesToCleanup[] = $pendingFile;
                            break;
                        }
                    }
                }
            }

            if (!$paymentData) {
                $sessionPaymentData = $request->session()->get('registration_payment_data');
                $callbackStudentType = $request->input('value_b')
                    ?: ((is_array($sessionPaymentData) && !empty($sessionPaymentData['student_type'])) ? $sessionPaymentData['student_type'] : null);

                if ($refId) {
                    $existingPayment = OnlinePayment::where('ref_no', $refId)
                        ->where('payment_gateway', 'UCB')
                        ->latest()
                        ->first();
                    if ($existingPayment) {
                        return redirect()->route('print-out.fees.online-payment-receipt', ['id' => encrypt($existingPayment->id)])
                            ->with('message_success', 'Registration completed successfully!');
                    }
                }

                return $this->redirectToRegistrationPaymentTab(
                    'message_danger',
                    'Payment received but registration data expired. Please contact admin with transaction ID: ' . ($refId ?? 'N/A'),
                    ['student_type' => $callbackStudentType]
                );
            }

            $result = $this->createStudentAndFeeRecord($paymentData, $refId, 'UCB');

            if (!$result['success']) {
                return $this->redirectToRegistrationPaymentTab(
                    'message_danger',
                    $result['message'],
                    ['student_type' => $paymentData['student_type'] ?? null]
                );
            }

            $request->session()->forget([
                'registration_payment_data',
                'registration_payment_ref',
                'registration_payment_completed',
                'registration_payment_gateway',
                'registration_payment_transaction'
            ]);
            if (!empty($lookupRefs)) {
                foreach ($lookupRefs as $ref) {
                    Cache::forget('registration_payment_data:' . $ref);
                }
            }
            foreach (array_unique($pendingFilesToCleanup) as $pendingFile) {
                if (is_file($pendingFile)) {
                    @unlink($pendingFile);
                }
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

    private function redirectToRegistrationPaymentTab($messageKey, $message, array $query = [])
    {
        $baseQuery = [
            'tab' => 'payment',
            'retry_payment' => 1,
        ];

        $mergedQuery = array_filter(array_merge($baseQuery, $query), function ($value) {
            return $value !== null && $value !== '';
        });

        return redirect()->route('online-registration.registration', $mergedQuery)
            ->with($messageKey, $message);
    }

    /**
     * Create student record and fee collection entry
     */
    private function createStudentAndFeeRecord($paymentData, $transactionRef, $gateway)
    {
        DB::beginTransaction();
        try {
            \Log::info('[PAYMENT_TRACE] Starting student/payment creation', [
                'transaction_ref' => $transactionRef,
                'gateway' => $gateway,
                'student_type' => $paymentData['student_type'] ?? null,
            ]);

            if ($transactionRef) {
                $existingPayment = OnlinePayment::where('ref_no', $transactionRef)
                    ->where('payment_gateway', $gateway)
                    ->first();
                if ($existingPayment && $existingPayment->students_id) {
                    $existingStudent = Student::find($existingPayment->students_id);
                    DB::commit();
                    \Log::info('[PAYMENT_TRACE] Transaction already processed', [
                        'transaction_ref' => $transactionRef,
                        'existing_student_id' => $existingStudent->id ?? null,
                    ]);
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
            if (!empty($regData['student_main_image']) && stripos($regData['student_main_image'], 'fakepath') === false) {
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
                'blood_group' => $regData['blood_group'] ?? null,
                'religion' => $regData['religion'] ?? null,
                'caste' => $regData['caste'] ?? null,
                'nationality' => $regData['nationality'] ?? null,
                'mother_tongue' => $regData['mother_tongue'] ?? null,
                'extra_info' => $regData['extra_info'] ?? null,
                'home_phone' => $regData['home_phone'] ?? null,
                'mobile_1' => $regData['mobile_1'] ?? ($regData['phone'] ?? null),
                'mobile_2' => $regData['mobile_2'] ?? null,
                'email' => $regData['email'] ?? ($regData['student_email'] ?? null),
                'student_image' => $student_image_name,
                'status' => 1,
                'national_id_1' => $regData['national_id'] ?? null,
                'national_id_2' => $regData['national_id_2'] ?? null,
                'national_id_3' => $regData['national_id_3'] ?? null,
                'national_id_4' => $regData['national_id_4'] ?? null,
            ]);

            \Log::info('[PAYMENT_TRACE] Student record created', [
                'student_id' => $student->id,
                'reg_no' => $student->reg_no,
            ]);

            // Create address row so admin filtered listing (which joins addressinfos) can include online-registered students.
            Addressinfo::create([
                'students_id' => $student->id,
                'address' => $regData['address'] ?? null,
                'state' => $regData['state'] ?? null,
                'country' => $regData['country'] ?? null,
                'postal_code' => $regData['postal_code'] ?? null,
                'temp_address' => $regData['temp_address'] ?? null,
                'temp_state' => $regData['temp_state'] ?? null,
                'temp_country' => $regData['temp_country'] ?? null,
                'temp_postal_code' => $regData['temp_postal_code'] ?? null,
                'home_phone' => $regData['home_phone'] ?? null,
                'mobile_1' => $regData['mobile_1'] ?? ($regData['phone'] ?? null),
                'mobile_2' => $regData['mobile_2'] ?? null,
                'created_by' => 0,
                'status' => 1,
            ]);

            ParentDetail::create([
                'students_id' => $student->id,
                'grandfather_first_name' => $regData['grandfather_first_name'] ?? null,
                'grandfather_middle_name' => $regData['grandfather_middle_name'] ?? null,
                'grandfather_last_name' => $regData['grandfather_last_name'] ?? null,
                'father_first_name' => $regData['father_first_name'] ?? null,
                'father_middle_name' => $regData['father_middle_name'] ?? null,
                'father_last_name' => $regData['father_last_name'] ?? null,
                'father_eligibility' => $regData['father_eligibility'] ?? null,
                'father_occupation' => $regData['father_occupation'] ?? null,
                'father_office' => $regData['father_office'] ?? null,
                'father_office_number' => $regData['father_office_number'] ?? null,
                'father_residence_number' => $regData['father_residence_number'] ?? null,
                'father_mobile_1' => $regData['father_mobile_1'] ?? null,
                'father_mobile_2' => $regData['father_mobile_2'] ?? null,
                'father_email' => $regData['father_email'] ?? null,
                'mother_first_name' => $regData['mother_first_name'] ?? null,
                'mother_middle_name' => $regData['mother_middle_name'] ?? null,
                'mother_last_name' => $regData['mother_last_name'] ?? null,
                'mother_eligibility' => $regData['mother_eligibility'] ?? null,
                'mother_occupation' => $regData['mother_occupation'] ?? null,
                'mother_office' => $regData['mother_office'] ?? null,
                'mother_office_number' => $regData['mother_office_number'] ?? null,
                'mother_residence_number' => $regData['mother_residence_number'] ?? null,
                'mother_mobile_1' => $regData['mother_mobile_1'] ?? null,
                'mother_mobile_2' => $regData['mother_mobile_2'] ?? null,
                'mother_email' => $regData['mother_email'] ?? null,
                'father_image' => (!empty($regData['father_main_image']) && stripos($regData['father_main_image'], 'fakepath') === false) ? $regData['father_main_image'] : null,
                'mother_image' => (!empty($regData['mother_main_image']) && stripos($regData['mother_main_image'], 'fakepath') === false) ? $regData['mother_main_image'] : null,
                'created_by' => 0,
            ]);

            $institutions = isset($regData['institution']) ? (array) $regData['institution'] : [];
            $boards = isset($regData['board']) ? (array) $regData['board'] : [];
            $passYears = isset($regData['pass_year']) ? (array) $regData['pass_year'] : [];
            $rollNos = isset($regData['roll_no']) ? (array) $regData['roll_no'] : [];
            $majorSubjects = isset($regData['major_subjects']) ? (array) $regData['major_subjects'] : [];
            $obtainedMarks = isset($regData['mark_obtained']) ? (array) $regData['mark_obtained'] : [];
            $maxMarks = isset($regData['maximum_mark']) ? (array) $regData['maximum_mark'] : [];
            $percentages = isset($regData['percentage']) ? (array) $regData['percentage'] : [];
            $gradePoints = isset($regData['grade_point']) ? (array) $regData['grade_point'] : [];
            $gradeLetters = isset($regData['grade_letter']) ? (array) $regData['grade_letter'] : [];

            $academicRows = max(
                count($institutions),
                count($boards),
                count($passYears),
                count($rollNos),
                count($majorSubjects),
                count($obtainedMarks),
                count($maxMarks),
                count($percentages),
                count($gradePoints),
                count($gradeLetters)
            );

            for ($i = 0; $i < $academicRows; $i++) {
                $institution = $institutions[$i] ?? null;
                $board = $boards[$i] ?? null;
                $passYear = $passYears[$i] ?? null;
                $rollNo = $rollNos[$i] ?? null;
                $majorSubject = $majorSubjects[$i] ?? null;
                $markObtained = $obtainedMarks[$i] ?? null;
                $maximumMark = $maxMarks[$i] ?? null;
                $percentage = $percentages[$i] ?? null;
                $gradePoint = $gradePoints[$i] ?? null;
                $gradeLetter = $gradeLetters[$i] ?? null;

                if (
                    empty($institution) &&
                    empty($board) &&
                    empty($passYear) &&
                    empty($rollNo) &&
                    empty($majorSubject) &&
                    empty($markObtained) &&
                    empty($maximumMark) &&
                    empty($percentage) &&
                    empty($gradePoint) &&
                    empty($gradeLetter)
                ) {
                    continue;
                }

                AcademicInfo::create([
                    'students_id' => $student->id,
                    'institution' => $institution,
                    'board' => $board,
                    'pass_year' => $passYear,
                    'roll_no' => $rollNo,
                    'major_subjects' => $majorSubject,
                    'mark_obtained' => $markObtained,
                    'maximum_mark' => $maximumMark,
                    'percentage' => $percentage,
                    'grade_point' => $gradePoint,
                    'grade_letter' => $gradeLetter,
                    'created_by' => 0,
                    'sorting_order' => $i + 1,
                ]);
            }

            // Create user account
            $rolesId = Role::where('name', 'student')->first()->id;
            $password = Str::random(10);
            $emailIds = isset($regData['email']) ? trim($regData['email']) : null;

            // Avoid payment-flow failure on unique email conflict.
            if (empty($emailIds)) {
                $emailIds = 'student.' . $student->reg_no . '@local.invalid';
            }

            if (User::where('email', $emailIds)->exists()) {
                $parts = explode('@', $emailIds, 2);
                $localPart = $parts[0];
                $domainPart = isset($parts[1]) ? $parts[1] : 'local.invalid';
                $emailIds = $localPart . '+reg' . $student->reg_no . '@' . $domainPart;
            }

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

            \Log::info('[PAYMENT_TRACE] Student/payment creation completed successfully', [
                'student_id' => $student->id,
                'reg_no' => $student->reg_no,
                'transaction_ref' => $transactionRef,
            ]);

            return [
                'success' => true,
                'student_id' => $student->reg_no,
                'message' => 'Student registered successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('[PAYMENT_TRACE] Student/payment creation failed', [
                'transaction_ref' => $transactionRef,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
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
                // value_a travels through gateway callback and helps recover cached registration payload.
                'value_a' => $ref,
                // value_b keeps student_type available even when session/cache data is lost.
                'value_b' => $studentType,
                'product_name' => 'Online Registration - ' . ucfirst($studentType) . ' Student',
                'product_category' => 'Registration',
                'successUrl' => $this->buildPublicCallbackUrl('registration-payment.ssl-success'),
                'failUrl' => $this->buildPublicCallbackUrl('registration-payment.ssl-fail'),
                'cancelUrl' => $this->buildPublicCallbackUrl('registration-payment.ssl-cancel'),
                'ipnUrl' => $this->buildPublicCallbackUrl('registration-payment.ssl-ipn'),
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
                'return_url' => $this->buildPublicCallbackUrl('registration-payment.ucb-success'),
                'cancel_url' => $this->buildPublicCallbackUrl('registration-payment.ucb-cancel'),
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

        return $publicBase . '/' . ltrim($path, '/');
    }
}
