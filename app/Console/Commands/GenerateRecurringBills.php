<?php

namespace App\Console\Commands;

use App\Jobs\SendBillingSmsBatch;
use App\Models\BillingProfile;
use App\Models\BillingRun;
use App\Models\BillingRunDetail;
use App\Models\FeeHead;
use App\Models\FeeMaster;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Generates recurring fee bills for all active billing profiles due today.
 *
 * Usage:
 *   php artisan bill:generate-recurring                    — auto-run (daily scheduler)
 *   php artisan bill:generate-recurring --profile=5        — specific profile only
 *   php artisan bill:generate-recurring --dry-run          — preview, no DB writes
 *   php artisan bill:generate-recurring --profile=5 --period=2026-06 --force  — re-run a period
 */
class GenerateRecurringBills extends Command
{
    protected $signature = 'bill:generate-recurring
                            {--profile=  : Run a specific billing profile ID only}
                            {--period=   : Override period key (e.g. 2026-06). Requires --profile}
                            {--force     : Re-run even if period already billed (creates duplicate check)}
                            {--dry-run   : Preview students and amounts without creating any records}';

    protected $description = 'Auto-generate recurring student bills from active billing profiles.';

    public function handle(): int
    {
        $today    = Carbon::today();
        $isDryRun = (bool) $this->option('dry-run');
        $isForce  = (bool) $this->option('force');
        $profileId = $this->option('profile') ? (int) $this->option('profile') : null;
        $periodOverride = $this->option('period') ?? null;

        if ($isDryRun) {
            $this->warn('⚡ DRY-RUN mode — no records will be created.');
        }

        // Load profiles
        $query = BillingProfile::with(['profileItems.feeHead'])->where('status', 1);
        if ($profileId) {
            $query->where('id', $profileId);
        }
        $profiles = $query->get();

        if ($profiles->isEmpty()) {
            $this->info('No active billing profiles found.');
            return 0;
        }

        $this->info("Checking {$profiles->count()} profile(s) for date: {$today->toDateString()}");

        foreach ($profiles as $profile) {
            // Skip profiles not due today (unless --profile with --period override)
            if (!$periodOverride && !$profile->isDueOn($today)) {
                $this->line("  [SKIP] Profile #{$profile->id} \"{$profile->profile_name}\" — not due today.");
                continue;
            }

            $periodKey   = $periodOverride ?? $profile->generatePeriodKey($today);
            $periodLabel = $profile->generatePeriodLabel($today);
            $dueDate     = $today->copy()->addDays($profile->due_days);

            $this->info("\n▶ Profile #{$profile->id}: {$profile->profile_name}");
            $this->line("  Period: {$periodLabel} ({$periodKey}) | Due: {$dueDate->toDateString()}");

            // Duplicate run check
            $existingRun = BillingRun::where('billing_profile_id', $profile->id)
                ->where('period_key', $periodKey)
                ->first();

            if ($existingRun && !$isForce) {
                $this->warn("  [SKIP] Already billed for period {$periodKey} (Run #{$existingRun->id}, status: {$existingRun->status}).");
                continue;
            }

            // Resolve students in scope
            $students = $this->resolveStudents($profile);
            $totalStudents = $students->count();
            $this->line("  Students in scope: {$totalStudents}");

            if ($totalStudents === 0) {
                $this->warn("  [SKIP] No students match profile scope.");
                continue;
            }

            if ($isDryRun) {
                $this->showDryRunTable($profile, $students, $periodLabel, $dueDate);
                continue;
            }

            // Create or reuse billing run
            if ($existingRun && $isForce) {
                $run = $existingRun;
                $run->update([
                    'status'     => 'running',
                    'started_at' => Carbon::now(),
                    'finished_at' => null,
                    'error_log'   => null,
                ]);
                $this->warn("  --force: Re-running existing run #{$run->id}");
            } else {
                $run = BillingRun::create([
                    'billing_profile_id' => $profile->id,
                    'period_key'         => $periodKey,
                    'period_label'       => $periodLabel,
                    'period_year'        => $today->year,
                    'period_month'       => in_array($profile->billing_cycle, ['yearly', 'one_time']) ? null : $today->month,
                    'run_date'           => $today->toDateString(),
                    'due_date'           => $dueDate->toDateString(),
                    'total_students'     => $totalStudents,
                    'triggered_by'       => 'schedule',
                    'status'             => 'running',
                    'started_at'         => Carbon::now(),
                ]);
            }

            $billsCreated = 0;
            $billsSkipped = 0;
            $billsFailed  = 0;
            $totalAmount  = 0;

            foreach ($students as $student) {
                try {
                    $result = $this->processSingleStudent(
                        $student, $profile, $run, $periodKey, $dueDate
                    );
                    if ($result['status'] === 'created') {
                        $billsCreated++;
                        $totalAmount += $result['amount'];
                    } elseif ($result['status'] === 'skipped') {
                        $billsSkipped++;
                    } else {
                        $billsFailed++;
                    }
                } catch (\Throwable $e) {
                    $billsFailed++;
                    BillingRunDetail::create([
                        'billing_run_id' => $run->id,
                        'student_id'     => $student->id,
                        'amount'         => 0,
                        'status'         => 'failed',
                        'error_message'  => substr($e->getMessage(), 0, 490),
                        'sms_status'     => 'skipped',
                    ]);
                    Log::error("GenerateRecurringBills: Student #{$student->id} failed in run #{$run->id}: " . $e->getMessage());
                }
            }

            // Determine final run status
            $finalStatus = 'completed';
            if ($billsFailed > 0 && $billsCreated === 0) {
                $finalStatus = 'failed';
            } elseif ($billsFailed > 0) {
                $finalStatus = 'partial';
            }

            $run->update([
                'status'         => $finalStatus,
                'bills_created'  => $billsCreated,
                'bills_skipped'  => $billsSkipped,
                'bills_failed'   => $billsFailed,
                'total_amount'   => $totalAmount,
                'finished_at'    => Carbon::now(),
            ]);

            $this->info("  ✅ Done: {$billsCreated} created, {$billsSkipped} skipped, {$billsFailed} failed. Total: BDT {$totalAmount}");

            // Dispatch SMS job if enabled
            if ($profile->sms_on_generation && $billsCreated > 0) {
                // Mark all created details as pending SMS
                BillingRunDetail::where('billing_run_id', $run->id)
                    ->where('status', 'created')
                    ->update(['sms_status' => 'pending']);

                dispatch(new SendBillingSmsBatch($run->id));
                $run->update(['sms_queued' => $billsCreated]);
                $this->info("  📱 SMS batch dispatched for {$billsCreated} students.");
            }
        }

        $this->info("\n✅ bill:generate-recurring complete.");
        return 0;
    }

