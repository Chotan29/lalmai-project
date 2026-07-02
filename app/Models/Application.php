<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'application_type_id', 'years_id', 'date', 'end_date', 'subject', 'message', 'file', 'approve_status', 'status'];
}
