<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipsoiAttendanceLog extends Model
{
    use HasFactory;

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