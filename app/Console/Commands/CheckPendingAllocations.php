<?php

//php artisan tipsoi:check-allocations
//php artisan tipsoi:check-allocations --hours=72 --retry-failed
//php artisan tipsoi:check-allocations --limit=50

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TipsoiAttendanceService;
use App\Models\{Person, TipsoiDevice, PersonDevice};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckPendingAllocations extends Command
{
    protected $signature = 'tipsoi:check-allocations 
                            {--hours=24 : Check allocations from last X hours}
                            {--retry-failed : Retry failed allocations}
                            {--limit=100 : Maximum number of records to process}';

    protected $description = 'Check status of pending TIPSOI device allocations';

    protected $tipsoiService;

    public function __construct(TipsoiAttendanceService $tipsoiService)
    {
        parent::__construct();
        $this->tipsoiService = $tipsoiService;
    }

    public function handle()
    {
        $hours = $this->option('hours');
        $retryFailed = $this->option('retry-failed');
        $limit = $this->option('limit');

        $this->info("Checking pending allocations from last {$hours} hours (limit: {$limit})...");
        if ($retryFailed) {
            $this->info("Will attempt to retry failed allocations.");
        }

        // Get pending allocations from person_device table
        $query = PersonDevice::where('allocated_at', '>', Carbon::now()->subHours($hours))
                    ->whereNull('revoked_at')
                    ->where(function($q) use ($retryFailed) {
                        $q->whereNull('synced_at');
                        if ($retryFailed) {
                            $q->orWhere('sync_failed', true);
                        }
                    })
                    ->limit($limit);

        $count = $query->count();
        $this->info("Found {$count} pending/failed allocations to check.");

        if ($count === 0) {
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $results = [
            'verified' => 0,
            'failed' => 0,
            'still_pending' => 0,
            'retried' => 0,
            'sync_updated' => 0
        ];

        $query->chunk(100, function ($allocations) use ($bar, $retryFailed, &$results) {
            foreach ($allocations as $allocation) {
                try {
                    $person = Person::find($allocation->person_id);
                    $device = TipsoiDevice::find($allocation->device_id);

                    if (!$person || !$device) {
                        $this->markAsFailed($allocation, 'Person or device not found');
                        $results['failed']++;
                        $bar->advance();
                        continue;
                    }

                    // Check allocation status with TIPSOI API
                    $status = $this->verifyAllocation($person->identifier, $device->identifier);

                    if ($status['verified']) {
                        $this->markAsSynced($allocation);
                        $results['verified']++;
                        $results['sync_updated']++;
                    } elseif ($status['failed'] && $retryFailed) {
                        $retryResult = $this->retryAllocation($person, $device);
                        if ($retryResult) {
                            $results['retried']++;
                        } else {
                            $results['failed']++;
                        }
                    } else {
                        $results['still_pending']++;
                    }
                } catch (\Exception $e) {
                    $this->error("Error checking allocation ID {$allocation->id}: " . $e->getMessage());
                    $results['failed']++;
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        // Display results
        $this->table(
            ['Status', 'Count'],
            [
                ['Verified Allocations', $results['verified']],
                ['Still Pending', $results['still_pending']],
                ['Failed', $results['failed']],
                ['Retried', $results['retried']],
                ['Local DB Updated', $results['sync_updated']]
            ]
        );

        return 0;
    }
    protected function verifyAllocation(string $personIdentifier, string $deviceIdentifier): array
    {
        try {
            // Option 1: Check via TIPSOI API if available
            if (method_exists($this->tipsoiService, 'verifyAllocation')) {
                return $this->tipsoiService->verifyAllocation($personIdentifier, $deviceIdentifier);
            }

            // Option 2: Check via attendance logs as fallback
            $recentLog = DB::table('tipsoi_attendance_logs')
                ->where('person_identifier', $personIdentifier)
                ->where('device_identifier', $deviceIdentifier)
                ->where('logged_time', '>', now()->subDay())
                ->orderBy('logged_time', 'desc')
                ->first();

            return [
                'verified' => $recentLog !== null,
                'failed' => false,
                'message' => $recentLog ? 'Found recent attendance log' : 'No recent activity found'
            ];
        } catch (\Exception $e) {
            return [
                'verified' => false,
                'failed' => true,
                'message' => 'Verification failed: ' . $e->getMessage()
            ];
        }
    }

    protected function markAsSynced(PersonDevice $allocation): void
    {
        DB::transaction(function () use ($allocation) {
            $allocation->update([
                'synced_at' => now(),
                'sync_failed' => false,
                'last_sync_attempt' => now()
            ]);

            // Update person's last sync time
            Person::where('id', $allocation->person_id)
                ->update([
                    'last_sync_at' => now(),
                    'sync_failed' => false,
                    'sync_notes' => 'Device allocation verified'
                ]);
        });
    }

    protected function markAsFailed(PersonDevice $allocation, string $reason): void
    {
        DB::transaction(function () use ($allocation, $reason) {
            $allocation->update([
                'sync_failed' => true,
                'last_sync_attempt' => now(),
                'sync_notes' => $reason
            ]);

            Person::where('id', $allocation->person_id)
                ->update([
                    'sync_failed' => true,
                    'sync_notes' => $reason
                ]);
        });
    }

    protected function retryAllocation(Person $person, TipsoiDevice $device): bool
    {
        try {
            $this->info("Retrying allocation for {$person->identifier} to {$device->identifier}");

            $response = $this->tipsoiService->allocatePersonToDevice(
                $person->identifier,
                $device->identifier
            );

            if ($response['success'] ?? false) {
                PersonDevice::where('person_id', $person->id)
                    ->where('device_id', $device->id)
                    ->update([
                        'sync_failed' => false,
                        'last_sync_attempt' => now(),
                        'sync_notes' => 'Retry attempt successful'
                    ]);

                return true;
            }

            $error = $response['message'] ?? 'Unknown error';
            $this->markAsFailed(
                PersonDevice::where('person_id', $person->id)
                    ->where('device_id', $device->id)
                    ->first(),
                'Retry failed: ' . $error
            );

            return false;
        } catch (\Exception $e) {
            \Log::error("Failed to retry allocation for {$person->id} to {$device->id}: " . $e->getMessage());
            return false;
        }
    }
}