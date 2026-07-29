<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamMarkLedger extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'exam_schedule_id','students_id', 'obtain_mark_theory',
        'absent_theory','obtain_mark_practical','obtain_mark_mcq','absent_practical', 'sorting_order', 'status'];

    /**
     * A mark row is NEVER deleted. When a leftover/duplicate row has to be taken out of
     * circulation it is deactivated (status = 0) and logged, so the mark can always be
     * recovered. Every read (ledger screen, result, gradesheet, print) must therefore
     * ignore the deactivated rows - handled once, here, with a global scope.
     *
     * The column is qualified because most of these queries join `students`, which also
     * has a `status` column.
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('activeMarkRow', function ($builder) {
            $builder->where('exam_mark_ledgers.status', '=', 1);
        });
    }

    /*Escape hatch: include deactivated rows (audit / recovery screens).*/
    public function scopeWithDeactivated($query)
    {
        return $query->withoutGlobalScope('activeMarkRow');
    }

    public function students()
    {
        return $this->belongsTo(Student::class, 'id','students_id');
    }

    public function examSchedule()
    {
        return $this->belongsTo(ExamSchedule::class, 'exam_schedule_id','id');
    }


}
