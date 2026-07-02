<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NirgamUtaraCertificate extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'students_id', 'nu_num', 'date_of_issue', 'date_of_leaving',
        'leaving_time_class', 'previous_school_name','join_time_class', 'reason_to_leave', 'mention_body_mark',
         'any_other_remark', 'ref_text', 'status'];
}
