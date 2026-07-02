<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDegree extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'students_id', 'degrees_id', 'obtained_mark', 'total_marks', 'receive_in_college_date', 'issue_date','status'];
}
