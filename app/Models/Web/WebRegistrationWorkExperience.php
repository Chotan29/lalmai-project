<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebRegistrationWorkExperience extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'web_registrations_id','experience_info','status'];
}