    // -------------------------------------------------------
    // PRIVATE: RESOLVE STUDENTS BY SCOPE
    // -------------------------------------------------------

    private function resolveStudents(BillingProfile $profile)
    {
        $query = Student::query();

        if ($profile->only_active_students) {
            $query->where('status', 1);
        }

        switch ($profile->scope_type) {
            case 'faculty':
                $query->where('faculty', $profile->faculty_id);
                break;
            case 'semester':
                $query->where('semester', $profile->semester_id);
                break;
            case 'batch':
                $query->where('batch', $profile->batch_id);
                break;
            case 'all':
            default:
                // no extra filter
                break;
        }

        return $query->get(['id', 'reg_no', 'first_name', 'last_name', 'semester', 'faculty', 'mobile_1']);
    }

    // -------------------------------------------------------
    // PRIVATE: PROCESS ONE STUDENT
    // -------------------------------------------------------

    private function processSingleStudent(
        Student $student,
        BillingProfile $profile,
        BillingRun $run,
        string $periodKey,
        Carbon $dueDate
    ): array {
        // Duplicate bill check: same profile scope + same period_key + same student
        $alreadyBilled = FeeMaster::where('students_id', $student->id)
            ->where('billing_period_key', $periodKey)
            ->whereHas('billingRun', function ($q) use ($profile) {
                $q->where('billing_profile_id', $profile->id);
            })
            ->exists();

        if ($alreadyBilled) {
            BillingRunDetail::create([
                'billing_run_id' => $run->id,
                'student_id'     => $student->id,
                'amount'         => 0,
                'status'         => 'skipped',
                'skip_reason'    => "Already billed for period {$periodKey}",
                'sms_status'     => 'skipped',
            ]);
            return ['status' => 'skipped', 'amount' => 0];
        }

        $totalAmount = 0;
        $firstFeeMasterId = null;

        DB::transaction(function () use ($student, $profile, $run, $periodKey, $dueDate, &$totalAmount, &$firstFeeMasterId) {
            foreach ($profile->profileItems as $item) {
                $amount = $item->effective_amount;
                if ($amount <= 0) {
                    continue;
                }
                $fm = FeeMaster::create([
                    'created_by'          => $run->initiated_by ?? 1,
                    'students_id'         => $student->id,
                    'semester'            => $student->semester ?? '',
                    'fee_head'            => $item->fee_head_id,
                    'fee_due_date'        => $dueDate->toDateTimeString(),
                    'fee_due_date2'       => $dueDate->toDateTimeString(),
                    'fee_due_date3'       => $dueDate->toDateTimeString(),
                    'fee_amount'          => $amount,
                    'status'              => 1,
                    'billing_run_id'      => $run->id,
                    'billing_period_key'  => $periodKey,
                    'source_type'         => 'recurring',
                ]);
                $totalAmount += $amount;
                if (!$firstFeeMasterId) {
                    $firstFeeMasterId = $fm->id;
                }
            }
        });

        BillingRunDetail::create([
            'billing_run_id' => $run->id,
            'student_id'     => $student->id,
            'fee_master_id'  => $firstFeeMasterId,
            'amount'         => $totalAmount,
            'status'         => 'created',
            'sms_status'     => $profile->sms_on_generation ? 'pending' : 'skipped',
        ]);

        return ['status' => 'created', 'amount' => $totalAmount];
    }

    // -------------------------------------------------------
    // PRIVATE: DRY-RUN TABLE
    // -------------------------------------------------------

    private function showDryRunTable(BillingProfile $profile, $students, string $periodLabel, Carbon $dueDate): void
    {
        $totalPerStudent = $profile->profileItems->sum('effective_amount');
        $grandTotal = $totalPerStudent * $students->count();

        $this->table(
            ['Reg No', 'Name', 'Amount (BDT)'],
            $students->take(10)->map(fn($s) => [
                $s->reg_no,
                trim("{$s->first_name} {$s->last_name}"),
                number_format($totalPerStudent, 2),
            ])->toArray()
        );

        if ($students->count() > 10) {
            $this->line("  ... and " . ($students->count() - 10) . " more students.");
        }

        $this->line("  Period: {$periodLabel} | Due: {$dueDate->toDateString()}");
        $this->line("  Total students: {$students->count()} | Per student: BDT {$totalPerStudent} | Grand total: BDT " . number_format($grandTotal, 2));
        $this->line("  Fee heads: " . $profile->profileItems->map(fn($i) => $i->fee_head_title)->implode(', '));
    }
}
