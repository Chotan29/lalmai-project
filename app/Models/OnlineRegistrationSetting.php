<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineRegistrationSetting extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'status','base', 'title', 'logo','start_date', 'end_date',
                            'rules_status','rules', 'agreement_status','agreement','registration_guidelines','registration_close_message'];
}
