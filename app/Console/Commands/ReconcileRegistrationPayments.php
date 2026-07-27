<?php

namespace App\Console\Commands;

use App\Http\Controllers\Student\RegistrationPaymentController;
use App\Models\OnlinePayment;
use App\Services\SslCommerzService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Self-healing registration payments.
 *
 * A student can pay at SSLCommerz and still end up with no registration: the browser
 * closes, the network drops, or the gateway callback never reaches us. Waiting for a
 * human to notice is what used to leave students stranded.
 *
 * This command runs every few minutes, looks at every payment that was started but
 * never finished, asks SSLCommerz what really happened, and completes the registration
 * itself when the payment is VALID. Attempts the gateway says were never paid are
 * dropped once they are old enough, so the list stays meaningful.
 */
class ReconcileRegistrationPayments extends Command
{
    protected $signature = 'registration:reconcile
                            {--minutes=5 : Only look at attempts older than this}
                            {--keep-days=30 : Delete unpaid attempts older than this}
                            {--dry-run : Report what would happen without changing anything}';

    protected $description = 'Complete registrations whose payment succeeded but whose callback was lost';

    public function handle()
    {
        $olderThan = (int) $this->option('minutes') * 60;
        $keepDays  = (int) $this->option('keep-days');
        $dryRun    = (bool) $this->option('dry-run');

        $dir = storage_path('app/pending_payments');
        if (!is_dir($dir)) {
            $this->info('Nothing pending.');
            return 0;
        }

        $files = glob($dir . DIRECTORY_SEPARATOR . '*.json');
        if (empty($files)) {
            $this->info('Nothing pending.');
            return 0;
        }

        /** @var SslCommerzService $ssl */
        $ssl = app(SslCommerzService::class);
        $paymentController = app(RegistrationPaymentController::class);

        $checked = $recovered = $failed = $unpaid = $removed = 0;
        $now = time();

        foreach ($files as $file) {
            $ref = basename($file, '.json');
            $startedAt = @filemtime($file);

            /* Give the student time to finish paying before we interfere. */
            if (($now - $startedAt) < $olderThan) {
                continue;
            }

            $payload = json_decode(@file_get_contents($file), true);
            if (!is_array($payload)) {
                continue;
            }

            /* Already completed by someone else? Then just clear the leftover. */
            $existing = OnlinePayment::where('ref_no', $ref)
                ->where('payment_gateway', 'SSLCommerz')->latest()->first();
            if ($existing && $existing->students_id) {
                if (!$dryRun) {
                    @unlink($file);
                    Cache::forget('registration_payment_data:' . $ref);
                }
                $removed++;
                continue;
            }

            $checked++;

            /* The reference we sent to the gateway is the transaction id. */
            $gateway = $ssl->queryByTransactionId($ref);

            if (!$gateway['valid']) {
                $unpaid++;

                /* Not paid and past the retention window - stop carrying it forever. */
                if (($now - $startedAt) > ($keepDays * 86400)) {
                    if (!$dryRun) {
                        @unlink($file);
                        Cache::forget('registration_payment_data:' . $ref);
                        Cache::forget('reg_recovery_verify:' . $ref);
                    }
                    $removed++;
                    $this->line("  dropped (never paid, expired): {$ref}");
                }
                continue;
            }

            /* Paid. Charge what the gateway actually took, then finish the job. */
            if ($gateway['amount'] > 0) {
                $payload['amount'] = (float) $gateway['amount'];
            }

            $name = trim(($payload['registration_data']['first_name'] ?? '') . ' '
                       . ($payload['registration_data']['last_name'] ?? '')) ?: 'Unknown';

            if ($dryRun) {
                $this->line("  WOULD RECOVER: {$ref} | {$name} | {$gateway['amount']}");
                $recovered++;
                continue;
            }

            try {
                $result = $paymentController->createStudentAndFeeRecord($payload, $ref, 'SSLCommerz');
            } catch (\Exception $e) {
                $failed++;
                Log::error('[RECONCILE] Recovery failed', ['ref' => $ref, 'error' => $e->getMessage()]);
                $this->error("  FAILED: {$ref} | {$e->getMessage()}");
                continue;
            }

            if (empty($result['success'])) {
                $failed++;
                Log::error('[RECONCILE] Recovery unsuccessful', ['ref' => $ref, 'message' => $result['message'] ?? null]);
                $this->error("  FAILED: {$ref} | " . ($result['message'] ?? 'unknown reason'));
                continue;
            }

            $recovered++;
            @unlink($file);
            Cache::forget('registration_payment_data:' . $ref);
            Cache::forget('reg_recovery_verify:' . $ref);

            Log::info('[RECONCILE] Registration recovered automatically', [
                'ref' => $ref,
                'student_reg_no' => $result['student_id'] ?? null,
                'amount' => $gateway['amount'],
            ]);
            $this->info("  recovered: {$ref} | {$name}");
        }

        $summary = sprintf(
            'checked %d, recovered %d, still unpaid %d, failed %d, cleaned %d',
            $checked, $recovered, $unpaid, $failed, $removed
        );

        $this->info('Reconcile finished: ' . $summary);

        if ($recovered > 0 || $failed > 0) {
            Log::info('[RECONCILE] ' . $summary);
        }

        return 0;
    }
}
