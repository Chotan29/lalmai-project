<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FeeMaster extends Model
{
    protected $table = 'fee_masters';
    protected $fillable = [
        'created_by',
        'last_updated_by',
        'students_id',
        'semester',
        'fee_head',
        'fee_due_date',
        'fee_due_date2',
        'fee_due_date3',
        'fee_amount',
        'status',
        // Recurring billing columns (added 2026-05-31)
        'billing_run_id',
        'billing_period_key',
        'source_type',
    ];

    const INSTALLMENT_PERCENTAGES = [
        1 => 30, // 1st installment 30%
        2 => 40, // 2nd installment 40%
        3 => 30  // 3rd installment 30%
    ];

    public $fine_ammount = 500; // Fine for overdue third installment
    public $excludedFeeHeads = [61,74,75]; // Fee heads to exclude from installment calculations

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id');
    }

    public function billingRun()
    {
        return $this->belongsTo(BillingRun::class, 'billing_run_id');
    }

    public function collections()
    {
        return $this->hasMany(FeeCollection::class, 'fee_masters_id');
    }

    public function feeCollect()
    {
        return $this->hasMany(FeeCollection::class, 'fee_masters_id')->where('status', 1);
    }
    
public function calculateInstallments()
{
    $currentDate = Carbon::now();
    $studentId = $this->students_id;

    // Get all fee masters for the student (excluding excluded heads)
    $allFeeMasters = FeeMaster::where('students_id', $studentId)
        ->whereNotIn('fee_head', config('api.excluded_heads'))
        //->orderBy('semester', 'desc')
        ->latest()
        ->get();

       // dd($allFeeMasters->toArray());

    $calculateOutstanding = function ($feeMasters) {
        return round($feeMasters->sum(function ($feeMaster) {
            $total = round($feeMaster->fee_amount, 2);
            $paid = round($feeMaster->collections()->where('status', 1)->sum('paid_amount'), 2);
            $discount = round($feeMaster->collections()->where('status', 1)->sum('discount'), 2);
            $fine = round($feeMaster->collections()->where('status', 1)->sum('fine'), 2);

            return max(0, round(($total + $fine) - ($paid + $discount), 2));
        }), 2);
    };

    $totalPaid = round($allFeeMasters->sum(function ($feeMaster) {
        return $feeMaster->collections()->where('status', 1)->sum('paid_amount');
    }), 2);

    $totalDiscount = round($allFeeMasters->sum(function ($feeMaster) {
        return $feeMaster->collections()->where('status', 1)->sum('discount');
    }), 2);

    $totalFine = round($allFeeMasters->sum(function ($feeMaster) {
        return $feeMaster->collections()->where('status', 1)->sum('fine');
    }), 2);

    $totalUnpaid = $calculateOutstanding($allFeeMasters);
    $previousUnpaid = $calculateOutstanding($allFeeMasters->where('semester', '!=', $this->semester));
    $currentSemesterDue = $calculateOutstanding($allFeeMasters->where('semester', $this->semester));
    $currentSemesterAmount = round($allFeeMasters->where('semester', $this->semester)->sum('fee_amount'), 2);
    $originalTotalDue = round($allFeeMasters->sum('fee_amount') + $totalFine, 2);

    $dueDateSource = $allFeeMasters->where('semester', $this->semester)->min('fee_due_date')
        ?: $allFeeMasters->min('fee_due_date')
        ?: now()->format('Y-m-d');

    $isOverdue = $totalUnpaid > 0 && $currentDate->gt(Carbon::parse($dueDateSource));
    $status = $totalUnpaid <= 0 ? 'paid' : ($isOverdue ? 'overdue' : 'pending');

    $allInstallments = [[
        'number' => 1,
        'percentage' => 100,
        'initial_due_amount' => $originalTotalDue,
        'due_date' => $dueDateSource,
        'status' => $status,
        'is_overdue' => $isOverdue,
        'paid_amount' => $totalPaid,
        'discount_amount' => $totalDiscount,
        'fine' => $totalFine,
        'due_amount' => $totalUnpaid,
    ]];

    // Format student name
    $fullName = trim(implode(' ', array_filter([
        $this->student->first_name ?? '',
        $this->student->middle_name ?? '',
        $this->student->last_name ?? ''
    ])));

    return [
        'studentId' => $this->student->reg_no,
        'studentName' => $fullName,
        'semester' => $this->semester,
        'current_semester_amount' => $currentSemesterAmount,
        'current_semester_due' => $currentSemesterDue,
        'previous_unpaid' => $previousUnpaid,
        'total_due' => $totalUnpaid,
        'current_payable_amount' => $totalUnpaid,
        'fee_heads' => [], // You can populate this if needed
        'current_installment' => $allInstallments[0],
        'total_paid' => $totalPaid,
        'all_installments' => $allInstallments,
        'original_total_due' => $originalTotalDue
    ];
}


// public function calculateInstallments()
// {
//     $currentDate = Carbon::now();
//     $studentId = $this->students_id;

//     // Get all fee masters for the student (excluding excluded heads)
//     $allFeeMasters = FeeMaster::where('students_id', $studentId)
//         ->whereNotIn('fee_head', config('api.excluded_heads'))
//         ->orderBy('semester', 'desc')
//         ->get();

//     // Calculate total unpaid amount across all semesters (with proper discount/fine logic)
//     $totalUnpaid = $allFeeMasters->sum(function ($feeMaster) {
//         $total = round($feeMaster->fee_amount, 2);
//         $paid = round($feeMaster->collections()
//             ->where('status', 1)
//             ->whereNull('installment_number')
//             ->sum('paid_amount'), 2);
//         $discount = round($feeMaster->collections()
//             ->where('status', 1)
//             ->whereNull('installment_number')
//             ->sum('discount'), 2);
//         $fine = round($feeMaster->collections()
//             ->where('status', 1)
//             ->whereNull('installment_number')
//             ->sum('fine'), 2);
//         return max(0, round(($total + $fine) - ($paid + $discount), 2));
//     });

