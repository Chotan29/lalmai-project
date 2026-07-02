<?php

namespace App\Jobs\AttendanceJobs;

use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\Student;
use App\Models\GuardianDetail;
use App\Models\ParentDetail;
use App\Models\AlertSetting;
use App\Jobs\AllEmail;
use App\Traits\SmsEmailScope;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SendAttendanceNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SmsEmailScope;

    public $afterCommit = true;   // harmless on older Laravel
    public $timeout = 120;
    public $tries   = 3;

    /** @var int */
    public $attendanceId;

    public function __construct(int $attendanceId)
    {
        $this->attendanceId = $attendanceId;
    }

    public function handle(): void
    {
        try {
            $att = Attendance::with(['attendable','status'])->find($this->attendanceId);
            if (!$att) { $this->markAs('failed', ['error'=>'attendance_not_found']); return; }

            // students only
            if (!in_array($att->attendable_type, [Student::class, 'student'], true)) {
                $this->markAs('sent', ['reason'=>'staff_skipped']); return;
            }
            if (!$att->attendance_status_id) { $this->markAs('failed', ['error'=>'no_status_id']); return; }

            /** @var \App\Models\Student|null $student */
            $student = $att->attendable instanceof Student ? $att->attendable : null;
            if (!$student) { $this->markAs('failed', ['error'=>'student_not_loaded_or_deleted']); return; }

            // Use direct lookup to avoid shadowing by the 'status' tinyint column on attendances table
            $statusModel = $att->attendance_status_id ? AttendanceStatus::find($att->attendance_status_id) : null;
            $statusLabel = $statusModel ? ($statusModel->label ?: $statusModel->code) : null;
            if (!$statusLabel) { $statusLabel = 'Marked'; }

            // idempotency: if we already marked sent for the same status, do nothing
            $meta = is_array($att->meta) ? $att->meta : [];
            $last = isset($meta['notify']['last_status']) ? $meta['notify']['last_status'] : null;
            if ((string)$att->notification_status === 'sent' && $last === $statusLabel) {
                return;
            }

            // Alert setting (StudentAttendance). Defaults if missing.
            $alert = AlertSetting::select('sms','email','subject','template')
                ->where('event','StudentAttendance')->first();

            $subject   = $alert ? ($alert->subject ?: 'Student Attendance Information') : 'Student Attendance Information';
            $template  = $alert ? ($alert->template ?: 'Dear Guardian, This is to inform you that {{first_name}} is {{status}} on {{date}}.')
                                : 'Dear Guardian, This is to inform you that {{first_name}} is {{status}} on {{date}}.';
            $sendSms   = $alert ? ((int)$alert->sms === 1)   : true;   // default send SMS if no row
            $sendEmail = $alert ? ((int)$alert->email === 1) : false;  // default email off if no row

            $date = $att->date ? $att->date->format('Y-m-d') : '';
            $in   = $att->check_in_at  ? $att->check_in_at->format('H:i')  : '';
            $out  = $att->check_out_at ? $att->check_out_at->format('H:i') : '';

            list($firstName, $fullName) = $this->studentNames($student);
            $timeParts = array_filter([$in ? "In: {$in}" : null, $out ? "Out: {$out}" : null]);
            $timeBlock = $timeParts ? ' ('.implode(', ', $timeParts).')' : '';

            $fill = function (string $s) use ($firstName,$fullName,$statusLabel,$date,$in,$out,$timeBlock) {
                return strtr($s, [
                    '{{first_name}}'   => $firstName,
                    '{{student_name}}' => $fullName,
                    '{{status}}'       => $statusLabel,
                    '{{date}}'         => $date,
                    '{{in_time}}'      => $in,
                    '{{out_time}}'     => $out,
                    '{{time_block}}'   => $timeBlock,
                    '{{app_name}}'     => config('app.name','College'),
                ]);
            };

            $finalSubject = $fill($subject);
            $finalBody    = $fill($template);

            list($numbers, $emails) = $this->guardianContacts($student->id);

            $sentAny = false;
            $lastErr = null;

            // SMS
            if ($sendSms && !empty($numbers)) {
                try {
                    $numbers = $this->contactFilter($numbers);
                    if (!empty($numbers)) {
                        $smsRes = $this->sendSMS($numbers, $finalBody);
                        $sentAny = ($smsRes === true) ?: $sentAny;
                        if ($smsRes !== true) $lastErr = is_string($smsRes) ? $smsRes : 'sms_send_failed';
                    }
                } catch (\Throwable $e) {
                    Log::warning('Attendance SMS error', ['id'=>$att->id, 'error'=>$e->getMessage()]);
                    $lastErr = 'sms_exception: '.$e->getMessage();
                }
            }

            // Email
            if ($sendEmail && !empty($emails)) {
                try {
                    $emails = $this->emailFilter($emails);
                    $emails = is_string($emails) ? explode(',', $emails) : (array)$emails;
                    $emails = array_values(array_filter(array_map('trim', $emails)));
                    if (!empty($emails)) {
                        dispatch(new AllEmail($emails, $finalSubject, $finalBody))->delay(now()->addSeconds(5));
                        $sentAny = true;
                    }
                } catch (\Throwable $e) {
                    Log::warning('Attendance Email error', ['id'=>$att->id, 'error'=>$e->getMessage()]);
                    $lastErr = 'email_exception: '.$e->getMessage();
                }
            }

            if ($sentAny) {
                $this->markAs('sent', [
                    'last_status' => $statusLabel,
                    'numbers'     => $numbers,
                    'emails'      => $emails,
                    'subject'     => $finalSubject,
                ]);
            } else {
                // Don’t crash the worker — just mark failed & log why
                $this->markAs('failed', [
                    'last_status' => $statusLabel,
                    'error'       => $lastErr ?: 'no_recipients_or_notification_disabled',
                ]);
                Log::info('Attendance notification not sent', [
                    'attendance_id' => $att->id,
                    'reason' => $lastErr ?: 'no_recipients_or_notification_disabled'
                ]);
                return; // swallow
            }
        } catch (\Throwable $e) {
            // Log, mark failed, DO NOT rethrow (prevents worker "Failed" line)
            Log::error('SendAttendanceNotification crashed', [
                'attendance_id' => $this->attendanceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->markAs('failed', [
                'exception' => get_class($e),
                'error'     => $e->getMessage(),
            ]);
            return; // swallow
        }
    }

    private function studentNames(Student $st): array
    {
        $fn = Schema::hasColumn('students','first_name')  ? trim((string)($st->first_name ?? ''))  : '';
        $mn = Schema::hasColumn('students','middle_name') ? trim((string)($st->middle_name ?? '')) : '';
        $ln = Schema::hasColumn('students','last_name')   ? trim((string)($st->last_name ?? ''))   : '';
        $full  = trim($fn.' '.($mn ? $mn.' ' : '').$ln);
        $first = $fn !== '' ? $fn : ($full !== '' ? $full : ('Student #'.$st->id));
        if ($full === '') $full = $first;
        return [$first, $full];
    }

    private function guardianContacts(int $studentId): array
    {
        $guardianIds = DB::table('student_guardians')
            ->where('students_id', $studentId)
            ->pluck('guardians_id')
            ->all();

        $phones = [];
        $emails = [];

        if (!empty($guardianIds)) {
            $guards = GuardianDetail::query()
                ->whereIn('id', $guardianIds)
                ->get(['guardian_mobile_1','guardian_mobile_2','guardian_email']);

            foreach ($guards as $g) {
                $m1 = isset($g->guardian_mobile_1) ? trim((string)$g->guardian_mobile_1) : '';
                $m2 = isset($g->guardian_mobile_2) ? trim((string)$g->guardian_mobile_2) : '';
                $em = isset($g->guardian_email)    ? trim((string)$g->guardian_email)    : '';
                if ($m1 !== '') $phones[] = $m1;
                if ($m2 !== '') $phones[] = $m2;
                if ($em !== '') $emails[] = $em;
            }
        }

        $parent = ParentDetail::query()
            ->where('students_id', $studentId)
            ->first(['father_mobile_1', 'father_mobile_2', 'father_email']);

        if ($parent) {
            $f1 = isset($parent->father_mobile_1) ? trim((string)$parent->father_mobile_1) : '';
            $f2 = isset($parent->father_mobile_2) ? trim((string)$parent->father_mobile_2) : '';
            $fe = isset($parent->father_email)    ? trim((string)$parent->father_email)    : '';
            if ($f1 !== '') $phones[] = $f1;
            if ($f2 !== '') $phones[] = $f2;
            if ($fe !== '') $emails[] = $fe;
        }

        $phones = array_values(array_unique(array_filter($phones)));
        $emails = array_values(array_unique(array_filter($emails)));

        return [$phones, $emails];
    }

    /** update status/meta directly (no events) */
    private function markAs(string $status, array $extraMeta = []): void
    {
        $row = Attendance::find($this->attendanceId);
        if (!$row) return;

        $meta = is_array($row->meta) ? $row->meta : [];
        $notify = isset($meta['notify']) && is_array($meta['notify']) ? $meta['notify'] : [];
        $notify = array_merge($notify, $extraMeta, [
            'attendance_id' => $this->attendanceId,
            'updated_at'    => now()->toDateTimeString(),
        ]);
        if (!isset($notify['queued_at'])) {
            $notify['queued_at'] = now()->toDateTimeString();
        }
        $meta['notify'] = $notify;

        DB::table('attendances')->where('id', $this->attendanceId)->update([
            'notification_status' => $status,
            'meta'                => json_encode($meta),
            'updated_at'          => now(),
        ]);
    }
}
