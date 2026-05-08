<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebStaff extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by','slug', 'name','position',  'address','tel',
        'cell_1','cell_2','email','description','profile_image',
        'twitter_url', 'linkedIn_url', 'facebook_url', 'instagram_url','whatsapp_url', 'skype_url','pinterest_url', 'rank', 'status'];
}
