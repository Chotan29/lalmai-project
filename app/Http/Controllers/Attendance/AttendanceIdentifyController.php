<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\AttendanceMaster;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\Student;
use App\Models\Staff;

class AttendanceIdentifyController extends Controller
{
    // GET /attendance/scanner/{attendanceMaster}
    public function scanner(AttendanceMaster $attendanceMaster)
    {
        return view('attendance.scanner', compact('attendanceMaster'));
    }

    // POST /attendance/identify/check
    public function identifyCheck(Request $request)
    {
        $data = $request->validate([
            'attendance_master_id' => 'required|integer|exists:attendance_masters,id',
            'code' => 'required|string|max:191',
        ]);

        $master = AttendanceMaster::findOrFail($data['attendance_master_id']);

        if ($master->is_locked) {
            return response()->json(['message' => 'Day is locked.'], 423);
        }

        $code = trim($data['code']);
        $now  = Carbon::now();

        // 1) Identify person based on the master's type
        $person = null;
        $personType = null;

        if ($master->type === 'student') {
            $person = $this->findStudentByCode($code);
            $personType = Student::class;
        } elseif ($master->type === 'staff') {
            $person = $this->findStaffByCode($code);
            $personType = Staff::class;
        } else {
            // Fallback: try student first, then staff
            $person = $this->findStudentByCode($code);
            $personType = Student::class;
            if (!$person) {
                $person = $this->findStaffByCode($code);
                $personType = Staff::class;
            }
        }

        if (!$person) {
            return response()->json(['message' => 'Person not found'], 404);
        }

        // 2) Upsert attendance row for this person under this master
        $row = Attendance::firstOrCreate(
            [
                'attendance_master_id' => $master->id,
                'attendable_type'      => $personType,
                'attendable_id'        => $person->id,
            ],
            [
                // default status = Present if you want; otherwise leave null
                'attendance_status_id' => $this->statusId('P'),
            ]
        );

        // Toggle check-in / check-out
        if (!$row->check_in_at) {
            $row->check_in_at = $now;
            // If status is empty, default to Present
            if (!$row->attendance_status_id) {
                $row->attendance_status_id = $this->statusId('P');
            }
        } elseif (!$row->check_out_at) {
            $row->check_out_at = $now;
        } else {
            // already both set; you may choose to update check_out_at = now instead
            $row->check_out_at = $now;
        }

        $row->save();

        // Eager load status for response
        $row->load('status');

        return response()->json([
            'row'  => [
                'id'                  => $row->id,
                'attendance_status_id'=> $row->attendance_status_id,
                'status'              => $row->status ? ['code' => $row->status->code, 'label' => $row->status->label] : null,
                'check_in_at'         => optional($row->check_in_at)->toIso8601String(),
                'check_out_at'        => optional($row->check_out_at)->toIso8601String(),
            ],
            'person' => [
                'id'   => $person->id,
                'name' => trim(($person->name ?? '') . ' ' . ($person->getFullNameAttribute() ?? '')),
                'type' => class_basename($personType),
            ],
        ]);
    }

    /**
     * Match student by common fields (adjust to your schema).
     * We use OR conditions to cover barcodes/QRs mapping to reg_no/admission_no/etc.
     */
    protected function findStudentByCode(string $code)
    {
        return Student::query()
            ->where('reg_no', $code)
            ->orWhere('university_enrollment_no', $code)
            ->orWhere('admission_no', $code)
            ->orWhere('mobile_1', $code)
            // ->orWhere('barcode', $code) // uncomment if you have a barcode column
            ->first();
    }

    /**
     * Match staff by common fields (adjust to your schema).
     */
    protected function findStaffByCode(string $code)
    {
        return Staff::query()
            ->where('reg_no', $code)
            //->orWhere('reg_no', $code)
            //->orWhere('employee_id', $code)
            ->orWhere('mobile_1', $code)
            // ->orWhere('barcode', $code)
            ->first();
    }

    /**
     * Resolve attendance status id by code, e.g. 'P', 'A', 'L', 'E', 'HL'
     */
    protected function statusId(?string $code): ?int
    {
        if (!$code) return null;
        $status = AttendanceStatus::where('code', strtoupper($code))->first();
        return $status ? $status->id : null;
    }
}
