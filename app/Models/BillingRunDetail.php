<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingRunDetail extends Model
{
    protected $table = 'fee_billing_run_details';

    protected $fillable = [
        'billing_run_id',
        'student_id',
        'fee_master_id',
        'amount',
        'status',
        'skip_reason',
        'error_message',
        'sms_status',
        'cancelled_at',
        'cancelled_by',
        'cancel_reason',
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
    ];

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    public function billingRun()
    {
        return $this->belongsTo(BillingRun::class, 'billing_run_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function feeMaster()
    {
        return $this->belongsTo(FeeMaster::class, 'fee_master_id');
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    public function scopeCreated($query)
    {
        return $query->where('status', 'created');
    }

    public function scopeSmsPending($query)
    {
        return $query->where('sms_status', 'pending');
    }

    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------

    public function getStatusBadgeAttribute(): string
    {
        switch ($this->status) {
            case 'created':   return '<span class="label label-success">Created</span>';
            case 'skipped':   return '<span class="label label-warning">Skipped</span>';
            case 'failed':    return '<span class="label label-danger">Failed</span>';
            case 'cancelled': return '<span class="label label-default"><i class="fa fa-ban"></i> Cancelled</span>';
            default:          return '<span class="label label-default">' . ucfirst($this->status) . '</span>';
        }
    }

    public function getSmsBadgeAttribute(): string
    {
        switch ($this->sms_status) {
            case 'sent':    return '<i class="fa fa-check text-success" title="SMS Sent"></i>';
            case 'queued':  return '<i class="fa fa-clock-o text-info" title="SMS Queued"></i>';
            case 'failed':  return '<i class="fa fa-times text-danger" title="SMS Failed"></i>';
            case 'pending': return '<i class="fa fa-hourglass text-warning" title="Pending"></i>';
            case 'skipped': return '<i class="fa fa-minus text-muted" title="Skipped"></i>';
            default:        return '—';
        }
    }
}
