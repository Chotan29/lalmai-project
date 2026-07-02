<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends BaseModel
{
    protected $table = 'faculties';
    protected $fillable = ['created_by', 'last_updated_by', 'faculty', 'faculty_code', 'gradingType_id', 'scale', 'sorting', 'duration', 'credit_required', 'registration_validate', 'status'];

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_programs', 'faculty_id', 'department_id');
    }

    public function semesters()
    {
        return $this->belongsToMany(Semester::class, 'faculty_semester', 'faculty_id', 'semester_id');
    }

    public function routines()
    {
        return $this->hasMany(ClassRoutine::class, 'faculty_id');
    }

    public function batches()
    {
        return $this->hasManyThrough(
            StudentBatch::class,
            FacultySemester::class,
            'faculty_id',    // Foreign key on faculty_semester table
            'semester_id',   // Foreign key on student_batches table
            'id',            // Local key on faculties table
            'semester_id'    // Local key on faculty_semester table
        );
    }
}