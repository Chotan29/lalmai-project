<?php
// app/Services/AttendanceService.php
namespace App\Services;
use App\Models\{Attendance, AttendanceMaster, AttendanceStatus};
use Carbon\Carbon;

class AttendanceService {
    public function ensureMaster(array $payload){
        return AttendanceMaster::firstOrCreate([
            'date'=>$payload['date'],'type'=>$payload['type'],
            'department_id'=>array_key_exists('department_id',$payload)?$payload['department_id']:null,
            'faculty_id'   =>array_key_exists('faculty_id',$payload)?$payload['faculty_id']:null,
            'semester_id'  =>array_key_exists('semester_id',$payload)?$payload['semester_id']:null,
            'batch_id'     =>array_key_exists('batch_id',$payload)?$payload['batch_id']:null,
        ],[
            'start_time'=>$payload['start_time'] ?? null,
            'end_time'  =>$payload['end_time'] ?? null,
            'shift'     =>$payload['shift'] ?? config('attendance.default_shift'),
        ]);
    }
    public function seedRows(AttendanceMaster $master, $people){
        $absent = AttendanceStatus::where('code','A')->first();
        foreach($people as $p){
            Attendance::firstOrCreate([
                'attendance_master_id'=>$master->id,
                'attendable_type'=>get_class($p),
                'attendable_id'=>$p->getKey(),
            ],[
                'attendance_status_id'=>$absent ? $absent->id : null
            ]);
        }
    }
    public function mark(Attendance $row, $code, array $meta=[]){
        $status = AttendanceStatus::where('code',$code)->firstOrFail();
        $row->attendance_status_id = $status->id;
        $row->meta = array_merge($row->meta ?: [], $meta);
        $row->save();
        return $row->load('status');
    }
    public function toggleCheckInOut(Attendance $row){
        $now = Carbon::now();
        if (!$row->check_in_at){
            $row->check_in_at = $now;
            $present = AttendanceStatus::where('code', config('attendance.default_present_code','P'))->first();
            if ($present){ $row->attendance_status_id = $present->id; }
        } else {
            $row->check_out_at = $now;
        }
        $row->save();
        return $row;
    }
}
