<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\Student;
use App\Models\Staff;
use App\Models\DepartmentHead;
use App\Models\ClassRoutine;
use App\Models\SubjectAttendance;

class LiveAttendanceController extends CollegeBaseController
{
    protected $base_route = 'attendance.live';
    protected $view_path  = 'attendance.live';
    protected $panel      = 'Live Attendance';

    /* =========================================================
     * Pages
     * =======================================================*/

    // /attendance/live -> students
    public function redirectToStudents()
    {
        return redirect()->route('attendance.live.students');
    }

    public function students(Request $request)
    {
        $data = $this->baseLiveData();
        $data['page_type'] = 'students';
        return view(parent::loadDataToView($this->view_path.'.students'), compact('data'));
    }

    public function staff(Request $request)
    {
        $data = $this->baseLiveData();
        $data['page_type'] = 'staff';
        return view(parent::loadDataToView($this->view_path.'.staff'), compact('data'));
    }

    private function baseLiveData(): array
    {
        $today    = Carbon::today();
        $statuses = AttendanceStatus::select('id','code','label','color')
            ->orderBy('order')->orderBy('id')->get();

        $department_heads = DepartmentHead::active()->pluck('department_head', 'id');

        return [
            'today'             => $today->toDateString(),
            'statuses'          => $statuses,
            'department_heads'  => $department_heads,
            'default_per_page'  => (int) env('PAGINATION_LIMIT', 100),
        ];
    }

    /* =========================================================
     * Scan page
     * =======================================================*/

    public function scan(Request $request)
    {
        $today    = Carbon::today();
        $statuses = AttendanceStatus::select('id','code','label','color')
            ->orderBy('order')->orderBy('id')->get();

        $data = [
            'today'    => $today->toDateString(),
            'statuses' => $statuses,
        ];

        return view(parent::loadDataToView($this->view_path.'.scan'), compact('data'));
    }

    /* =========================================================
     * List API
     * =======================================================*/

