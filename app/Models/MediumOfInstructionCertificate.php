<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediumOfInstructionCertificate extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'students_id', 'date_of_issue','ref_num', 'moic_num',
        'ref_text','status'];
}
