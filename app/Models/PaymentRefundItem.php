<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One line of a refund: how much came back out of one fee head.
 *
 * Plain Eloquent rather than BaseModel - these rows have no status of their own and are never
 * audited separately. They belong to their refund, and stand or fall with it.
 */
class PaymentRefundItem extends Model
{
    protected $table = 'payment_refund_items';

    protected $fillable = ['payment_refund_id', 'fee_head', 'fee_collection_id', 'amount', 'date'];

    public function refund()
    {
        return $this->belongsTo(PaymentRefund::class, 'payment_refund_id');
    }
}
