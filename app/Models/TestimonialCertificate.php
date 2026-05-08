<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestimonialCertificate extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'students_id', 'date_of_issue','ref_num', 'tmc_num',
        'program_duration', 'year', 'gpa', 'scale', 'average_grade', 'ref_text','status'];
}
