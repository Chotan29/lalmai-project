<?php
namespace App\Http\Controllers\API;

use App\Models\FeeCollection;
class PaymentDistribution
{
    private $remainingAmount;
    private $installments;
    private $feeMasters;
    private $studentId;
    private $bankRef;
    private $universityRef;
    private $paymentRecords = [];

    public function __construct($amount, $installments, $feeMasters, $studentId, $bankRef, $universityRef)
    {
        $this->remainingAmount = $amount;
        $this->installments = collect($installments)->map(function($i) {
            return [
                'number' => $i->number,
                'due_amount' => (int)round($i->due_amount * 100),
                'fine' => (int)round(($i->fine ?? 0) * 100),
                'is_overdue' => $i->is_overdue ?? false,
                'fee_master_id' => $i->fee_master_id ?? null
            ];
        });
        $this->feeMasters = $feeMasters;
        $this->studentId = $studentId;
        $this->bankRef = $bankRef;
        $this->universityRef = $universityRef;
    }

    public function process()
    {
        // 1. Pay all overdue fines first
        $this->payOverdueFines();

        // 2. Pay installments in order (1, 2, 3)
        $this->payInstallments();

        // 3. Apply any remaining amount to general dues
        $this->payGeneralDues();

        // Create all payment records
        $this->createPaymentRecords();
    }

    private function payOverdueFines()
    {
        $thirdInstallment = $this->installments->firstWhere('number', 3);
        if (!$thirdInstallment || !$thirdInstallment['is_overdue'] || $thirdInstallment['fine'] <= 0) {
            return;
        }

        $fineAmount = $thirdInstallment['fine'];
        $fineFeeMaster = $this->getFineFeeMaster();

        if ($fineFeeMaster && $this->remainingAmount > 0) {
            $paidFine = min($fineAmount, $this->remainingAmount);
            $this->addPaymentRecord($fineFeeMaster->id, 0, $paidFine, 3);
            $this->remainingAmount -= $paidFine;
        }
    }

    private function payInstallments()
    {
        foreach ([1, 2, 3] as $installmentNumber) {
            if ($this->remainingAmount <= 0) break;

            $installment = $this->installments->firstWhere('number', $installmentNumber);
            if (!$installment || $installment['due_amount'] <= 0) continue;

            $amountToPay = min($installment['due_amount'], $this->remainingAmount);
            $this->distributePayment($amountToPay, $installmentNumber);
        }
    }

    private function payGeneralDues()
    {
        if ($this->remainingAmount <= 0) return;

        foreach ($this->feeMasters as $feeMaster) {
            if ($this->remainingAmount <= 0) break;

            $feeDue = $this->calculateFeeDue($feeMaster);
            if ($feeDue <= 0) continue;

            $paymentAmount = min($feeDue, $this->remainingAmount);
            $this->addPaymentRecord($feeMaster->id, $paymentAmount, 0, null);
            $this->remainingAmount -= $paymentAmount;
        }
    }

    private function distributePayment($amount, $installmentNumber)
    {
        $distributed = 0;
        
        foreach ($this->feeMasters as $feeMaster) {
            if ($distributed >= $amount) break;

            $feeDue = $this->calculateFeeDue($feeMaster, true);
            if ($feeDue <= 0) continue;

            $paymentAmount = min($amount - $distributed, $feeDue);
            $this->addPaymentRecord($feeMaster->id, $paymentAmount, 0, $installmentNumber);
            $distributed += $paymentAmount;
            $this->remainingAmount -= $paymentAmount;
        }
    }

    private function calculateFeeDue($feeMaster, $excludeInstallments = false)
    {
        $feeAmount = (int)round($feeMaster->fee_amount * 100);
        $collections = $feeMaster->collections;

        if ($excludeInstallments) {
            $collections = $collections->whereNull('installment_number');
        }

        $paidAmount = (int)round($collections->sum('paid_amount') * 100);
        $discountAmount = (int)round($collections->sum('discount') * 100);
        $existingFine = (int)round($collections->sum('fine') * 100);

        return max(0, ($feeAmount + $existingFine) - ($paidAmount + $discountAmount));
    }

    private function getFineFeeMaster()
    {
        // Try to find a fee master from current semester first
        $currentSemester = $this->feeMasters->first()->semester;
        $currentSemesterFeeMaster = $this->feeMasters
            ->where('semester', $currentSemester)
            ->first();

        return $currentSemesterFeeMaster ?: $this->feeMasters->first();
    }

    private function addPaymentRecord($feeMasterId, $amount, $fine, $installmentNumber)
    {
        if ($amount <= 0 && $fine <= 0) return;

        $this->paymentRecords[] = [
            'students_id' => $this->studentId,
            'fee_masters_id' => $feeMasterId,
            'date' => now()->toDateString(),
            'paid_amount' => $amount / 100,
            'discount' => 0,
            'fine' => $fine / 100,
            'external_ref_no' => $this->bankRef,
            'ref_no' => $this->universityRef,
            'payment_method' => 'Bank',
            'note' => $installmentNumber ? "Installment #{$installmentNumber} Payment" : "General Payment",
            'installment_number' => $installmentNumber,
            'status' => 1,
            'created_by' => auth()->id() ?? 1,
        ];
    }

    private function createPaymentRecords()
    {
        foreach ($this->paymentRecords as $record) {
            FeeCollection::create($record);
        }
    }

    public function getRemainingAmount()
    {
        return $this->remainingAmount;
    }
}