<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/*
 * HasFactory was imported and used here, but that trait arrived in Laravel 8 and this is 5.8 -
 * so the class could never be loaded at all: touching it threw "Trait
 * Illuminate\Database\Eloquent\Factories\HasFactory not found" before any code ran. It only went
 * unnoticed because nothing had ever written a punch. Removed rather than replaced; it exists
 * only to provide ::factory() for tests, which this version has no notion of.
 */

class TipsoiAttendanceLog extends Model
{
    protected $table = 'tipsoi_attendance_logs';

    protected $casts = [
        'logged_time' => 'datetime',
        'sync_time' => 'datetime',
        'raw_data' => 'array'
    ];

    protected $fillable = [
        'device_identifier',
        'device_location',
        'person_identifier',
        'person_name',
        'rfid',
        'logged_time',
        'sync_time',
        'type',
        'primary_display_text',
        'secondary_display_text',
        'uid',
        'person_id_in_device',
        'project_id',
        'raw_data'
    ];

    // Relationships
    public function person()
    {
        return $this->belongsTo(Person::class, 'person_identifier', 'identifier');
    }

    public function device()
    {
        return $this->belongsTo(TipsoiDevice::class, 'device_identifier', 'identifier');
    }

    // Scopes
    public function scopeForPerson($query, $identifier)
    {
        return $query->where('person_identifier', $identifier);
    }

    public function scopeForDevice($query, $identifier)
    {
        return $query->where('device_identifier', $identifier);
    }

    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('logged_time', [$start, $end]);
    }
}