<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebHomeSetting extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by',
        'slider_status','slider_caption_status','slider_call_to_action_status', 'slider_call_to_action_title', 'slider_call_to_action_link',
        'notice_status', 'notice_title', 'blog_status', 'blog_title', 'event_status', 'event_title',
        'welcome_status', 'welcome_title','welcome_sub_title', 'welcome_description', 'welcome_image', 'welcome_button_text', 'welcome_link',
        'introduction_status', 'introduction_title', 'introduction_description', 'introduction_button_text', 'introduction_link',
        'staff_status', 'staff_title', 'counter_status', 'counter_title',
        'services_status', 'services_title',
        'testimonial_status', 'testimonial_title', 'client_status', 'client_title',
        'email_call_to_action_status', 'email_call_to_action_title', 'email_call_to_action_button_text','email_call_to_action_link'];
}
