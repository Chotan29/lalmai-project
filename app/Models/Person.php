<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends BaseModel
{
    protected $table = 'persons';

   protected $fillable = [
        'identifier',
        'name',
        'rfid',
        'primary_display_text',
        'secondary_display_text',
        'person_type',
        'status',
        'photo_url',
        'last_sync_at'
    ];


    protected $casts = [
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime'

    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeNeedsSync($query)
    {
        return $query->whereNull('last_sync_at')
            ->orWhereColumn('updated_at', '>', 'last_sync_at');
    }

    public function scopeFailedSyncs($query)
    {
        return $query->where('sync_failed', true);
    }

    // Add this relationship if not already present
    public function tipsoiDevices()
    {
        return $this->belongsToMany(TipsoiDevice::class, 'person_device')
            ->withPivot('allocated_at', 'revoked_at', 'allocated_by', 'revoked_by')
            ->withTimestamps();
    }
}