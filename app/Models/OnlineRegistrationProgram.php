<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineRegistrationProgram extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'faculties_id', 'semesters_id','start_date', 'end_date','status'];
}
