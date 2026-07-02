<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ClassRoutineDetail extends BaseModel
{
    protected $fillable = [
        'class_routine_id','subject_id','teacher_id','day_of_week','start_time','end_time','room','active'
    ];

    public function routine()
    {
        return $this->belongsTo(ClassRoutine::class, 'class_routine_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function scopeToday($q)
    {
        // 0=Sunday .. 6=Saturday (JS-like; adjust if your app uses Mon=1)
        $dow = (int) Carbon::today()->format('w'); 
        return $q->where('day_of_week', $dow)->where('active', 1);
    }
}
