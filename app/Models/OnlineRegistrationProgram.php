<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineRegistrationProgram extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'faculties_id', 'semesters_id','start_date', 'end_date',
        'new_student_fee', 'old_student_fee', 'fee_head_group_id', 'status'];

    /**
     * The Main Fee Head this admission charges - the 26 sub heads the payment is split across.
     *
     * Null means no fee has been built for this department yet, and the payment falls back to
     * the single ADMISSION FEE row it has always used.
     */
    public function feeHeadGroup()
    {
        return $this->belongsTo(FeeHeadGroup::class, 'fee_head_group_id');
    }

    /**
     * Registration fee for this program.
     *
     * The fee lives only on the department (Faculty/Program) row - there is no global
     * fallback fee any more, so each department charges exactly what is configured for it.
     *
     * @param string $studentType 'new' or 'old'
     * @return float
     */
    public function feeFor($studentType)
    {
        $programFee = $studentType === 'old' ? $this->old_student_fee : $this->new_student_fee;

        return ($programFee === null || $programFee === '') ? 0.0 : (float) $programFee;
    }

    /**
     * Resolve the fee for a faculty (optionally a specific semester).
     * Returns 0 when the department has no fee configured - the caller then refuses
     * to start a payment instead of silently charging some other amount.
     */
    public static function resolveFee($facultyId, $semesterId, $studentType, $setting = null)
    {
        if (!$facultyId) {
            return 0.0;
        }

        $query = static::where('faculties_id', $facultyId);

        if ($semesterId) {
            $exact = (clone $query)->where('semesters_id', $semesterId)->first();
            if ($exact) {
                return $exact->feeFor($studentType);
            }
        }

        $program = $query->first();

        return $program ? $program->feeFor($studentType) : 0.0;
    }
}
