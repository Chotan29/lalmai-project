<?php

namespace App\Http\Controllers\Account\Fees;

use App\Console\Commands\GenerateRecurringBills;
use App\Http\Controllers\CollegeBaseController;
use App\Models\BillingAuditLog;
use App\Models\BillingProfile;
use App\Models\BillingRun;
use App\Models\BillingRunDetail;
use App\Models\FeeMaster;
use App\Jobs\SendBillingSmsBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class BillingRunController extends CollegeBaseController
{
    protected $base_route = 'account.fees.billing-run';
    protected $view_path  = 'account.fees.billing-run';
    protected $panel      = 'Billing Runs';

    // -------------------------------------------------------
    // LIST: All runs (optionally filtered by profile)
    // -------------------------------------------------------

    public function index(Request $request)
    {
        $query = BillingRun::with('billingProfile')->orderByDesc('run_date');

        if ($request->filled('profile_id')) {
            $query->where('billing_profile_id', $request->profile_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data['runs']     = $query->paginate(env('PAGINATION_LIMIT', 50));
        $data['profiles'] = BillingProfile::orderBy('profile_name')->get(['id', 'profile_name']);
        $data['view_path']  = $this->view_path;
        $data['base_route'] = $this->base_route;
        $data['panel']      = $this->panel;

        return view(parent::loadDataToView($this->view_path . '.index'), $data);
    }

    // -------------------------------------------------------
    // DETAIL: Per-student drill-down for one run
    // -------------------------------------------------------

    public function detail(int $id)
    {
        $run = BillingRun::with('billingProfile')->findOrFail($id);

        $data['run']        = $run;
        $data['details']    = BillingRunDetail::with('student')
            ->where('billing_run_id', $id)
            ->orderBy('status')
            ->paginate(100);

        $data['view_path']  = $this->view_path;
        $data['base_route'] = $this->base_route;
        $data['panel']      = $this->panel;

        return view(parent::loadDataToView($this->view_path . '.detail'), $data);
    }

    // -------------------------------------------------------
    // MANUAL TRIGGER: "Run Now" for a specific profile
    // -------------------------------------------------------

    public function trigger(Request $request, int $profileId)
    {
        $request->validate([
            'period_override' => 'nullable|string|max:20',
        ]);

        $profile = BillingProfile::findOrFail($profileId);

        $options = ['--profile' => $profileId];
        if ($request->filled('period_override')) {
            $options['--period'] = $request->period_override;
        }
        if ($request->input('force')) {
            $options['--force'] = true;
        }

        try {
            Artisan::call('bill:generate-recurring', $options);

            $latestRun = BillingRun::where('billing_profile_id', $profileId)
                ->latest('created_at')
                ->first();

            if ($latestRun) {
                // Audit
                BillingAuditLog::create([
                    'action'         => 'bill_created',
                    'entity_type'    => 'billing_run',
                    'entity_id'      => $latestRun->id,
                    'billing_run_id' => $latestRun->id,
                    'notes'          => "Manual trigger: {$latestRun->bills_created} bills created, {$latestRun->bills_skipped} skipped.",
                    'performed_by'   => auth()->id(),
                    'ip_address'     => $request->ip(),
                ]);

                return redirect()
                    ->route('account.fees.billing-run.detail', $latestRun->id)
                    ->with('message_success', "Billing run completed: {$latestRun->bills_created} bills created, {$latestRun->bills_skipped} skipped.");
            }

            return redirect()
                ->route('account.fees.billing-run')
                ->with('message_warning', 'Run completed but no new bills were created (possibly already billed for this period).');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('message_warning', 'Run failed: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------
    // APPROVE RUN
    // -------------------------------------------------------

    public function approveRun(Request $request, int $id)
    {
        $run = BillingRun::findOrFail($id);

        if ($run->status === 'approved') {
            return redirect()->back()->with('message_warning', 'Run is already approved.');
        }
        if ($run->status === 'cancelled') {
            return redirect()->back()->with('message_warning', 'Cannot approve a cancelled run.');
        }

        $run->update([
            'status'      => 'approved',
            'approved_at' => Carbon::now(),
            'approved_by' => auth()->id(),
        ]);

        BillingAuditLog::create([
            'action'         => 'run_approved',
            'entity_type'    => 'billing_run',
            'entity_id'      => $run->id,
            'billing_run_id' => $run->id,
            'notes'          => 'Run approved. Period: ' . $run->period_label,
            'performed_by'   => auth()->id(),
            'ip_address'     => $request->ip(),
        ]);

        return redirect()->back()
            ->with('message_success', 'Billing run #' . $id . ' approved successfully.');
    }

    // -------------------------------------------------------
    // CANCEL RUN (deactivates all unpaid FeeMasters)
    // -------------------------------------------------------

    public function cancelRun(Request $request, int $id)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $run = BillingRun::findOrFail($id);

        if ($run->status === 'cancelled') {
            return redirect()->back()->with('message_warning', 'Run is already cancelled.');
        }
        if ($run->status === 'running') {
            return redirect()->back()->with('message_warning', 'Cannot cancel a run that is currently running.');
        }

        $cancelled = 0;
        $protected = 0;
        $reason    = $request->input('reason', 'Cancelled by admin');

        $details = BillingRunDetail::where('billing_run_id', $id)
            ->where('status', 'created')
            ->get();

        foreach ($details as $detail) {
            if ($detail->fee_master_id) {
                $fm = FeeMaster::find($detail->fee_master_id);
                if ($fm) {
                    $hasPaid = $fm->collections()->where('status', 1)->sum('paid_amount') > 0;
                    if ($hasPaid) {
                        $protected++;
                        continue;
                    }
                    $fm->update(['status' => 0]);
                }
            }
            $detail->update([
                'status'       => 'cancelled',
                'cancelled_at' => Carbon::now(),
                'cancelled_by' => auth()->id(),
                'cancel_reason'=> $reason,
            ]);
            $cancelled++;
        }

        $newStatus = ($protected > 0 && $cancelled > 0) ? 'partial' : ($protected > 0 ? $run->status : 'cancelled');
        $run->update([
            'status'       => $newStatus,
            'cancelled_at' => Carbon::now(),
            'cancelled_by' => auth()->id(),
            'cancel_reason'=> $reason,
        ]);

        BillingAuditLog::create([
            'action'         => 'run_cancelled',
            'entity_type'    => 'billing_run',
            'entity_id'      => $run->id,
            'billing_run_id' => $run->id,
            'notes'          => "{$cancelled} bills cancelled, {$protected} protected (paid). Reason: {$reason}",
            'performed_by'   => auth()->id(),
            'ip_address'     => $request->ip(),
        ]);

        $msg = "{$cancelled} bill(s) cancelled.";
        if ($protected > 0) {
            $msg .= " {$protected} bill(s) were protected (already have payments).";
        }

        return redirect()->back()->with('message_success', $msg);
    }

    // -------------------------------------------------------
    // DELETE RUN (only if no paid bills)
    // -------------------------------------------------------

    public function deleteRun(Request $request, int $id)
    {
        $run = BillingRun::findOrFail($id);

        if ($run->status === 'running') {
            return redirect()->back()->with('message_warning', 'Cannot delete a run that is currently running.');
        }

        // Check for any paid fee masters linked to this run
        $paidCount = FeeMaster::where('billing_run_id', $id)
            ->whereHas('feeCollect')
            ->count();

        if ($paidCount > 0) {
            return redirect()->back()
                ->with('message_warning', "Cannot delete: {$paidCount} fee(s) linked to this run have payment records. Cancel individual bills instead.");
        }

        $auditNote = "Run #{$id} ({$run->period_label}) deleted. Profile: " . optional($run->billingProfile)->profile_name;

        // Delete fee masters (not cascade on billing_run_id — do manually)
        FeeMaster::where('billing_run_id', $id)->delete();

        // Log before deleting so run ID is still meaningful
        BillingAuditLog::create([
            'action'         => 'run_deleted',
            'entity_type'    => 'billing_run',
            'entity_id'      => $run->id,
            'billing_run_id' => $run->id,
            'notes'          => $auditNote,
            'performed_by'   => auth()->id(),
            'ip_address'     => $request->ip(),
        ]);

        $run->delete(); // cascades to fee_billing_run_details

        return redirect()->route('account.fees.billing-run')
            ->with('message_success', 'Billing run deleted successfully.');
    }

    // -------------------------------------------------------
    // CANCEL INDIVIDUAL STUDENT BILL
    // -------------------------------------------------------

    public function cancelDetail(Request $request, int $detailId)
    {
        $request->validate(['reason' => 'nullable|string|max:300']);

        $detail = BillingRunDetail::findOrFail($detailId);

        if ($detail->status === 'cancelled') {
            return redirect()->back()->with('message_warning', 'Bill is already cancelled.');
        }

        if ($detail->fee_master_id) {
            $fm = FeeMaster::find($detail->fee_master_id);
            if ($fm && $fm->collections()->where('status', 1)->sum('paid_amount') > 0) {
                return redirect()->back()
                    ->with('message_warning', 'Cannot cancel: this fee has an existing payment record.');
            }
            if ($fm) {
                $fm->update(['status' => 0]);
            }
        }

        $reason = $request->input('reason', 'Cancelled by admin');
        $detail->update([
            'status'        => 'cancelled',
            'cancelled_at'  => Carbon::now(),
            'cancelled_by'  => auth()->id(),
            'cancel_reason' => $reason,
        ]);

        BillingAuditLog::create([
            'action'         => 'bill_cancelled',
            'entity_type'    => 'billing_run_detail',
            'entity_id'      => $detail->id,
            'billing_run_id' => $detail->billing_run_id,
            'student_id'     => $detail->student_id,
            'notes'          => "Individual bill cancelled. Reason: {$reason}",
            'performed_by'   => auth()->id(),
            'ip_address'     => $request->ip(),
        ]);

        return redirect()->back()
            ->with('message_success', 'Bill cancelled for student #' . $detail->student_id . '.');
    }

    // -------------------------------------------------------
    // RESTORE INDIVIDUAL STUDENT BILL
    // -------------------------------------------------------

    public function restoreDetail(Request $request, int $detailId)
    {
        $detail = BillingRunDetail::where('id', $detailId)
            ->where('status', 'cancelled')
            ->firstOrFail();

        if ($detail->fee_master_id) {
            FeeMaster::where('id', $detail->fee_master_id)->update(['status' => 1]);
        }

        $detail->update([
            'status'        => 'created',
            'cancelled_at'  => null,
            'cancelled_by'  => null,
            'cancel_reason' => null,
        ]);

        BillingAuditLog::create([
            'action'         => 'bill_restored',
            'entity_type'    => 'billing_run_detail',
            'entity_id'      => $detail->id,
            'billing_run_id' => $detail->billing_run_id,
            'student_id'     => $detail->student_id,
            'notes'          => 'Individual bill restored.',
            'performed_by'   => auth()->id(),
            'ip_address'     => $request->ip(),
        ]);

        return redirect()->back()
            ->with('message_success', 'Bill restored for student #' . $detail->student_id . '.');
    }

    // -------------------------------------------------------
    // BULK ACTION (cancel / restore selected details)
    // -------------------------------------------------------

    public function bulkAction(Request $request, int $runId)
    {
        $request->validate([
            'action'      => 'required|in:cancel,restore',
            'detail_ids'  => 'required|array|min:1',
            'detail_ids.*'=> 'integer|exists:fee_billing_run_details,id',
            'reason'      => 'nullable|string|max:300',
        ]);

        $run    = BillingRun::findOrFail($runId);
        $action = $request->action;
        $reason = $request->input('reason', 'Bulk ' . $action . ' by admin');
        $ids    = $request->detail_ids;

        $done      = 0;
        $protected = 0;

        DB::transaction(function () use ($ids, $action, $reason, &$done, &$protected) {
            foreach ($ids as $detailId) {
                $detail = BillingRunDetail::find($detailId);
                if (!$detail) {
                    continue;
                }

                if ($action === 'cancel') {
                    if ($detail->status === 'cancelled') {
                        continue;
                    }
                    if ($detail->fee_master_id) {
                        $fm = FeeMaster::find($detail->fee_master_id);
                        if ($fm && $fm->collections()->where('status', 1)->sum('paid_amount') > 0) {
                            $protected++;
                            continue;
                        }
                        if ($fm) {
                            $fm->update(['status' => 0]);
                        }
                    }
                    $detail->update([
                        'status'        => 'cancelled',
                        'cancelled_at'  => Carbon::now(),
                        'cancelled_by'  => auth()->id(),
                        'cancel_reason' => $reason,
                    ]);
                    $done++;
                } elseif ($action === 'restore') {
                    if ($detail->status !== 'cancelled') {
                        continue;
                    }
                    if ($detail->fee_master_id) {
                        FeeMaster::where('id', $detail->fee_master_id)->update(['status' => 1]);
                    }
                    $detail->update([
                        'status'        => 'created',
                        'cancelled_at'  => null,
                        'cancelled_by'  => null,
                        'cancel_reason' => null,
                    ]);
                    $done++;
                }
            }
        });

        BillingAuditLog::create([
            'action'         => $action === 'cancel' ? 'bulk_cancelled' : 'bulk_restored',
            'entity_type'    => 'billing_run',
            'entity_id'      => $runId,
            'billing_run_id' => $runId,
            'notes'          => "{$done} bills {$action}d. {$protected} protected. Reason: {$reason}",
            'new_values'     => ['detail_ids' => $ids],
            'performed_by'   => auth()->id(),
            'ip_address'     => $request->ip(),
        ]);

        $msg = "{$done} bill(s) {$action}d successfully.";
        if ($protected > 0) {
            $msg .= " {$protected} bill(s) skipped (already paid).";
        }

        return redirect()->back()->with('message_success', $msg);
    }

    // -------------------------------------------------------
    // RESEND SMS: Retry failed/skipped SMS for a run
    // -------------------------------------------------------

    public function resendSms(int $runId)
    {
        $run = BillingRun::findOrFail($runId);

        BillingRunDetail::where('billing_run_id', $runId)
            ->where('status', 'created')
            ->whereIn('sms_status', ['failed', 'skipped'])
            ->update(['sms_status' => 'pending']);

        dispatch(new SendBillingSmsBatch($runId));

        return redirect()->back()
            ->with('message_success', 'SMS batch re-dispatched for run #' . $runId . '.');
    }
}
