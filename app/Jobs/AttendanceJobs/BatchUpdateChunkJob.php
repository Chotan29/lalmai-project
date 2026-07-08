<?php

namespace App\Jobs\AttendanceJobs;

use App\Models\IntegrationRun;
use App\Models\Student;
use App\Models\Staff;
use App\Services\InovaceApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * One small chunk (~60 people) of a batch device update.
 * Replaces the old single monster BatchUpdateRunJob which died on
 * shared-hosting worker timeouts (retry_after 90s / --timeout 120s).
 */
class BatchUpdateChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** each chunk is small; keep well under worker limits */
    public $timeout = 240;
    public $tries   = 2;

    public int    $runId;
    public string $type;        // student|staff
    public array  $ids;
    public int    $chunkNo;
    public int    $totalChunks;
    public array  $opts;        // devices[], with_photos, use_rfid

    public function __construct(int $runId, string $type, array $ids, int $chunkNo, int $totalChunks, array $opts)
    {
        $this->runId       = $runId;
        $this->type        = $type;
        $this->ids         = $ids;
        $this->chunkNo     = $chunkNo;
        $this->totalChunks = $totalChunks;
        $this->opts        = $opts;
        $this->onQueue('attendance');
    }

    public function handle(InovaceApi $api): void
    {
        $run = IntegrationRun::find($this->runId);
        if (!$run) return;
        if ($run->status === 'queued') {
            $run->update(['status' => 'running', 'started_at' => now(), 'error' => null]);
        }

        $withPhotos = (bool) ($this->opts['with_photos'] ?? false);
        $useRfid    = (bool) ($this->opts['use_rfid'] ?? false);
        $devices    = array_values(array_filter((array) ($this->opts['devices'] ?? [])));

        $chunk = [
            'active' => 0, 'inactive' => 0, 'upserted' => 0, 'revoked' => 0,
            'alloc_ok' => 0, 'alloc_total' => 0, 'errors' => [],
        ];
        $activeIdentifiers = [];
        $processed = 0;

        $table = $this->type === 'student' ? 'students' : 'staff';
        $models = ($this->type === 'student' ? Student::query() : Staff::query())
            ->whereIn('id', $this->ids)->get();

        foreach ($models as $m) {
            $identifier = (string) ($m->reg_no ?? '');
            $processed++;
            if ($identifier === '') continue;

            try {
                if ($this->isActiveRow($m, $table)) {
                    $chunk['active']++;
                    if ($this->type === 'student') {
                        $pdt = mb_substr((string) ($m->first_name ?? 'Welcome'), 0, 20);
                        $sdt = mb_substr((string) ($m->last_name ?? '-'), 0, 20);
                    } else {
                        [$first, $last] = $this->firstLastFromStaff($m);
                        $pdt = mb_substr($first ?: 'Welcome', 0, 20);
                        $sdt = mb_substr($last ?: '-', 0, 20);
                    }

                    $payload = [
                        'identifier'             => $identifier,
                        'name'                   => $this->buildName($this->type, $m),
                        'rfid'                   => $useRfid ? $identifier : null,
                        'primary_display_text'   => $pdt,
                        'secondary_display_text' => $sdt,
                    ];
                    $img = $withPhotos ? $this->photoPath($this->type, $m) : null;

                    $up = $api->upsertPersonSafe($payload, $img);
                    if (!empty($up['ok'])) {
                        $chunk['upserted']++;
                        $activeIdentifiers[] = $identifier;
                    } else {
                        $chunk['errors'][] = ['who' => $identifier, 'err' => $up['message'] ?? 'Upsert failed'];
                    }
                } else {
                    $chunk['inactive']++;
                    $rv = $api->revokePerson($identifier, null);
                    if (empty($rv['error'])) {
                        $chunk['revoked']++;
                    } else {
                        $chunk['errors'][] = ['who' => $identifier, 'err' => $rv['message'] ?? 'Revoke failed'];
                    }
                }
            } catch (Throwable $e) {
                $chunk['errors'][] = ['who' => $identifier, 'err' => $e->getMessage()];
                Log::warning('BatchUpdateChunk row error', ['reg_no' => $identifier, 'msg' => $e->getMessage()]);
            }
        }

        /* allocate this chunk's active people to selected devices */
        if (count($activeIdentifiers) && count($devices)) {
            $chunk['alloc_total'] = count($activeIdentifiers) * count($devices);
            try {
                $alloc = $api->batchAllocations('allocate', $activeIdentifiers, $devices);
                $ok = 0;
                if (isset($alloc['data']['by_device']) && is_array($alloc['data']['by_device'])) {
                    foreach ($alloc['data']['by_device'] as $dv) $ok += (int) ($dv['ok'] ?? 0);
                } elseif (isset($alloc['ok'])) {
                    $ok = (int) $alloc['ok'];
                }
                $chunk['alloc_ok'] = $ok;
            } catch (Throwable $e) {
                $chunk['errors'][] = ['who' => '__allocations__', 'err' => $e->getMessage()];
                Log::warning('BatchUpdateChunk allocation error', ['msg' => $e->getMessage()]);
            }
        }

        $this->mergeIntoRun($chunk, $processed);
    }

    /** merge chunk stats into the run row atomically; finish run on last chunk */
    protected function mergeIntoRun(array $chunk, int $processed): void
    {
        DB::transaction(function () use ($chunk, $processed) {
            $run = IntegrationRun::lockForUpdate()->find($this->runId);
            if (!$run) return;

            $result = $run->result ?? [];
            $result['counts']['active']   = (int) ($result['counts']['active'] ?? 0) + $chunk['active'];
            $result['counts']['inactive'] = (int) ($result['counts']['inactive'] ?? 0) + $chunk['inactive'];
            $result['upserted']  = (int) (is_array($result['upserted'] ?? null) ? count($result['upserted']) : ($result['upserted'] ?? 0)) + $chunk['upserted'];
            $result['revoked']   = (int) ($result['revoked'] ?? 0) + $chunk['revoked'];
            $result['allocated']['ok']    = (int) ($result['allocated']['ok'] ?? 0) + $chunk['alloc_ok'];
            $result['allocated']['total'] = (int) ($result['allocated']['total'] ?? 0) + $chunk['alloc_total'];
            $result['errors'] = array_slice(array_merge((array) ($result['errors'] ?? []), $chunk['errors']), -200);
            $result['chunks_done'] = (int) ($result['chunks_done'] ?? 0) + 1;
            $result['chunks_total'] = $this->totalChunks;

            $update = [
                'done_steps' => min($run->total_steps, $run->done_steps + $processed),
                'result'     => $result,
            ];
            if ($result['chunks_done'] >= $this->totalChunks) {
                $update['status'] = 'finished';
                $update['finished_at'] = now();
                $update['done_steps'] = $run->total_steps;
            }
            $run->update($update);
        });
    }

    /** even if a chunk fails permanently, count it so the run can finish */
    public function failed(Throwable $e): void
    {
        try {
            $this->mergeIntoRun([
                'active' => 0, 'inactive' => 0, 'upserted' => 0, 'revoked' => 0,
                'alloc_ok' => 0, 'alloc_total' => 0,
                'errors' => [['who' => '__chunk_' . $this->chunkNo . '__', 'err' => $e->getMessage()]],
            ], count($this->ids));
        } catch (Throwable $ignored) {
            IntegrationRun::where('id', $this->runId)->update([
                'status' => 'failed', 'error' => $e->getMessage(), 'finished_at' => now(),
            ]);
        }
    }

    /* ---------- helpers (same rules as legacy BatchUpdateRunJob) ---------- */

    protected function isActiveRow($m, string $table): bool
    {
        if (!Schema::hasColumn($table, 'status')) return true;
        $s = $m->status;
        if (is_null($s)) return false;
        if (is_numeric($s)) return ((int) $s) === 1;
        $sv = strtolower(trim((string) $s));
        return in_array($sv, ['1', 'active', 'enabled', 'true', 'yes'], true);
    }

    protected function buildName(string $type, $m): string
    {
        if ($type === 'student') {
            $nm = trim(($m->first_name ?? '') . ' ' . (($m->middle_name ?? '') ? $m->middle_name . ' ' : '') . ($m->last_name ?? ''));
            return $nm ?: ('Student#' . $m->id);
        }
        if (Schema::hasColumn('staff', 'first_name')) {
            $nm = trim(($m->first_name ?? '') . ' ' . (($m->middle_name ?? '') ? $m->middle_name . ' ' : '') . ($m->last_name ?? ''));
            if ($nm !== '') return $nm;
        }
        if (Schema::hasColumn('staff', 'name') && !empty($m->name)) return (string) $m->name;
        return 'Staff#' . $m->id;
    }

    protected function photoPath(string $type, $m): ?string
    {
        if ($type === 'student' && Schema::hasColumn('students', 'student_image') && !empty($m->student_image)) {
            $p = public_path('images/studentProfile/' . $m->student_image);
            return is_file($p) ? $p : null;
        }
        if ($type === 'staff' && Schema::hasColumn('staff', 'staff_image') && !empty($m->staff_image)) {
            $p = public_path('images/staff/' . $m->staff_image);
            return is_file($p) ? $p : null;
        }
        return null;
    }

    protected function firstLastFromStaff($m): array
    {
        $first = null;
        $last = null;
        if (Schema::hasColumn('staff', 'first_name')) {
            $first = $m->first_name;
            $last = $m->last_name;
        } else {
            $nm = (string) ($m->name ?? '');
            $parts = preg_split('/\s+/', trim($nm));
            $first = $parts ? $parts[0] : 'Welcome';
            $last = ($parts && count($parts) > 1) ? end($parts) : '-';
        }
        return [$first, $last];
    }
}
