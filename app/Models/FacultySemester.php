<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultySemester extends BaseModel
{
    protected $table = 'faculty_semester';
    protected $fillable = ['faculty_id','semester_id'];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}