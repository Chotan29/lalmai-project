<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
/**
 * Created by PhpStorm.
 * User: Umesh Kumar Yadav
 * Date: 03/03/2018
 * Time: 7:05 PM
 */
namespace App\Http\Controllers\Examination;

use App\Http\Controllers\CollegeBaseController;
use App\Models\Exam;
use App\Models\ExamMarkLedger;
use App\Models\ExamSchedule;
use App\Models\Faculty;
use App\Models\Month;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Year;
use Illuminate\Http\Request;
use URL;

class ExamMarkLedgerController extends CollegeBaseController
{
    protected $base_route = 'exam.mark-ledger';
    protected $view_path = 'examination.mark-ledger';
    protected $panel = 'Exam Mark Ledger';
    protected $filter_query = [];

    public function __construct()
    {

    }

    /**
     * Teacher detection based on the role_user pivot (Entrust roles).
     * Admin / super-admin always get full (unlocked) access, even if their
     * legacy users.role_id column is 5. Falls back to the legacy column
     * only when the user has no matching pivot role.
     */
    private function isTeacherUser()
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        /*Pivot admin / super-admin => never treated as locked teacher*/
        if ($user->hasRole(['super-admin', 'admin'])) {
            return false;
        }

        /*Pivot teacher => locked to own entries*/
        if ($user->hasRole('teacher')) {
            return true;
        }

