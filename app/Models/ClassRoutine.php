<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoutine extends BaseModel
{
   protected $fillable = [
        'department_id', 'faculty_id', 'semester_id', 'student_batch_id',
        'subject_id', 'teacher_id', 'day_of_week', 'start_time', 'end_time',
        'room_number', 'period', 'created_by', 'last_updated_by', 'status'
    ];

      protected $casts = [
        'status'      => 'boolean',
        // keep as string if you store "HH:MM", otherwise you can cast to datetime and format in accessors
        'start_time'  => 'string',
        'end_time'    => 'string',
    ];


    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function faculty() {
        return $this->belongsTo(Faculty::class);
    }

    public function semester() {
        return $this->belongsTo(Semester::class);
    }

    public function batch() {
        return $this->belongsTo(StudentBatch::class, 'student_batch_id');
    }

    // public function batch()
    // {
    //     return $this->belongsTo(StudentBatch::class, 'student_batch_id');
    // }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }
    
}