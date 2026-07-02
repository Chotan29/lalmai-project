<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\CollegeBaseController;
use App\Models\ClassRoutine;
use App\Models\DepartmentHead;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\StudentBatch;
use App\Models\Subject;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Academic\ClassRoutineImport;
use App\Exports\Academic\ClassRoutineExport;
use App\Exports\Academic\ClassRoutineTemplateExport;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Maatwebsite\Excel\Excel as ExcelFormat;

class ClassRoutineController extends CollegeBaseController
{
    protected $base_route = 'routine';
    protected $view_path  = 'academic.class-routine';
    protected $panel      = 'Class Routine';

    /**
     * Index: browse via hierarchy (DeptHead -> Department -> Faculty -> Semester -> Batch -> Subject (optional)).
     * Returns full page or JSON partials for AJAX (hierarchy/breadcrumbs/routines).
     */
    public function index(Request $request)
    {
        try {
            $data = [];
            $data['department_heads'] = DepartmentHead::active()->pluck('department_head', 'id');

            if ($request->has('department_head_id')) {
                $data['current_department_head'] = DepartmentHead::find($request->department_head_id);

                // Departments that actually have routines under this head
                $data['departments'] = Department::whereHas('heads', function ($q) use ($request) {
                        $q->where('department_heads.id', $request->department_head_id);
                    })
                    ->whereHas('routines')
                    ->pluck('department', 'id');

                if ($request->has('department_id')) {
                    $data['current_department'] = Department::find($request->department_id);

                    // Faculties under department (optionally only those with routines)
                    $data['faculties'] = Faculty::whereHas('departments', function ($q) use ($request) {
                            $q->where('departments.id', $request->department_id);
                        })
                        ->when($request->has('show_only_with_routines'), function ($query) {
                            $query->whereHas('routines');
                        })
                        ->pluck('faculty', 'id');

                    if ($request->has('faculty_id')) {
                        $data['current_faculty'] = Faculty::find($request->faculty_id);

                        // Semesters mapped to this faculty that have routines
                        $data['semesters'] = Semester::select('semesters.semester', 'semesters.id')
                            ->join('faculty_semester', 'semesters.id', '=', 'faculty_semester.semester_id')
                            ->join('class_routines', function ($join) use ($request) {
                                $join->on('class_routines.faculty_id', '=', DB::raw((int) $request->faculty_id))
                                     ->whereColumn('class_routines.semester_id', 'semesters.id');
                            })
                            ->where('faculty_semester.faculty_id', $request->faculty_id)
                            ->distinct()
                            ->pluck('semester', 'id');

                        if ($request->has('semester_id')) {
                            $data['current_semester'] = Semester::find($request->semester_id);

                            // Batches having routines within the chosen Faculty + Semester
                            $data['batches'] = StudentBatch::whereHas('routines', function ($q) use ($request) {
                                    $q->where('semester_id', $request->semester_id)
                                      ->where('faculty_id',  $request->faculty_id);
                                })
                                ->pluck('title', 'id');

                            // SUBJECT FILTER
                            $semester = Semester::find($request->semester_id);
                            if ($semester && method_exists($semester, 'subjects')) {
                                $data['subjects'] = $semester->subjects()->pluck('subjects.title', 'subjects.id');
                            } else {
                                $data['subjects'] = Subject::where('semester_id', $request->semester_id)
                                    ->pluck('title', 'id');
                            }

                            // When batch is chosen, show routines (optionally narrowed by subject)
                            if ($request->has('batch_id')) {
                                $data['current_batch'] = StudentBatch::find($request->batch_id);

                                $rQuery = ClassRoutine::with([
                                        'department', 'faculty', 'semester', 'batch', 'subject', 'teacher',
                                    ])
                                    ->where('faculty_id',       $request->faculty_id)
                                    ->where('semester_id',      $request->semester_id)
                                    ->where('student_batch_id', $request->batch_id)
                                    ->where('status', 1);

                                if ($request->filled('subject_id')) {
                                    $data['current_subject'] = Subject::find($request->subject_id);
                                    $rQuery->where('subject_id', $request->subject_id);
                                }

                                // $data['routines'] = $rQuery
                                //     ->orderBy('day_of_week')
                                //     ->orderBy('start_time')
                                //     ->get()
                                //     ->groupBy('day_of_week');

                                $routines = $rQuery->orderBy('day_of_week')
                                ->orderBy('start_time')
                                ->get();

                            // attach department_head_id (requested or first linked to department)
                            $requestedHeadId = $request->input('department_head_id');
                            $routines->each(function ($r) use ($requestedHeadId) {
                                $r->department_head_id = $requestedHeadId
                                    ?: optional(optional($r->department)->heads->first())->id;
                            });

                            $data['routines'] = $routines->groupBy('day_of_week');
                            }
                        }
                    }
                }
            }

            // AJAX: return partials
            if ($request->ajax()) {
                return response()->json([
                    'success'     => true,
                    'hierarchy'   => view('academic.class-routine.includes.hierarchy', $data)->render(),
                    'breadcrumbs' => view('academic.class-routine.includes.breadcrumbs', $data)->render(),
                    'routines'    => isset($data['routines'])
                        ? view('academic.class-routine.includes.routines', $data)->render()
                        : '',
                ]);
            }

            // Full page
            return view(parent::loadDataToView($this->view_path.'.index'), $data);
        } catch (\Throwable $e) {
            Log::error('Routine index failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to load routines.'], 500);
            }
            $request->session()->flash($this->message_danger, 'Failed to load data.');
            return back();
        }
    }

    /**
     * Create (single-subject multi-day builder)
     */
    public function create()
    {
        try {
            $data = [];
            $data['department_heads'] = DepartmentHead::active()->pluck('department_head', 'id');
            $data['batches']          = StudentBatch::active();
            $data['teachers'] = Staff::active()
                ->select('id', 'first_name', 'middle_name', 'last_name')
                ->orderBy('first_name')
                ->get()
                ->mapWithKeys(function ($staff) {
                    $fullName = trim($staff->first_name . ' ' . ($staff->middle_name ?? '') . ' ' . $staff->last_name);
                    return [$staff->id => $fullName];
                });

            return view(parent::loadDataToView($this->view_path.'.create'), $data);
        } catch (\Throwable $e) {
            Log::error('Routine create failed', ['error' => $e->getMessage()]);
            session()->flash($this->message_danger, 'Failed to load create form.');
            return back();
        }
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $rules = [
            'department_id'     => 'required|exists:departments,id',
            'faculty_id'        => 'required|exists:faculties,id',
            'semester_id'       => 'required|exists:semesters,id',
            'student_batch_id'  => 'required|exists:student_batches,id',
            'subject_id'        => 'required|exists:subjects,id',

            'schedules'                       => 'required|array|min:1',
            'schedules.*.day_of_week'         => 'required|string|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'schedules.*.period'              => 'nullable|string|max:50',
            'schedules.*.start_time'          => 'required|date_format:H:i',
            'schedules.*.end_time'            => 'required|date_format:H:i',
            'schedules.*.room_number'         => 'required|string|max:255',
            'schedules.*.teacher_id'          => 'required|exists:staff,id',
        ];

        $validator  = Validator::make($request->all(), $rules, [
            'schedules.required' => 'Please add at least one day/time slot.',
        ]);
        $schedules  = $request->input('schedules', []);
        $composite  = [
            'department_id'    => $request->department_id,
            'faculty_id'       => $request->faculty_id,
            'semester_id'      => $request->semester_id,
            'student_batch_id' => $request->student_batch_id,
            'subject_id'       => $request->subject_id,
        ];

        $validator->after(function ($v) use ($schedules, $composite) {
            // A) sanity + intra-submission overlaps per day
            $byDay = [];
            foreach ($schedules as $i => $slot) {
                if (!isset($slot['day_of_week'], $slot['start_time'], $slot['end_time'])) {
                    $v->errors()->add("schedules.$i.day_of_week", 'Row '.($i+1).': Please complete this row.');
                    continue;
                }
                if (strtotime($slot['start_time']) >= strtotime($slot['end_time'])) {
                    $v->errors()->add("schedules.$i.end_time", 'Row '.($i+1).': End time must be after start time.');
                    continue;
                }
                $byDay[$slot['day_of_week']][] = [
                    'i' => $i,
                    's' => strtotime($slot['start_time']),
                    'e' => strtotime($slot['end_time']),
                ];
            }
            foreach ($byDay as $day => $rows) {
                usort($rows, fn($a,$b) => $a['s'] <=> $b['s']);
                for ($k = 1; $k < count($rows); $k++) {
                    if ($rows[$k]['s'] < $rows[$k-1]['e']) {
                        $v->errors()->add(
                            "schedules.{$rows[$k]['i']}.start_time",
                            'Row '.($rows[$k]['i']+1).": Overlaps with another row on $day within this submission."
                        );
                    }
                }
            }

            // B) DB conflict (same composite + day overlap)
            foreach ($schedules as $i => $slot) {
                if (empty($slot['day_of_week']) || empty($slot['start_time']) || empty($slot['end_time'])) {
                    continue;
                }

                $conflict = ClassRoutine::with(['teacher:id,first_name,last_name','subject:id,title','batch:id,title'])
                    ->where($composite)
                    ->where('day_of_week', $slot['day_of_week'])
                    ->where(function ($q) use ($slot) {
                        $q->whereBetween('start_time', [$slot['start_time'], $slot['end_time']])
                          ->orWhereBetween('end_time',   [$slot['start_time'], $slot['end_time']])
                          ->orWhere(function ($qq) use ($slot) {
                              $qq->where('start_time', '<=', $slot['start_time'])
                                 ->where('end_time',   '>=', $slot['end_time']);
                          });
                    })
                    ->first();

                if ($conflict) {
                    $teacher = trim(($conflict->teacher->first_name ?? '').' '.($conflict->teacher->last_name ?? ''));
                    $teacher = $teacher !== '' ? $teacher : ('Teacher #'.$conflict->teacher_id);
                    $time    = substr($conflict->start_time, 0, 5).'–'.substr($conflict->end_time, 0, 5);
                    $subject = $conflict->subject->title ?? 'N/A';
                    $batch   = $conflict->batch->title   ?? 'N/A';
                    $room    = $conflict->room_number    ?? 'N/A';

                    $msg = sprintf(
                        'Row %d: Already scheduled for this Department/Faculty/Semester/Batch/Subject on %s %s (Subject: %s, Teacher: %s, Room: %s, Batch: %s).',
                        $i + 1, $slot['day_of_week'], $time, $subject, $teacher, $room, $batch
                    );
                    $v->errors()->add("schedules.$i.start_time", $msg);
                }

                // C) Teacher double-booking anywhere
                if (!empty($slot['teacher_id'])) {
                    $tRow = ClassRoutine::with(['teacher:id,first_name,last_name','subject:id,title','batch:id,title'])
                        ->where('teacher_id', $slot['teacher_id'])
                        ->where('day_of_week', $slot['day_of_week'])
                        ->where(function ($q) use ($slot) {
                            $q->whereBetween('start_time', [$slot['start_time'], $slot['end_time']])
                              ->orWhereBetween('end_time',   [$slot['start_time'], $slot['end_time']])
                              ->orWhere(function ($qq) use ($slot) {
                                  $qq->where('start_time', '<=', $slot['start_time'])
                                     ->where('end_time',   '>=', $slot['end_time']);
                              });
                        })
                        ->first();

                    if ($tRow) {
                        $tName  = trim(($tRow->teacher->first_name ?? '').' '.($tRow->teacher->last_name ?? ''));
                        $tName  = $tName !== '' ? $tName : ('Teacher #'.$slot['teacher_id']);
                        $tTime  = substr($tRow->start_time, 0, 5).'–'.substr($tRow->end_time, 0, 5);
                        $tSub   = $tRow->subject->title ?? 'N/A';
                        $tBatch = $tRow->batch->title   ?? 'N/A';
                        $tRoom  = $tRow->room_number    ?? 'N/A';

                        $msg = sprintf(
                            'Row %d: %s is already scheduled on %s %s (Subject: %s, Batch: %s, Room: %s).',
                            $i + 1, $tName, $slot['day_of_week'], $tTime, $tSub, $tBatch, $tRoom
                        );
                        $v->errors()->add("schedules.$i.teacher_id", $msg);
                    }
                }
            }
        });

        if ($validator->fails()) {
            $request->session()->flash($this->message_danger, 'Please fix the highlighted errors below.');
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $schedules) {
                foreach ($schedules as $slot) {
                    ClassRoutine::create([
                        'department_id'     => $request->department_id,
                        'faculty_id'        => $request->faculty_id,
                        'semester_id'       => $request->semester_id,
                        'student_batch_id'  => $request->student_batch_id,
                        'subject_id'        => $request->subject_id,
                        'teacher_id'        => $slot['teacher_id'],
                        'day_of_week'       => $slot['day_of_week'],
                        'start_time'        => $slot['start_time'],
                        'end_time'          => $slot['end_time'],
                        'room_number'       => $slot['room_number'],
                        'period'            => $slot['period'] ?? null,
                        'status'            => 1,
                        'created_by'        => auth()->id(),
                    ]);
                }
            });
            $request->session()->flash($this->message_success, $this->panel.' Created Successfully.');
            return redirect()->route($this->base_route.'.index');
        } catch (\Throwable $e) {
            Log::error('Routine store failed', ['error' => $e->getMessage(), 'payload' => $request->all()]);
            $request->session()->flash($this->message_danger, 'Failed to create routines.');
            return back()->withInput();
        }
    }

    /**
     * Manage: composite editor (multi-row upsert) for a selected composite + subject.
     */
    public function manage(Request $request)
    {
        try {
            $data = [];
            $data['department_heads'] = DepartmentHead::active()->pluck('department_head', 'id');
            //$data['teachers']         = Staff::active()->pluck('first_name', 'id');
            $data['teachers'] = Staff::active()
                ->select('id', 'first_name', 'middle_name', 'last_name')
                ->orderBy('first_name')
                ->get()
                ->mapWithKeys(function ($staff) {
                    $fullName = trim($staff->first_name . ' ' . ($staff->middle_name ?? '') . ' ' . $staff->last_name);
                    return [$staff->id => $fullName];
                });

            // Optional prefill
            $data['prefill'] = [
                'department_head_id' => $request->department_head_id,
                'department_id'      => $request->department_id,
                'faculty_id'         => $request->faculty_id,
                'semester_id'        => $request->semester_id,
                'student_batch_id'   => $request->student_batch_id,
                'subject_id'         => $request->subject_id,
            ];

            return view(parent::loadDataToView($this->view_path.'.manage'), $data);
        } catch (\Throwable $e) {
            Log::error('Routine manage failed', ['error' => $e->getMessage()]);
            $request->session()->flash($this->message_danger, 'Failed to load manage screen.');
            return back();
        }
    }

    /**
     * AJAX: Get existing schedules for the selected composite (dept/faculty/semester/batch/subject)
     */
    public function getExistingSchedules(Request $request)
    {
        try {
            $request->validate([
                'department_id'    => 'required|integer|exists:departments,id',
                'faculty_id'       => 'required|integer|exists:faculties,id',
                'semester_id'      => 'required|integer|exists:semesters,id',
                'student_batch_id' => 'required|integer|exists:student_batches,id',
                'subject_id'       => 'required|integer|exists:subjects,id',
            ]);

            $routines = ClassRoutine::with('teacher:id,first_name,last_name')
                ->where('department_id',    $request->department_id)
                ->where('faculty_id',       $request->faculty_id)
                ->where('semester_id',      $request->semester_id)
                ->where('student_batch_id', $request->student_batch_id)
                ->where('subject_id',       $request->subject_id)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
                ->map(function ($r) {
                    return [
                        'id'          => $r->id,
                        'day_of_week' => $r->day_of_week,
                        'period'      => $r->period,
                        'start_time'  => substr($r->start_time, 0, 5),
                        'end_time'    => substr($r->end_time, 0, 5),
                        'room_number' => $r->room_number,
                        'teacher_id'  => $r->teacher_id,
                        'teacher'     => trim(($r->teacher->first_name ?? '').' '.($r->teacher->last_name ?? '')),
                    ];
                })->values();

            return response()->json([
                'count'     => $routines->count(),
                'schedules' => $routines,
            ]);
        } catch (\Throwable $e) {
            Log::error('getExistingSchedules failed', ['error' => $e->getMessage(), 'payload' => $request->all()]);
            return response()->json(['message' => 'Failed to fetch schedules'], 500);
        }
    }

    /**
     * Save: bulk upsert for manage() with conflict checks.
     */
    public function save(Request $request)
    {
        $rules = [
            'department_id'     => 'required|exists:departments,id',
            'faculty_id'        => 'required|exists:faculties,id',
            'semester_id'       => 'required|exists:semesters,id',
            'student_batch_id'  => 'required|exists:student_batches,id',
            'subject_id'        => 'required|exists:subjects,id',

            'schedules'               => 'required|array|min:1',
            'schedules.*.id'          => 'nullable|integer|exists:class_routines,id',
            'schedules.*.day_of_week' => 'required|string|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'schedules.*.period'      => 'nullable|string|max:50',
            'schedules.*.start_time'  => 'required|date_format:H:i',
            'schedules.*.end_time'    => 'required|date_format:H:i',
            'schedules.*.room_number' => 'required|string|max:255',
            'schedules.*.teacher_id'  => 'required|exists:staff,id',
        ];

        $validator = Validator::make($request->all(), $rules, [
            'schedules.required' => 'Please add at least one day/time slot.',
        ]);

        $composite = [
            'department_id'    => $request->department_id,
            'faculty_id'       => $request->faculty_id,
            'semester_id'      => $request->semester_id,
            'student_batch_id' => $request->student_batch_id,
            'subject_id'       => $request->subject_id,
        ];
        $schedules = $request->input('schedules', []);

        $validator->after(function ($v) use ($schedules, $composite) {
            // 1) sanity + intra overlaps
            $byDay = [];
            foreach ($schedules as $i => $slot) {
                if (!isset($slot['day_of_week'], $slot['start_time'], $slot['end_time'])) {
                    $v->errors()->add("schedules.$i.day_of_week", 'Row '.($i+1).': Please complete this row.');
                    continue;
                }
                if (strtotime($slot['start_time']) >= strtotime($slot['end_time'])) {
                    $v->errors()->add("schedules.$i.end_time", 'Row '.($i+1).': End time must be after start time.');
                    continue;
                }
                $byDay[$slot['day_of_week']][] = ['i' => $i, 's' => strtotime($slot['start_time']), 'e' => strtotime($slot['end_time'])];
            }
            foreach ($byDay as $day => $rows) {
                usort($rows, fn($a,$b) => $a['s'] <=> $b['s']);
                for ($k = 1; $k < count($rows); $k++) {
                    if ($rows[$k]['s'] < $rows[$k-1]['e']) {
                        $v->errors()->add(
                            "schedules.{$rows[$k]['i']}.start_time",
                            'Row '.($rows[$k]['i']+1).": Overlaps with another row on $day within this submission."
                        );
                    }
                }
            }

            // 2) DB conflicts within same composite
            foreach ($schedules as $i => $slot) {
                if (empty($slot['day_of_week']) || empty($slot['start_time']) || empty($slot['end_time'])) {
                    continue;
                }

                $q = ClassRoutine::with(['teacher:id,first_name,last_name','subject:id,title','batch:id,title'])
                    ->where($composite)
                    ->where('day_of_week', $slot['day_of_week'])
                    ->where(function ($q) use ($slot) {
                        $q->whereBetween('start_time', [$slot['start_time'], $slot['end_time']])
                          ->orWhereBetween('end_time',   [$slot['start_time'], $slot['end_time']])
                          ->orWhere(function ($qq) use ($slot) {
                              $qq->where('start_time', '<=', $slot['start_time'])
                                 ->where('end_time',   '>=', $slot['end_time']);
                          });
                    });
                if (!empty($slot['id'])) {
                    $q->where('id', '!=', $slot['id']);
                }

                if ($conflict = $q->first()) {
                    $teacher = trim(($conflict->teacher->first_name ?? '').' '.($conflict->teacher->last_name ?? ''));
                    $teacher = $teacher !== '' ? $teacher : ('Teacher #'.$conflict->teacher_id);
                    $time    = substr($conflict->start_time, 0, 5).'–'.substr($conflict->end_time, 0, 5);
                    $subject = $conflict->subject->title ?? 'N/A';
                    $batch   = $conflict->batch->title   ?? 'N/A';
                    $room    = $conflict->room_number    ?? 'N/A';

                    $msg = sprintf(
                        'Row %d: Already scheduled for this Department/Faculty/Semester/Batch/Subject on %s %s (Subject: %s, Teacher: %s, Room: %s, Batch: %s).',
                        $i + 1, $slot['day_of_week'], $time, $subject, $teacher, $room, $batch
                    );
                    $v->errors()->add("schedules.$i.start_time", $msg);
                }

                // 3) Teacher double-booking anywhere
                if (!empty($slot['teacher_id'])) {
                    $qt = ClassRoutine::with(['teacher:id,first_name,last_name','subject:id,title','batch:id,title'])
                        ->where('teacher_id', $slot['teacher_id'])
                        ->where('day_of_week', $slot['day_of_week'])
                        ->where(function ($q) use ($slot) {
                            $q->whereBetween('start_time', [$slot['start_time'], $slot['end_time']])
                              ->orWhereBetween('end_time',   [$slot['start_time'], $slot['end_time']])
                              ->orWhere(function ($qq) use ($slot) {
                                  $qq->where('start_time', '<=', $slot['start_time'])
                                     ->where('end_time',   '>=', $slot['end_time']);
                              });
                        });
                    if (!empty($slot['id'])) {
                        $qt->where('id', '!=', $slot['id']);
                    }

                    if ($tRow = $qt->first()) {
                        $tName  = trim(($tRow->teacher->first_name ?? '').' '.($tRow->teacher->last_name ?? ''));
                        $tName  = $tName !== '' ? $tName : ('Teacher #'.$slot['teacher_id']);
                        $tTime  = substr($tRow->start_time, 0, 5).'–'.substr($tRow->end_time, 0, 5);
                        $tSub   = $tRow->subject->title ?? 'N/A';
                        $tBatch = $tRow->batch->title   ?? 'N/A';
                        $tRoom  = $tRow->room_number    ?? 'N/A';

                        $msg = sprintf(
                            'Row %d: %s is already scheduled on %s %s (Subject: %s, Batch: %s, Room: %s).',
                            $i + 1, $tName, $slot['day_of_week'], $tTime, $tSub, $tBatch, $tRoom
                        );
                        $v->errors()->add("schedules.$i.teacher_id", $msg);
                    }
                }
            }
        });

        if ($validator->fails()) {
            $request->session()->flash($this->message_danger, 'Please fix the highlighted errors.');
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($composite, $schedules) {
                $existing     = ClassRoutine::where($composite)->pluck('id')->all();
                $submittedIds = [];

                foreach ($schedules as $slot) {
                    $payload = $composite + [
                        'teacher_id'  => $slot['teacher_id'],
                        'day_of_week' => $slot['day_of_week'],
                        'start_time'  => $slot['start_time'],
                        'end_time'    => $slot['end_time'],
                        'room_number' => $slot['room_number'],
                        'period'      => $slot['period'] ?? null,
                        'status'      => 1,
                    ];

                    if (!empty($slot['id'])) {
                        $submittedIds[] = (int) $slot['id'];
                        ClassRoutine::where('id', $slot['id'])->update($payload + [
                            'last_updated_by' => auth()->id(),
                        ]);
                    } else {
                        $created = ClassRoutine::create($payload + [
                            'created_by' => auth()->id(),
                        ]);
                        $submittedIds[] = $created->id;
                    }
                }

                // delete removed rows
                $toDelete = array_diff($existing, $submittedIds);
                if (!empty($toDelete)) {
                    ClassRoutine::whereIn('id', $toDelete)->delete();
                }
            });

            $request->session()->flash($this->message_success, 'Schedule saved successfully.');
            return redirect()->route($this->base_route.'.index');
        } catch (\Throwable $e) {
            Log::error('Routine save failed', ['error' => $e->getMessage(), 'payload' => $request->all()]);
            $request->session()->flash($this->message_danger, 'Failed to save schedule.');
            return back()->withInput();
        }
    }

    /**
     * Show single row
     */
    public function show($id)
    {
        try {
            $data = [];
            $data['routine'] = ClassRoutine::with([
                'department','faculty','semester','batch','subject','teacher'
            ])->findOrFail($id);

            return view(parent::loadDataToView($this->view_path.'.show'), $data);
        } catch (\Throwable $e) {
            Log::error('Routine show failed', ['error' => $e->getMessage(), 'id' => $id]);
            session()->flash($this->message_danger, 'Failed to load routine.');
            return back();
        }
    }

    /**
     * Edit single row
     */
    public function edit($id)
    {
        try {
            $routine = ClassRoutine::findOrFail($id);

            // Try to find any Department Head that owns this Department (optional, nice to have)
            // If none found, we simply omit it – the Manage page can still work.
            $departmentHeadId = \App\Models\DepartmentHead::whereHas('departments', function ($q) use ($routine) {
                    $q->where('departments.id', $routine->department_id);
                })
                ->value('id');

            $params = array_filter([
                'department_head_id' => $departmentHeadId,              // optional
                'department_id'      => $routine->department_id,
                'faculty_id'         => $routine->faculty_id,
                'semester_id'        => $routine->semester_id,
                'student_batch_id'   => $routine->student_batch_id,
                'subject_id'         => $routine->subject_id,
            ], fn($v) => !is_null($v) && $v !== '');

            // Redirect to Manage, which will preselect hierarchy and load schedules
            return redirect()->route('routine.manage', $params);

        } catch (\Throwable $e) {
            Log::error('Routine edit->manage redirect failed', ['id' => $id, 'error' => $e->getMessage()]);
            session()->flash($this->message_danger, 'Failed to load edit form.');
            return back();
        }
    }



    /**
     * Update single row
     */
    public function update(Request $request, $id)
    {
        try {
            $routine = ClassRoutine::findOrFail($id);

            $request->validate([
                'department_id'    => 'required|exists:departments,id',
                'faculty_id'       => 'required|exists:faculties,id',
                'semester_id'      => 'required|exists:semesters,id',
                'student_batch_id' => 'required|exists:student_batches,id',
                'subject_id'       => 'required|exists:subjects,id',
                'teacher_id'       => 'required|exists:staff,id',
                'day_of_week'      => 'required|string|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
                'start_time'       => 'required|string',
                'end_time'         => 'required|string',
                'room_number'      => 'required|string|max:255',
                'period'           => 'nullable|string|max:50',
            ]);

            // prevent overlap for the SAME composite
            $key = [
                'department_id'    => $request->department_id,
                'faculty_id'       => $request->faculty_id,
                'semester_id'      => $request->semester_id,
                'student_batch_id' => $request->student_batch_id,
                'subject_id'       => $request->subject_id,
            ];

            $conflict = ClassRoutine::where($key)
                ->where('day_of_week', $request->day_of_week)
                ->where('id', '!=', $id)
                ->where(function ($q) use ($request) {
                    $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time',   [$request->start_time, $request->end_time])
                      ->orWhere(function ($qq) use ($request) {
                          $qq->where('start_time', '<=', $request->start_time)
                             ->where('end_time',   '>=', $request->end_time);
                      });
                })->exists();

            if ($conflict) {
                $request->session()->flash($this->message_danger, 'Time conflict detected with existing routine for the same subject/cohort!');
                return back()->withInput();
            }

            // teacher double-booking
            $teacherClash = ClassRoutine::where('teacher_id', $request->teacher_id)
                ->where('day_of_week', $request->day_of_week)
                ->where('id', '!=', $id)
                ->where(function ($q) use ($request) {
                    $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time',   [$request->start_time, $request->end_time])
                      ->orWhere(function ($qq) use ($request) {
                          $qq->where('start_time', '<=', $request->start_time)
                             ->where('end_time',   '>=', $request->end_time);
                      });
                })->exists();

            if ($teacherClash) {
                $request->session()->flash($this->message_danger, 'Selected teacher is already scheduled at this time.');
                return back()->withInput();
            }

            $routine->update($request->all() + ['last_updated_by' => auth()->id()]);

            $request->session()->flash($this->message_success, $this->panel.' updated successfully.');
            return redirect()->route($this->base_route.'.index');
        } catch (\Throwable $e) {
            Log::error('Routine update failed', ['error' => $e->getMessage(), 'id' => $id, 'payload' => $request->all()]);
            $request->session()->flash($this->message_danger, 'Failed to update routine.');
            return back()->withInput();
        }
    }

    /**
     * Destroy single row
     */
    public function destroy($id)
    {
        try {
            $routine = ClassRoutine::findOrFail($id);
            $routine->delete();

            session()->flash($this->message_success, $this->panel.' deleted successfully.');
            //return redirect()->route($this->base_route.'.index');
            return back();
        } catch (\Throwable $e) {
            Log::error('Routine destroy failed', ['error' => $e->getMessage(), 'id' => $id]);
            session()->flash($this->message_danger, 'Failed to delete routine.');
            return back();
        }
    }

    /* =======================
     * PRINT HELPERS + VIEWS
     * ======================= */

    protected function fetchAndGroupRoutines(array $filters = [])
    {
        $q = ClassRoutine::with([
            'department:id,department',
            'faculty:id,faculty',
            'semester:id,semester',
            'batch:id,title',
            'subject:id,title,code',
            'teacher:id,first_name,last_name',
        ]);

        foreach (['department_id','faculty_id','semester_id','student_batch_id','subject_id'] as $f) {
            if (isset($filters[$f]) && $filters[$f] !== null && $filters[$f] !== '') {
                $q->where($f, $filters[$f]);
            }
        }

        if (!empty($filters['day_of_week'])) {
            $q->where('day_of_week', $filters['day_of_week']);
        }

        $routines = $q->where('status', 1)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return $routines->groupBy('day_of_week');
    }

    protected function buildPrintMeta(array $filters)
    {
        return [
            'department' => isset($filters['department_id'])    ? Department::find($filters['department_id'])       : null,
            'faculty'    => isset($filters['faculty_id'])       ? Faculty::find($filters['faculty_id'])             : null,
            'semester'   => isset($filters['semester_id'])      ? Semester::find($filters['semester_id'])           : null,
            'batch'      => isset($filters['student_batch_id']) ? StudentBatch::find($filters['student_batch_id'])  : null,
            'subject'    => isset($filters['subject_id'])       ? Subject::find($filters['subject_id'])             : null,
        ];
    }

    // Department
    public function printDepartment($departmentId)
    {
        try {
            $filters = ['department_id' => $departmentId];
            $grouped = $this->fetchAndGroupRoutines($filters);
            $meta    = $this->buildPrintMeta($filters);

            return view(parent::loadDataToView('print.'.$this->view_path.'.grid'), [
                'groupedRoutines' => $grouped,
                'scope'           => 'Department',
                'meta'            => $meta,
            ]);
        } catch (\Throwable $e) {
            Log::error('printDepartment failed', ['error' => $e->getMessage(), 'departmentId' => $departmentId]);
            session()->flash($this->message_danger, 'Failed to render print view.');
            return back();
        }
    }

    // Faculty
    public function printFaculty($departmentId, $facultyId)
    {
        try {
            $filters = ['department_id' => $departmentId, 'faculty_id' => $facultyId];
            $grouped = $this->fetchAndGroupRoutines($filters);
            $meta    = $this->buildPrintMeta($filters);

            return view(parent::loadDataToView('print.'.$this->view_path.'.grid'), [
                'groupedRoutines' => $grouped,
                'scope'           => 'Faculty/Program',
                'meta'            => $meta,
            ]);
        } catch (\Throwable $e) {
            Log::error('printFaculty failed', ['error' => $e->getMessage(), 'departmentId' => $departmentId, 'facultyId' => $facultyId]);
            session()->flash($this->message_danger, 'Failed to render print view.');
            return back();
        }
    }

    // Semester
    public function printSemester($departmentId, $facultyId, $semesterId)
    {
        try {
            $filters = ['department_id' => $departmentId, 'faculty_id' => $facultyId, 'semester_id' => $semesterId];
            $grouped = $this->fetchAndGroupRoutines($filters);
            $meta    = $this->buildPrintMeta($filters);

            return view(parent::loadDataToView('print.'.$this->view_path.'.grid'), [
                'groupedRoutines' => $grouped,
                'scope'           => 'Semester',
                'meta'            => $meta,
            ]);
        } catch (\Throwable $e) {
            Log::error('printSemester failed', ['error' => $e->getMessage(), 'ids' => func_get_args()]);
            session()->flash($this->message_danger, 'Failed to render print view.');
            return back();
        }
    }

    // Batch
    public function printBatch($departmentId, $batchId)
    {
        try {
            $filters = ['department_id' => $departmentId, 'student_batch_id' => $batchId];
            $grouped = $this->fetchAndGroupRoutines($filters);
            $meta    = $this->buildPrintMeta($filters);

            return view(parent::loadDataToView('print.'.$this->view_path.'.grid'), [
                'groupedRoutines' => $grouped,
                'scope'           => 'Batch',
                'meta'            => $meta,
            ]);
        } catch (\Throwable $e) {
            Log::error('printBatch failed', ['error' => $e->getMessage(), 'ids' => func_get_args()]);
            session()->flash($this->message_danger, 'Failed to render print view.');
            return back();
        }
    }

    // Subject – all batches
    public function printSubjectAllBatches($departmentId, $facultyId, $semesterId, $subjectId)
    {
        try {
            $filters = [
                'department_id' => $departmentId,
                'faculty_id'    => $facultyId,
                'semester_id'   => $semesterId,
                'subject_id'    => $subjectId,
            ];
            $grouped = $this->fetchAndGroupRoutines($filters);
            $meta    = $this->buildPrintMeta($filters);

            return view(parent::loadDataToView('print.'.$this->view_path.'.grid'), [
                'groupedRoutines' => $grouped,
                'scope'           => 'Subject (All Batches)',
                'meta'            => $meta,
            ]);
        } catch (\Throwable $e) {
            Log::error('printSubjectAllBatches failed', ['error' => $e->getMessage(), 'ids' => func_get_args()]);
            session()->flash($this->message_danger, 'Failed to render print view.');
            return back();
        }
    }

    // Subject – specific batch
    public function printSubjectForBatch($departmentId, $facultyId, $semesterId, $subjectId, $batchId)
    {
        try {
            $filters = [
                'department_id'    => $departmentId,
                'faculty_id'       => $facultyId,
                'semester_id'      => $semesterId,
                'subject_id'       => $subjectId,
                'student_batch_id' => $batchId,
            ];
            $grouped = $this->fetchAndGroupRoutines($filters);
            $meta    = $this->buildPrintMeta($filters);

            return view(parent::loadDataToView('print.'.$this->view_path.'.grid'), [
                'groupedRoutines' => $grouped,
                'scope'           => 'Subject (Batch)',
                'meta'            => $meta,
            ]);
        } catch (\Throwable $e) {
            Log::error('printSubjectForBatch failed', ['error' => $e->getMessage(), 'ids' => func_get_args()]);
            session()->flash($this->message_danger, 'Failed to render print view.');
            return back();
        }
    }

    

    /* =======================
     * IMPORT / EXPORT
     * ======================= */
    public function importForm()
    {
        try {
            $data = [
                'department_heads' => DepartmentHead::orderBy('department_head')->pluck('department_head','id'),
                'departments' => Department::orderBy('department')->pluck('department','id'),
                'faculties'   => Faculty::orderBy('faculty')->pluck('faculty','id'),
                'semesters'   => Semester::orderBy('semester')->pluck('semester','id'),
                'batches'     => StudentBatch::orderBy('title')->pluck('title','id'),
                'subjects'    => Subject::orderBy('title')->pluck('title','id'),
            ];
            return view(parent::loadDataToView($this->view_path.'.import'), $data);
        } catch (\Throwable $e) {
            Log::error('importForm failed', ['error' => $e->getMessage()]);
            session()->flash($this->message_danger, 'Failed to load import form.');
            return back();
        }
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file'              => 'required|file|mimes:xlsx,csv,xls,txt',
            'mode'              => 'required|in:insert,upsert,replace',
            'assume_unique_by'  => 'required|in:composite,subject_teacher_day_time',
            // Optional scoping filters (useful when "replace" mode):
            'department_id'     => 'nullable|exists:departments,id',
            'faculty_id'        => 'nullable|exists:faculties,id',
            'semester_id'       => 'nullable|exists:semesters,id',
            'student_batch_id'  => 'nullable|exists:student_batches,id',
        ]);

        $scopes = $request->only(['department_id','faculty_id','semester_id','student_batch_id']);

        // If replace: delete existing in scope before insert
        if ($request->mode === 'replace') {
            try {
                $q = ClassRoutine::query();
                foreach ($scopes as $k => $v) {
                    if (!empty($v)) $q->where($k, $v);
                }
                $q->delete();
            } catch (\Throwable $e) {
                Log::error('importStore replace delete failed', ['error' => $e->getMessage(), 'scopes' => $scopes]);
                $request->session()->flash($this->message_danger, 'Failed to clear existing data for replace mode.');
                return back()->withInput();
            }
        }

        $import = new ClassRoutineImport(
            $request->mode,
            $request->assume_unique_by,
            auth()->id()
        );

        try {
            Excel::import($import, $request->file('file'));
        } catch (\Throwable $e) {
            Log::error('Excel import failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['file' => 'Import failed: '.$e->getMessage()]);
        }

        $summary = $import->summary();

        $request->session()->flash(
            $this->message_success,
            "Import done. Inserted: {$summary['inserted']}, Updated: {$summary['updated']}, Skipped: {$summary['skipped']}, Errors: {$summary['errors_count']}"
        );
        if (!empty($summary['errors'])) {
            $request->session()->flash($this->message_danger, 'Some rows could not be imported. Please review the error list.');
        }

        return redirect()->route($this->base_route.'.index')
            ->with('import_errors', $summary['errors']);
    }

    public function exportForm()
    {
        try {
            $data = [
                // needed by the export.blade to render the first hierarchy column
                'department_heads' => DepartmentHead::active()->pluck('department_head','id'),

                // (optional) keep these only if your export view uses them elsewhere
                'departments' => Department::orderBy('department')->pluck('department','id'),
                'faculties'   => Faculty::orderBy('faculty')->pluck('faculty','id'),
                'semesters'   => Semester::orderBy('semester')->pluck('semester','id'),
                'batches'     => StudentBatch::orderBy('title')->pluck('title','id'),
                'subjects'    => Subject::orderBy('title')->pluck('title','id'),
            ];

            return view(parent::loadDataToView($this->view_path.'.export'), $data);
        } catch (\Throwable $e) {
            \Log::error('exportForm failed', ['error' => $e->getMessage()]);
            session()->flash($this->message_danger, 'Failed to load export form.');
            return back();
        }
    }


    public function exportDownload(Request $request)
    {
        $request->validate([
            'format'           => 'required|in:xlsx,csv',
            'department_id'    => 'nullable|exists:departments,id',
            'faculty_id'       => 'nullable|exists:faculties,id',
            'semester_id'      => 'nullable|exists:semesters,id',
            'student_batch_id' => 'nullable|exists:student_batches,id',
            'subject_id'       => 'nullable|exists:subjects,id',
            'teacher_id'       => 'nullable|exists:staff,id', // table is `staff`
            'day_of_week'      => 'nullable|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'status'           => 'nullable|in:0,1',
        ]);

        // Keep only non-empty values to avoid weird WHERE "" cases.
        $filters = array_filter(
            $request->only([
                'department_id','faculty_id','semester_id','student_batch_id',
                'subject_id','teacher_id','day_of_week','status'
            ]),
            fn($v) => $v !== null && $v !== ''
        );

        try {
            // Clean any output buffers (prevents partial-output/network errors)
            if (function_exists('ob_get_level')) {
                while (ob_get_level() > 0) { @ob_end_clean(); }
            }
            @ini_set('zlib.output_compression', 'Off');
            @set_time_limit(0);

            // Preflight: storage write test (Windows/XAMPP sometimes blocks)
            if (!Storage::exists('exports')) {
                Storage::makeDirectory('exports');
            }
            try {
                Storage::put('exports/.write_test', 'ok');
                Storage::delete('exports/.write_test');
            } catch (\Throwable $w) {
                throw new \RuntimeException('Storage not writable: '.storage_path('app/exports'));
            }

            $export     = new ClassRoutineExport($filters);
            $ext        = $request->format;   // xlsx | csv
            $writerType = $ext === 'csv' ? ExcelFormat::CSV : ExcelFormat::XLSX;
            $mime       = $ext === 'csv'
                ? 'text/csv'
                : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

            // Generate to disk first (no streaming while generating)
            $storedRelativePath = 'exports/'.uniqid('class_routines_', true).'.'.$ext;

            try {
                ExcelFacade::store($export, $storedRelativePath, null, $writerType);
            } catch (\Throwable $xlsExc) {
                // If XLSX/CSV writer fails, fallback to a safe streamed CSV
                Log::warning('Excel::store failed, falling back to streamed CSV', [
                    'message' => $xlsExc->getMessage(),
                ]);

                $downloadName = 'class_routines_'.now()->format('Ymd_His').'.csv';
                $rows         = $export->array();
                $headings     = $export->headings();

                return response()->streamDownload(function () use ($headings, $rows) {
                    $out = fopen('php://output', 'w');
                    // If your CSV needs UTF-8 BOM for Excel on Windows:
                    // fwrite($out, "\xEF\xBB\xBF");
                    fputcsv($out, $headings);
                    foreach ($rows as $r) {
                        fputcsv($out, $r);
                    }
                    fclose($out);
                }, $downloadName, [
                    'Content-Type'          => 'text/csv',
                    'Cache-Control'         => 'private, must-revalidate, max-age=0',
                    'Pragma'                => 'public',
                    'Expires'               => '0',
                    'X-Fallback-Reason'     => 'excel_store_failed',
                ]);
            }

            $downloadName = 'class_routines_'.now()->format('Ymd_His').'.'.$ext;

            return response()->download(
                storage_path('app/'.$storedRelativePath),
                $downloadName,
                [
                    'Content-Type'      => $mime,
                    'X-Accel-Buffering' => 'no',
                    'Cache-Control'     => 'private, must-revalidate, max-age=0',
                    'Pragma'            => 'public',
                    'Expires'           => '0',
                ]
            )->deleteFileAfterSend(true);

        } catch (\Throwable $e) {
            Log::error('exportDownload failed', [
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'filters' => $filters,
            ]);

            $msg = 'Failed to generate export file.';
            // In local/dev, show the real reason to speed up debugging
            if (config('app.debug')) {
                $msg .= ' '.$e->getMessage();
            }

            $request->session()->flash($this->message_danger, $msg);
            return back()->withInput();
        }
    }

    public function exportTemplate()
{
    try {
        // Clear any output buffering that can corrupt the download
        if (function_exists('ob_get_level')) {
            while (ob_get_level() > 0) { @ob_end_clean(); }
        }

        // Ensure export directory exists
        $disk = Storage::disk('local');
        $dir  = 'exports';
        if (! $disk->exists($dir)) {
            $disk->makeDirectory($dir);
        }

        // Build a unique temp path and store XLSX there
        $tmpRelPath = $dir.'/class_routine_template_'.now()->format('Ymd_His').'.xlsx';
        Excel::store(
            new \App\Exports\Academic\ClassRoutineTemplateExport,
            $tmpRelPath,
            'local',
            ExcelWriter::XLSX
        );

        $abs = storage_path('app/'.$tmpRelPath);
        if (!is_file($abs) || filesize($abs) === 0) {
            throw new \RuntimeException('Template file not written. Check storage permissions.');
        }

        // Stream the file to the browser and delete after send
        return response()->download(
            $abs,
            'class_routine_template.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);

    } catch (\Throwable $e) {
        Log::error('exportTemplate failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        // Optional CSV fallback so users still get *something* to download if XLSX writer fails
        try {
            $dir  = 'exports';
            $csvRel = $dir.'/class_routine_template_'.now()->format('Ymd_His').'.csv';
            if (!Storage::disk('local')->exists($dir)) {
                Storage::disk('local')->makeDirectory($dir);
            }

            $tmpl = new \App\Exports\Academic\ClassRoutineTemplateExport;
            $head = $tmpl->headings();
            $rows = $tmpl->array();

            $csvAbs = storage_path('app/'.$csvRel);
            $fh = fopen($csvAbs, 'w');
            fputcsv($fh, $head);
            foreach ($rows as $r) { fputcsv($fh, $r); }
            fclose($fh);

            return response()->download(
                $csvAbs,
                'class_routine_template.csv',
                ['Content-Type' => 'text/csv']
            )->deleteFileAfterSend(true);
        } catch (\Throwable $fallback) {
            Log::error('exportTemplate CSV fallback failed', ['error' => $fallback->getMessage()]);
        }

        // Friendly message; show exception in debug
        $msg = config('app.debug')
            ? ('Failed to download template: '.$e->getMessage())
            : 'Failed to download template.';
        session()->flash($this->message_danger, $msg);
        return back();
    }
}

}
