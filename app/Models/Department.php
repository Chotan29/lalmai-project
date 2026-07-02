<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'department', 'sorting', 'status'];

    public function heads()
    {
        return $this->belongsToMany(DepartmentHead::class, 'department_heads_department');
    }

    public function routines()
    {
        return $this->hasMany(ClassRoutine::class);
    }

    public function faculties()
    {
        return $this->belongsToMany(Faculty::class, 'department_programs');
    }

}