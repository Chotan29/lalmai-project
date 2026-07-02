<?php

namespace App\Jobs\AttendanceJobs;

use App\Models\IntegrationRun;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\Student;
use App\Models\Staff;
use App\Services\InovaceApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class SyncLogsPageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $runId; public string $start; public string $end; public int $page; public int $perPage;

    public function __construct(int $runId, string $start, string $end, int $page, int $perPage)
    {
        $this->runId=$runId; $this->start=$start; $this->end=$end; $this->page=$page; $this->perPage=$perPage;
        $this->onQueue('long');
    }

    public function handle(InovaceApi $api): void
    {
        $run = IntegrationRun::find($this->runId); if(!$run) return;

        $resp = $api->logs($this->start, $this->end, $this->page, $this->perPage);
        $rows = is_array($resp) && isset($resp['data']) ? $resp['data'] : [];

        foreach ($rows as $log) $this->applyLogRow($log);

        $res = $run->result ?? ['synced'=>0];
        $res['synced'] = (int)($res['synced'] ?? 0) + count($rows);

        $run->update(['result'=>$res,'done_steps'=>$run->done_steps+1]);
    }

    protected function ensureStatusId($code = 'P'): int
    {
        $id = AttendanceStatus::where('code', strtoupper($code))->value('id');
        if ($id) return (int)$id;
        $row = AttendanceStatus::firstOrCreate(['code'=>'P'], ['label'=>'Present','order'=>1,'color'=>'#10b981']);
        return (int)$row->id;
    }

    protected function applyLogRow(array $log): void
    {
        $loggedTime = $log['logged_time'] ?? null;
        $identifier = $log['person_identifier'] ?? null;
        if (!$loggedTime || !$identifier) return;

        $model=null; $type=null;
        if (Schema::hasColumn('students','reg_no')) { $s=Student::where('reg_no',$identifier)->first(); if($s){$model=$s;$type='student';} }
        if (!$model && Schema::hasColumn('staff','reg_no')) { $s=Staff::where('reg_no',$identifier)->first(); if($s){$model=$s;$type='staff';} }
        if (!$model) return;

        $date = Carbon::parse($loggedTime)->startOfDay()->toDateString();
        $attType = $type==='student' ? Student::class : Staff::class;
        $row = Attendance::whereDate('date',$date)->where('attendable_type',$attType)->where('attendable_id',$model->id)->first();

        $statusId = $this->ensureStatusId('P');
        $lt = Carbon::parse($loggedTime);

        if (!$row) {
            Attendance::create([
                'date' => $date,
                'attendable_type' => $attType,
                'attendable_id' => $model->id,
                'reg_no' => $identifier,
                'attendance_status_id' => $statusId,
                'check_in_at' => $lt,
                'check_out_at'=> $lt,
                'source' => 'device',
            ]);
            return;
        }
        if (!$row->check_in_at || $lt->lt($row->check_in_at))  $row->check_in_at  = $lt;
        if (!$row->check_out_at || $lt->gt($row->check_out_at))$row->check_out_at = $lt;
        if (!$row->attendance_status_id) $row->attendance_status_id = $statusId;
        if (!$row->source) $row->source = 'device';
        $row->save();
    }
}
