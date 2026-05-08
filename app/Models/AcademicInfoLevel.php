<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicInfoLevel extends BaseModel
{
    //faculty_academic_infos
    protected $fillable = ['created_by', 'last_updated_by', 'title', 'status'];

    /*public function faculty()
    {
        return $this->belongsToMany(Faculty::class);
    }*/
}
