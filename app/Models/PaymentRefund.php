<?php

namespace App\Models;

/**
 * Money handed back to somebody who paid and then did not take admission.
 *
 * A refund is its own event, not a correction of the payment. The payment row stays exactly as it
 * was - the money really did come in on that day - and this row records that it went out again on
 * another. The month's collection figure is the difference, and both halves can still be shown to
 * anybody who asks.
 */
class PaymentRefund extends BaseModel
{
    protected $table = 'payment_refunds';

    protected $fillable = [
        'online_payment_id', 'students_id', 'amount', 'date', 'method',
        'ref_no', 'note', 'voucher_no', 'created_by', 'last_updated_by', 'status',
    ];

    protected $dates = ['date'];

    /** How the money actually left the office. */
    public static function methods()
    {
        return [
            'cash'    => 'Cash',
            'bank'    => 'Bank / Cheque',
            'bkash'   => 'bKash',
            'nagad'   => 'Nagad',
            'gateway' => 'Back through the payment gateway',
            'other'   => 'Other',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id');
    }

    public function payment()
    {
        return $this->belongsTo(OnlinePayment::class, 'online_payment_id');
    }

    /**
     * Refunds that count. A row switched off was entered by mistake; it is kept rather than
     * deleted, so the correction is visible, but it must not be subtracted twice.
     */
    public function scopeCounted($query)
    {
        return $query->where('status', 1);
    }
}
