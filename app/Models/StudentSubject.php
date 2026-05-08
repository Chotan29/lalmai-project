<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSubject extends BaseModel
{
    protected $table = 'student_subject';
    protected $fillable = ['created_by', 'last_updated_by', 'students_id',  'subjects_id', 'status'];
}
