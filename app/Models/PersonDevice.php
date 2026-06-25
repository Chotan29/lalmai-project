<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonDevice extends Model
{
    protected $table = 'person_device';

    protected $fillable = [
        'person_id',
        'device_id',
        'allocated_at',
        'revoked_at',
        'allocated_by',
        'revoked_by',
        'synced_at',
        'sync_failed',
        'sync_notes',
        'last_sync_attempt',
    ];

    protected $casts = [
        'allocated_at'       => 'datetime',
        'revoked_at'         => 'datetime',
        'synced_at'          => 'datetime',
        'last_sync_attempt'  => 'datetime',
        'sync_failed'        => 'boolean',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function device()
    {
        return $this->belongsTo(TipsoiDevice::class, 'device_id');
    }
}
