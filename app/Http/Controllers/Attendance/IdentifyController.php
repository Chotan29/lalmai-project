<?php
// app/Http/Controllers/Attendance/IdentifyController.php
namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\Attendance;
use App\Models\AttendanceMaster;
use App\Models\AttendanceStatus;
use App\Models\Student;
use App\Models\Staff;
use App\Services\SubjectAttendanceService;

class IdentifyController extends Controller
{
    public function identify(Request $request, SubjectAttendanceService $subjectSvc)
    {
        $data = $request->validate([
            'code'           => 'required|string|max:191', // reg_no (student or staff)
            'source'         => 'nullable|string|max:50',  // manual|qr|barcode|tipsoi
            // optional hierarchy for auto seeding visible list (does not restrict marking)
            'department_id'  => 'nullable|integer',
            'faculty_id'     => 'nullable|integer',
            'semester_id'    => 'nullable|integer',
            'batch_id'       => 'nullable|integer',
        ]);

        $now = Carbon::now();
        $code = trim($data['code']);

        // Determine person by reg_no-first (student), then staff fallbacks
        $person = Student::where('reg_no',$code)
                    //->orWhere('university_enrollment_no',$code)
                    //->orWhere('admission_no',$code)
                    //->orWhere('mobile_1',$code)
                    ->first();
        $type = 'student';
        $personClass = Student::class;

        if (!$person) {
            $person = Staff::where('reg_no',$code)
                        //->orWhere('reg_no',$code)
                        //->orWhere('employee_id',$code)
                        //->orWhere('mobile_1',$code)
                        ->first();
            $type = 'staff';
            $personClass = Staff::class;
        }

        if (!$person) {
            return response()->json(['message'=>'Person not found'], 404);
        }

        // silently ensure daily master for the detected type
        $master = AttendanceMaster::forToday($type);
        if ($master->is_locked) {
            return response()->json(['message' => 'Today is locked'], 423);
        }

        // upsert attendance row (unique per day per person)
        $row = Attendance::firstOrCreate(
            [
                'attendance_master_id' => $master->id,
                'attendable_type'      => $personClass,
                'attendable_id'        => $person->id,
            ],
            [
                'attendance_status_id' => $this->statusId('P'),
                'check_in_at'          => $now,
            ]
        );

        // If already checked-in, set/refresh check-out to now (optional policy)
        if ($row->check_in_at && !$row->check_out_at) {
            $row->check_out_at = null; // keep open until explicit "check" call
        }

        // make sure status not empty
        if (!$row->attendance_status_id) $row->attendance_status_id = $this->statusId('P');

        $row->save();
        $row->load('status');

        // If student: mark ALL today's class routine slots as Present
        if ($type === 'student') {
            try {
                $subjectSvc->markStudentPresentForNow($person, $now);
            } catch (\Throwable $e) {
                // don't block attendance on subject errors
            }
        }

        return response()->json([
            'ok'     => true,
            'type'   => $type,
            'row'    => [
                'id'          => $row->id,
                'status_code' => optional($row->status)->code,
                'check_in_at' => optional($row->check_in_at)->toIso8601String(),
                'check_out_at'=> optional($row->check_out_at)->toIso8601String(),
            ]
        ]);
    }

    private function statusId(?string $code): ?int
    {
        if (!$code) return null;
        $st = AttendanceStatus::where('code', strtoupper($code))->first();
        return $st ? $st->id : null;
    }
}
