<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingProfileItem extends Model
{
    protected $table = 'fee_billing_profile_items';

    protected $fillable = [
        'billing_profile_id',
        'fee_head_id',
        'fee_head_group_id',
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

    /** Set when this line is a whole Main Fee Head rather than a single head. */
    public function feeHeadGroup()
    {
        return $this->belongsTo(FeeHeadGroup::class, 'fee_head_group_id');
    }

    public function getIsGroupAttribute(): bool
    {
        return !is_null($this->fee_head_group_id);
    }

    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------

    /**
     * Effective amount: override > fee_head default > 0
     */
    public function getEffectiveAmountAttribute(): float
    {
        /* A Main Fee Head is worth what its sub heads add up to. There is deliberately no
           override for one: the amount comes from the fee, so correcting the fee corrects every
           profile that uses it, and no profile can quietly bill a different total. */
        if ($this->is_group) {
            return (float) optional($this->feeHeadGroup)->total_amount ?? 0;
        }

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
        if ($this->is_group) {
            return optional($this->feeHeadGroup)->title ?? 'Unknown Main Fee Head';
        }

        return optional($this->feeHead)->fee_head_title ?? 'Unknown Fee Head';
    }
}
