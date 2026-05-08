<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\CollegeBaseController;
use App\Models\Student;
use App\Models\FeeMaster;
use App\Models\FeeCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class PaymentController extends CollegeBaseController
{
    public $excludedFeeHeads = [61,74,75]; // Fee heads to exclude from installment calculations
    /**
 * Generate JWT token for API authentication
 * 
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function generateToken(Request $request)
{
    \Log::info('API Token generation attempt', ['email' => $request->email]);

    $credentials = $request->only('email', 'password');

    try {
        // Attempt to verify the credentials and create a token
        if (!$token = JWTAuth::attempt($credentials)) {
            \Log::warning('Invalid API credentials attempt', ['email' => $request->email]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'payload' => null
            ], 401);
        }

        // Get authenticated user
        $user = auth()->user();
        
        // Check if user is active (status = 'active')
        if ($user->status != 'active') {
            \Log::warning('Inactive user attempt to generate token', [
                'user_id' => $user->id,
                'email' => $user->email,
                'status' => $user->status
            ]);
            
            // Invalidate the token (log out the user)
            JWTAuth::invalidate($token);
            
            return response()->json([
                'success' => false,
                'message' => 'Account is not active',
                'payload' => null
            ], 403);
        }

        // Check if user has the "bank" role
        $isBankUser = $user->roles()->where('name', 'bank')->exists();
        
        if (!$isBankUser) {
            \Log::warning('Non-bank user attempt to generate token', [
                'user_id' => $user->id,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')
            ]);
            
            JWTAuth::invalidate($token);
            
            return response()->json([
                'success' => false,
                'message' => 'Access restricted to bank users only',
                'payload' => null
            ], 403);
        }
        
        \Log::info('API Token generated successfully for bank user', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        // Return successful response
        return response()->json([
            'success' => true,
            'message' => null,
            'payload' => [
                'token' => $token,
                'expires_in' => 43200, // 12 hours in seconds
                'expiry' => now()->addSeconds(43200)->toDateTimeString() // Human-readable expiry
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('JWT Token generation error: '.$e->getMessage(), [
            'email' => $request->email,
            'exception' => $e
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Could not create token. Account is not active. Contact University',
            'payload' => null
        ], 500);
    }
}

    // Get Student Info
    /**
 * Fetches student fee details (reusable for both methods)
 */
    public function getStudentInfo($studentId)
    {
        try {
            // Get the full student detail response (JsonResponse object)
            $studentDetailResponse = $this->getStudentDetailInfo($studentId);
            
            // Extract data from the JsonResponse
            $responseData = $studentDetailResponse->getData();

            // Check if the original request failed
            if (!$responseData->success) {
                return response()->json([
                    'success' => false,
                    'message' => $responseData->message
                ], 404);
            }

            // Return simplified payload
            return response()->json([
                'success' => true,
                'message' => 'Student details fetched successfully',
                'payload' => [
                    'studentId' => $responseData->payload->studentId,
                    'studentName' => $responseData->payload->studentName,
                    'installmentAmount' => $responseData->payload->current_payable_amount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function getStudentDetailInfo($studentId)
{
    try {
        
        $student = Student::where('reg_no', $studentId)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        // Get all fee masters for the student
        $feeMasters = FeeMaster::where('students_id', $student->id)
            ->whereNotIn('fee_head', config('api.excluded_heads'))
            ->orderBy('semester', 'desc')
            ->orderBy('fee_due_date', 'asc')
            ->get();

        $studentName = trim(implode(' ', array_filter([
            $student->first_name ?? '',
            $student->middle_name ?? '',
            $student->last_name ?? ''
        ])));

        $latestSemester = $student->semester;
        $currentFeeMasters = $feeMasters->where('semester', $latestSemester);
        $currentFeeMaster = $currentFeeMasters->first();

        // Get the most recent FeeMaster (current or previous)
        $mostRecentFeeMaster = $currentFeeMaster ?? $feeMasters->first();

        // Calculate installments using the model's method
        $installmentDetails = $mostRecentFeeMaster 
            ? $mostRecentFeeMaster->calculateInstallments()
            : [
                'all_installments' => [],
                'current_payable_amount' => 0,
                'total_due' => 0,
                'previous_unpaid' => 0,
                'current_semester_amount' => 0
            ];
            

        // Aggregate semester details
        $semesterDetails = [];
        $groupedFeeMasters = $feeMasters->groupBy('semester')->sortKeysDesc();

        foreach ($groupedFeeMasters as $semester => $semesterFeeMasters) {
            $semesterTotal = round($semesterFeeMasters->sum('fee_amount'), 2);
            $semesterPaid = round($semesterFeeMasters->sum(function ($feeMaster) {
                return $feeMaster->collections()
                    ->where('status', 1)
                    ->sum('paid_amount');
            }), 2);
            $semesterDiscount = round($semesterFeeMasters->sum(function ($feeMaster) {
                return $feeMaster->collections()
                    ->where('status', 1)
                    ->sum('discount');
            }), 2);
            $semesterFine = round($semesterFeeMasters->sum(function ($feeMaster) {
                return $feeMaster->collections()
                    ->where('status', 1)
                    ->sum('fine');
            }), 2);
            $semesterDue = max(0, round(($semesterTotal + $semesterFine) - ($semesterPaid + $semesterDiscount), 2));

            $semesterDetails[] = [
                'semester' => (int) $semester,
                'semester_title' => $this->getSemesterTitle($semester),
                'due_amount' => $semesterDue,
                'is_current' => $semester == $latestSemester
            ];
        }

        // If no fee masters for current semester, include it with zero due
        if (!$currentFeeMasters->isNotEmpty() && !collect($semesterDetails)->contains('semester', $latestSemester)) {
            $semesterDetails[] = [
                'semester' => $latestSemester,
                'semester_title' => $this->getSemesterTitle($latestSemester),
                'due_amount' => 0,
                'is_current' => true
            ];
        }

        return response()->json([
            'success' => true,
            'message' => null,
            'payload' => [
                'studentId' => $studentId,
                'studentName' => $studentName,
                'total_due' => $installmentDetails['total_due'],
                'current_semester' => $latestSemester,
                'current_payable_amount' => $installmentDetails['current_payable_amount'],
                //'current_semester_due' => $installmentDetails['current_semester_due'],
                'previous_unpaid' => $installmentDetails['previous_unpaid'],
                'semester_details' => $semesterDetails,
                'installments' => $installmentDetails['all_installments'],
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

 
        public function confirmPayment(Request $request)
    {
        try {
             // Validate input
            $data = $request->only('studentId', 'amountPaid', 'bankRef', 'paymentMethod', 'gatewayName');
            
            $validator = Validator::make($data, [
                            'studentId' => 'required|exists:students,reg_no',
                            'amountPaid' => 'required|numeric|min:1',
                            'bankRef' => 'required|unique:fee_collections,external_ref_no',
                            'paymentMethod' => 'nullable|string|max:50',
                            'gatewayName' => 'nullable|string|max:50'
                        ], [
                            // studentId messages
                            'studentId.required' => 'Student registration number is required.',
                            'studentId.exists' => 'The provided registration number does not exist in our system.',
                            
                            // amountPaid messages
                            'amountPaid.required' => 'Payment amount is required.',
                            'amountPaid.numeric' => 'Payment amount must be a valid number.',
                            'amountPaid.min' => 'Payment amount must be at least :min.',
                            
                            // bankRef messages
                            'bankRef.required' => 'Reference number is required.',
                            'bankRef.unique' => 'This reference has already been used. Please verify the number.',
                            
                            // paymentMethod messages
                            'paymentMethod.string' => 'Payment method must be text.',
                            'paymentMethod.max' => 'Payment method cannot exceed :max characters.',
                            
                            // gatewayName messages
                            'gatewayName.string' => 'Gateway name must be text.',
                            'gatewayName.max' => 'Gateway name cannot exceed :max characters.'
                        ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Get student and details
            $student = Student::where('reg_no', $data['studentId'])->firstOrFail();
            $studentDetailResponse = $this->getStudentDetailInfo($data['studentId']);
            if (!$studentDetailResponse->getData()->success) {
                return $studentDetailResponse;
            }
            $studentDetail = $studentDetailResponse->getData()->payload;
            

            // Get current payable amount with precise decimal handling
            $currentPayableAmount = number_format((float)$studentDetail->current_payable_amount, 2, '.', '');
            //$amountPaid = $currentPayableAmount;
            $amountPaid = number_format((float)$data['amountPaid'], 2, '.', '');


          

            // Check if there are any dues
            if ($studentDetail->total_due == 0 || $currentPayableAmount < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student has no dues to pay.'
                ], 422);
            }

              // Check if payment matches exactly using string comparison
            if ($amountPaid !== $currentPayableAmount) {
                return response()->json([
                    'success' => false,
                    'message' => "Payment amount must be exactly {$currentPayableAmount} for the current installment."
                ], 422);
            }

            DB::beginTransaction();
        

            // Get all fee masters with remaining dues, ordered by semester DESC
            $feeMasters = FeeMaster::where('students_id', $student->id)
                ->whereNotIn('fee_head', config('api.excluded_heads'))
                ->orderBy('semester', 'desc')
                ->with(['collections' => function ($query) {
                    $query->where('status', 1);
                }])
                ->get()
                ->filter(function ($feeMaster) {
                    $feeAmount = number_format($feeMaster->fee_amount, 2, '.', '');
                    $paidAmount = number_format($feeMaster->collections->sum('paid_amount'), 2, '.', '');
                    $discountAmount = number_format($feeMaster->collections->sum('discount'), 2, '.', '');
                    $existingFine = number_format($feeMaster->collections->sum('fine'), 2, '.', '');
                    $feeMasterDue = max(0, $feeAmount + $existingFine - $paidAmount - $discountAmount);
                    return $feeMasterDue >= 0.01;
                });

               

            if ($feeMasters->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No outstanding dues found for payment.'
                ], 422);
            }

             //umesh custom:
            //dd($studentDetail);
            // dd($feeMasters->toArray());

            $remainingAmount = (float)$amountPaid;
            //$paymentMethod = 'Bank';
            $referenceNumbers = [];
            $totalApplied = 0;
            $paymentMethod = $data['paymentMethod'] ?? 'Bank';
            if (!empty($data['gatewayName'])) {
                $paymentMethod = $paymentMethod . ' (' . $data['gatewayName'] . ')';
            }

            // Convert installments to array
            $allInstallments = collect(array_map(function ($installment) {
                return (array) $installment;
            }, (array) $studentDetail->installments));

           // dd($allInstallments);

            // Determine unpaid installments
            $unpaidInstallments = $allInstallments->filter(function ($installment) {
                return (isset($installment['due_amount']) && $installment['due_amount'] > 0) || 
                    (isset($installment['fine']) && $installment['fine'] > 0);
            })->values();
            

            if ($unpaidInstallments->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No unpaid installments found.'
                ], 422);
            }

            // Calculate total fine (only for third installment) with precise rounding
            $thirdInstallmentFine = 0;
            $thirdInstallment = $unpaidInstallments->firstWhere('number', 3);
            if ($thirdInstallment && $thirdInstallment['is_overdue'] && isset($thirdInstallment['fine'])) {
                $thirdInstallmentFine = number_format($thirdInstallment['fine'], 2, '.', '');
            }

            //dd($unpaidInstallments, $thirdInstallmentFine);

            // Select a FeeMaster for fine allocation (current semester preferred)
            $fineFeeMaster = null;
            if ($thirdInstallmentFine > 0) {
                $feeMastersBySemester = $feeMasters->groupBy('semester')->sortKeysDesc();
                if ($feeMastersBySemester->has($studentDetail->current_semester)) {
                    $fineFeeMaster = $feeMastersBySemester->get($studentDetail->current_semester)->first();
                } elseif ($feeMastersBySemester->isNotEmpty()) {
                    $fineFeeMaster = $feeMastersBySemester->first()->first();
                }
            }

            // Determine installments to pay with precise amounts
            $installmentsToPay = [];
            $tempRemaining = (float)$amountPaid;
            //dd($thirdInstallmentFine);
            
            foreach ($unpaidInstallments as $installment) {
                $installmentDue = number_format($installment['initial_due_amount'], 2, '.', '');
                $isThirdInstallment = ($installment['number'] == 3);
                $installmentFine = $isThirdInstallment ? $thirdInstallmentFine : 0;
                
                if ($tempRemaining >= ($installmentDue + $installmentFine)) {
                    $installmentsToPay[] = [
                        'number' => $installment['number'],
                        'amount' => $installmentDue,
                        'fine' => $installmentFine,
                        'fee_master_id' => $installment['fee_master_id'] ?? null,
                        'is_third' => $isThirdInstallment,
                    ];
                    $tempRemaining -= ($installmentDue + $installmentFine);
                } elseif ($tempRemaining > 0) {
                    $amountToPay = min($tempRemaining, $installmentDue + $installmentFine);
                    $installmentsToPay[] = [
                        'number' => $installment['number'],
                        'amount' => min($amountToPay, $installmentDue),
                        'fine' => max(0, $amountToPay - min($amountToPay, $installmentDue)),
                        'fee_master_id' => $installment['fee_master_id'] ?? null,
                        'is_third' => $isThirdInstallment,
                    ];
                    $tempRemaining = 0;
                    break;
                } else {
                    break;
                }
            }
            //dd($installmentsToPay);

            if (empty($installmentsToPay)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No installments could be matched for payment.'
                ], 422);
            }

            // Group fee masters by semester
            $feeMastersBySemester = $feeMasters->groupBy('semester');
            $semesterDues = collect($studentDetail->semester_details)
                ->pluck('due_amount', 'semester')
                ->map(fn($due) => number_format($due, 2, '.', ''))
                ->filter(fn($due) => $due > 0);
                
            $universityUniquePaymentRef = 'REF-' . time() . '-' . mt_rand(1000, 9999);

            // Process each installment with precise distribution
            foreach ($installmentsToPay as $installment) {
                $installmentAmount = (float)$installment['amount'];
                $installmentFine = (float)$installment['fine'];
                $installmentNumber = $installment['number'];
                $isThirdInstallment = $installment['is_third'];
                //array:3 [▼
                    // 0 => array:5 [▼
                    //     "number" => 1
                    //     "amount" => "12000.00"
                    //     "fine" => 0
                    //     "fee_master_id" => null
                    //     "is_third" => false
                    // ]
                    // 1 => array:5 [▼
                    //     "number" => 2
                    //     "amount" => "16000.00"
                    //     "fine" => 0
                    //     "fee_master_id" => null
                    //     "is_third" => false
                    // ]
                    // 2 => array:5 [▼
                    //     "number" => 3
                    //     "amount" => "12000.00"
                    //     "fine" => "500.00"
                    //     "fee_master_id" => null
                    //     "is_third" => true
                    // ]
                    // ]

                if ($remainingAmount < 0.01) {
                    break;
                }

                // Calculate total remaining due for proportional distribution
                $totalRemainingDue = (float)array_sum($semesterDues->toArray());

                // Distribute payment across semesters
                foreach ($semesterDues as $semester => $semesterDue) {
                    if ($remainingAmount <= 0) {
                        break;
                    }

                    $semesterFeeMasters = $feeMastersBySemester->get($semester, collect());
                    if ($semesterFeeMasters->isEmpty()) {
                        continue;
                    }

                    $semesterProportion = $totalRemainingDue > 0 ? 
                        (float)$semesterDue / $totalRemainingDue : 0;
                    $semesterPayment = $installmentAmount * $semesterProportion;
                    $semesterTotal = min($remainingAmount, $semesterPayment, (float)$semesterDue);

                    if ($semesterTotal < 0.01) {
                        continue;
                    }

                    $semesterFeeMasterDue = (float)$semesterFeeMasters->sum(function ($feeMaster) {
                        $feeAmount = number_format($feeMaster->fee_amount, 2, '.', '');
                        $paidAmount = number_format($feeMaster->collections->sum('paid_amount'), 2, '.', '');
                        $discountAmount = number_format($feeMaster->collections->sum('discount'), 2, '.', '');
                        $existingFine = number_format($feeMaster->collections->sum('fine'), 2, '.', '');
                        return max(0, $feeAmount + $existingFine - $paidAmount - $discountAmount);
                    });

                    // Distribute semester payment across fee masters
                    foreach ($semesterFeeMasters as $feeMaster) {
                        if ($remainingAmount <= 0) {
                            break;
                        }

                        $feeHead = $feeMaster->fee_head ?? 'General';
                        $feeAmount = number_format($feeMaster->fee_amount, 2, '.', '');
                        $paidAmount = number_format($feeMaster->collections->sum('paid_amount'), 2, '.', '');
                        $discountAmount = number_format($feeMaster->collections->sum('discount'), 2, '.', '');
                        $existingFine = number_format($feeMaster->collections->sum('fine'), 2, '.', '');
                        $feeMasterDue = max(0, $feeAmount + $existingFine - $paidAmount - $discountAmount);

                        if ($feeMasterDue < 0.01) {
                            continue;
                        }

                        $feeMasterProportion = $semesterFeeMasterDue > 0 ? 
                            $feeMasterDue / $semesterFeeMasterDue : 0;
                        $paymentToApply = $semesterPayment * $feeMasterProportion;
                        $totalToApply = min($remainingAmount, $paymentToApply, $feeMasterDue);

                        if ($totalToApply < 0.01) {
                            continue;
                        }

                        $paidToBalance = $totalToApply;
                        $paidToFine = 0;

                        // Apply fine only for third installment to the designated FeeMaster
                        if ($isThirdInstallment && $installmentFine > 0 && $fineFeeMaster && $feeMaster->id === $fineFeeMaster->id) {
                            $paidToFine = min($installmentFine, $remainingAmount - $paidToBalance);
                            $installmentFine -= $paidToFine;
                        }

                        $collectionData = [
                            'students_id' => $student->id,
                            'fee_masters_id' => $feeMaster->id,
                            'date' => now()->toDateString(),
                            'paid_amount' => number_format($paidToBalance + $paidToFine, 2, '.', ''),
                            'discount' => '0.00',
                            'fine' => number_format($paidToFine, 2, '.', ''),
                            'external_ref_no' => $data['bankRef'],
                            'ref_no' => $universityUniquePaymentRef,
                            'payment_method' => $paymentMethod,
                            'note' => "Installment #{$installmentNumber} Payment | Semester: {$semester} | Fee Head: {$feeHead}" . 
                                    ($paidToFine > 0 ? " | Fine: {$paidToFine}" : ""),
                            'installment_number' => $installmentNumber,
                            'status' => 1,
                            'created_by' => auth()->id() ?? 1,
                        ];

                        $feeCollection = FeeCollection::create($collectionData);
                        $referenceNumbers[] = $feeCollection->ref_no;
                        $totalApplied += ($paidToBalance + $paidToFine);
                        $remainingAmount -= ($paidToBalance + $paidToFine);
                    }
                }
            }

            // Handle small remaining amounts
            if ($remainingAmount > 0 && $remainingAmount <= 500 && !empty($referenceNumbers)) {
                $lastRefNo = end($referenceNumbers);
                $lastFeeCollection = FeeCollection::where('ref_no', $lastRefNo)->first();
                if ($lastFeeCollection) {
                    $lastFeeCollection->paid_amount = number_format(
                        $lastFeeCollection->paid_amount + $remainingAmount, 
                        2, 
                        '.', 
                        ''
                    );
                    $lastFeeCollection->save();
                    $totalApplied += $remainingAmount;
                    $remainingAmount = 0;
                }
            }

            if ($remainingAmount > 500) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Payment could not be fully applied. Remaining: {$remainingAmount}"
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Payment of " . number_format($totalApplied, 2) . " processed successfully",
                'payload' => [
                    'studentId' => $student->reg_no,
                    'referenceNumber' => $universityUniquePaymentRef,
                    'bankRef' => $data['bankRef'],
                    'amountPaid' => number_format($totalApplied, 2),
                    'remainingAmount' => number_format($remainingAmount, 2)
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Payment failed: {$e->getMessage()}"
            ], 500);
        }
    }


    // Verify Payment
    public function verifyPayment($externalRefNo)
    {
        try {
            
            $feeCollections = FeeCollection::select(
                'fee_collections.*',
                'students.reg_no as student_reg_no'
            )
                ->join('students', 'fee_collections.students_id', '=', 'students.id')
                ->where('fee_collections.external_ref_no', $externalRefNo)
                ->where('fee_collections.status', 1)
                ->get();

            if ($feeCollections->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active payment found for the provided reference number.'
                ], 404);
            }

            $alreadyVerified = $feeCollections->contains(function ($collection) {
                return !is_null($collection->verified_at);
            });

            if ($alreadyVerified) {
                $firstCollection = $feeCollections->first();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment already verified',
                    // 'payload' => [
                    //     'transactionId' => $firstCollection->ref_no,
                    //     'studentId' => $firstCollection->student_reg_no,
                    //     'amountPaid' => $feeCollections->sum('paid_amount'),
                    //     'paymentDate' => $firstCollection->date . ' 00:00:00',
                    //     'status' => 'completed',
                    //     'verifiedAt' => $firstCollection->verified_at->toDateTimeString(),
                    //     'verificationStatus' => 'verified'
                    // ]
                ], 422);
            }

            DB::beginTransaction();

            $now = Carbon::now();
            $updated = FeeCollection::where('external_ref_no', $externalRefNo)
                ->where('status', 1)
                ->whereNull('verified_at')
                ->update(['verified_at' => $now]);

            if ($updated === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to verify payment. No eligible records updated.'
                ], 500);
            }

            $updatedCollections = FeeCollection::select(
                'fee_collections.*',
                'students.reg_no as student_reg_no'
            )
                ->join('students', 'fee_collections.students_id', '=', 'students.id')
                ->where('fee_collections.external_ref_no', $externalRefNo)
                ->where('fee_collections.status', 1)
                ->get();

            DB::commit();

            $firstCollection = $updatedCollections->first();
            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'payload' => [
                    'transactionId' => $firstCollection->ref_no,
                    'studentId' => $firstCollection->student_reg_no,
                    'amountPaid' => $updatedCollections->sum('paid_amount'),
                    'paymentDate' => $firstCollection->date . ' 00:00:00',
                    'status' => 'completed',
                    'verifiedAt' => $now->toDateTimeString(),
                    'verificationStatus' => 'verified'
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Cancel Payment
    public function cancelPayment(Request $request)
    {
        try {
            $data = $request->only('bankRef', 'reason');//'transactionRef',

            $validator = Validator::make($data, [
                'bankRef' => 'required|exists:fee_collections,external_ref_no',
                //'transactionRef' => 'required|exists:fee_collections,ref_no',
                'reason' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            DB::beginTransaction();

            $payments = FeeCollection::where('external_ref_no', $data['bankRef'])
               // ->where('ref_no', $data['transactionRef'])
                ->get();

            if ($payments->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

            $allInactive = $payments->every(function ($payment) {
                return in_array($payment->status, [0, 'in-active']);
            });

            if ($allInactive) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment is already cancelled'
                ], 422);
            }

            foreach ($payments as $payment) {
                if ($payment->created_at->diffInHours(Carbon::now()) > 24) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Cancellation only allowed within 24 hours'
                    ], 422);
                }
            }

            $totalCancelledAmount = $payments->whereIn('status', [1, 'active'])->sum('paid_amount');

            if ($totalCancelledAmount === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No active payments found to cancel'
                ], 422);
            }

            foreach ($payments as $payment) {
                $payment->update([
                    'status' => 'in-active',
                    'note' => $data['reason'],
                    'last_updated_by' => auth()->id() ?? null
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment cancelled successfully',
                'payload' => [
                    'cancelledAmount' => $totalCancelledAmount,
                    'cancellationTime' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Cancellation failed: {$e->getMessage()}"
            ], 500);
        }
    }

    // Get Report By Date
    public function getReportByDate($date)
    {
        try {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !Carbon::hasFormat($date, 'Y-m-d')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date format. Use YYYY-MM-DD.',
                    'payload' => []
                ], 422);
            }

            $feeCollections = FeeCollection::select(
                'fee_collections.date as transactionDate',
                'students.reg_no as studentId',
                DB::raw("TRIM(CONCAT(COALESCE(students.first_name, ''), ' ', COALESCE(students.middle_name, ''), ' ', COALESCE(students.last_name, ''))) as studentName"),
                'fee_collections.paid_amount as paymentAmount',
                'fee_collections.external_ref_no as externalRefNo',
                'fee_collections.ref_no as referenceNo',
                'fee_collections.note as remarks',
                'fee_collections.verified_at as verifyAt',
                'fee_collections.status'
            )
                ->join('students', 'fee_collections.students_id', '=', 'students.id')
                ->where('fee_collections.date', $date)
                ->orderBy('fee_collections.created_at', 'desc')
                ->get();

            $payload = $feeCollections->map(function ($collection) {
                return [
                    'transactionDate' => $collection->transactionDate,
                    'studentId' => $collection->studentId,
                    'studentName' => $collection->studentName,
                    'paymentAmount' => (float) $collection->paymentAmount,
                    'externalRefNo' => $collection->externalRefNo,
                    'referenceNo' => $collection->referenceNo,
                    'verifyAt' => isset($collection->verifyAt) ? $collection->verifyAt : "N/A",
                    //'remarks' => $collection->remarks,
                    'payment_status' => $collection->status == 1 ? 'success' : 'cancel'
                ];
            })->filter(function ($item) {
                return $item['paymentAmount'] > 0 || ($item['payment_status'] == 'cancel' && !empty($item['remarks']));
            })->values()->all();

            return response()->json([
                'success' => true,
                'message' => null,
                'payload' => $payload
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'payload' => []
            ], 500);
        }
    }

}

