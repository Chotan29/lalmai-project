<?php

namespace App\Models;

/**
 * One head inside a fee, with the amount it takes and the position it takes it in.
 *
 * The amount lives here rather than on the head because the same head is worth different
 * things in different fees - লাইব্রেরি is 25 at admission and 10 on an exam fee - and because a
 * new session is then a new group plus its items, leaving last year's amounts untouched.
 */
class FeeHeadGroupItem extends BaseModel
{
    protected $table = 'fee_head_group_items';

    protected $fillable = ['created_by', 'last_updated_by', 'fee_head_group_id', 'fee_head_id',
        'amount', 'sort_order', 'status'];

    public function group()
    {
        return $this->belongsTo(FeeHeadGroup::class, 'fee_head_group_id');
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class, 'fee_head_id');
    }
}