    public function list(Request $request)
    {
        try {
            $type   = strtolower($request->query('type', 'student'));
            $search = trim((string)$request->query('q', ''));
            $per    = max(1, min((int)$request->query('per_page', (int) env('PAGINATION_LIMIT', 100)), 200));
            $page   = max(1, (int)$request->query('page', 1));
            $sCode  = strtoupper(trim((string)$request->query('status', '')));
            $today  = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::today();

            $deptHeadId = $request->query('department_head_id');
            $deptId     = $request->query('department_id');
            $facId      = $request->query('faculty_id');
            $semId      = $request->query('semester_id');
            $batchId    = $request->query('batch_id');
            $subjectId  = $request->query('subject_id');

            $meta = [
                'need_hierarchy'     => false,
                'subject_selected'   => (bool) $subjectId,
                'subject_scheduled'  => null,
                'schedule_periods'   => [],
                'active_period'      => null,
                'within_schedule'    => null,
                'schedule_message'   => '',
                'server_now'         => Carbon::now()->format('H:i:s'),
            ];

            if ($type === 'student') {
                // Require full chain: head -> department -> faculty -> semester -> batch -> subject
                if (!$deptId || !$facId || !$semId || !$batchId) {
                    $meta['need_hierarchy'] = true;
                    return response()->json(['success'=>true,'type'=>'student','data'=>[],'meta'=>$meta]);
                }

                if (!$subjectId) {
                    $meta['subject_selected'] = false;
                    $meta['subject_scheduled'] = false;
                    $meta['schedule_message'] = 'Select a scheduled subject to load students.';
                    return response()->json(['success'=>true,'type'=>'student','data'=>[],'meta'=>$meta]);
                }

                // Check today's schedule periods
                [$periods, $message] = $this->subjectPeriodsTodayForCohort($facId, $semId, $batchId, $subjectId, $today);
                $meta['schedule_periods'] = $periods;
                $meta['subject_scheduled'] = !empty($periods);
                $meta['schedule_message'] = $message;

                if (!$meta['subject_scheduled']) {
                    return response()->json(['success'=>true,'type'=>'student','data'=>[],'meta'=>$meta]);
                }

                // Is now within a period?
                [$within, $active] = $this->isWithinAnyPeriod($periods, Carbon::now());
                $meta['within_schedule'] = $within;
                $meta['active_period']   = $active;

                // Load students (list visible even outside time; actions are client-guarded)
                $q = $this->studentBase($search, $facId, $semId, $batchId);

                $selects = ['id'];
                foreach (['reg_no','first_name','middle_name','last_name','faculty','semester','batch','student_image'] as $c) {
                    if (Schema::hasColumn('students', $c)) $selects[] = $c;
                }

                $paginator = $q->select($selects)->orderBy('id')->paginate($per, ['*'], 'page', $page);
                $ids = collect($paginator->items())->pluck('id')->all();

                // Day-level rows
                $rowsDay = Attendance::with('status:id,code')
                    ->whereDate('date', $today)
                    ->where('attendable_type', Student::class)
                    ->when(!empty($ids), function($qq) use ($ids) { $qq->whereIn('attendable_id', $ids); })
                    ->get()->keyBy('attendable_id');

                // Subject-level rows (if subject selected)
                $rowsSubj = collect();
                if ($subjectId) {
                    $rowsSubj = SubjectAttendance::query()
                        ->whereDate('date', $today)
                        ->where('subject_id', (int)$subjectId)
                        ->when(!empty($ids), function($qq) use ($ids) { $qq->whereIn('student_id', $ids); })
                        ->get()->keyBy('student_id');
                }

                $data = collect($paginator->items())->map(function($st) use ($rowsDay, $rowsSubj) {
                    $rowDay  = $rowsDay->get($st->id);
                    $rowSubj = $rowsSubj->get($st->id);

                    $name = trim(($st->first_name ?? '').' '.(($st->middle_name ?? '')?$st->middle_name.' ':'').($st->last_name ?? ''));
                    if ($name === '') $name = '#'.$st->id;

                    $status = null; $updated=null; $in=null; $out=null;
                    if ($rowSubj) {
                        $status  = $this->statusCodeFromId($rowSubj->attendance_status_id ?? null);
                        $updated = $rowSubj->updated_at ? $rowSubj->updated_at->format('H:i') : null;
                        $in      = $rowSubj->in_at      ? Carbon::parse($rowSubj->in_at)->format('H:i') : null;
                        $out     = $rowSubj->out_at     ? Carbon::parse($rowSubj->out_at)->format('H:i') : null;
                    } else {
                        $status  = $this->statusCodeFromRow($rowDay);
                        $updated = $rowDay && $rowDay->updated_at ? $rowDay->updated_at->format('H:i') : null;
                        $in      = $rowDay && $rowDay->check_in_at ? $rowDay->check_in_at->format('H:i') : null;
                        $out     = $rowDay && $rowDay->check_out_at? $rowDay->check_out_at->format('H:i') : null;
                    }

                    $img = null;
                    if (Schema::hasColumn('students','student_image') && !empty($st->student_image)) {
                        $img = asset('images/studentProfile/'.$st->student_image);
                    }

                    return [
                        'attendance_id'     => $rowDay ? $rowDay->id : null,  // keep day row id for row.mark/row.check
                        'person_type'       => 'student',
                        'pid'               => $st->id,
                        'name'              => $name,
                        'code'              => $st->reg_no ?? null,
                        'last_status_code'  => $status,
                        'updated_at'        => $updated,
                        'check_in_at'       => $in,
                        'check_out_at'      => $out,
                        'avatar_url'        => $img,
                    ];
                });

                if ($sCode !== '') {
                    $data = $data->filter(function($i) use ($sCode) {
                        return ($i['last_status_code'] ?? '') === $sCode;
                    })->values();
                }

                return response()->json(['success'=>true,'type'=>'student','data'=>$data->values(),'meta'=>$meta]);
            }

            // Staff list (unchanged)
            $q = Staff::query()->where('status', 1);
            if ($search !== '') {
                $q->where(function($qq) use ($search) {
                    if (Schema::hasColumn('staff','reg_no')) $qq->orWhere('reg_no','like',"%{$search}%");
                    foreach (['name','first_name','middle_name','last_name'] as $c) {
                        if (Schema::hasColumn('staff',$c)) $qq->orWhere($c,'like',"%{$search}%");
                    }
                });
            }

            $sSelects = ['id'];
            foreach (['reg_no','name','first_name','middle_name','last_name','staff_image'] as $c) {
                if (Schema::hasColumn('staff', $c)) $sSelects[] = $c;
            }

            $paginator = $q->select($sSelects)->orderBy('id')->paginate($per, ['*'], 'page', $page);
            $ids = collect($paginator->items())->pluck('id')->all();

            $rows = Attendance::with('status:id,code')
                ->whereDate('date', $today)
                ->where('attendable_type', Staff::class)
                ->when(!empty($ids), function($qq) use ($ids) { $qq->whereIn('attendable_id', $ids); })
                ->get()->keyBy('attendable_id');

            $data = collect($paginator->items())->map(function($st) use ($rows) {
                $row = $rows->get($st->id);
                $name = 'Staff #'.$st->id;
                if (Schema::hasColumn('staff','first_name')) {
                    $name = trim(($st->first_name ?? '').' '.(($st->middle_name ?? '')?$st->middle_name.' ':'').($st->last_name ?? ''));
                } elseif (Schema::hasColumn('staff','name')) {
                    $name = $st->name ?? $name;
                }

                $status = $this->statusCodeFromRow($row);
                $img = null;
                if (Schema::hasColumn('staff','staff_image') && !empty($st->staff_image)) {
                    $img = asset('images/staff/'.$st->staff_image);
                }

                return [
                    'attendance_id'     => $row ? $row->id : null,
                    'person_type'       => 'staff',
                    'pid'               => $st->id,
                    'name'              => $name ?: ('Staff #'.$st->id),
                    'code'              => $st->reg_no ?? null,
                    'last_status_code'  => $status,
                    'updated_at'        => $row && $row->updated_at ? $row->updated_at->format('H:i') : null,
                    'check_in_at'       => $row && $row->check_in_at ? $row->check_in_at->format('H:i') : null,
                    'check_out_at'      => $row && $row->check_out_at ? $row->check_out_at->format('H:i') : null,
                    'avatar_url'        => $img,
                ];
            });

            if ($sCode !== '') {
                $data = $data->filter(function($i) use ($sCode) {
                    return ($i['last_status_code'] ?? '') === $sCode;
                })->values();
            }

            return response()->json(['success'=>true,'type'=>'staff','data'=>$data->values(),'meta'=>$meta]);

        } catch (\Throwable $e) {
            Log::error('LiveAttendance.list error', ['e'=>$e->getMessage(), 'trace'=>$e->getTraceAsString()]);
            return response()->json(['success'=>false,'message'=>'Failed to load list.'], 500);
        }
    }

