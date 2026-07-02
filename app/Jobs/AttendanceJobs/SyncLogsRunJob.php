<?php

namespace App\Jobs\AttendanceJobs;

use App\Models\IntegrationRun;
use App\Models\IntegrationCursor;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\BiometricPerson; // used as a fallback matcher if available
use App\Services\InovaceApi;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class SyncLogsRunJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** generous timeout for large windows */
    public $timeout = 900;

    public int $runId;

    public function __construct(int $runId)
    {
        $this->runId = $runId;
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

        $payload   = $run->payload ?? [];
        $perPage   = (int) config('inovace.per_page', 500);

        // cursor support
        $cursorKey = (string) config('inovace.cursor_key_logs', 'tipsoi_logs_cursor');
        $cursor    = IntegrationCursor::firstOrCreate(['key' => $cursorKey], ['value' => null]);

        // inputs (dates may be given as Y-m-d or Y-m-d H:i:s)
        $startIn = trim((string) ($payload['start'] ?? ''));
        $endIn   = trim((string) ($payload['end']   ?? ''));

        // defaults: from cursor (or yesterday) → now
        if ($startIn === '') {
            $startIn = $cursor->value ?: Carbon::yesterday()->startOfDay()->toDateTimeString();
        }
        if ($endIn === '') {
            $endIn = Carbon::now()->toDateTimeString();
        }

        // normalize to full window
        $start = Carbon::parse($startIn)->startOfDay()->toDateTimeString();
        $end   = Carbon::parse($endIn)->endOfDay()->toDateTimeString();

        $pagesProcessed = 0;
        $totalSteps     = 0;  // we’ll set this to pagesProcessed
        $maxSync        = null;

        $stats = [
            'fetched'   => 0,
            'matched'   => 0,
            'created'   => 0,
            'updated'   => 0,
            'skipped'   => 0,
            'unmatched_examples' => [],
        ];

        $errors = [];

        $page = 1;
        @set_time_limit(0);

        try {
            while (true) {
                $resp = $api->logs($start, $end, $page, $perPage, 'logged_time', 'logged_time', 'asc');

                // rows parsing (support several shapes)
                $rows = $this->extractRows($resp);
                $countRows = count($rows);
                $stats['fetched'] += $countRows;

                foreach ($rows as $idx => $log) {
                    try {
                        $ok = $this->applyLogRowSafe($log, $maxSync, $stats);
                        if ($ok === false && count($stats['unmatched_examples']) < 5) {
                            $stats['unmatched_examples'][] = $log;
                        }
                    } catch (Throwable $rowEx) {
                        // Keep processing next rows
                        $stats['skipped']++;
                        $errors[] = [
                            'who' => isset($log['person_identifier']) ? (string)$log['person_identifier'] : '__unknown__',
                            'err' => $rowEx->getMessage(),
                        ];
                        // log with context
                        Log::warning('SyncLogs row error: '.$rowEx->getMessage(), [
                            'page' => $page, 'row_index' => $idx, 'pid' => $log['person_identifier'] ?? null
                        ]);
                    }
                }

                $pagesProcessed++;
                $totalSteps = $pagesProcessed;

                $run->update([
                    'total_steps' => $totalSteps,
                    'done_steps'  => $pagesProcessed,
                    'result'      => [
                        'synced' => $stats['created'] + $stats['updated'],
                        'pages'  => $pagesProcessed,
                        'errors' => array_slice($errors, -50),
                        'stats'  => $stats,
                        'debug'  => [
                            'cursor_used'  => $cursor->value,
                            'start_final'  => $start,
                            'end_final'    => $end,
                            'rows_count_p' => $countRows,
                        ],
                    ],
                ]);

                // stop when the page is not full
                if ($countRows < $perPage) {
                    break;
                }
                $page++;
            }

            if ($maxSync) {
                $cursor->value = $maxSync;
                $cursor->save();
            }

            $run->update([
                'status'      => 'finished',
                'finished_at' => now(),
                'result'      => [
                    'synced' => $stats['created'] + $stats['updated'],
                    'pages'  => $pagesProcessed,
                    'errors' => array_slice($errors, -200),
                    'stats'  => $stats,
                ],
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

    /* -------------------- helpers -------------------- */

    protected function extractRows($resp): array
    {
        if (!is_array($resp)) return [];
        if (isset($resp['data']) && is_array($resp['data'])) {
            if (isset($resp['data']['data']) && is_array($resp['data']['data'])) {
                return $resp['data']['data'];
            }
            return $resp['data'];
        }
        foreach (['logs','items','records','attendance_logs'] as $k) {
            if (isset($resp[$k]) && is_array($resp[$k])) return $resp[$k];
        }
        $isList = !array_diff(array_keys($resp), range(0, count($resp) - 1));
        return $isList ? $resp : [];
    }

    /**
     * Safe wrapper: ensure a single row cannot fail the whole run.
     */
    protected function applyLogRowSafe(array $log, ?string &$maxSync, array &$stats): bool
    {
        try {
            return $this->applyLogRow($log, $maxSync, $stats);
        } catch (Throwable $e) {
            // bubble up to outer foreach where we add to $errors and keep going
            throw $e;
        }
    }

    /**
     * Merge a single log row into attendances table.
     * Returns true if merged, false if skipped/unmatched.
     */
    protected function applyLogRow(array $log, ?string &$maxSync, array &$stats): bool
    {
        $syncTime   = $log['sync_time']   ?? null;
        $loggedTime = $log['logged_time'] ?? null;
        $identifier = $log['person_identifier'] ?? null;

        if ($syncTime && (!$maxSync || $syncTime > $maxSync)) $maxSync = $syncTime;
        if (!$loggedTime || !$identifier) { $stats['skipped']++; return false; }

        // resolve person (student/staff) by reg_no OR mapping table fallback
        [$model, $type] = $this->resolveLocalPerson($identifier);
        if (!$model) { $stats['skipped']++; return false; }
        $stats['matched']++;

        $date    = Carbon::parse($loggedTime)->startOfDay()->toDateString();
        $attType = $type === 'student' ? Student::class : Staff::class;
        $lt      = Carbon::parse($loggedTime);

        $statusId = $this->ensureStatusId('P');

        // Avoid observers/appends that might dereference null relations (->code)
        $created = false;
        Model::withoutEvents(function () use ($date, $attType, $model, $lt, $statusId, $type, &$created) {
            $row = Attendance::whereDate('date', $date)
                ->where('attendable_type', $attType)
                ->where('attendable_id', $model->id)
                ->first();

            if (!$row) {
                $reg = $this->personIdentifierFor($type, $model);
                Attendance::create([
                    'date'                 => $date,
                    'attendable_type'      => $attType,
                    'attendable_id'        => $model->id,
                    'reg_no'               => $reg,
                    'attendance_status_id' => $statusId,
                    'check_in_at'          => $lt,
                    'check_out_at'         => $lt,
                    'source'               => 'device',
                ]);
                $created = true;
                return;
            }

            $changed = false;
            if (!$row->check_in_at || $lt->lt($row->check_in_at))   { $row->check_in_at  = $lt; $changed = true; }
            if (!$row->check_out_at || $lt->gt($row->check_out_at)) { $row->check_out_at = $lt; $changed = true; }
            if (!$row->attendance_status_id)                        { $row->attendance_status_id = $statusId; $changed = true; }
            if (!$row->source)                                      { $row->source = 'device';   $changed = true; }

            if ($changed) $row->save();
            $created = false;
        });

        if ($created) { $stats['created']++; } else { $stats['updated']++; }

        // Subject-level attendance (student only) — fully sandboxed
        if ($type === 'student') {
            try { $this->maybeWriteSubjectAttendance($model, $lt, $date); }
            catch (Throwable $e) { Log::warning('SubjectAttendance write skipped: '.$e->getMessage()); }
        }

        return true;
    }

    protected function ensureStatusId($code = 'P'): int
    {
        $code = strtoupper($code);
        $id = AttendanceStatus::where('code', $code)->value('id');
        if ($id) return (int)$id;

        // Create defensively
        $row = AttendanceStatus::firstOrCreate(
            ['code' => $code],
            ['label' => $code === 'P' ? 'Present' : $code, 'order' => 1, 'color' => '#10b981']
        );
        return (int) $row->id;
    }

    protected function personIdentifierFor(string $type, $model): string
    {
        if ($type === 'student') {
            if (Schema::hasColumn('students','reg_no') && !empty($model->reg_no)) return (string) $model->reg_no;
            return 'STU-'.$model->id;
        }
        if (Schema::hasColumn('staff','reg_no') && !empty($model->reg_no)) return (string) $model->reg_no;
        return 'STF-'.$model->id;
    }

    /**
     * Prefer direct reg_no match; if not found, use BiometricPerson map if present.
     * @return array{0:?object,1:?string} [$model, $type]
     */
    protected function resolveLocalPerson(string $identifier): array
    {
        // fast path: reg_no columns
        if (Schema::hasColumn('students','reg_no')) {
            $s = Student::where('reg_no', $identifier)->first();
            if ($s) return [$s, 'student'];
        }
        if (Schema::hasColumn('staff','reg_no')) {
            $t = Staff::where('reg_no', $identifier)->first();
            if ($t) return [$t, 'staff'];
        }

        // fallback: mapping table (if available)
        if (class_exists(BiometricPerson::class)) {
            $map = BiometricPerson::where('person_identifier', $identifier)->first();
            if ($map) {
                if ($map->attendable_type === Student::class) {
                    $s = Student::find($map->attendable_id);
                    if ($s) return [$s, 'student'];
                }
                if ($map->attendable_type === Staff::class) {
                    $t = Staff::find($map->attendable_id);
                    if ($t) return [$t, 'staff'];
                }
            }
        }

        return [null, null];
    }

    /**
     * No-op if your model doesn’t exist; replace with your real period/subject logic.
     */
    protected function maybeWriteSubjectAttendance($student, Carbon $logTime, string $date): void
    {
        if (!class_exists('\App\Models\SubjectAttendance')) return;

        try {
            $SubjectAttendance = '\App\Models\SubjectAttendance';
            $SubjectAttendance::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'date'       => $date,
                ],
                [
                    'status'     => 'P',
                    'source'     => 'device',
                ]
            );
        } catch (Throwable $e) {
            // Swallow any errors here so the job never fails for subject writes
            Log::warning('SubjectAttendance write skipped: ' . $e->getMessage());
        }
    }
}
