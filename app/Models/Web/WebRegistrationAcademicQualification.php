<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebRegistrationAcademicQualification extends BaseModel
{
    protected $fillable = ['last_updated_by', 'web_registrations_id','examination','institution','board_university','year_of_pass','percentage_grade','status'];
}
