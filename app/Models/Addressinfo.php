<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addressinfo extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'students_id', 'address', 'state', 'country','postal_code', 'temp_address',
        'temp_state', 'temp_country', 'temp_postal_code', 'home_phone', 'mobile_1', 'mobile_2', 'status'];

    public function students()
    {
        return $this->belongsTo(Student::class, 'id');
    }

}
