<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebGeneralSetting extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by',
        'site_title', 'site_slogan',  'site_desc', 'site_keyword', 'favicon', 'site_logo', 'page_banner',
        'top_nav_status','top_nav_text','top_nav_text_link',
        'main_nav_status','main_nav_button_status','main_nav_button_button_text','main_nav_button_link',
        'sticky_nav_status','footer_status','quick_nav_status','footer_nav_status',
        'header_codes','footer_codes','post_detail_foot','custom_css',
        'analytics_view_id','analytics_json_file','recaptcha_site_key','recaptcha_secret_key',
        'facebook_widget'];
}
