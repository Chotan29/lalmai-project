<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentProgram extends BaseModel
{
    protected $fillable = ['department_id','faculty_id'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
}