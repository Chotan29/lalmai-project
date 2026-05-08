<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicInfoLevelSemester extends BaseModel
{
    Protected $table = 'academic_info_level_semester';
    protected $fillable = ['semester_id','academic_info_level_id'];
}
