<?php

namespace App\Models;

/**
 * A fee as the student sees it: one title, one amount.
 *
 * What it is actually made of lives in the items - one row per fee head, each with its own
 * amount. The student never sees those; the accounts never see anything else.
 */
class FeeHeadGroup extends BaseModel
{
    protected $table = 'fee_head_groups';

    protected $fillable = ['created_by', 'last_updated_by', 'title', 'session', 'description',
        'total_amount', 'is_locked', 'status'];

    /**
     * The sub heads currently in this fee.
     *
     * Ordered the way money must fill them - college heads first, department heads last - and
     * limited to the live ones. A head taken out of the fee is switched off rather than
     * removed, so allItems() still shows what the fee used to contain.
     */
    public function items()
    {
        return $this->hasMany(FeeHeadGroupItem::class, 'fee_head_group_id')
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function allItems()
    {
        return $this->hasMany(FeeHeadGroupItem::class, 'fee_head_group_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /** What the items actually add up to - which must equal total_amount before this can be saved. */
    public function itemsTotal()
    {
        return (float) $this->items()->sum('amount');
    }

    public function isBalanced()
    {
        /* Compared in paisa. Two decimals of money should never be compared as floats. */
        return (int) round($this->itemsTotal() * 100) === (int) round(((float) $this->total_amount) * 100);
    }
}