//     // Calculate previous unpaid amount (for other semesters) with proper discount/fine logic
//     $previousUnpaid = $allFeeMasters->where('semester', '!=', $this->semester)->sum(function ($feeMaster) {
//         $total = round($feeMaster->fee_amount, 2);
//         $paid = round($feeMaster->collections()
//             ->where('status', 1)
//             ->whereNull('installment_number')
//             ->sum('paid_amount'), 2);
//         $discount = round($feeMaster->collections()
//             ->where('status', 1)
//             ->whereNull('installment_number')
//             ->sum('discount'), 2);
//         $fine = round($feeMaster->collections()
//             ->where('status', 1)
//             ->whereNull('installment_number')
//             ->sum('fine'), 2);
//         return max(0, round(($total + $fine) - ($paid + $discount), 2));
//     });

//     // Calculate current semester amount (original fee amount without deductions)
//     $currentSemesterAmount = $allFeeMasters->where('semester', $this->semester)->sum('fee_amount');

//     // Calculate installment payments, discounts and fines
//     $installmentData = [];
//     foreach ([1, 2, 3] as $number) {
//         $installmentData[$number] = [
//             'paid' => round($allFeeMasters->sum(function($feeMaster) use($number) {
//                 return $feeMaster->collections()
//                     ->where('status', 1)
//                     ->where('installment_number', $number)
//                     ->sum('paid_amount');
//             }, 2)),
//             'discount' => round($allFeeMasters->sum(function($feeMaster) use($number) {
//                 return $feeMaster->collections()
//                     ->where('status', 1)
//                     ->where('installment_number', $number)
//                     ->sum('discount');
//             }, 2)),
//             'fine' => round($allFeeMasters->sum(function($feeMaster) use($number) {
//                 return $feeMaster->collections()
//                     ->where('status', 1)
//                     ->where('installment_number', $number)
//                     ->sum('fine');
//             }, 2))
//         ];
//     }

//     // Calculate initial installment amounts (30%, 40%, 30% of total unpaid)
//     $initialInstallmentAmounts = [
//         1 => round(($totalUnpaid * 30) / 100, 2),
//         2 => round(($totalUnpaid * 40) / 100, 2),
//         3 => round(($totalUnpaid * 30) / 100, 2)
//     ];

//     // Get due dates from the most recent FeeMaster
//     $mostRecentFeeMaster = $allFeeMasters->first();
//     $dueDates = [
//         1 => $mostRecentFeeMaster->fee_due_date ?? now()->format('Y-m-d'),
//         2 => $mostRecentFeeMaster->fee_due_date2 ?? now()->format('Y-m-d'),
//         3 => $mostRecentFeeMaster->fee_due_date3 ?? now()->format('Y-m-d')
//     ];

//     // Calculate installment statuses and amounts
//     $allInstallments = [];
//     $unpaidInstallments = [];
//     $currentPayableAmount = 0;
//     $fineApplied = false;

//     foreach (self::INSTALLMENT_PERCENTAGES as $number => $percentage) {
//         $dueDate = $dueDates[$number];
//         $isOverdue = $currentDate->gt(Carbon::parse($dueDate));
        
//         $initialAmount = $initialInstallmentAmounts[$number];
//         $paidAmount = $installmentData[$number]['paid'] ?? 0;
//         $discountAmount = $installmentData[$number]['discount'] ?? 0;
//         $existingFine = $installmentData[$number]['fine'] ?? 0;
        
//         $dueAmount = max(0, round($initialAmount - ($paidAmount + $discountAmount), 2));
        
//         // Apply fine only if installment is overdue and not already paid
//         $fine = $existingFine;
//         if ($isOverdue && $dueAmount > 0 && $fine == 0) {
//             // Apply fine only to the last installment if not already applied
//             if ($number == 3 && !$fineApplied) {
//                 $fine = 500;
//                 $fineApplied = true;
//             }
//         }

//         $status = $dueAmount <= 0 ? 'paid' : ($isOverdue ? 'overdue' : 'pending');

//         $installment = [
//             'number' => $number,
//             'percentage' => $percentage * 100,
//             'initial_due_amount' => $initialAmount,
//             'due_date' => $dueDate,
//             'status' => $status,
//             'is_overdue' => $isOverdue,
//             'paid_amount' => $paidAmount,
//             'discount_amount' => $discountAmount,
//             'fine' => $fine,
//             'due_amount' => max(0, $dueAmount + $fine)
//         ];

//         $allInstallments[] = $installment;

//         if ($dueAmount > 0) {
//             $unpaidInstallments[] = $installment;
//         }
//     }

//     // Calculate current payable amount (sum of all overdue installments or first unpaid)
//     if (!empty($unpaidInstallments)) {
//         if (collect($unpaidInstallments)->where('is_overdue', true)->isNotEmpty()) {
//             // Sum all overdue installments
//             $currentPayableAmount = collect($unpaidInstallments)
//                 ->where('is_overdue', true)
//                 ->sum('due_amount');
//         } else {
//             // Only the first unpaid installment
//             $currentPayableAmount = collect($unpaidInstallments)->first()['due_amount'] ?? 0;
//         }
//     }

//     // Format student name
//     $fullName = trim(implode(' ', array_filter([
//         $this->student->first_name ?? '',
//         $this->student->middle_name ?? '',
//         $this->student->last_name ?? ''
//     ])));

//     return [
//         'studentId' => $this->student->reg_no,
//         'studentName' => $fullName,
//         'semester' => $this->semester,
//         'current_semester_amount' => $currentSemesterAmount,
//         'previous_unpaid' => $previousUnpaid,
//         'total_due' => $totalUnpaid,
//         'current_payable_amount' => round($currentPayableAmount, 2),
//         'fee_heads' => [], // You can populate this if needed
//         'current_installment' => null, // You can calculate this if needed
//         'total_paid' => array_sum(array_column($installmentData, 'paid')),
//         'all_installments' => $allInstallments,
//         'original_total_due' => $totalUnpaid
//     ];
// }



