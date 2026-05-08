<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiometricPerson extends Model
{
    protected $table = 'biometric_people';
    protected $fillable = [
        'attendable_type','attendable_id','person_identifier','remote_person_id',
        'rfid','primary_display_text','secondary_display_text','photo_url','last_pushed_at'
    ];

    public function attendable()
    {
        return $this->morphTo(__FUNCTION__, 'attendable_type', 'attendable_id');
    }
}
