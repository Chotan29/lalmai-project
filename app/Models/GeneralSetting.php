<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'institute', 'salogan','copyright', 'address','phone','email',
        'website', 'favicon', 'email', 'logo','nav_layout', 'tracking_code', 'print_header', 'print_footer', 'facebook', 'twitter',
        'linkedIn', 'youtube', 'googlePlus', 'instagram', 'whatsApp', 'skype', 'pinterest','wordpress', 'time_zones_id',
        'quick_menu', 'public_registration', 'web_cms','front_desk', 'student_staff', 'account', 'inventory', 'library',
        'attendance', 'exam', 'certificate', 'hostel', 'transport', 'assignment','application', 'application', 'upload_download',
        'meeting', 'alert', 'academic', 'help', 'status'];
}