// public function calculateInstallments()
// {
//     $currentDate = Carbon::now();
//     $studentId = $this->students_id;

//     // Get all fee masters for the student (excluding excluded heads)
//     $allFeeMasters = FeeMaster::where('students_id', $studentId)
//         ->whereNotIn('fee_head', config('api.excluded_heads'))
//         ->orderBy('semester', 'desc')
//         ->get();

//     // Calculate total unpaid amount across all semesters
//     $totalUnpaid = $allFeeMasters->sum(function ($feeMaster) {
//         $total = round($feeMaster->fee_amount, 2);
//         $paid = round($feeMaster->collections()
//             ->where('status', 1)
//             ->whereNull('installment_number')
//             ->sum('paid_amount'), 2);
//         $discount = round($feeMaster->collections()
//             ->where('status', 1)
//             ->whereNull('installment_number')
//             ->sum('discount'), 2);
//         $fine = round($feeMaster->collections()
//             ->where('status', 1)
//             ->whereNull('installment_number')
//             ->sum('fine'), 2);
//         return max(0, round(($total + $fine) - ($paid + $discount), 2));
//     });

//     // Calculate total paid via installments
//     $totalPaid = round($allFeeMasters->sum(function($feeMaster) {
//         return $feeMaster->collections()
//             ->where('status', 1)
//             ->whereNotNull('installment_number')
//             ->sum('paid_amount');
//     }), 2);

//     $totalDiscount = round($allFeeMasters->sum(function($feeMaster) {
//         return $feeMaster->collections()
//             ->where('status', 1)
//             ->whereNotNull('installment_number')
//             ->sum('discount');
//     }), 2);

//     $totalFine = round($allFeeMasters->sum(function($feeMaster) {
//         return $feeMaster->collections()
//             ->where('status', 1)
//             ->whereNotNull('installment_number')
//             ->sum('fine');
//     }), 2);

//     // Calculate installment amounts
//     $initialInstallmentAmounts = [
//         1 => round(($totalUnpaid * 30) / 100, 2),
//         2 => round(($totalUnpaid * 40) / 100, 2),
//         3 => round(($totalUnpaid * 30) / 100, 2)
//     ];

//     // Calculate payments per installment
//     $installmentPaid = [];
//     $installmentDiscount = [];
//     $installmentFine = [];
    
//     foreach ([1, 2, 3] as $number) {
//         $installmentPaid[$number] = round($allFeeMasters->sum(function($feeMaster) use($number) {
//             return $feeMaster->collections()
//                 ->where('status', 1)
//                 ->where('installment_number', $number)
//                 ->sum('paid_amount');
//         }), 2);

//         $installmentDiscount[$number] = round($allFeeMasters->sum(function($feeMaster) use($number) {
//             return $feeMaster->collections()
//                 ->where('status', 1)
//                 ->where('installment_number', $number)
//                 ->sum('discount');
//         }), 2);

//         $installmentFine[$number] = round($allFeeMasters->sum(function($feeMaster) use($number) {
//             return $feeMaster->collections()
//                 ->where('status', 1)
//                 ->where('installment_number', $number)
//                 ->sum('fine');
//         }), 2);
//     }

//     // Get due dates from the most recent FeeMaster
//     $mostRecentFeeMaster = $allFeeMasters->first();
//     $dueDate1 = $mostRecentFeeMaster && $mostRecentFeeMaster->fee_due_date 
//         ? $mostRecentFeeMaster->fee_due_date 
//         : now()->format('Y-m-d');
//     $dueDate2 = $mostRecentFeeMaster && $mostRecentFeeMaster->fee_due_date2 
//         ? $mostRecentFeeMaster->fee_due_date2 
//         : now()->format('Y-m-d');
//     $dueDate3 = $mostRecentFeeMaster && $mostRecentFeeMaster->fee_due_date3 
//         ? $mostRecentFeeMaster->fee_due_date3 
//         : now()->format('Y-m-d');

//     // Calculate installment statuses
//     $allInstallments = [];
//     $unpaidInstallments = [];
//     $currentPayableAmount = 0;
//     $fineApplied = false;

//     foreach (self::INSTALLMENT_PERCENTAGES as $number => $percentage) {
//         $dueDate = $number == 1 ? $dueDate1 : ($number == 2 ? $dueDate2 : $dueDate3);
//         //$isOverdue = $currentDate->gt(Carbon::parse($dueDate));
//         $isOverdue = $currentDate->gt(Carbon::parse($dueDate));
        
//         $initialAmount = $initialInstallmentAmounts[$number];
//         $paidAmount = $installmentPaid[$number] ?? 0;
//         $discountAmount = $installmentDiscount[$number] ?? 0;
//         $dueAmount = max(0, round($initialAmount - ($paidAmount + $discountAmount), 2));
        
//         $fine = 0;
//         if ($number == 3 && $isOverdue && $dueAmount > 0 && !$fineApplied) {
//             $fine = 500;
//             $fineApplied = true;
//         }

//         $status = $dueAmount <= 0 ? 'paid' : ($isOverdue ? 'overdue' : 'pending');

//         $installment = [
//             'number' => $number,
//             'percentage' => $percentage * 100,
//             'initial_due_amount' => $initialAmount,
//             'due_date' => $dueDate,
//             'status' => $status,
//             'is_overdue' => $isOverdue,
//             //'fee_master_id' => $mostRecentFeeMaster ? $mostRecentFeeMaster->id : null,
//             'fine' => $fine,
//             'due_amount' => $dueAmount + $fine
//         ];

//         $allInstallments[] = $installment;

//         if ($dueAmount > 0) {
//             $unpaidInstallments[] = $installment;
//         }
//     }

//     // Calculate current payable amount
//     if (!empty($unpaidInstallments)) {
//         $firstUnpaid = collect($unpaidInstallments)->firstWhere('number', 1);
//         $secondUnpaid = collect($unpaidInstallments)->firstWhere('number', 2);
//         $thirdUnpaid = collect($unpaidInstallments)->firstWhere('number', 3);

