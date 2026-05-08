<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['created_by', 'last_updated_by', 'title', 'code','course_fee', 'full_mark_theory', 'pass_mark_theory',
        'full_mark_practical', 'pass_mark_practical', 'credit_hour', 'sub_type', 'class_type', 'staff_id',
        'description', 'status'];


    public function semester()
    {
        return $this->belongsToMany(Semester::class);
    }
}
