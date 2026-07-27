<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineRegistrationProgram extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'faculties_id', 'semesters_id','start_date', 'end_date',
        'new_student_fee', 'old_student_fee', 'status'];

    /**
     * Registration fee for this program, falling back to the global setting when the
     * program row leaves the fee empty.
     *
     * @param string $studentType 'new' or 'old'
     * @param \App\Models\OnlineRegistrationSetting|null $setting
     * @return float
     */
    public function feeFor($studentType, $setting = null)
    {
        $programFee = $studentType === 'old' ? $this->old_student_fee : $this->new_student_fee;

        if ($programFee !== null && $programFee !== '' && (float) $programFee > 0) {
            return (float) $programFee;
        }

        if (!$setting) {
            return 0.0;
        }

        return $studentType === 'old'
            ? (float) $setting->old_student_registration_fee
            : (float) $setting->new_student_registration_fee;
    }

    /**
     * Resolve the fee for a faculty (optionally a specific semester).
     * Used by the registration + payment flow so each department can charge its own fee.
     */
    public static function resolveFee($facultyId, $semesterId, $studentType, $setting = null)
    {
        $fallback = $setting
            ? ($studentType === 'old'
                ? (float) $setting->old_student_registration_fee
                : (float) $setting->new_student_registration_fee)
            : 0.0;

        if (!$facultyId) {
            return $fallback;
        }

        $query = static::where('faculties_id', $facultyId);
        if ($semesterId) {
            $exact = (clone $query)->where('semesters_id', $semesterId)->first();
            if ($exact) {
                return $exact->feeFor($studentType, $setting);
            }
        }

        $program = $query->first();

        return $program ? $program->feeFor($studentType, $setting) : $fallback;
    }
}