//         $isFirstOverdue = $firstUnpaid && $firstUnpaid['is_overdue'];
//         $isSecondOverdue = $secondUnpaid && $secondUnpaid['is_overdue'];
//         $isThirdOverdue = $thirdUnpaid && $thirdUnpaid['is_overdue'];

//         if ($isThirdOverdue) {
//             $currentPayableAmount = ($firstUnpaid ? $firstUnpaid['due_amount'] : 0) +
//                                   ($secondUnpaid ? $secondUnpaid['due_amount'] : 0) +
//                                   ($thirdUnpaid ? $thirdUnpaid['due_amount'] : 0);
//         } elseif ($isSecondOverdue) {
//             $currentPayableAmount = ($firstUnpaid ? $firstUnpaid['due_amount'] : 0) +
//                                   ($secondUnpaid ? $secondUnpaid['due_amount'] : 0);
//         } elseif ($isFirstOverdue) {
//             $currentPayableAmount = ($firstUnpaid ? $firstUnpaid['due_amount'] : 0) +
//                                   ($secondUnpaid ? $secondUnpaid['due_amount'] : 0);
//         } else {
//             $currentPayableAmount = collect($unpaidInstallments)->first()['due_amount'] ?? 0;
//         }
//     }

//     // Format student name
//     $fullName = trim(implode(' ', array_filter([
//         $this->student->first_name ?? '',
//         $this->student->middle_name ?? '',
//         $this->student->last_name ?? ''
//     ])));

//     return [
//         'studentId' => $this->student->reg_no,
//         'studentName' => $fullName,
//         'semester' => $this->semester,
//         'current_semester_amount' => $allFeeMasters->where('semester', $this->semester)->sum('fee_amount'),
//         'previous_unpaid' => $allFeeMasters->where('semester', '!=', $this->semester)->sum('fee_amount'),
//         'total_due' => $totalUnpaid,
//         'current_payable_amount' => round($currentPayableAmount, 2),
//         'fee_heads' => [], // You can populate this if needed
//         'current_installment' => null, // You can calculate this if needed
//         'total_paid' => $totalPaid,
//         'all_installments' => $allInstallments,
//         'original_total_due' => $totalUnpaid
//     ];
// }



