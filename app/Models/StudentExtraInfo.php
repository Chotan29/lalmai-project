<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentExtraInfo extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'students_id', 'total_fee_per_year', 'bank_name', 'bank_code', 'bank_account_number', 'admission_portal_login_id', 'admission_portal_login_password', 'status'];
}
