<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAnnexure extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'students_id',  'annexures_id', 'status'];
}