//new
    // public function calculateInstallments()
    // {
    //     $currentDate = Carbon::now();
    //     $studentId = $this->students_id;

    //     // Get all current fees for the student's current semester
    //     $currentFees = FeeMaster::where('students_id', $studentId)
    //         ->where('semester', $this->semester)
    //         ->whereNotIn('fee_head', config('api.excluded_heads'))
    //         ->get();
    //     $currentPreviousFees = FeeMaster::where('students_id', $studentId)
    //         ->where('semester', '!=', $this->semester)
    //         ->whereNotIn('fee_head',  config('api.excluded_heads'))
    //         ->get();

          

    //     // Calculate previous unpaid (all unpaid amounts from other semesters)
    //     $previousUnpaid = $this->getPreviousUnpaid();

    //     // Return zero dues if no fee masters for current semester
    //     if ($currentFees->isEmpty()) {
    //         $originalTotalDue = $previousUnpaid;

    //         return [
    //             'studentId' => $this->student->reg_no,
    //             'studentName' => $this->student->name,
    //             'semester' => $this->semester,
    //             'current_semester_amount' => 0,
    //             'previous_unpaid' => $previousUnpaid,
    //             'total_due' => $originalTotalDue,
    //             'current_payable_amount' => 0,
    //             'fee_heads' => [],
    //             'current_installment' => null,
    //             'total_paid' => 0,
    //             'all_installments' => [],
    //             'original_total_due' => $originalTotalDue
    //         ];
    //     }

    //     // Calculate total fee amount for current semester
    //     $originalSemesterTotal = round($currentFees->sum('fee_amount'), 2);

    //     // Calculate pre-installment payments (without installment_number)
    //     $preInstallmentPaid = round($currentFees->sum(function($feeMaster) {
    //         return $feeMaster->collections()
    //             ->where('status', 1)
    //             ->whereNull('installment_number')                
    //             ->sum('paid_amount');
    //     }), 2);

    //     $preInstallmentDiscount = round($currentFees->sum(function($feeMaster) {
    //         return $feeMaster->collections()
    //             ->where('status', 1)
    //             ->whereNull('installment_number')
    //             ->sum('discount');
    //     }), 2);

    //     $preInstallmentFine = round($currentFees->sum(function($feeMaster) {
    //         return $feeMaster->collections()
    //             ->where('status', 1)
    //             ->whereNull('installment_number')
    //             ->sum('fine');
    //     }), 2);

    //     // Calculate total paid for current semester (including all payments, for fee_heads)
    //     $totalPaid = round($currentFees->sum(function($feeMaster) {
    //         return $feeMaster->collections()->where('status', 1)->whereNotNull('installment_number')->sum('paid_amount');
    //     }), 2);

    //     $totalDiscount = round($currentFees->sum(function($feeMaster) {
    //         return $feeMaster->collections()->where('status', 1)->whereNotNull('installment_number')->sum('discount');
    //     }), 2);

    //     $totalFine = round($currentFees->sum(function($feeMaster) {
    //         return $feeMaster->collections()->where('status', 1)->whereNotNull('installment_number')->sum('fine');
    //     }), 2);

    //     // Calculate original total due before any payments (including previous semesters)
    //     $originalTotalDue = round($originalSemesterTotal + $this->getPreviousUnpaid(false), 2);

    //     // Adjust total due by subtracting pre-installment payments and discounts
    //     $adjustedTotalDue = max(0, round($originalTotalDue - ($preInstallmentPaid + $preInstallmentDiscount), 2));

    //     // Calculate current semester due using only pre-installment payments
    //     $currentSemesterDue = max(0, round(($originalSemesterTotal + $preInstallmentFine) - ($preInstallmentPaid + $preInstallmentDiscount), 2));

    //     // Calculate total due (current semester due + previous unpaid)
    //     $totalDue = round($currentSemesterDue + $previousUnpaid, 2);

    //     // Define installment amounts based on adjusted total due
    //     // $initialInstallmentAmounts = [
    //     //     1 => round($totalDue * self::INSTALLMENT_PERCENTAGES[1], 2),
    //     //     2 => round($totalDue * self::INSTALLMENT_PERCENTAGES[2], 2),
    //     //     3 => round($totalDue * self::INSTALLMENT_PERCENTAGES[3], 2)
    //     // ];

    //     $initialInstallmentAmounts = [
    //                                     1 => round(($totalDue * 30) / 100, 2),
    //                                     2 => round(($totalDue * 40) / 100, 2),
    //                                     3 => round(($totalDue * 30) / 100, 2)
    //                                 ];

    //     // Calculate installment paid, discount, and fine
    //     $installmentPaid = [];
    //     $installmentDiscount = [];
    //     $installmentFine = [];
    //     // Current semester
    //     foreach ([1, 2, 3] as $number) {
    //         $installmentPaid[$number] = round($currentFees->sum(function($feeMaster) use($number) {
    //             return $feeMaster->collections()
    //                 ->where('status', 1)
    //                 ->where('installment_number', $number)
    //                 ->sum('paid_amount');
    //         }), 2);

    //         $installmentDiscount[$number] = round($currentFees->sum(function($feeMaster) use($number) {
    //             return $feeMaster->collections()
    //                 ->where('status', 1)
    //                 ->where('installment_number', $number)
    //                 ->sum('discount');
    //         }), 2);

    //         $installmentFine[$number] = round($currentFees->sum(function($feeMaster) use($number) {
    //             return $feeMaster->collections()
    //                 ->where('status', 1)
    //                 ->where('installment_number', $number)
    //                 ->sum('fine');
    //         }), 2);
    //     }
    //     // Previous semesters
    //     $previousInstallmentPaid = [];
    //     $previousInstallmentDiscount = [];
    //     $previousInstallmentFine = [];
    //     foreach ([1, 2, 3] as $number) {
    //         $previousInstallmentPaid[$number] = round($currentPreviousFees->sum(function($feeMaster) use($number) {
    //             return $feeMaster->collections()
    //                 ->where('status', 1)
    //                 ->where('installment_number', $number)
    //                 ->sum('paid_amount');
    //         }), 2);

    //         $previousInstallmentDiscount[$number] = round($currentPreviousFees->sum(function($feeMaster) use($number) {
    //             return $feeMaster->collections()
    //                 ->where('status', 1)
    //                 ->where('installment_number', $number)
    //                 ->sum('discount');
    //         }), 2);

    //         $previousInstallmentFine[$number] = round($currentPreviousFees->sum(function($feeMaster) use($number) {
    //             return $feeMaster->collections()
    //                 ->where('status', 1)
    //                 ->where('installment_number', $number)
    //                 ->sum('fine');
    //         }), 2);
    //     }

    //     // Initialize variables for installment calculation
    //     $allInstallments = [];
    //     $currentPayableAmount = 0;
    //     $hasOverdue = false;
    //     $fineApplied = false;
    //     $feeHeads = [];
    //     $totalNewFine = 0;

    //     // Get earliest due dates for the semester with fallback to current date
    //     $dueDate1 = Carbon::parse($currentFees->min('fee_due_date') ?? now()->format('Y-m-d'))->format('Y-m-d');
    //     $dueDate2 = Carbon::parse($currentFees->min('fee_due_date2') ?? now()->format('Y-m-d'))->format('Y-m-d');
    //     $dueDate3 = Carbon::parse($currentFees->min('fee_due_date3') ?? now()->format('Y-m-d'))->format('Y-m-d');

    //     $isFirstOverdue = $currentDate->gt($dueDate1);
    //     $isSecondOverdue = $currentDate->gt($dueDate2);
    //     $isThirdOverdue = $currentDate->gt($dueDate3);

    //     // Calculate installments and determine current installment
    //     $currentInstallment = 1;
    //     $unpaidInstallments = [];
    //     foreach (self::INSTALLMENT_PERCENTAGES as $number => $percentage) {
    //         $dueDate = $number == 1 ? $dueDate1 : ($number == 2 ? $dueDate2 : $dueDate3);
    //         $isOverdue = $currentDate->gt(Carbon::parse($dueDate)->addday(1));
    //         $initialAmount = $initialInstallmentAmounts[$number];
    //         $paidAmount = ($installmentPaid[$number] ?? 0) + ($previousInstallmentPaid[$number] ?? 0);
    //         $discountAmount = ($installmentDiscount[$number] ?? 0) + ($previousInstallmentDiscount[$number] ?? 0);
    //         $existingFine = ($installmentFine[$number] ?? 0) + ($previousInstallmentFine[$number] ?? 0);
    //         $dueAmount = max(0, round(round($initialAmount, 2) - round($paidAmount + $discountAmount, 2), 2));
    //         $fine = 0;

    //         // Apply fine for third installment if overdue
    //         if ($number == 3 && $isThirdOverdue && $dueAmount >= 0.01 && !$fineApplied) {
    //             //$fine = $this->fine_amount;
    //             $fine = $number ==3 ? 500 : 0;
    //             $fineApplied = true;
    //             $totalNewFine += $fine;
    //         }

    //         if ($dueAmount < 0.01) {
    //             $dueAmount = 0;
    //         } else {
    //             $unpaidInstallments[] = [
    //                 'number' => $number,
    //                 'due_amount' => $dueAmount,
    //                 'fine' => $fine,
    //                 'is_overdue' => $isOverdue,
    //                 'due_date' => $dueDate
    //             ];
    //         }

    //         if ($isOverdue && $dueAmount > 0) {
    //             $hasOverdue = true;
    //         }

    //         if ($initialAmount > 0) {
    //             $allInstallments[] = [
    //                 'number' => $number,
    //                 'percentage' => $percentage * 100,
    //                 'initial_due_amount' => $initialAmount,
    //                 'due_date' => $dueDate,
    //                 'status' => $dueAmount <= 0 ? 'paid' : ($isOverdue ? 'overdue' : 'pending'),
    //                 'is_overdue' => $isOverdue && $dueAmount > 0,
    //                 'fee_master_id' => $currentFees->first()->id,
    //                 'fine' => $number ==3 ? 500 : 0, // Fine only for third installment
    //                 'due_amount' => $dueAmount
    //             ];
    //         }

    //         // Update current installment based on payment status
    //         if ($number == 1 && ($paidAmount + $discountAmount) >= $initialAmount) {
    //             $currentInstallment = 2;
    //         } elseif ($number == 2 && ($paidAmount + $discountAmount) >= $initialAmount && (($installmentPaid[1] ?? 0) + ($installmentDiscount[1] ?? 0) + ($previousInstallmentPaid[1] ?? 0) + ($previousInstallmentDiscount[1] ?? 0)) >= $initialInstallmentAmounts[1]) {
    //             $currentInstallment = 3;
    //         } elseif ($number == 3 && ($paidAmount + $discountAmount) >= $initialAmount && (($installmentPaid[2] ?? 0) + ($installmentDiscount[2] ?? 0) + ($previousInstallmentPaid[2] ?? 0) + ($previousInstallmentDiscount[2] ?? 0)) >= $initialInstallmentAmounts[2]) {
    //             $currentInstallment = null; // All paid
    //         }
    //     }

    //     // Determine current_payable_amount
    //     $currentPayableAmount = 0;
    //     if (!empty($unpaidInstallments)) {
    //         $firstInstallment = collect($unpaidInstallments)->firstWhere('number', 1);
    //         $secondInstallment = collect($unpaidInstallments)->firstWhere('number', 2);
    //         $thirdInstallment = collect($unpaidInstallments)->firstWhere('number', 3);

    //         $isFirstOverdue = $firstInstallment && $firstInstallment['is_overdue'];
    //         $isSecondOverdue = $secondInstallment && $secondInstallment['is_overdue'];
    //         $isThirdOverdue = $thirdInstallment && $thirdInstallment['is_overdue'];

    //         if ($isThirdOverdue) {
    //             //dd($thirdInstallment);
    //             // Include all unpaid installments if third is overdue
    //             $currentPayableAmount = ($firstInstallment ? $firstInstallment['due_amount'] : 0) + 
    //                                 ($secondInstallment ? $secondInstallment['due_amount'] : 0) +
    //                                 ($thirdInstallment ? $thirdInstallment['due_amount'] : 0)+
    //                                 ($thirdInstallment ? $thirdInstallment['fine'] : 0);
    //         } elseif ($isSecondOverdue) {
    //             // Include first and second if second is overdue
    //             $currentPayableAmount = ($firstInstallment ? $firstInstallment['due_amount'] : 0) + 
    //                                 ($secondInstallment ? $secondInstallment['due_amount'] : 0)+
    //                                 ($thirdInstallment ? $thirdInstallment['due_amount'] : 0);
    //         } elseif ($isFirstOverdue) {
    //             // Include only first if first is overdue
    //             $currentPayableAmount =  ($firstInstallment ? $firstInstallment['due_amount'] : 0) + 
    //                                 ($secondInstallment ? $secondInstallment['due_amount'] : 0);
    //         } else {
    //             // Include only the first unpaid installment
    //             $currentPayableAmount = collect($unpaidInstallments)->first()['due_amount'] ?? 0;
    //         }
    //     }

    //     // Update total_due to include new fine
    //     $totalDueWithFine = round($totalDue + $totalNewFine, 2);

    //     // Populate fee heads
    //     foreach ($currentFees as $feeMaster) {
    //         $feeHeadAmount = round($feeMaster->fee_amount, 2);
    //         $feeHeadPaid = round($feeMaster->collections()->where('status', 1)->sum('paid_amount'), 2);
    //         $feeHeadDiscount = round($feeMaster->collections()->where('status', 1)->sum('discount'), 2);
    //         $feeHeadFine = round($feeMaster->collections()->where('status', 1)->sum('fine'), 2);
    //         $feeHeadUnpaid = max(0, round(($feeHeadAmount + $feeHeadFine) - ($feeHeadPaid + $feeHeadDiscount), 2));

    //         $feeHeads[] = [
    //             'fee_head' => $feeMaster->fee_head,
    //             'amount' => $feeHeadAmount,
    //             'paid' => $feeHeadPaid,
    //             'discount' => $feeHeadDiscount,
    //             'fine' => $feeHeadFine,
    //             'unpaid' => $feeHeadUnpaid,
    //             'has_overdue' => $hasOverdue,
    //             'fee_master_id' => $feeMaster->id
    //         ];
    //     }

    //     // Format full name
    //     $fullName = trim(implode(' ', [
    //         $this->student->first_name ?? '',
    //         $this->student->middle_name ?? '',
    //         $this->student->last_name ?? ''
    //     ]));

    //     // Log::info("Calculated installments for student {$this->students_id}, semester {$this->semester}", [
    //     //     'current_payable_amount' => $currentPayableAmount,
    //     //     'current_installment' => $currentInstallment,
    //     //     'installments' => $allInstallments,
    //     //     'total_current_semester_due' => $currentSemesterDue,
    //     //     'previous_unpaid' => $previousUnpaid,
    //     //     'total_due' => $totalDueWithFine,
    //     //     'original_total_due' => $originalTotalDue,
    //     //     'adjusted_total_due' => $adjustedTotalDue,
    //     //     'pre_installment_paid' => $preInstallmentPaid,
    //     //     'pre_installment_discount' => $preInstallmentDiscount,
    //     //     'pre_installment_fine' => $preInstallmentFine,
    //     //     'fine_applied' => $fineApplied ? $this->fine_amount : 0
    //     // ]);

    //     return [
    //         'studentId' => $this->student->reg_no,
    //         'studentName' => $fullName,
    //         'semester' => $this->semester,
    //         'current_semester_amount' => $currentSemesterDue,
    //         'previous_unpaid' => $previousUnpaid,
    //         'total_due' => $totalDueWithFine,
    //         'current_payable_amount' => round($currentPayableAmount, 2),
    //         'fee_heads' => $feeHeads,
    //         'current_installment' => $currentInstallment,
    //         'total_paid' => $totalPaid,
    //         'all_installments' => $allInstallments,
    //         'original_total_due' => $adjustedTotalDue
    //     ];
    // }

