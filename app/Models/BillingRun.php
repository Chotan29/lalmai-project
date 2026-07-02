<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingRun extends Model
{
    protected $table = 'fee_billing_runs';

    protected $fillable = [
        'billing_profile_id',
        'period_key',
        'period_label',
        'period_year',
        'period_month',
        'run_date',
        'due_date',
        'total_students',
        'bills_created',
        'bills_skipped',
        'bills_failed',
        'sms_queued',
        'total_amount',
        'triggered_by',
        'initiated_by',
        'status',
        'started_at',
        'finished_at',
        'error_log',
        'approved_at',
        'approved_by',
        'cancelled_at',
        'cancelled_by',
        'cancel_reason',
    ];

    protected $casts = [
        'run_date'     => 'date',
        'due_date'     => 'date',
        'started_at'   => 'datetime',
        'finished_at'  => 'datetime',
        'approved_at'  => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    public function billingProfile()
    {
        return $this->belongsTo(BillingProfile::class, 'billing_profile_id');
    }

    public function details()
    {
        return $this->hasMany(BillingRunDetail::class, 'billing_run_id');
    }

    public function createdDetails()
    {
        return $this->hasMany(BillingRunDetail::class, 'billing_run_id')
            ->where('status', 'created');
    }

    public function skippedDetails()
    {
        return $this->hasMany(BillingRunDetail::class, 'billing_run_id')
            ->where('status', 'skipped');
    }

    public function failedDetails()
    {
        return $this->hasMany(BillingRunDetail::class, 'billing_run_id')
            ->where('status', 'failed');
    }

    public function initiatedBy()
    {
        return $this->belongsTo(\App\User::class, 'initiated_by');
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForProfile($query, int $profileId)
    {
        return $query->where('billing_profile_id', $profileId);
    }

    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------

    /**
     * Success rate as percentage
     */
    public function getSuccessRateAttribute(): float
    {
        if ($this->total_students === 0) {
            return 0;
        }
        return round(($this->bills_created / $this->total_students) * 100, 1);
    }

    public function getStatusBadgeAttribute(): string
    {
        switch ($this->status) {
            case 'pending':   return '<span class="label label-default">Pending</span>';
            case 'running':   return '<span class="label label-info">Running</span>';
            case 'completed': return '<span class="label label-success">Completed</span>';
            case 'partial':   return '<span class="label label-warning">Partial</span>';
            case 'failed':    return '<span class="label label-danger">Failed</span>';
            case 'cancelled': return '<span class="label label-danger"><i class="fa fa-ban"></i> Cancelled</span>';
            case 'approved':  return '<span class="label label-success"><i class="fa fa-check-circle"></i> Approved</span>';
            default:          return '<span class="label label-default">' . ucfirst($this->status) . '</span>';
        }
    }

    /**
     * Whether this run can still be modified (not yet approved/cancelled).
     */
    public function isModifiable(): bool
    {
        return in_array($this->status, ['completed', 'partial', 'failed']);
    }

    public function isCancellable(): bool
    {
        return !in_array($this->status, ['cancelled', 'running']);
    }

    public function getTriggeredByBadgeAttribute(): string
    {
        return $this->triggered_by === 'manual'
            ? '<span class="label label-primary">Manual</span>'
            : '<span class="label label-default">Schedule</span>';
    }
}
