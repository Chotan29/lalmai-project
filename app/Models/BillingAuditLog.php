<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingAuditLog extends Model
{
    protected $table = 'fee_billing_audit_logs';

    protected $fillable = [
        'action',
        'entity_type',
        'entity_id',
        'billing_run_id',
        'student_id',
        'notes',
        'old_values',
        'new_values',
        'performed_by',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // -------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------

    public function performer()
    {
        return $this->belongsTo(\App\User::class, 'performed_by');
    }

    public function billingRun()
    {
        return $this->belongsTo(BillingRun::class, 'billing_run_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // -------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------

    public function getActionLabelAttribute(): string
    {
        $map = [
            'bill_created'      => '<span class="label label-success">Bill Created</span>',
            'bill_cancelled'    => '<span class="label label-warning">Bill Cancelled</span>',
            'bill_restored'     => '<span class="label label-info">Bill Restored</span>',
            'run_approved'      => '<span class="label label-success">Run Approved</span>',
            'run_cancelled'     => '<span class="label label-warning">Run Cancelled</span>',
            'run_deleted'       => '<span class="label label-danger">Run Deleted</span>',
            'bulk_cancelled'    => '<span class="label label-warning">Bulk Cancelled</span>',
            'bulk_restored'     => '<span class="label label-info">Bulk Restored</span>',
            'setting_updated'   => '<span class="label label-default">Settings Updated</span>',
        ];
        return $map[$this->action] ?? '<span class="label label-default">' . ucwords(str_replace('_', ' ', $this->action)) . '</span>';
    }

    // -------------------------------------------------------
    // STATIC FACTORY
    // -------------------------------------------------------

    /**
     * Convenience method to record a billing audit entry.
     */
    public static function record(
        string $action,
        string $entityType,
        int $entityId,
        array $extra = [],
        ?Request $request = null
    ): self {
        return static::create(array_merge([
            'action'       => $action,
            'entity_type'  => $entityType,
            'entity_id'    => $entityId,
            'performed_by' => auth()->id() ?? 1,
            'ip_address'   => request()->ip(),
        ], $extra));
    }
}
