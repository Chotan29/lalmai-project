<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Schema;

trait HasRegNo
{
    /**
     * Returns the best-guess reg_no column for the model table.
     */
    public static function regNoColumn()
    {
        $table = (new static)->getTable();
        $cands = ['reg_no','reg_number','registration_no','register_no','regd_no','enrollment_no','enroll_no','roll_no','staff_code','employee_code','code'];
        foreach ($cands as $c) {
            if (Schema::hasColumn($table, $c)) return $c;
        }
        return 'reg_no';
    }

    /**
     * Find by reg_no (or synonym).
     */
    public static function findByRegNo($value)
    {
        $col = static::regNoColumn();
        return static::query()->where($col, $value)->first();
    }

    /**
     * Full name accessor default.
     */
    public function getFullNameAttribute()
    {
        if (isset($this->attributes['name']) && $this->attributes['name']) {
            return $this->attributes['name'];
        }
        $parts = [];
        foreach (['first_name','middle_name','last_name'] as $k) {
            if (!empty($this->{$k})) $parts[] = trim($this->{$k});
        }
        return $parts ? implode(' ', $parts) : ('#'.$this->getKey());
    }
}
