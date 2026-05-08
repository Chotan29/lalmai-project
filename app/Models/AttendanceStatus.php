<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceStatus extends BaseModel
{
    protected $fillable = ['code','label','color','order'];

    public function scopeOrdered($q)
    {
        return $q->orderBy('order')->orderBy('id');
    }
}
