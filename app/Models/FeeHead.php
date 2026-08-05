<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeHead extends BaseModel
{
    protected $table = 'fee_heads';
    protected $fillable = ['created_by', 'last_updated_by', 'fee_head_title', 'fee_head_amount',
        'collected_by', 'is_treasury', 'hide_from_student', 'status'];

    /** Money the college collects but owes to the departments. */
    public function scopeDepartment($query)
    {
        return $query->where('collected_by', 'department');
    }

    public function scopeCollege($query)
    {
        return $query->where('collected_by', 'college');
    }

    /** Heads that have to be deposited to the government treasury. */
    public function scopeTreasury($query)
    {
        return $query->where('is_treasury', 1);
    }
}
