<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends BaseModel
{
     protected $fillable = ['created_by', 'last_updated_by', 'semester', 'slug', 'semester_fee', 'staff_id', 'gradingType_id', 'major_subject_count', 'max_compulsory_count', 'max_optional_count', 'status'];

    public function faculty()
    {
        return $this->belongsToMany(Faculty::class, 'faculty_semester', 'semester_id', 'faculty_id');
    }
    
    public function faculties()
    {
        return $this->belongsToMany(Faculty::class, 'faculty_semester', 'semester_id', 'faculty_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'semester_subject', 'semester_id', 'subject_id');
    }

    public function gradingType()
    {
        return $this->belongsTo(GradingType::class, 'gradingType_id');
    }

    public function batches() {
        return $this->hasMany(StudentBatch::class, 'semester_id');
    }

    public function routines()
    {
        return $this->hasManyThrough(
            ClassRoutine::class,
            StudentBatch::class,
            'semester_id',    // Foreign key on student_batches table
            'student_batch_id', // Foreign key on class_routines table
            'id',             // Local key on semesters table
            'id'              // Local key on student_batches table
        );
    }

    // public function subjects()
    // {
    //     return $this->belongsToMany(Subject::class);
    // }

    // public function gradingType()
    // {
    //     return $this->hasOne(GradingType::class, 'id','gradingType_id');
    // }

    public function programNeedAcademicLevel() {
//        return $this->belongsToMany(AcademicInfoLevel::class);
        return $this->belongsToMany(AcademicInfoLevel::class);
    }

    public function downloads()
    {
        return $this->hasMany(Download::class,'semesters_id','id');
    }

    public function assets()
    {
        return $this->hasMany(SemesterAsset::class,'semesters_id','id');
    }
}
