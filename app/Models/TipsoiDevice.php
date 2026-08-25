<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Person;

class TipsoiDevice extends BaseModel
{
    protected $table = 'tipsoi_devices';
    protected $fillable = [
        'identifier',
        'vendor',
        'name',
        'status',
        'enrol_students',
        'model',
        'ip_address',
        'location',
        'notes',
        'last_seen',
        'connected'
    ];

    protected $casts = [
        'connected'      => 'boolean',
        'enrol_students' => 'boolean',
        'last_seen'      => 'datetime'
    ];

    public function persons()
    {
        return $this->belongsToMany(Person::class, 'person_device')
            ->withPivot('allocated_at', 'revoked_at', 'allocated_by', 'revoked_by')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

}