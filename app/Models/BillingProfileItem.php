<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingProfileItem extends Model
{
    protected $table = 'fee_billing_profile_items';

    protected $fillable = [
        'billing_profile_id',
        'fee_head_id',
        'amount_override',
        'is_optional',
        'sort_order',
    ];

    protected $casts = [
        'is_optional' => 'boolean',
    ];

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    public function billingProfile()
    {
        return $this->belongsTo(BillingProfile::class, 'billing_profile_id');
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class, 'fee_head_id');
    }

    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------

    /**
     * Effective amount: override > fee_head default > 0
     */
    public function getEffectiveAmountAttribute(): float
    {
        if (!is_null($this->amount_override)) {
            return (float) $this->amount_override;
        }
        return (float) optional($this->feeHead)->fee_head_amount ?? 0;
    }

    /**
     * Fee head title snapshot (safe even if fee_head deleted)
     */
    public function getFeeHeadTitleAttribute(): string
    {
        return optional($this->feeHead)->fee_head_title ?? 'Unknown Fee Head';
    }
}
