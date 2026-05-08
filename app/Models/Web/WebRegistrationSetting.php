<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebRegistrationSetting extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'title', 'sub_title', 'logo',
        'medical_info_status','guardian_detail_status','permanent_address_status', 'mailing_address_status',
        'photo_status','applicant_photo_status','applicant_signature_status','guardian_photo_status',
        'qualification','experience','rules_status','rules', 'agreement_status','agreement',
        'start_date', 'end_date', 'status'];
}
