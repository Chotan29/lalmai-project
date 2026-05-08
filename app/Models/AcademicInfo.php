<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicInfo extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'students_id', 'institution', 'board','pass_year','roll_no',
        'percentage', 'mark_obtained', 'maximum_mark','division_grade','grade_point','grade_letter', 'major_subjects', 'remark', 'sorting_order','status'];

}
