<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPlacement extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'students_id', 'placements_id', 'placement_date', 'placement_salary', 'placement_location', 'placement_designation', 'status'];
}