    /* =========================================================
     * Identify (scanner) & Actions
     * =======================================================*/

   public function identify(Request $request)
    {
        try {
            $request->validate([
                'code'   => 'required|string|max:191',
                'source' => 'nullable|string|max:50',
                'date'   => 'nullable|date',
                'type'   => 'nullable|in:student,staff',
            ]);

            $code   = trim($request->input('code'));
            $source = $request->input('source', 'manual');
            $today  = $request->filled('date') ? Carbon::parse($request->input('date')) : Carbon::today();

            // Look up student first, then staff (matches your earlier behavior)
            $student = null; $staff = null;
            if (Schema::hasColumn('students','reg_no')) {
                $student = Student::where('reg_no', $code)->first();
            }
            if (!$student && Schema::hasColumn('staff','reg_no')) {
                $staff = Staff::where('reg_no', $code)->first();
            }
            if (!$student && !$staff) {
                return response()->json(['message'=>'No matching Student/Staff for: '.$code], 422);
            }

            $attendable = $student ?: $staff;
            $attType    = $student ? Student::class : Staff::class;
            $defaultStatusId = $this->ensureStatusId('P');

            // Day-level attendance row: create / update check-in/out like before
            $row = null;
            Model::withoutEvents(function() use (&$row, $today, $attType, $attendable, $source, $defaultStatusId) {
                $row = Attendance::whereDate('date', $today)
                    ->where('attendable_type', $attType)
                    ->where('attendable_id', $attendable->id)
                    ->first();

                if (!$row) {
                    $row = Attendance::create([
                        'date'                 => $today->toDateString(),
                        'attendable_type'      => $attType,
                        'attendable_id'        => $attendable->id,
                        'reg_no'               => ($attType === Student::class) ? $this->studentRegNo($attendable) : $this->staffRegNo($attendable),
                        'source'               => $source,
                        'attendance_status_id' => $defaultStatusId,
                        'check_in_at'          => Carbon::now(),
                    ]);
                } else {
                    if (!$row->check_in_at)      $row->check_in_at  = Carbon::now();
                    elseif (!$row->check_out_at) $row->check_out_at = Carbon::now();
                    else                          $row->check_out_at = Carbon::now();

                    if (!$row->attendance_status_id) $row->attendance_status_id = $defaultStatusId;
                    $row->source = $source;
                    $row->save();
                }
            });

            // Build the person payload the scan page expects
            if ($student) {
                // Name
                $name = '';
                if (Schema::hasColumn('students','first_name')) {
                    $name = trim(($attendable->first_name ?? '').' '.(($attendable->middle_name ?? '')?$attendable->middle_name.' ':'').($attendable->last_name ?? ''));
                }
                if ($name === '') { $name = 'Student #'.$attendable->id; }

                // Reg and image
                $regNo = Schema::hasColumn('students','reg_no') ? ($attendable->reg_no ?? null) : null;
                $studentImage = Schema::hasColumn('students','student_image') ? ($attendable->student_image ?? null) : null;

                $person = [
                    'id'            => (int)$attendable->id,
                    'type'          => 'student',
                    'name'          => $name,
                    'reg_no'        => $regNo,
                    'code'          => $regNo, // fallback key used in your JS
                    'student_image' => $studentImage, // your JS builds path if image_url is empty
                ];
                if ($studentImage) {
                    $person['image_url'] = asset('images/studentProfile/'.$studentImage);
                }
            } else { // staff
                // Name
                if (Schema::hasColumn('staff','first_name')) {
                    $name = trim(($attendable->first_name ?? '').' '.(($attendable->middle_name ?? '')?$attendable->middle_name.' ':'').($attendable->last_name ?? ''));
                } elseif (Schema::hasColumn('staff','name')) {
                    $name = $attendable->name ?: ('Staff #'.$attendable->id);
                } else {
                    $name = 'Staff #'.$attendable->id;
                }

                // Reg and image
                $regNo = Schema::hasColumn('staff','reg_no') ? ($attendable->reg_no ?? null) : null;
                $staffImage = Schema::hasColumn('staff','staff_image') ? ($attendable->staff_image ?? null) : null;

                $person = [
                    'id'          => (int)$attendable->id,
                    'type'        => 'staff',
                    'name'        => $name,
                    'reg_no'      => $regNo,
                    'code'        => $regNo,
                    'staff_image' => $staffImage,
                ];
                if ($staffImage) {
                    $person['image_url'] = asset('images/staff/'.$staffImage);
                }
            }

            // (Optional) mirror to all subjects scheduled today stays as you had it
            if ($student) {
                try {
                    $subjectsToday = $this->subjectsScheduledTodayForStudent($attendable, $today);
                    if (!empty($subjectsToday)) {
                        $statusId = $row->attendance_status_id ?: $defaultStatusId;
                        Model::withoutEvents(function() use ($attendable, $subjectsToday, $today, $statusId, $row) {
                            foreach ($subjectsToday as $sid) {
                                SubjectAttendance::updateOrCreate(
                                    ['student_id'=>$attendable->id,'subject_id'=>$sid,'date'=>$today->toDateString()],
                                    ['attendance_status_id'=>$statusId, 'attendance_id' => $row ? $row->id : null]
                                );
                            }
                        });
                    }
                } catch (\Throwable $e) {
                    Log::warning('Subject mirror failed', ['e'=>$e->getMessage()]);
                }
            }

            // status_code for scan page (use helper so it's robust)
            $statusCode = $this->statusCodeFromRow($row);

            return response()->json([
                'success' => true,
                'person'  => $person, // <-- what your scan page reads
                'row'     => [
                    'id'            => $row->id,
                    'attendance_id' => $row->id,
                    'status_code'   => $statusCode,
                    'check_in_at'   => optional($row->check_in_at)->toDateTimeString(),
                    'check_out_at'  => optional($row->check_out_at)->toDateTimeString(),
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('identify error', ['msg'=>$e->getMessage(), 'trace'=>$e->getTraceAsString()]);
            return response()->json(['message'=>$e->getMessage()], 500);
        }
    }

    public function quickMark(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:student,staff',
            'person_id'  => 'required|integer|min:1',
            'code'       => 'required|string|max:10',
            'date'       => 'nullable|date',
            'subject_id' => 'nullable|integer',
        ]);

        $type      = $request->input('type');
        $pid       = (int)$request->input('person_id');
        $code      = strtoupper($request->input('code'));
        $subjectId = $request->input('subject_id');
        $today     = $request->filled('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        $statusId  = $this->statusIdByCode($code);
        if (!$statusId) return response()->json(['message'=>'Unknown status code'], 422);

        $attType = $type === 'student' ? Student::class : Staff::class;
        $defaultStatusId = $this->ensureStatusId('P');

        // Enforce subject + window for students
        if ($type === 'student') {
            if (!$subjectId) {
                return response()->json(['message'=>'Select a scheduled subject before taking attendance.'], 422);
            }
            $student = Student::find($pid);
            if (!$student) return response()->json(['message'=>'Student not found.'], 404);
            [$ok, $msg] = $this->validateScheduleWindowForStudent($student, (int)$subjectId, $today);
            if (!$ok) return response()->json(['message'=>$msg], 422);
        }

        $row = null;
        Model::withoutEvents(function() use ($type, $pid, $today, $statusId, $subjectId, $attType, $defaultStatusId, &$row) {
            $row = Attendance::whereDate('date', $today)
                ->where('attendable_type', $attType)
                ->where('attendable_id', $pid)
                ->first();


            if (!$row) {
                $reg = $type === 'student' ? $this->studentRegNo(Student::find($pid)) : $this->staffRegNo(Staff::find($pid));
                $row = Attendance::create([
                    'date'                 => $today->toDateString(),
                    'attendable_type'      => $attType,
                    'attendable_id'        => $pid,
                    'reg_no'               => $reg,
                    'attendance_status_id' => $statusId,
                    'check_in_at'          => Carbon::now(),
                ]);
               //\App\Models\Attendance::queueGuardianNotificationIfNeeded($row);
            } else {
                $row->attendance_status_id = $statusId;
                if (!$row->check_in_at) $row->check_in_at = Carbon::now();
                if (!$row->attendance_status_id) $row->attendance_status_id = $defaultStatusId;
                $row->save();
                
            }

            if ($type === 'student') {
                $student = Student::find($pid);
                if ($student && $subjectId) {
                    $periodId = $this->currentActivePeriodIdForStudentSubject($student, (int)$subjectId, $today);
                    $this->persistSubjectAttendance(
                        (int)$student->id,
                        (int)$subjectId,
                        $today,
                        (int)$statusId,
                        $periodId,
                        (int)$row->id,
                        'status'
                    );
                }
            }
        });

        return response()->json(['success'=>true]);
    }

    public function quickCheck(Request $request)
    {
        // Daily check-in/out (optionally mirror to subject if subject_id provided)
        $request->validate([
            'type'       => 'required|in:student,staff',
            'person_id'  => 'required|integer|min:1',
            'date'       => 'nullable|date',
            'subject_id' => 'nullable|integer',
        ]);

        $type      = $request->input('type');
        $pid       = (int)$request->input('person_id');
        $subjectId = $request->input('subject_id');
        $today     = $request->filled('date') ? Carbon::parse($request->input('date')) : Carbon::today();

        $attType = $type === 'student' ? Student::class : Staff::class;
        $defaultStatusId = $this->ensureStatusId('P');

        $row = null;
        Model::withoutEvents(function() use ($type, $pid, $today, $attType, $defaultStatusId, &$row) {
            $row = Attendance::whereDate('date', $today)
                ->where('attendable_type', $attType)
                ->where('attendable_id', $pid)
                ->first();

            if (!$row) {
                $reg = $type === 'student' ? $this->studentRegNo(Student::find($pid)) : $this->staffRegNo(Staff::find($pid));
                $row = Attendance::create([
                    'date'                 => $today->toDateString(),
                    'attendable_type'      => $attType,
                    'attendable_id'        => $pid,
                    'reg_no'               => $reg,
                    'attendance_status_id' => $defaultStatusId,
                    'check_in_at'          => Carbon::now(),
                ]);
            } else {
                if (!$row->check_in_at) $row->check_in_at = Carbon::now();
                else                    $row->check_out_at = Carbon::now();
                if (!$row->attendance_status_id) $row->attendance_status_id = $defaultStatusId;
                $row->save();
            }
        });

        // Optional subject mirror for students
        if ($type === 'student' && $subjectId) {
            $student  = Student::find($pid);
            if ($student) {
                $periodId = $this->currentActivePeriodIdForStudentSubject($student, (int)$subjectId, $today);
                $this->persistSubjectAttendance(
                    (int)$student->id,
                    (int)$subjectId,
                    $today,
                    null,                                 // status unchanged by check
                    $periodId,
                    $row ? (int)$row->id : null,
                    'auto'                                // decide in/out
                );
            }
        }

        return response()->json(['success'=>true]);
    }

    public function mark(Request $request, Attendance $attendance)
    {
        $request->validate([
            'code'       => 'required|string|max:10',
            'subject_id' => 'nullable|integer',
        ]);

        $code      = strtoupper($request->code);
        $subjectId = $request->input('subject_id');
        $statusId  = $this->statusIdByCode($code);
        if (!$statusId) return response()->json(['message'=>'Unknown status code'], 422);

        // Students require subject & active window
        if ($attendance->attendable_type === Student::class) {
            if (!$subjectId) return response()->json(['message'=>'Select a scheduled subject before taking attendance.'], 422);
            $student = Student::find($attendance->attendable_id);
            if ($student) {
                $today = $attendance->date ? Carbon::parse($attendance->date) : Carbon::today();
                [$ok, $msg] = $this->validateScheduleWindowForStudent($student, (int)$subjectId, $today);
                if (!$ok) return response()->json(['message'=>$msg], 422);
            }
        }

        Model::withoutEvents(function() use ($attendance, $statusId, $subjectId) {
            $attendance->attendance_status_id = $statusId;
            if (!$attendance->check_in_at) $attendance->check_in_at = Carbon::now();
            $attendance->save();

            if ($attendance->attendable_type === Student::class && $subjectId) {
                $student = Student::find($attendance->attendable_id);
                if ($student) {
                    $today = $attendance->date ? Carbon::parse($attendance->date) : Carbon::today();
                    $periodId = $this->currentActivePeriodIdForStudentSubject($student, (int)$subjectId, $today);
                    $this->persistSubjectAttendance(
                        (int)$student->id,
                        (int)$subjectId,
                        $today,
                        (int)$statusId,
                        $periodId,
                        (int)$attendance->id,
                        'status'
                    );
                }
            }
        });

        return response()->json(['success'=>true]);
    }

    /**
     * NEW: row check endpoint used by route('attendance.row.check')
     * Mirrors in/out into subject_attendances when subject_id is provided.
     */
    public function check(Request $request, Attendance $attendance)
    {
        $request->validate([
            'subject_id' => 'nullable|integer',
        ]);

        $subjectId = $request->input('subject_id');
        $today     = $attendance->date ? Carbon::parse($attendance->date) : Carbon::today();

        Model::withoutEvents(function() use ($attendance) {
            if (!$attendance->check_in_at) {
                $attendance->check_in_at = Carbon::now();
            } else {
                $attendance->check_out_at = Carbon::now();
            }
            if (!$attendance->attendance_status_id) {
                $attendance->attendance_status_id = $this->ensureStatusId('P');
            }
            $attendance->save();
        });

        if ($attendance->attendable_type === Student::class && $subjectId) {
            $student = Student::find($attendance->attendable_id);
            if ($student) {
                $periodId = $this->currentActivePeriodIdForStudentSubject($student, (int)$subjectId, $today);
                $this->persistSubjectAttendance(
                    (int)$student->id,
                    (int)$subjectId,
                    $today,
                    null,                              // status unchanged here
                    $periodId,
                    (int)$attendance->id,
                    'auto'
                );
            }
        }

        return response()->json(['success' => true]);
    }

    public function bulkMark(Request $request)
    {
        $request->validate([
            'type'          => 'required|in:student,staff',
            'person_ids'    => 'required|array|min:1',
            'person_ids.*'  => 'integer|min:1',
            'code'          => 'required|string|max:10',
            'date'          => 'nullable|date',
            'subject_id'    => 'nullable|integer',
        ]);

        $type      = $request->input('type');
        $ids       = $request->input('person_ids', []);
        $code      = strtoupper($request->input('code'));
        $subjectId = $request->input('subject_id');
        $today     = $request->filled('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        $statusId  = $this->statusIdByCode($code);
        if (!$statusId) return response()->json(['message'=>'Unknown status code'], 422);

        // Students bulk require subject & active window
        if ($type === 'student') {
            if (!$subjectId) return response()->json(['message'=>'Select a scheduled subject before taking attendance.'], 422);
            $first = Student::find($ids[0] ?? 0);
            if (!$first) return response()->json(['message'=>'Student not found.'], 404);
            [$ok, $msg] = $this->validateScheduleWindowForStudent($first, (int)$subjectId, $today);
            if (!$ok) return response()->json(['message'=>$msg], 422);
        }

        $ok = 0;
        Model::withoutEvents(function() use ($type, $ids, $today, $statusId, $subjectId, &$ok) {
            foreach ($ids as $pid) {
                try {
                    $this->applyMark((string)$type, (int)$pid, $today, (int)$statusId, $subjectId);
                    $ok++;
                } catch (\Throwable $e) {
                    Log::warning('bulkMark skip', ['pid'=>$pid, 'err'=>$e->getMessage()]);
                }
            }
        });

        return response()->json(['success'=>true,'updated'=>$ok,'total'=>count($ids)]);
    }

    public function bulkCheck(Request $request)
    {
        // Daily check-in/out; if you want to also mirror per-subject here, pass subject_id and call persistSubjectAttendance in a loop.
        $request->validate([
            'type'          => 'required|in:student,staff',
            'person_ids'    => 'required|array|min:1',
            'person_ids.*'  => 'integer|min:1',
            'date'          => 'nullable|date',
            'subject_id'    => 'nullable|integer',
        ]);

        $type      = $request->input('type');
        $ids       = $request->input('person_ids', []);
        $today     = $request->filled('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        $subjectId = $request->input('subject_id'); // optional

        $ok = 0;
        Model::withoutEvents(function() use ($type, $ids, $today, $subjectId, &$ok) {
            foreach ($ids as $pid) {
                try {
                    $this->applyCheck((string)$type, (int)$pid, $today);
                    // Optional subject mirror on bulk check:
                    if ($type === 'student' && $subjectId) {
                        $student  = Student::find((int)$pid);
                        if ($student) {
                            $periodId = $this->currentActivePeriodIdForStudentSubject($student, (int)$subjectId, $today);
                            $this->persistSubjectAttendance(
                                (int)$student->id,
                                (int)$subjectId,
                                $today,
                                null,
                                $periodId,
                                null,
                                'auto'
                            );
                        }
                    }
                    $ok++;
                } catch (\Throwable $e) {
                    Log::warning('bulkCheck skip', ['pid'=>$pid, 'err'=>$e->getMessage()]);
                }
            }
        });

        return response()->json(['success'=>true,'updated'=>$ok,'total'=>count($ids)]);
    }

    /* =========================================================
     * Helpers
     * =======================================================*/

    private static array $STATUS_CODE_BY_ID = [];
    private static array $STATUS_ID_BY_CODE = [];

    private function ensureStatusId(string $codePreferred = 'P'): ?int
    {
        $codePreferred = strtoupper($codePreferred);
        $id = AttendanceStatus::where('code', $codePreferred)->value('id');
        if ($id) return (int)$id;

        $any = AttendanceStatus::value('id');
        if ($any) return (int)$any;

        try {
            $created = AttendanceStatus::create([
                'code'  => 'P',
                'label' => 'Present',
                'order' => 1,
                'color' => '#10b981',
            ]);
            return (int)$created->id;
        } catch (\Throwable $e) {
            Log::error('ensureStatusId failed', ['e'=>$e->getMessage()]);
            return null;
        }
    }

    private function statusIdByCode($code)
    {
        $code = strtoupper((string)$code);
        if (!isset(self::$STATUS_ID_BY_CODE[$code])) {
            self::$STATUS_ID_BY_CODE[$code] = AttendanceStatus::where('code',$code)->value('id');
        }
        return self::$STATUS_ID_BY_CODE[$code];
    }

    private function statusCodeFromRow($row): ?string
    {
        if (!$row || !$row->attendance_status_id) return null;
        $id = (int)$row->attendance_status_id;
        if (!isset(self::$STATUS_CODE_BY_ID[$id])) {
            self::$STATUS_CODE_BY_ID[$id] = AttendanceStatus::whereKey($id)->value('code');
        }
        return self::$STATUS_CODE_BY_ID[$id] ?? null;
    }

    private function statusCodeFromId($id): ?string
    {
        if (!$id) return null;
        $id = (int) $id;
        if (!isset(self::$STATUS_CODE_BY_ID[$id])) {
            self::$STATUS_CODE_BY_ID[$id] = AttendanceStatus::whereKey($id)->value('code');
        }
        return self::$STATUS_CODE_BY_ID[$id] ?? null;
    }

    private function studentRegNo($student): string
    {
        if (!$student) return '';
        if (Schema::hasColumn('students','reg_no') && !empty($student->reg_no)) return (string)$student->reg_no;
        return 'STU-'.$student->id;
    }

    private function staffRegNo($staff): string
    {
        if (!$staff) return '';
        if (Schema::hasColumn('staff','reg_no') && !empty($staff->reg_no)) return (string)$staff->reg_no;
        return 'STAFF-'.$staff->id;
    }

    private function applyMark(string $type, int $pid, Carbon $date, int $statusId, $subjectId = null): void
    {
        $attType = $type === 'student' ? Student::class : Staff::class;
        $defaultStatusId = $this->ensureStatusId('P');

        $row = Attendance::whereDate('date', $date)
            ->where('attendable_type', $attType)
            ->where('attendable_id', $pid)
            ->first();

        if (!$row) {
            $reg = $type === 'student' ? $this->studentRegNo(Student::find($pid)) : $this->staffRegNo(Staff::find($pid));
            $row = Attendance::create([
                'date'                 => $date->toDateString(),
                'attendable_type'      => $attType,
                'attendable_id'        => $pid,
                'reg_no'               => $reg,
                'attendance_status_id' => $statusId,
                'check_in_at'          => Carbon::now(),
            ]);
        } else {
            $row->attendance_status_id = $statusId;
            if (!$row->check_in_at) $row->check_in_at = Carbon::now();
            if (!$row->attendance_status_id) $row->attendance_status_id = $defaultStatusId;
            $row->save();
        }

        if ($type === 'student' && $subjectId) {
            $student  = Student::find($pid);
            if ($student) {
                $periodId = $this->currentActivePeriodIdForStudentSubject($student, (int)$subjectId, $date);
                $this->persistSubjectAttendance(
                    (int)$student->id,
                    (int)$subjectId,
                    $date,
                    (int)$statusId,
                    $periodId,
                    (int)$row->id,
                    'status'
                );
            }
        }
    }

    private function applyCheck(string $type, int $pid, Carbon $date): void
    {
        $attType = $type === 'student' ? Student::class : Staff::class;
        $defaultStatusId = $this->ensureStatusId('P');

        $row = Attendance::whereDate('date', $date)
            ->where('attendable_type', $attType)
            ->where('attendable_id', $pid)
            ->first();

        if (!$row) {
            $reg = $type === 'student' ? $this->studentRegNo(Student::find($pid)) : $this->staffRegNo(Staff::find($pid));
            Attendance::create([
                'date'                 => $date->toDateString(),
                'attendable_type'      => $attType,
                'attendable_id'        => $pid,
                'reg_no'               => $reg,
                'attendance_status_id' => $defaultStatusId,
                'check_in_at'          => Carbon::now(),
            ]);
        } else {
            if (!$row->check_in_at) $row->check_in_at = Carbon::now();
            else $row->check_out_at = Carbon::now();
            if (!$row->attendance_status_id) $row->attendance_status_id = $defaultStatusId;
            $row->save();
        }
    }

    private function studentBase($search = '', $facId = null, $semId = null, $batchId = null)
    {
        $q = Student::query()->where('status', 1);

        $map = ['faculty_id'=>'faculty','semester_id'=>'semester','batch_id'=>'batch'];
        foreach ($map as $param => $col) {
            $val = null;
            if ($param==='faculty_id')  $val = $facId;
            if ($param==='semester_id') $val = $semId;
            if ($param==='batch_id')    $val = $batchId;
            if ($val !== null && Schema::hasColumn('students', $col)) $q->where($col, $val);
        }

        if ($search !== '') {
            $q->where(function($qq) use ($search) {
                if (Schema::hasColumn('students','reg_no')) $qq->orWhere('reg_no','like',"%{$search}%");
                foreach (['first_name','middle_name','last_name'] as $c) {
                    if (Schema::hasColumn('students',$c)) $qq->orWhere($c,'like',"%{$search}%");
                }
            });
        }
        return $q;
    }

    /* =========================================================
     * Schedule helpers (Students)
     * =======================================================*/

    /**
     * Return today's periods for the cohort+subject with start/end times.
     * @return array{0: array<int, array>, 1: string}
     */
    private function subjectPeriodsTodayForCohort($facultyId, $semesterId, $batchId, $subjectId, Carbon $date): array
    {
        $rows = [];
        $msg  = '';

        $hasStart = Schema::hasColumn('class_routines','start_time');
        $hasEnd   = Schema::hasColumn('class_routines','end_time');
        $hasDOW   = Schema::hasColumn('class_routines','day_of_week');

        if (!$hasDOW) {
            return [[], 'Routine missing day_of_week column.'
            ]
            ;
        }

        $dowName = $date->format('l');      // e.g., Monday
        $dow0    = (int)$date->format('w'); // 0..6 (Sun..Sat)
        $dow1    = $dow0 === 0 ? 7 : $dow0; // 1..7 (Mon..Sun)

        $q = ClassRoutine::query()
            ->where('status', 1)
            ->where('faculty_id', $facultyId)
            ->where('semester_id', $semesterId)
            ->where('student_batch_id', $batchId)
            ->where('subject_id', $subjectId)
            ->where(function($q) use ($dowName, $dow0, $dow1) {
                $q->orWhere('day_of_week', $dowName)
                  ->orWhere('day_of_week', $dow0)
                  ->orWhere('day_of_week', $dow1);
            });

        $routines = $q->orderBy('start_time')->get();

        foreach ($routines as $rt) {
            $start = $hasStart ? ($rt->start_time ?? null) : null;
            $end   = $hasEnd ? ($rt->end_time ?? null) : null;

            if (!$start || !$end) {
                // If times are not stored, skip—no time-locked attendance possible for this row
                continue;
            }

            $startDT = Carbon::parse($date->toDateString().' '.$start);
            $endDT   = Carbon::parse($date->toDateString().' '.$end);
            if ($endDT->lessThan($startDT)) {
                [$startDT, $endDT] = [$endDT, $startDT];
            }

            $rows[] = [
                'start'     => $startDT->toDateTimeString(),
                'end'       => $endDT->toDateTimeString(),
                'start_hm'  => $startDT->format('H:i'),
                'end_hm'    => $endDT->format('H:i'),
                'label'     => ($rt->title ?? 'Period'),
                'id'        => $rt->id, // used as class_routine_detail_id
            ];
        }

        if (empty($rows)) {
            $msg = 'Selected subject is not scheduled today.';
        }

        return [$rows, $msg];
    }

    /**
     * Is "now" within any of the provided periods?
     * @param array<int, array{start: string, end: string}> $periods
     * @return array{0: bool, 1: ?array}
     */
    private function isWithinAnyPeriod(array $periods, Carbon $now): array
    {
        foreach ($periods as $p) {
            $start = Carbon::parse($p['start']);
            $end   = Carbon::parse($p['end']);
            if ($now->between($start, $end)) {
                return [true, $p];
            }
        }
        return [false, null];
    }

    /**
     * Validate that student's cohort has the subject scheduled *now*.
     * @return array{0: bool, 1: string}
     */
    private function validateScheduleWindowForStudent(Student $student, int $subjectId, Carbon $date): array
    {
        $fac = $student->faculty ?? null;
        $sem = $student->semester ?? null;
        $bat = $student->batch ?? null;

        if (!$fac || !$sem || !$bat || !$subjectId) {
            return [false, 'Missing cohort or subject to validate schedule.'];
        }

        [$periods, $msg] = $this->subjectPeriodsTodayForCohort($fac, $sem, $bat, $subjectId, $date);
        if (empty($periods)) {
            return [false, $msg ?: 'Selected subject is not scheduled today.'];
        }

        [$within, $active] = $this->isWithinAnyPeriod($periods, Carbon::now());
        if (!$within) {
            $times = collect($periods)->map(function($x){ return $x['start_hm'].'-'.$x['end_hm']; })->implode(', ');
            return [false, 'Attendance locked. Allowed period(s): '.$times];
        }

        return [true, ''];
    }

    private function subjectsScheduledTodayForStudent(Student $student, Carbon $date)
    {
        $fac = $student->faculty ?? null;
        $sem = $student->semester ?? null;
        $bat = $student->batch ?? null;
        if (!$fac || !$sem || !$bat) return [];

        $dowName = $date->format('l');
        $dow0    = (int)$date->format('w');
        $dow1    = $dow0 === 0 ? 7 : $dow0;

        return ClassRoutine::query()
            ->where('status', 1)
            ->where('faculty_id', $fac)
            ->where('semester_id', $sem)
            ->where('student_batch_id', $bat)
            ->where(function($q) use ($dowName, $dow0, $dow1) {
                $q->orWhere('day_of_week', $dowName)
                  ->orWhere('day_of_week', $dow0)
                  ->orWhere('day_of_week', $dow1);
            })
            ->pluck('subject_id')->filter()->unique()->values()->all();
    }

    /**
     * Decide the active routine period id for a student's subject at "now".
     */
    private function currentActivePeriodIdForStudentSubject(Student $student, int $subjectId, Carbon $date): ?int
    {
        $fac = $student->faculty ?? null;
        $sem = $student->semester ?? null;
        $bat = $student->batch ?? null;
        if (!$fac || !$sem || !$bat) return null;

        [$periods, ] = $this->subjectPeriodsTodayForCohort($fac, $sem, $bat, $subjectId, $date);
        if (empty($periods)) return null;

        [$within, $active] = $this->isWithinAnyPeriod($periods, Carbon::now());
        return $within ? ($active['id'] ?? null) : null;
    }

    /**
     * Create/update subject_attendances with audit and optional in/out/status.
     *
     * @param string|null $mode 'status' | 'check_in' | 'check_out' | 'auto'
     */
    private function persistSubjectAttendance(
        int $studentId,
        int $subjectId,
        Carbon $date,
        ?int $statusId = null,
        ?int $classRoutineDetailId = null,
        ?int $attendanceId = null,
        ?string $mode = null
    ): void {
        $uid = auth()->id();

        /** @var \App\Models\SubjectAttendance $row */
        $row = SubjectAttendance::firstOrNew([
            'date'       => $date->toDateString(),
            'student_id' => $studentId,
            'subject_id' => $subjectId,
        ]);

        if (!$row->exists && $uid) {
            $row->created_by = (int) $uid;
        }

        if ($attendanceId) {
            $row->attendance_id = $attendanceId;
        }
        if ($classRoutineDetailId) {
            $row->class_routine_detail_id = $classRoutineDetailId;
        }
        if ($statusId) {
            $row->attendance_status_id = $statusId;
        }

        // Handle check in/out / auto toggle
        if ($mode === 'check_in') {
            if (!$row->in_at) {
                $row->in_at = Carbon::now();
            }
        } elseif ($mode === 'check_out') {
            $row->out_at = Carbon::now();
        } elseif ($mode === 'auto') {
            if (!$row->in_at)         $row->in_at  = Carbon::now();
            elseif (!$row->out_at)    $row->out_at = Carbon::now();
            else                      $row->out_at = Carbon::now();
        }

        if ($uid) {
            $row->updated_by = (int) $uid;
        }

        $row->save();
    }
}
