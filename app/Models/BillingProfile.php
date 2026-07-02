<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BillingProfile extends Model
{
    protected $table = 'fee_billing_profiles';

    protected $fillable = [
        'created_by',
        'updated_by',
        'profile_name',
        'description',
        'scope_type',
        'faculty_id',
        'semester_id',
        'batch_id',
        'only_active_students',
        'only_regular_status',
        'billing_cycle',
        'billing_day',
        'billing_months',
        'one_time_date',
        'due_days',
        'fine_type',
        'fine_amount',
        'fine_grace_days',
        'max_fine',
        'installment_count',
        'installment_splits',
        'sms_on_generation',
        'alert_event_key',
        'status',
    ];

    protected $casts = [
        'billing_months'     => 'array',
        'installment_splits' => 'array',
        'one_time_date'      => 'date',
        'sms_on_generation'  => 'boolean',
        'only_active_students' => 'boolean',
        'only_regular_status'  => 'boolean',
        'status'             => 'boolean',
    ];

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    public function profileItems()
    {
        return $this->hasMany(BillingProfileItem::class, 'billing_profile_id')
            ->orderBy('sort_order');
    }

    public function runs()
    {
        return $this->hasMany(BillingRun::class, 'billing_profile_id')
            ->orderByDesc('run_date');
    }

    public function latestRun()
    {
        return $this->hasOne(BillingRun::class, 'billing_profile_id')
            ->latest('run_date');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function batch()
    {
        return $this->belongsTo(StudentBatch::class, 'batch_id');
    }

    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------

    /**
     * Human-readable cycle label
     */
    public function getCycleLabelAttribute(): string
    {
        switch ($this->billing_cycle) {
            case 'monthly':     return 'Monthly';
            case 'quarterly':   return 'Quarterly';
            case 'half_yearly': return 'Half-Yearly';
            case 'yearly':      return 'Yearly';
            case 'one_time':    return 'One-Time';
            default:            return ucfirst($this->billing_cycle);
        }
    }

    /**
     * Human-readable scope label
     */
    public function getScopeLabelAttribute(): string
    {
        switch ($this->scope_type) {
            case 'all':      return 'All Students';
            case 'faculty':  return 'Faculty: ' . ($this->faculty->faculty ?? '—');
            case 'semester': return 'Semester: ' . ($this->semester->semester ?? '—');
            case 'batch':    return 'Batch: ' . ($this->batch->title ?? '—');
            default:         return ucfirst($this->scope_type);
        }
    }

    /**
     * Total fee amount per student (sum of profile items)
     */
    public function getTotalAmountAttribute(): float
    {
        return (float) $this->profileItems->sum(function ($item) {
            return $item->amount_override ?? optional($item->feeHead)->fee_head_amount ?? 0;
        });
    }

    /**
     * Generate a period_key for given date (e.g. "2026-06", "2026-Q2", "2026-H1", "2026")
     */
    public function generatePeriodKey(Carbon $date): string
    {
        switch ($this->billing_cycle) {
            case 'monthly':     return $date->format('Y-m');
            case 'quarterly':   return $date->format('Y') . '-Q' . ceil($date->month / 3);
            case 'half_yearly': return $date->format('Y') . '-H' . ($date->month <= 6 ? '1' : '2');
            case 'yearly':      return $date->format('Y');
            case 'one_time':    return $this->one_time_date ? $this->one_time_date->format('Y-m-d') : $date->format('Y-m-d');
            default:            return $date->format('Y-m');
        }
    }

    /**
     * Generate a human-readable period label (e.g. "June 2026", "Q2 2026")
     */
    public function generatePeriodLabel(Carbon $date): string
    {
        switch ($this->billing_cycle) {
            case 'monthly':     return $date->format('F Y');
            case 'quarterly':   return 'Q' . ceil($date->month / 3) . ' ' . $date->year;
            case 'half_yearly': return 'H' . ($date->month <= 6 ? '1' : '2') . ' ' . $date->year;
            case 'yearly':      return (string) $date->year;
            case 'one_time':    return $this->one_time_date ? $this->one_time_date->format('d M Y') : $date->format('d M Y');
            default:            return $date->format('F Y');
        }
    }

    /**
     * Check if this profile should trigger on a given date.
     */
    public function isDueOn(Carbon $date): bool
    {
        if (!$this->status) {
            return false;
        }

        if ($this->billing_cycle === 'one_time') {
            return $this->one_time_date && $this->one_time_date->isSameDay($date);
        }

        if ((int) $this->billing_day !== (int) $date->day) {
            return false;
        }

        $months = $this->billing_months ?? [];

        if (empty($months)) {
            // monthly — every month
            return $this->billing_cycle === 'monthly';
        }

        return in_array($date->month, $months);
    }
}
