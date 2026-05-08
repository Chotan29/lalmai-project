<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProvisionalCertificate extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'students_id', 'date_of_issue','pc_num', 'year','gpa','scale','result_publish_date','ref_text','status'];
}
