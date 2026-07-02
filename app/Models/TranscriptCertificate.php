<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranscriptCertificate extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'students_id', 'date_of_issue','trc_num', 'year','duration',
        'credit_required','gpa','verification_code', 'mark_sheet_sn', 'provisional_certificate_num','ref_text','status'];
}
