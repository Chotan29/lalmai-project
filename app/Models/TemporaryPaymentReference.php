<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemporaryPaymentReference extends BaseModel
{
    protected $fillable = [
        'uuid',
        'student_reg_no',
        'invoice_id',
        'ref_no',
        'amount',
        'created_by',
    ];
}
