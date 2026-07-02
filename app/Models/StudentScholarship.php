<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentScholarship extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'students_id', 'scholarships_id', 'scholarship_application_id', 'scholarship_user_name', 'scholarship_password', 'status'];
}
