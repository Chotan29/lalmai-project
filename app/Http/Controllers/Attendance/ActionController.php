<?php
// app/Http/Controllers/Attendance/ActionController.php
namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use App\Models\Attendance;
use App\Models\AttendanceStatus;

class ActionController extends Controller
{
    public function mark(Attendance $attendance, Request $request)
    {
        $data = $request->validate(['code' => 'required|string|max:10']);
        $attendance->attendance_status_id = $this->statusId($data['code']);
        $attendance->save();
        $attendance->load('status');

        return response()->json([
            'row' => [
                'id'          => $attendance->id,
                'status_code' => optional($attendance->status)->code
            ]
        ]);
    }

    public function check(Attendance $attendance)
    {
        $now = Carbon::now();
        if (!$attendance->check_in_at) {
            $attendance->check_in_at = $now;
            if (!$attendance->attendance_status_id) {
                $attendance->attendance_status_id = $this->statusId('P');
            }
        } elseif (!$attendance->check_out_at) {
            $attendance->check_out_at = $now;
        } else {
            // already both set, overwrite checkout to now
            $attendance->check_out_at = $now;
        }
        $attendance->save();

        return response()->json([
            'row' => [
                'id'          => $attendance->id,
                'check_in_at' => optional($attendance->check_in_at)->toIso8601String(),
                'check_out_at'=> optional($attendance->check_out_at)->toIso8601String(),
                'attendance_status_id' => $attendance->attendance_status_id,
            ]
        ]);
    }

    // Teachers can adjust an individual SubjectAttendance record
    public function updateSubjectAttendance($id, Request $request)
    {
        $data = $request->validate([
            'status_code' => 'required|string|max:10'
        ]);

        $status = strtoupper($data['status_code']);

        // Your SubjectAttendance model/columns may differ; adjust as needed.
        $sa = \App\Models\SubjectAttendance::findOrFail($id);
        $sa->status_code = $status; // or attendance_status_id if you store numeric
        $sa->save();

        return response()->json(['ok'=>true]);
    }

    private function statusId(?string $code): ?int
    {
        if (!$code) return null;
        $st = AttendanceStatus::where('code', strtoupper($code))->first();
        return $st ? $st->id : null;
    }
}