        /*Legacy fallback (old accounts without pivot role)*/
        return $user->role_id == 5;
    }

    public function index(Request $request)
    {
        $data = [];

        $year = $request->get('year');
        $month = $request->get('month');
        $exam = $request->get('exam');
        $faculty = $request->get('faculty');
        $semester = $request->get('semester');
        $subject = $request->get('subject');

        if($year && $month && $exam && $faculty && $semester && $subject) {
            $examScheduleCondition = [
                ['years_id', '=', $year],
                ['months_id', '=', $month],
                ['exams_id', '=', $exam],
                ['faculty_id', '=', $faculty],
                ['semesters_id', '=', $semester],
                ['subjects_id', '=', $subject]
            ];

            /*Find Exam Schedule Id*/
            $examScheduleId = ExamSchedule::select('id')
                ->where($examScheduleCondition)
                ->get();
            $examScheduleId = array_pluck($examScheduleId, 'id');

            $data['ledger_exist'] = ExamMarkLedger::select('exam_mark_ledgers.exam_schedule_id', 'exam_mark_ledgers.students_id',
                'exam_mark_ledgers.obtain_mark_theory', 'exam_mark_ledgers.obtain_mark_practical', 'exam_mark_ledgers.obtain_mark_mcq', 'exam_mark_ledgers.absent_theory','exam_mark_ledgers.absent_practical',
                'exam_mark_ledgers.status', 's.id as student_id', 's.reg_no', 's.first_name', 's.middle_name', 's.last_name',
                's.last_name')
                ->whereIn('exam_mark_ledgers.exam_schedule_id', $examScheduleId)
                ->join('students as s', 's.id', '=', 'exam_mark_ledgers.students_id')
                ->get();

        }

        $data['years'] = $this->activeYears();
        $data['months'] = $this->activeMonths();
        $data['exams'] = $this->activeExams();
        $data['faculties'] = $this->activeFaculties();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function add(Request $request)
    {
        $data = [];

        $data['years'] = $this->activeYears();
        $data['months'] = $this->activeMonths();
        $data['exams'] = $this->activeExams();
        $data['faculties'] = $this->activeFaculties();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }

    public function store(Request $request)
    {
        $response = [];
        $response['error'] = true;
        $year = $request->get('years_id');
        $month = $request->get('months_id');
        $exam = $request->get('exams_id');
        $faculty = $request->get('faculty');
        $semester = $request->get('semester_select');
        $subject = $request->get('schedule_subject');

        /*For Mark Schedule*/
        $examScheduleCondition = [
            ['years_id', '=' , $year],
            ['months_id', '=' , $month],
            ['exams_id', '=' , $exam],
            ['faculty_id', '=' , $faculty],
            ['semesters_id', '=' , $semester],
            ['subjects_id', '=' , $subject],
        ];

        /*Find Exam Schedule Id*/
        $examScheduleId = ExamSchedule::select('id')->where($examScheduleCondition)->first();

        if (!$examScheduleId) {
            $request->session()->flash($this->message_warning, 'Exam schedule not found for the selected filter.');
            return back()->withInput();
        }

        $examSchedule = ExamSchedule::select('id', 'subjects_id', 'full_mark_theory', 'full_mark_practical')
            ->find($examScheduleId->id);

        $subject = Subject::select('id', 'full_mark_theory', 'full_mark_practical', 'mcq_number_theory')->find($examSchedule->subjects_id);

        $scheduleTheoryMax = (float) ($examSchedule->full_mark_theory ?? 0);
        $schedulePracticalMax = (float) ($examSchedule->full_mark_practical ?? 0);
        $masterTheoryMax = (float) ($subject->full_mark_theory ?? 0);
        $masterPracticalMax = (float) ($subject->full_mark_practical ?? 0);

        $theoryMax = $scheduleTheoryMax > 0 ? $scheduleTheoryMax : $masterTheoryMax;
        $practicalMax = $schedulePracticalMax > 0 ? $schedulePracticalMax : $masterPracticalMax;
        $mcqMax = (float) ($subject->mcq_number_theory ?? 0);

        $students = Student::select('id', 'reg_no')->whereIn('id', (array) $request->get('students_id'))->get()->keyBy('id');

        $isTeacher = $this->isTeacherUser();
        $userId = auth()->user()->id;
        $savedCount = 0;
        $skippedLocked = 0;
        $invalidRows = [];

        if($request->has('students_id')) {
            foreach ($request->get('students_id') as $key => $student) {

                if($request->has('absent_theory') && in_array($student, $request->get('absent_theory'))) {
                    $trAbsentStudent = 1;
                }else {
                    $trAbsentStudent = 0;
                }

                if($request->has('absent_practical') && in_array($student, $request->get('absent_practical'))) {
                    $prAbsentStudent = 1;
                }else {
                    $prAbsentStudent = 0;
                }

                $thRaw = isset($request->get('obtain_mark_theory')[$key]) ? trim((string) $request->get('obtain_mark_theory')[$key]) : '';
                $mcqRaw = isset($request->get('obtain_mark_mcq')[$key]) ? trim((string) $request->get('obtain_mark_mcq')[$key]) : '';
                $prRaw = isset($request->get('obtain_mark_practical')[$key]) ? trim((string) $request->get('obtain_mark_practical')[$key]) : '';

                /*Row untouched? (no mark, no absent tick)*/
                $rowFilled = ($thRaw !== '' || $mcqRaw !== '' || $prRaw !== '' || $trAbsentStudent == 1 || $prAbsentStudent == 1);

                /*Ledger Already Exist*/
                $ledgerWhere = [
                    ['exam_schedule_id','=',$examScheduleId->id],
                    ['students_id','=', $student]
                ];
                $ledgerExist = ExamMarkLedger::select('id', 'created_by')->where($ledgerWhere)->first();

                /*Skip completely empty rows: no record is created for untouched students*/
                if (!$ledgerExist && !$rowFilled) {
                    continue;
                }

                /*Ownership guard: a teacher can only modify rows he/she entered.
                  Admin / super-admin (non-teacher roles) can modify everything.
                  A row whose created_by is empty has been unlocked by an admin, so any
                  teacher is allowed to edit it (and becomes nothing special — it stays open).*/
                if ($ledgerExist && $isTeacher && !empty($ledgerExist->created_by) && $ledgerExist->created_by != $userId) {
                    $skippedLocked++;
                    continue;
                }

                $thMark = (float) ($thRaw !== '' ? $thRaw : 0);
                $mcqMark = (float) ($mcqRaw !== '' ? $mcqRaw : 0);
                $prMark = (float) ($prRaw !== '' ? $prRaw : 0);

                // Absent components must always remain zero.
                if ($trAbsentStudent == 1) {
                    $thMark = 0;
                }

                if ($prAbsentStudent == 1) {
                    $prMark = 0;
                }

                $regNo = isset($students[$student]) ? $students[$student]->reg_no : ('Student ID '.$student);

                /*Validate THIS row only. An invalid row is skipped and reported, but it must
                  NOT stop the rest of the batch from saving. Previously a single out-of-range
                  mark did a return back() here, so every student processed after it (higher
                  rolls) silently failed to save while earlier ones persisted.*/
                $rowError = '';
                if ($thMark < 0 || $mcqMark < 0 || $prMark < 0) {
                    $rowError = 'negative mark';
                } elseif ($theoryMax > 0 && $thMark > $theoryMax) {
                    $rowError = 'theory exceeds '.$theoryMax;
                } elseif ($mcqMax > 0 && $mcqMark > $mcqMax) {
                    $rowError = 'MCQ exceeds '.$mcqMax;
                } elseif ($practicalMax > 0 && $prMark > $practicalMax) {
                    $rowError = 'practical exceeds '.$practicalMax;
                }

                if ($rowError !== '') {
                    $invalidRows[] = $regNo.' ('.$rowError.')';
                    continue;
                }

                if ($ledgerExist) {
                    /*Column-wise partial save: only overwrite a component (theory / MCQ /
                      practical) when it was actually touched in THIS submission — i.e. a
                      mark was typed OR its absent box was ticked. Untouched components keep
                      their previously saved value. This lets a teacher enter theory first
                      and add practical/MCQ later (even in a separate session) without the
                      blank boxes wiping the marks entered earlier.*/
                    $theoryTouched    = ($thRaw !== '' || $trAbsentStudent == 1);
                    $mcqTouched       = ($mcqRaw !== '');
                    $practicalTouched = ($prRaw !== '' || $prAbsentStudent == 1);

                    /*Nothing filled for this existing row -> leave it untouched.*/
                    if (!$theoryTouched && !$mcqTouched && !$practicalTouched) {
                        continue;
                    }

                    $ledgerUpdate = [
                        'sorting_order' => $key+1,
                        'last_updated_by' => $userId,
                    ];

                    if ($theoryTouched) {
                        $ledgerUpdate['obtain_mark_theory'] = $thMark;
                        $ledgerUpdate['absent_theory'] = $trAbsentStudent;
                    }
                    if ($mcqTouched) {
                        $ledgerUpdate['obtain_mark_mcq'] = $mcqMark;
                    }
                    if ($practicalTouched) {
                        $ledgerUpdate['obtain_mark_practical'] = $prMark;
                        $ledgerUpdate['absent_practical'] = $prAbsentStudent;
                    }

                    $ledgerExist->update($ledgerUpdate);
                    $savedCount++;

                }else{
                    /*First entry: this user becomes the owner of the row*/
                    ExamMarkLedger::create([
                        'exam_schedule_id' => $examScheduleId->id,
                        'students_id' => $student,
                        'obtain_mark_theory' => $thMark,
                        'obtain_mark_practical' => $prMark,
                        'obtain_mark_mcq' => $mcqMark,
                        'absent_theory' => $trAbsentStudent,
                        'absent_practical' => $prAbsentStudent,
                        'sorting_order' => $key+1,
                        'created_by' => $userId,
                    ]);
                    $savedCount++;

                }
            }

            $message = $this->panel.': '.$savedCount.' student(s) saved.';
            if ($skippedLocked > 0) {
                $message .= ' '.$skippedLocked.' student(s) skipped (entered by another teacher).';
            }
            if (count($invalidRows) > 0) {
                $message .= ' '.count($invalidRows).' student(s) NOT saved due to invalid marks: '.implode(', ', $invalidRows).'. Please correct and save again.';
            }

            if ($savedCount > 0) {
                /*Some rows saved but others had invalid marks -> show a warning so the
                  invalid ones are not missed, otherwise a plain success.*/
                if (count($invalidRows) > 0) {
                    $request->session()->flash($this->message_warning, $message);
                } else {
                    $request->session()->flash($this->message_success, $message);
                }
            } elseif (count($invalidRows) > 0) {
                $request->session()->flash($this->message_warning, 'No mark saved. Invalid marks for: '.implode(', ', $invalidRows).'. Please correct and save again.');
            } else {
                $request->session()->flash($this->message_warning, 'No mark saved. '.($skippedLocked > 0 ? $skippedLocked.' student(s) are locked by another teacher.' : 'Please fill mark for at least one student.'));
            }
        }else{
            $request->session()->flash($this->message_warning, 'You Have No Manage Student Mark Yet, Mark Ledger Not Manage. ');
        }

        // Always return to add page so teacher can enter marks for next subject
        return back()->withInput();
    }

    /**
     * Admin unlock: clear the owner (created_by) of one or more mark-ledger rows so that
     * any teacher can edit them again. Works for a single student or a bulk selection.
     * Only admin / super-admin (non-teacher) may unlock.
     */
    public function unlock(Request $request)
    {
        $response = ['error' => true, 'message' => ''];

        if ($this->isTeacherUser()) {
            $response['message'] = 'Only admin can unlock a mark row.';
            return response()->json($response);
        }

        /*Resolve the exam schedule the same way store() does.*/
        $examSchedule = ExamSchedule::select('id')->where([
            ['years_id', '=', $request->get('years_id')],
            ['months_id', '=', $request->get('months_id')],
            ['exams_id', '=', $request->get('exams_id')],
            ['faculty_id', '=', $request->get('faculty')],
            ['semesters_id', '=', $request->get('semester_select')],
            ['subjects_id', '=', $request->get('schedule_subject')],
        ])->first();

        if (!$examSchedule) {
            $response['message'] = 'Exam schedule not found for the selected filter.';
            return response()->json($response);
        }

        $studentIds = array_filter((array) $request->get('students_id'));
        if (empty($studentIds)) {
            $response['message'] = 'No student selected to unlock.';
            return response()->json($response);
        }

        /* created_by column is NOT NULL (unsignedInteger), so 0 is used as the
           "no owner / unlocked" sentinel. The store() guard treats an empty
           created_by (0) as editable by any teacher. */
        $affected = ExamMarkLedger::where('exam_schedule_id', $examSchedule->id)
            ->whereIn('students_id', $studentIds)
            ->update(['created_by' => 0, 'last_updated_by' => auth()->user()->id]);

        $response['error'] = false;
        $response['unlocked'] = $affected;
        $response['student_ids'] = array_values($studentIds);
        $response['message'] = $affected.' mark row(s) unlocked. Any teacher can now edit them.';

        return response()->json($response);
    }

    public function delete(Request $request, $exam=null, $student=null)
    {

        $row = ExamMarkLedger::where([
            ['exam_schedule_id', '=' , $exam],
            ['students_id', '=' , $student]
        ])->first();

        if (!$row) return parent::invalidRequest();

        /*Teacher can only delete own entries*/
        if ($this->isTeacherUser() && $row->created_by != auth()->user()->id) {
            $request->session()->flash($this->message_warning, 'This mark was entered by another teacher. You cannot delete it.');
            return redirect()->route($this->base_route);
        }

       $row->delete();

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }

    public function active(Request $request, $exam=null, $student=null)
    {

        $row = ExamMarkLedger::where([
            ['exam_schedule_id', '=' , $exam],
            ['students_id', '=' , $student]
        ])->first();

        if (!$row) return parent::invalidRequest();

        $row->update([
            'status' => 1
        ]);

        $request->session()->flash($this->message_success, $this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(Request $request, $exam=null, $student=null)
    {

        $row = ExamMarkLedger::where([
            ['exam_schedule_id', '=' , $exam],
            ['students_id', '=' , $student]
            ])->first();

        if (!$row) return parent::invalidRequest();

        $row->update([
            'status' => 0
        ]);

        $request->session()->flash($this->message_success, $this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }

    /**
     * Printable mark list. Teachers get ONLY the rows they entered
     * (created_by = own id); admin / super-admin get the full ledger.
     */
    public function printMyEntries(Request $request)
    {
        $examScheduleCondition = [
            ['years_id', '=', $request->get('years_id')],
            ['months_id', '=', $request->get('months_id')],
            ['exams_id', '=', $request->get('exams_id')],
            ['faculty_id', '=', $request->get('faculty_id')],
            ['semesters_id', '=', $request->get('semester_id')],
            ['subjects_id', '=', $request->get('subject_id')],
        ];

        $examSchedule = ExamSchedule::select('id', 'subjects_id', 'full_mark_theory', 'full_mark_practical')
            ->where($examScheduleCondition)
            ->first();

        if (!$examSchedule) {
            $request->session()->flash($this->message_warning, 'Exam schedule not found for the selected filter.');
            return redirect()->route($this->base_route.'.add');
        }

        $isTeacher = $this->isTeacherUser();
        $userId = auth()->user()->id;

        $rowsQuery = ExamMarkLedger::select('exam_mark_ledgers.students_id',
                'exam_mark_ledgers.obtain_mark_theory', 'exam_mark_ledgers.obtain_mark_mcq',
                'exam_mark_ledgers.obtain_mark_practical', 'exam_mark_ledgers.absent_theory',
                'exam_mark_ledgers.absent_practical', 'exam_mark_ledgers.created_by',
                'exam_mark_ledgers.updated_at',
                'u.name as entered_by_name',
                's.reg_no', 's.first_name', 's.middle_name', 's.last_name')
            ->where('exam_mark_ledgers.exam_schedule_id', $examSchedule->id)
            ->join('students as s', 's.id', '=', 'exam_mark_ledgers.students_id')
            ->leftJoin('users as u', 'u.id', '=', 'exam_mark_ledgers.created_by');

        if ($isTeacher) {
            $rowsQuery->where('exam_mark_ledgers.created_by', $userId);
        }

        $rows = $rowsQuery->orderBy('s.reg_no', 'asc')->get();

        $subject = Subject::select('id', 'title', 'code', 'full_mark_theory', 'full_mark_practical', 'mcq_number_theory')
            ->find($examSchedule->subjects_id);

        $scheduleTheoryMax = (float) ($examSchedule->full_mark_theory ?? 0);
        $schedulePracticalMax = (float) ($examSchedule->full_mark_practical ?? 0);

        $data = [
            'rows' => $rows,
            'subject' => $subject,
            'limits' => [
                'theory' => $scheduleTheoryMax > 0 ? $scheduleTheoryMax : (float) ($subject->full_mark_theory ?? 0),
                'mcq' => (float) ($subject->mcq_number_theory ?? 0),
                'practical' => $schedulePracticalMax > 0 ? $schedulePracticalMax : (float) ($subject->full_mark_practical ?? 0),
            ],
            'year' => Year::find($request->get('years_id')),
            'month' => Month::find($request->get('months_id')),
            'exam' => Exam::find($request->get('exams_id')),
            'faculty' => Faculty::find($request->get('faculty_id')),
            'semester' => Semester::find($request->get('semester_id')),
            'teacher_only' => $isTeacher,
            'printed_by' => auth()->user()->name,
        ];

        return view(parent::loadDataToView($this->view_path.'.print'), compact('data'));
    }

    public function findSubject(Request $request)
    {
        $row = ExamSchedule::where([
                    ['years_id', '=' , $request->get('years_id')],
                    ['months_id', '=' , $request->get('months_id')],
                    ['exams_id', '=' , $request->get('exams_id')],
                    ['faculty_id', '=' , $request->get('faculty_id')],
                    ['semesters_id', '=' , $request->get('semester_id')],
                 ])
                ->get();

        /*Get Subjects Ids as Arrays*/
        $existSubject = array_pluck($row, 'subjects_id');

        /*Find Subject Title with associated Ids*/
        if($this->isTeacherUser()){
            $subjects = Subject::select('id','title')->whereIn('id',$existSubject)->forTeacher(auth()->user()->hook_id)->get();
        }else{
            $subjects = Subject::select('id','title')->whereIn('id',$existSubject)->get();
        }


        if ($subjects->count() > 0) {

            $response['subjects'] = $subjects;
            $response['success'] = 'Scheduled Subject Get, Choose For Manage Mark.';
        }else {
            $response['error'] = 'No Any Subject Or you have not the permission. Please Schedule First.';
        }

        return response()->json(json_encode($response));
    }

    public function studentHtmlRow(Request $request)
    {
        $response = [];
        $response['error'] = true;
        $year = $request->get('years_id');
        $month = $request->get('months_id');
        $exam = $request->get('exams_id');
        $faculty = $request->get('faculty_id');
        $semester = $request->get('semester_id');
        $subject = $request->get('subject_id');

        /*For Student List*/
        $studentCondition = [['faculty', '=' , $faculty], ['semester', '=' , $semester] ];

        /*For Mark Schedule*/
        $examScheduleCondition = [
            ['years_id', '=' , $year],
            ['months_id', '=' , $month],
            ['exams_id', '=' , $exam],
            ['faculty_id', '=' , $faculty],
            ['semesters_id', '=' , $semester],
            ['subjects_id', '=' , $subject]
        ];

        /*Find Exam Schedule Id*/
        $examSchedule = ExamSchedule::select('id', 'subjects_id', 'full_mark_theory', 'full_mark_practical')
                ->where($examScheduleCondition)
                ->first();

        $examScheduleId = [];
        if ($examSchedule) {
            $examScheduleId[] = $examSchedule->id;
        }

        $subjectDetail = $examSchedule ? Subject::select('id', 'full_mark_theory', 'full_mark_practical', 'mcq_number_theory')->find($examSchedule->subjects_id) : null;
        $scheduleTheoryLimit = (float) ($examSchedule->full_mark_theory ?? 0);
        $schedulePracticalLimit = (float) ($examSchedule->full_mark_practical ?? 0);
        $masterTheoryLimit = (float) ($subjectDetail->full_mark_theory ?? 0);
        $masterPracticalLimit = (float) ($subjectDetail->full_mark_practical ?? 0);
        $markLimits = [
            'theory' => $scheduleTheoryLimit > 0 ? $scheduleTheoryLimit : $masterTheoryLimit,
            'mcq' => (float) ($subjectDetail->mcq_number_theory ?? 0),
            'practical' => $schedulePracticalLimit > 0 ? $schedulePracticalLimit : $masterPracticalLimit,
        ];

        if($examScheduleId){
            $ledgerExist = ExamMarkLedger::select('exam_mark_ledgers.exam_schedule_id',
                'exam_mark_ledgers.students_id',
                'exam_mark_ledgers.obtain_mark_theory',
                'exam_mark_ledgers.obtain_mark_practical',
                'exam_mark_ledgers.obtain_mark_mcq',
                'exam_mark_ledgers.absent_theory',
                'exam_mark_ledgers.absent_practical',
                'exam_mark_ledgers.created_by',
                'u.name as entered_by_name',
                's.id as student_id','s.reg_no','s.first_name','s.middle_name','s.last_name')
                ->whereIn('exam_mark_ledgers.exam_schedule_id',$examScheduleId)
                ->join('students as s','s.id','=','exam_mark_ledgers.students_id')
                ->leftJoin('users as u','u.id','=','exam_mark_ledgers.created_by')
                ->orderBy('s.reg_no','asc')
                ->get();

            /*Rows locked for the current teacher (entered by someone else)*/
            $isTeacher = $this->isTeacherUser();
            $userId = auth()->user()->id;
            $lockedIds = [];
            $ownerNames = [];
            foreach ($ledgerExist as $ledgerRow) {
                /*A row with empty created_by (0) has been unlocked by an admin, so it is
                  NOT locked for any teacher — treat it as open/editable (white row).*/
                if ($isTeacher && !empty($ledgerRow->created_by) && $ledgerRow->created_by != $userId) {
                    $lockedIds[] = $ledgerRow->students_id;
                    $ownerNames[$ledgerRow->students_id] = $ledgerRow->entered_by_name ?: 'Another user';
                }
            }

            /*get ledger exist student id*/
            $existStudentId  = array_pluck($ledgerExist, 'students_id');

            //Get Active Student For Related Faculty and Semester
            $activeStudent = Student::select('id','reg_no','first_name','middle_name','last_name')
                ->where($studentCondition)
                ->whereNotIn('id',$existStudentId)
                ->Active()
                //->orderBy('id','asc')
                ->orderBy('reg_no','asc')
                ->get();


            if($activeStudent) {
                /*filter absent student*/
                $trAbsentStudent =  $ledgerExist->filter(function ($item)
                {
                    return $item->absent_theory == 1;
                });
                /*get Absent student id*/
                $trAbsentStudent  = array_pluck($trAbsentStudent, 'students_id');

                $prAbsentStudent =  $ledgerExist->filter(function ($item)
                {
                    return $item->absent_practical == 1;
                });
                /*get Absent student id*/
                $prAbsentStudent  = array_pluck($prAbsentStudent, 'students_id');



                $response['limits'] = $markLimits;
                $response['exist_count'] = count($ledgerExist);
                $response['new_count'] = count($activeStudent);
                $response['locked_count'] = count($lockedIds);

                if($ledgerExist){
                    $response['error'] = false;

                    $response['exist'] = view($this->view_path.'.includes.student_tr_rows', [
                        'exist' => $ledgerExist,
                        'absent_theory' => $trAbsentStudent,
                        'absent_practical' => $prAbsentStudent,
                        'markLimits' => $markLimits,
                        'lockedIds' => $lockedIds,
                        'ownerNames' => $ownerNames,
                        'canUnlock' => !$isTeacher,
                    ])->render();

                    $response['students'] = view($this->view_path.'.includes.student_tr', [
                        'students' => $activeStudent,
                        'markLimits' => $markLimits,
                    ])->render();

                    $response['message'] = 'Active Students Found. Please, Manage Mark.';
                }else{
                    $response['error'] = false;

                    $response['students'] = view($this->view_path.'.includes.student_tr', [
                        'students' => $activeStudent,
                        'markLimits' => $markLimits,
                    ])->render();

                    $response['message'] = 'Active Students Found. Please, Manage Mark.';
                }
            }else{
                $response['error'] = 'No Any Active Students in This Faculty/Semester.';
            }
        }else{
            $response['error'] = 'Exam Not Scheduled. Please Schedule First';
        }

        return response()->json(json_encode($response));
    }

}