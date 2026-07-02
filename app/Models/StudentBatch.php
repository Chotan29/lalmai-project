<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentBatch extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'title', 'active_status', 'status'];

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function routines() {
        return $this->hasMany(ClassRoutine::class, 'student_batch_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'batch');
    }
}