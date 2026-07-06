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
                  Admin / super-admin (non-teacher roles) can modify everything.*/
                if ($ledgerExist && $isTeacher && $ledgerExist->created_by != $userId) {
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

                if ($thMark < 0 || $mcqMark < 0 || $prMark < 0) {
                    $request->session()->flash($this->message_warning, 'Mark cannot be negative for '.$regNo.'.');
                    return back()->withInput();
                }

                if ($theoryMax > 0 && $thMark > $theoryMax) {
                    $request->session()->flash($this->message_warning, 'Theory mark cannot exceed full mark ('.$theoryMax.') for '.$regNo.'.');
                    return back()->withInput();
                }

                if ($mcqMax > 0 && $mcqMark > $mcqMax) {
                    $request->session()->flash($this->message_warning, 'MCQ mark cannot exceed full mark ('.$mcqMax.') for '.$regNo.'.');
                    return back()->withInput();
                }

                if ($practicalMax > 0 && $prMark > $practicalMax) {
                    $request->session()->flash($this->message_warning, 'Practical mark cannot exceed full mark ('.$practicalMax.') for '.$regNo.'.');
                    return back()->withInput();
                }

                if ($ledgerExist) {
                    /*Update Own / Admin-Editable Mark Ledger*/
                    $ledgerUpdate = [
                        'exam_schedule_id' => $examScheduleId->id,
                        'students_id' => $student,
                        'obtain_mark_theory' => $thMark,
                        'obtain_mark_practical' => $prMark,
                        'obtain_mark_mcq' => $mcqMark,
                        'absent_theory' => $trAbsentStudent,
                        'absent_practical' => $prAbsentStudent,
                        'sorting_order' => $key+1,
                        'last_updated_by' => $userId
                    ];

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

            if ($savedCount > 0) {
                $request->session()->flash($this->message_success, $message);
            } else {
                $request->session()->flash($this->message_warning, 'No mark saved. '.($skippedLocked > 0 ? $skippedLocked.' student(s) are locked by another teacher.' : 'Please fill mark for at least one student.'));
            }
        }else{
            $request->session()->flash($this->message_warning, 'You Have No Manage Student Mark Yet, Mark Ledger Not Manage. ');
        }

        // Always return to add page so teacher can enter marks for next subject
        return back()->withInput();
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
                if ($isTeacher && $ledgerRow->created_by != $userId) {
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