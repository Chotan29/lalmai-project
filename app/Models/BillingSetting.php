<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingSetting extends Model
{
    protected $table = 'fee_billing_settings';

    protected $fillable = [
        'scheduler_hour',
        'scheduler_minute',
        'scheduler_enabled',
        'updated_by',
    ];

    protected $casts = [
        'scheduler_enabled' => 'boolean',
    ];

    /**
     * Get the formatted scheduler time string (HH:MM).
     */
    public function getSchedulerTimeAttribute(): string
    {
        return str_pad($this->scheduler_hour, 2, '0', STR_PAD_LEFT)
             . ':' . str_pad($this->scheduler_minute, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Singleton: get or create the one settings row.
     */
    public static function instance(): self
    {
        return static::firstOrCreate([], [
            'scheduler_hour'    => 6,
            'scheduler_minute'  => 30,
            'scheduler_enabled' => true,
        ]);
    }
}
