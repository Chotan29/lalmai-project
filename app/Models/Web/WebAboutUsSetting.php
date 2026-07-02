<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebAboutUsSetting extends BaseModel
{
    protected $table = 'web_about_us_settings';
    protected $fillable = ['created_by', 'last_updated_by', 'title', 'slogan', 'description', 'image',
        'counter_title', 'counter_status','staffs_status','staffs_title', 'status'];
}
