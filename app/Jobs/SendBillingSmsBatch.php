<?php

namespace App\Jobs;

use App\Models\AlertSetting;
use App\Models\BillingRun;
use App\Models\BillingRunDetail;
use App\Models\Student;
use App\Models\Addressinfo;
use App\Traits\SmsEmailScope;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Sends billing SMS notifications for all students in a given billing run.
 * Queued on 'notifications' channel — same as attendance SMS.
 * Processes in chunks to avoid memory issues on large student sets.
 */
class SendBillingSmsBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SmsEmailScope;

    public $timeout = 300; // 5 minutes max per run
    public $tries   = 2;

    protected int $billingRunId;

    public function __construct(int $billingRunId)
    {
        $this->billingRunId  = $billingRunId;
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $run = BillingRun::with('billingProfile')->find($this->billingRunId);
        if (!$run) {
            Log::warning("SendBillingSmsBatch: BillingRun #{$this->billingRunId} not found.");
            return;
        }

        $profile    = $run->billingProfile;
        $eventKey   = $profile->alert_event_key ?? 'BillingGenerated';
        $periodLabel = $run->period_label;
        $dueDate    = $run->due_date ? $run->due_date->format('d M Y') : '';
        $appName    = config('app.name', 'Institution');

        // Load SMS template from alert_settings
        $alert = AlertSetting::where('event', $eventKey)->where('status', 1)->first();
        if (!$alert || !$alert->sms) {
            Log::info("SendBillingSmsBatch: SMS disabled for event '{$eventKey}'. Run #{$this->billingRunId} skipped.");
            BillingRun::where('id', $this->billingRunId)->increment('sms_queued', 0); // no-op
            return;
        }

        $template = $alert->template;
        $smsSentCount = 0;

        // Process only 'created' details (not already skipped/failed)
        BillingRunDetail::where('billing_run_id', $this->billingRunId)
            ->where('status', 'created')
            ->where('sms_status', 'pending')
            ->with('student.address')
            ->chunk(50, function ($details) use ($template, $periodLabel, $dueDate, $appName, &$smsSentCount) {
                foreach ($details as $detail) {
                    $student = $detail->student;
                    if (!$student) {
                        $detail->update(['sms_status' => 'failed']);
                        continue;
                    }

                    $mobile = $this->getStudentMobile($student);
                    if (!$mobile) {
                        $detail->update(['sms_status' => 'skipped']);
                        continue;
                    }

                    // Build fee_heads label from billing profile items
                    $feeHeadsLabel = $this->buildFeeHeadsLabel($detail->billingRun);

                    $message = $this->fillTemplate($template, [
                        '{{name}}'       => trim($student->first_name . ' ' . $student->last_name),
                        '{{reg_no}}'     => $student->reg_no ?? '',
                        '{{amount}}'     => number_format($detail->amount, 0),
                        '{{due_date}}'   => $dueDate,
                        '{{period}}'     => $periodLabel,
                        '{{fee_heads}}'  => $feeHeadsLabel,
                        '{{app_name}}'   => $appName,
                    ]);

                    try {
                        $this->sendSMS($mobile, $message);
                        $detail->update(['sms_status' => 'sent']);
                        $smsSentCount++;
                    } catch (\Throwable $e) {
                        Log::error("SendBillingSmsBatch: SMS failed for student #{$student->id}: " . $e->getMessage());
                        $detail->update(['sms_status' => 'failed']);
                    }
                }
            });

        // Update SMS count on the run record
        BillingRun::where('id', $this->billingRunId)
            ->update(['sms_queued' => $smsSentCount]);

        Log::info("SendBillingSmsBatch: Run #{$this->billingRunId} — {$smsSentCount} SMS sent.");
    }

    // -------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------

    private function getStudentMobile(Student $student): ?string
    {
        // Try student's own mobile_1 first, then address
        if (!empty($student->mobile_1)) {
            return $student->mobile_1;
        }
        $address = $student->address;
        return $address ? ($address->mobile_1 ?? null) : null;
    }

    private function fillTemplate(string $template, array $vars): string
    {
        return strtr($template, $vars);
    }

    private function buildFeeHeadsLabel(BillingRun $run): string
    {
        $profile = $run->billingProfile;
        if (!$profile) {
            return '';
        }
        $items = $profile->profileItems()->with('feeHead')->get();
        return $items->map(function ($item) {
            $title = optional($item->feeHead)->fee_head_title ?? 'Fee';
            $amount = number_format($item->effective_amount, 0);
            return "{$title}: {$amount}";
        })->implode(', ');
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SendBillingSmsBatch: Job permanently failed for run #{$this->billingRunId}: " . $exception->getMessage());
    }
}