//old
    // public function calculateInstallments()
    // {
    //     $currentDate = Carbon::now();
    //     $studentId = $this->students_id;

    //     // Initialize all variables with default values
    //     $currentFees = collect();
    //     $currentPreviousFees = collect();
    //     $previousUnpaid = 0;
    //     $originalSemesterTotal = 0;
    //     $preInstallmentPaid = 0;
    //     $preInstallmentDiscount = 0;
    //     $preInstallmentFine = 0;
    //     $totalPaid = 0;
    //     $totalDiscount = 0;
    //     $totalFine = 0;
    //     $originalTotalDue = 0;
    //     $adjustedTotalDue = 0;
    //     $currentSemesterDue = 0;
    //     $totalDue = 0;
    //     $totalNewFine = 0;
    //     $currentInstallment = 1;

    //     try {
    //         // Get fee data
    //         $currentFees = FeeMaster::where('students_id', $studentId)
    //             ->where('semester', $this->semester)
    //             ->whereNotIn('fee_head', config('api.excluded_heads'))
    //             ->get();

    //         $currentPreviousFees = FeeMaster::where('students_id', $studentId)
    //             ->where('semester', '!=', $this->semester)
    //             ->whereNotIn('fee_head', config('api.excluded_heads'))
    //             ->get();

    //         $previousUnpaid = $this->getPreviousUnpaid();

    //         // Handle case when no current fees exist
    //         if ($currentFees->isEmpty()) {
    //             return [
    //                 'studentId' => $this->student->reg_no ?? null,
    //                 'studentName' => $this->student->name ?? 'Unknown',
    //                 'semester' => $this->semester,
    //                 'current_semester_amount' => 0,
    //                 'previous_unpaid' => $previousUnpaid,
    //                 'total_due' => $previousUnpaid,
    //                 'current_payable_amount' => 0,
    //                 'fee_heads' => [],
    //                 'current_installment' => null,
    //                 'total_paid' => 0,
    //                 'all_installments' => [],
    //                 'original_total_due' => $previousUnpaid
    //             ];
    //         }

    //         // Calculate amounts (same as before)
    //         $originalSemesterTotal = round($currentFees->sum('fee_amount'), 2);
    //         $preInstallmentPaid = round($currentFees->sum(function($feeMaster) {
    //             return $feeMaster->collections()
    //                 ->where('status', 1)
    //                 ->whereNull('installment_number')
    //                 ->sum('paid_amount');
    //         }), 2);
    //         // ... [rest of your calculation logic remains exactly the same] ...

    //         // Process installments
    //         $allInstallments = [];
    //         $unpaidInstallments = [];
    //         foreach (self::INSTALLMENT_PERCENTAGES as $number => $percentage) {
    //             $dueDate = $number == 1 ? $dueDate1 : ($number == 2 ? $dueDate2 : $dueDate3);
    //             $isOverdue = $currentDate->gt($dueDate);
    //             $initialAmount = $initialInstallmentAmounts[$number];
    //             $paidAmount = $installmentPaid[$number] ?? 0;
    //             $discountAmount = $installmentDiscount[$number] ?? 0;
    //             $existingFine = $installmentFine[$number] ?? 0;
    //             $dueAmount = max(0, round($initialAmount - ($paidAmount + $discountAmount), 2));
                
    //             $fine = 0;
    //             if ($number == 3 && $isThirdOverdue && $dueAmount > 0) {
    //                 $fine = $this->fine_amount;
    //                 $totalNewFine += $fine;
    //             }

    //             if ($dueAmount > 0) {
    //                 $unpaidInstallments[] = [
    //                     'number' => $number,
    //                     'due_amount' => $dueAmount,
    //                     'fine' => $fine,
    //                     'is_overdue' => $isOverdue,
    //                     'due_date' => $dueDate
    //                 ];
    //             }

    //             $allInstallments[] = [
    //                 'number' => $number,
    //                 'percentage' => $percentage * 100,
    //                 'initial_due_amount' => $initialAmount,
    //                 'due_date' => $dueDate,
    //                 'status' => $dueAmount <= 0 ? 'paid' : ($isOverdue ? 'overdue' : 'pending'),
    //                 'is_overdue' => $isOverdue && $dueAmount > 0,
    //                 'fee_master_id' => $currentFees->isNotEmpty() ? $currentFees->first()->id : null,
    //                 'fine' => $fine,
    //                 'due_amount' => $dueAmount + $fine
    //             ];
    //         }

    //         // ========== YOUR EXACT PAYABLE AMOUNT LOGIC ==========
    //         $currentPayableAmount = 0;
    //         if (!empty($unpaidInstallments)) {
    //             $firstUnpaid = collect($unpaidInstallments)->firstWhere('number', 1);
    //             $secondUnpaid = collect($unpaidInstallments)->firstWhere('number', 2);
    //             $thirdUnpaid = collect($unpaidInstallments)->firstWhere('number', 3);

    //             $isFirstOverdue = $firstUnpaid && $firstUnpaid['is_overdue'];
    //             $isSecondOverdue = $secondUnpaid && $secondUnpaid['is_overdue'];
    //             $isThirdOverdue = $thirdUnpaid && $thirdUnpaid['is_overdue'];

    //             if ($isThirdOverdue) {
    //                 // If third is overdue, include all installments with fine
    //                 $currentPayableAmount = ($firstUnpaid ? $firstUnpaid['due_amount'] : 0) +
    //                                     ($secondUnpaid ? $secondUnpaid['due_amount'] : 0) +
    //                                     ($thirdUnpaid ? $thirdUnpaid['due_amount'] + $this->fine_amount : 0);
    //             } elseif ($isSecondOverdue) {
    //                 // If second is overdue, include first and second
    //                 $currentPayableAmount = ($firstUnpaid ? $firstUnpaid['due_amount'] : 0) +
    //                                     ($secondUnpaid ? $secondUnpaid['due_amount'] : 0) +
    //                                     ($thirdUnpaid ? $thirdUnpaid['due_amount'] : 0);
    //             } elseif ($isFirstOverdue) {
    //                 // If first is overdue, include first and second
    //                 $currentPayableAmount = ($firstUnpaid ? $firstUnpaid['due_amount'] : 0) +
    //                                     ($secondUnpaid ? $secondUnpaid['due_amount'] : 0);
    //             } else {
    //                 // Otherwise, include only the first unpaid installment
    //                 $currentPayableAmount = $firstUnpaid['due_amount'] ?? 0;
    //             }

    //             \Log::info("Calculated current_payable_amount in FeeMaster for student {$this->students_id}", [
    //                 'currentPayableAmount' => $currentPayableAmount,
    //             ]);
    //         } else {
    //             $currentPayableAmount = 0;
    //         }
    //         // ========== END OF YOUR LOGIC ==========

    //         // Prepare fee heads and final response (same as before)
    //         $feeHeads = [];
    //         foreach ($currentFees as $feeMaster) {
    //             $feeHeadAmount = round($feeMaster->fee_amount, 2);
    //             $feeHeadPaid = round($feeMaster->collections()->where('status', 1)->sum('paid_amount'), 2);
    //             $feeHeadDiscount = round($feeMaster->collections()->where('status', 1)->sum('discount'), 2);
    //             $feeHeadFine = round($feeMaster->collections()->where('status', 1)->sum('fine'), 2);
    //             $feeHeadUnpaid = max(0, round(($feeHeadAmount + $feeHeadFine) - ($feeHeadPaid + $feeHeadDiscount), 2));

    //             $feeHeads[] = [
    //                 'fee_head' => $feeMaster->fee_head,
    //                 'amount' => $feeHeadAmount,
    //                 'paid' => $feeHeadPaid,
    //                 'discount' => $feeHeadDiscount,
    //                 'fine' => $feeHeadFine,
    //                 'unpaid' => $feeHeadUnpaid,
    //                 'fee_master_id' => $feeMaster->id
    //             ];
    //         }

    //         // Format student name
    //         $fullName = trim(implode(' ', array_filter([
    //             $this->student->first_name ?? '',
    //             $this->student->middle_name ?? '',
    //             $this->student->last_name ?? ''
    //         ])));

    //         return [
    //             'studentId' => $this->student->reg_no ?? null,
    //             'studentName' => $fullName,
    //             'semester' => $this->semester,
    //             'current_semester_amount' => $currentSemesterDue,
    //             'previous_unpaid' => $previousUnpaid,
    //             'total_due' => round($totalDue + $totalNewFine, 2),
    //             'current_payable_amount' => round($currentPayableAmount, 2),
    //             'fee_heads' => $feeHeads,
    //             'current_installment' => $currentInstallment,
    //             'total_paid' => $totalPaid,
    //             'all_installments' => $allInstallments,
    //             'original_total_due' => $adjustedTotalDue
    //         ];

    //     } catch (\Exception $e) {
    //         \Log::error('Installment calculation error: '.$e->getMessage());
    //         return [
    //             'studentId' => $this->student->reg_no ?? null,
    //             'studentName' => $this->student->name ?? 'Unknown',
    //             'error' => 'Error calculating installments',
    //             'current_payable_amount' => 0,
    //             'all_installments' => []
    //         ];
    //     }
    // }

    protected function getPreviousUnpaid($includePayments = true)
    {
        // Get all fee masters for semesters not equal to the student's current semester
        $previousFees = self::where('students_id', $this->students_id)
            ->where('semester', '!=', $this->student->semester)
            ->whereNotIn('fee_head',  config('api.excluded_heads'))
            ->get();

        if ($previousFees->isEmpty()) {
            return 0;
        }

        $totalUnpaid = 0;

        // Group by semester to aggregate fee heads
        $groupedFees = $previousFees->groupBy('semester');
        foreach ($groupedFees as $semester => $semesterFees) {
            $semesterTotal = round($semesterFees->sum('fee_amount'), 2);
            if ($includePayments) {
                $semesterPaid = round($semesterFees->sum(function($feeMaster) {
                    return $feeMaster->collections()->where('status', 1)->whereNull('installment_number')->sum('paid_amount');
                }), 2);
                $semesterDiscount = round($semesterFees->sum(function($feeMaster) {
                    return $feeMaster->collections()->where('status', 1)->whereNull('installment_number')->sum('discount');
                }), 2);
                $semesterFine = round($semesterFees->sum(function($feeMaster) {
                    return $feeMaster->collections()->where('status', 1)->whereNull('installment_number')->sum('fine');
                }), 2);
                $unpaid = max(0, round(($semesterTotal + $semesterFine) - ($semesterPaid + $semesterDiscount), 2));
            } else {
                $unpaid = $semesterTotal; // For original total due, ignore payments
            }

            // Log::info("Previous unpaid calculation for student {$this->students_id}, semester {$semester}", [
            //     'total' => $semesterTotal,
            //     'paid' => $includePayments ? $semesterPaid : 0,
            //     'discount' => $includePayments ? $semesterDiscount : 0,
            //     'fine' => $includePayments ? $semesterFine : 0,
            //     'unpaid' => $unpaid
            // ]);

            $totalUnpaid += $unpaid;
        }

        return round($totalUnpaid, 2);
    }

    public function getDueAmount()
    {
        $feeAmount = round($this->fee_amount, 2);
        $paidAmount = round($this->collections()->where('status', 1)->sum('paid_amount'), 2);
        $discountAmount = round($this->collections()->where('status', 1)->sum('discount'), 2);
        $existingFine = round($this->collections()->where('status', 1)->sum('fine'), 2);
        
        return max(0, round(($feeAmount + $existingFine) - ($paidAmount + $discountAmount), 2));
    }
}