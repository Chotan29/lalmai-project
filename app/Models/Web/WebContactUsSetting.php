<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebContactUsSetting extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by','contact_title', 'company', 'address', 'phone', 'fax', 'email', 'webURL', 'google_map', 'latitude','longitude', 'facebook_link', 'twitter_link', 'googlePlus_link',
        'linkedIn_link', 'instagram_link', 'whatsApp_link', 'skype_link', 'pinterest_link'];
}
