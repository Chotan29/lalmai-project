<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeCollection extends Model
{
    protected $table = 'fee_collections';
    protected $fillable = [
        'students_id',
        'fee_masters_id',
        'date',
        'paid_amount',
        'discount',
        'fine',
        'payment_method',
        'installment_number',
        'ref_no',
        'external_ref_no',
        'note',
        'response',
        'status',
        'verified_at',
        'created_by'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'date' => 'date'
    ];

    public function feeMaster()
    {
        return $this->belongsTo(FeeMaster::class, 'fee_masters_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id');
    }
}
