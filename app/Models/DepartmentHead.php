<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentHead extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'department_head',  'sorting', 'status'];

    public function department() {
        return $this->belongsToMany(Department::class,'department_heads_department','department_head_id');
    }
}
