<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebRegistration extends BaseModel
{
    protected $fillable = ['id','web_registration_programmes_id','reg_no','reg_date','name','sex','date_of_birth','religion','caste','nationality','state','mother_tongue','blood_group','medicine_info','disease_info','guardian_name','guardian_relation','guardian_occupation','guardian_annual_income','address','tel','cell_1','cell_2','email','mailing_address','mailing_tel','mailing_cell_1','mailing_cell_2','mailing_email','student_image','student_signature','guardian_signature','status'];

    public function academicInfo()
    {
        return $this->hasMany(WebRegistrationAcademicQualification::class,'web_registrations_id','id');
    }

    public function workExperience()
    {
        return $this->hasMany(WebRegistrationWorkExperience::class,'web_registrations_id','id');
    }
}
