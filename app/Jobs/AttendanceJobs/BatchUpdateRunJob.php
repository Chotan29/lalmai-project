<?php

namespace App\Jobs\AttendanceJobs;

use App\Models\IntegrationRun;
use App\Models\Student;
use App\Models\Staff;
use App\Services\InovaceApi;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Throwable;

class BatchUpdateRunJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Optional: increase timeout for large runs (seconds) */
    public $timeout = 900;

    public int $runId;

    public function __construct(int $runId)
    {
        $this->runId = $runId;

        // set default queue for this job instance
        $this->onQueue('attendance');
    }

    public function handle(InovaceApi $api): void
    {
        $run = IntegrationRun::findOrFail($this->runId);

        $run->update([
            'status'     => 'running',
            'started_at' => now(),
            'error'      => null,
        ]);

        $payload = $run->payload ?? [];

        // 👇 Accept both "who" and legacy "type"
        $whoRaw = $payload['who'] ?? $payload['type'] ?? 'student';
        $whoRaw = strtolower((string)$whoRaw);

        if ($whoRaw === 'both') {
            $types = ['student', 'staff'];
        } elseif (in_array($whoRaw, ['student','staff'], true)) {
            $types = [$whoRaw];
        } else {
            // default safe fallback
            $types = ['student'];
        }

        $deviceIds   = array_values(array_filter((array)($payload['device_identifier'] ?? [])));
        $withPhotos  = filter_var($payload['with_photos'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $useRfid     = filter_var($payload['use_rfid'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $batchId     = trim((string) ($payload['batch'] ?? ''));

        $result = [
            'counts'    => ['active' => 0, 'inactive' => 0],
            'upserted'  => 0,
            'revoked'   => 0,
            'allocated' => ['ok' => 0, 'total' => 0],
            'errors'    => [],
        ];

        $activeIdentifiers = [];

        // ---------- Total steps only for selected types ----------
        $total = 0;
        foreach ($types as $t) {
            if ($t === 'student') {
                $qb = Student::query();
                if (Schema::hasColumn('students','reg_no')) {
                    $qb->whereNotNull('reg_no')->where('reg_no','!=','');
                }
                if ($batchId !== '' && Schema::hasColumn('students','batch')) {
                    $qb->where('batch', $batchId);
                }
                $total += (int) $qb->count();
            } else { // staff
                $qb = Staff::query();
                if (Schema::hasColumn('staff','reg_no')) {
                    $qb->whereNotNull('reg_no')->where('reg_no','!=','');
                }
                $total += (int) $qb->count();
            }
        }
        $run->update(['total_steps' => $total, 'done_steps' => 0]);

        $done = 0;

        $saveProgress = function() use ($run, &$done, &$result) {
            $run->update([
                'done_steps' => $done,
                'result'     => [
                    'counts'    => $result['counts'],
                    'upserted'  => $result['upserted'],
                    'revoked'   => $result['revoked'],
                    'allocated' => $result['allocated'],
                    'errors'    => array_slice($result['errors'], -50),
                ],
            ]);
        };

        @set_time_limit(0);

        try {
            foreach ($types as $t) {
                if ($t === 'student') {
                    $select = ['id'];
                    foreach (['reg_no','first_name','middle_name','last_name','student_image','status','name'] as $c) {
                        if (Schema::hasColumn('students',$c)) $select[] = $c;
                    }

                    Student::query()
                        ->select($select)
                        ->when(Schema::hasColumn('students','reg_no'), function (Builder $q) {
                            $q->whereNotNull('reg_no')->where('reg_no','!=','');
                        })
                        ->when($batchId !== '' && Schema::hasColumn('students','batch'), function (Builder $q) use ($batchId) {
                            $q->where('batch', $batchId);
                        })
                        ->orderBy('id')
                        ->chunkById(200, function ($rows) use ($t, $withPhotos, $useRfid, $api, &$activeIdentifiers, &$result, &$done, $saveProgress) {
                            foreach ($rows as $m) {
                                $identifier = (string)($m->reg_no ?? '');
                                if ($identifier === '') { $done++; if ($done % 50 === 0) $saveProgress(); continue; }

                                try {
                                    if ($this->isActiveRow($m, 'students')) {
                                        $result['counts']['active']++;

                                        $payload = [
                                            'identifier'             => $identifier,
                                            'name'                   => $this->buildName('student', $m),
                                            'rfid'                   => $useRfid ? $identifier : null,
                                            'primary_display_text'   => mb_substr((string)($m->first_name ?? 'Welcome'), 0, 20),
                                            'secondary_display_text' => mb_substr((string)($m->last_name  ?? '-'),       0, 20),
                                        ];
                                        $img = $withPhotos ? $this->photoPath('student', $m) : null;

                                        $up = $api->upsertPersonSafe($payload, $img);
                                        if (!empty($up['ok'])) {
                                            $result['upserted']++;
                                            $activeIdentifiers[] = $identifier;
                                        } else {
                                            $result['errors'][] = ['who' => $identifier, 'err' => $up['message'] ?? 'Upsert failed'];
                                        }
                                    } else {
                                        $result['counts']['inactive']++;
                                        $rv = $api->revokePerson($identifier, null);
                                        if (empty($rv['error'])) {
                                            $result['revoked']++;
                                        } else {
                                            $result['errors'][] = ['who' => $identifier, 'err' => $rv['message'] ?? 'Revoke failed'];
                                        }
                                    }
                                } catch (Throwable $e) {
                                    $result['errors'][] = ['who' => $identifier, 'err' => $e->getMessage()];
                                    Log::warning('BatchUpdate student row error', ['reg_no' => $identifier, 'msg' => $e->getMessage()]);
                                } finally {
                                    $done++;
                                    if ($done % 50 === 0) $saveProgress();
                                }
                            }
                        });

                } else { // staff
                    $select = ['id'];
                    foreach (['reg_no','name','first_name','middle_name','last_name','staff_image','status'] as $c) {
                        if (Schema::hasColumn('staff',$c)) $select[] = $c;
                    }

                    Staff::query()
                        ->select($select)
                        ->when(Schema::hasColumn('staff','reg_no'), function (Builder $q) {
                            $q->whereNotNull('reg_no')->where('reg_no','!=','');
                        })
                        ->orderBy('id')
                        ->chunkById(200, function ($rows) use ($withPhotos, $useRfid, $api, &$activeIdentifiers, &$result, &$done, $saveProgress) {
                            foreach ($rows as $m) {
                                $identifier = (string)($m->reg_no ?? '');
                                if ($identifier === '') { $done++; if ($done % 50 === 0) $saveProgress(); continue; }

                                try {
                                    if ($this->isActiveRow($m, 'staff')) {
                                        $result['counts']['active']++;

                                        [$first,$last] = $this->firstLastFromStaff($m);
                                        $payload = [
                                            'identifier'             => $identifier,
                                            'name'                   => $this->buildName('staff', $m),
                                            'rfid'                   => $useRfid ? $identifier : null,
                                            'primary_display_text'   => mb_substr($first ?: 'Welcome', 0, 20),
                                            'secondary_display_text' => mb_substr($last  ?: '-',       0, 20),
                                        ];
                                        $img = $withPhotos ? $this->photoPath('staff', $m) : null;

                                        $up = $api->upsertPersonSafe($payload, $img);
                                        if (!empty($up['ok'])) {
                                            $result['upserted']++;
                                            $activeIdentifiers[] = $identifier;
                                        } else {
                                            $result['errors'][] = ['who' => $identifier, 'err' => $up['message'] ?? 'Upsert failed'];
                                        }
                                    } else {
                                        $result['counts']['inactive']++;
                                        $rv = $api->revokePerson($identifier, null);
                                        if (empty($rv['error'])) {
                                            $result['revoked']++;
                                        } else {
                                            $result['errors'][] = ['who' => $identifier, 'err' => $rv['message'] ?? 'Revoke failed'];
                                        }
                                    }
                                } catch (Throwable $e) {
                                    $result['errors'][] = ['who' => $identifier, 'err' => $e->getMessage()];
                                    Log::warning('BatchUpdate staff row error', ['reg_no' => $identifier, 'msg' => $e->getMessage()]);
                                } finally {
                                    $done++;
                                    if ($done % 50 === 0) $saveProgress();
                                }
                            }
                        });
                }
            }

            // allocations (only if we actually upserted and devices selected)
            if (!empty($activeIdentifiers) && !empty($deviceIds)) {
                $result['allocated']['total'] = count($activeIdentifiers) * count($deviceIds);

                foreach (array_chunk($activeIdentifiers, 200) as $chunk) {
                    try {
                        $alloc = $api->batchAllocations('allocate', $chunk, $deviceIds);

                        $ok = 0;
                        if (isset($alloc['data']['by_device']) && is_array($alloc['data']['by_device'])) {
                            foreach ($alloc['data']['by_device'] as $dv) {
                                $ok += (int)($dv['ok'] ?? 0);
                            }
                        } else {
                            $ok = (int)($alloc['ok'] ?? 0);
                        }
                        $result['allocated']['ok'] += $ok;
                    } catch (Throwable $e) {
                        $result['errors'][] = ['who' => '__allocations__', 'err' => $e->getMessage()];
                        Log::warning('BatchUpdate allocation error', ['msg' => $e->getMessage()]);
                    }
                    $saveProgress();
                }
            }

            $run->update([
                'done_steps' => $done,
                'result'     => [
                    'counts'    => $result['counts'],
                    'upserted'  => $result['upserted'],
                    'revoked'   => $result['revoked'],
                    'allocated' => $result['allocated'],
                    'errors'    => array_slice($result['errors'], -200),
                ],
                'status'     => 'finished',
                'finished_at'=> now(),
            ]);
        } catch (Throwable $e) {
            $run->update([
                'status'      => 'failed',
                'error'       => $e->getMessage(),
                'finished_at' => now(),
            ]);
            throw $e;
        }
    }

    public function failed(Throwable $e): void
    {
        IntegrationRun::where('id', $this->runId)->update([
            'status'      => 'failed',
            'error'       => $e->getMessage(),
            'finished_at' => now(),
        ]);
    }

    /* ---------- helpers ---------- */

    protected function isActiveRow($m, string $table): bool
    {
        if (!Schema::hasColumn($table, 'status')) return true;
        $s = $m->status;
        if (is_null($s)) return false;
        if (is_numeric($s)) return ((int)$s) === 1;
        $sv = strtolower(trim((string)$s));
        return in_array($sv, ['1','active','enabled','true','yes'], true);
    }

    protected function buildName(string $type, $m): string
    {
        if ($type === 'student') {
            $nm = trim(($m->first_name ?? '') . ' ' . (($m->middle_name ?? '') ? $m->middle_name.' ' : '') . ($m->last_name ?? ''));
            return $nm ?: ('Student#'.$m->id);
        }
        if (Schema::hasColumn('staff','first_name')) {
            $nm = trim(($m->first_name ?? '') . ' ' . (($m->middle_name ?? '') ? $m->middle_name.' ' : '') . ($m->last_name ?? ''));
            if ($nm !== '') return $nm;
        }
        if (Schema::hasColumn('staff','name') && !empty($m->name)) return (string)$m->name;
        return 'Staff#'.$m->id;
    }

    protected function photoPath(string $type, $m): ?string
    {
        if ($type === 'student' && Schema::hasColumn('students','student_image') && !empty($m->student_image)) {
            $p = public_path('images/studentProfile/' . $m->student_image);
            return is_file($p) ? $p : null;
        }
        if ($type === 'staff' && Schema::hasColumn('staff','staff_image') && !empty($m->staff_image)) {
            $p = public_path('images/staff/' . $m->staff_image);
            return is_file($p) ? $p : null;
        }
        return null;
    }

    protected function firstLastFromStaff($m): array
    {
        $first = null; $last = null;
        if (Schema::hasColumn('staff','first_name')) {
            $first = $m->first_name; $last = $m->last_name;
        } else {
            $nm = (string)($m->name ?? '');
            $parts = preg_split('/\s+/', trim($nm));
            $first = $parts ? $parts[0] : 'Welcome';
            $last  = ($parts && count($parts)>1) ? end($parts) : '-';
        }
        return [$first, $last];
    }
}
