<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternCompletionCertificate extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'students_id', 'date_of_issue', 'internship_title', 'period', 'character', 'ref_text','status'];
}
