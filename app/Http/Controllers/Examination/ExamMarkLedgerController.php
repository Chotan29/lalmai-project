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

    public function index(Request $request)
    {
        $data = [];

        $year = $request->get('year');
        $month = $request->get('month');
        $exam = $request->get('exam');
        $faculty = $request->get('faculty');
        $semester = $request->get('semester');
        $subject = $request->get('subject');

        // Staff may only view ledger for their own assigned subject
        if (auth()->user()->role_id == 5 && $subject) {
            if (!Subject::where('id', $subject)->where('staff_id', auth()->user()->hook_id)->exists()) {
                $subject = null;
            }
        }

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

        // Staff may only enter marks for their own assigned subject
        if (auth()->user()->role_id == 5) {
            if (!Subject::where('id', $subject)->where('staff_id', auth()->user()->hook_id)->exists()) {
                $request->session()->flash($this->message_warning, 'You do not have permission to enter marks for this subject.');
                return back();
            }
        }

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

                $thMark = (float) ($request->get('obtain_mark_theory')[$key] ? $request->get('obtain_mark_theory')[$key] : 0);
                $mcqMark = (float) ($request->get('obtain_mark_mcq')[$key] ? $request->get('obtain_mark_mcq')[$key] : 0);
                $prMark = (float) ($request->get('obtain_mark_practical')[$key] ? $request->get('obtain_mark_practical')[$key] : 0);

                // Absent components must always remain zero.
                if ($trAbsentStudent == 1) {
                    $thMark = 0;
                }

                if ($prAbsentStudent == 1) {
                    $prMark = 0;
                }

                $regNo = isset($students[$student]) ? $students[$student]->reg_no : ('Student ID '.$student);

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

                /*Ledger Already Exist*/
                $ledgerWhere = [
                    ['exam_schedule_id','=',$examScheduleId->id],
                    ['students_id','=', $student]
                ];
                $ledgerExist = ExamMarkLedger::select('id')->where($ledgerWhere)->first();

                if ($ledgerExist) {
                    /*Update Already Register Mark Ledger*/
                    $ledgerUpdate = [
                        'exam_schedule_id' => $examScheduleId->id,
                        'students_id' => $student,
                        'obtain_mark_theory' => $thMark,
                        'obtain_mark_practical' => $prMark,
                        'obtain_mark_mcq' => $mcqMark,
                        'absent_theory' => $trAbsentStudent,
                        'absent_practical' => $prAbsentStudent,
                        'sorting_order' => $key+1,
                        'last_updated_by' => auth()->user()->id
                    ];

                    $ledgerExist->update($ledgerUpdate);

                }else{
                    /*Schedule When Not Scheduled Yet*/
                    ExamMarkLedger::create([
                        'exam_schedule_id' => $examScheduleId->id,
                        'students_id' => $student,
                        'obtain_mark_theory' => $thMark,
                        'obtain_mark_practical' => $prMark,
                        'obtain_mark_mcq' => $mcqMark,
                        'absent_theory' => $trAbsentStudent,
                        'absent_practical' => $prAbsentStudent,
                        'sorting_order' => $key+1,
                        'created_by' => auth()->user()->id,
                    ]);

                }
            }
            $request->session()->flash($this->message_success, $this->panel. ' Manage Successfully.');
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
        if(auth()->user()->role_id == 5){
            $subjects = Subject::select('id','title')->whereIn('id',$existSubject)->where('staff_id',auth()->user()->hook_id)->get();
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

        // Staff may only load students for their own assigned subject
        if (auth()->user()->role_id == 5) {
            if (!Subject::where('id', $subject)->where('staff_id', auth()->user()->hook_id)->exists()) {
                $response['error'] = 'You do not have permission to access this subject.';
                return response()->json(json_encode($response));
            }
        }

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
                's.id as student_id','s.reg_no','s.first_name','s.middle_name','s.last_name')
                ->whereIn('exam_mark_ledgers.exam_schedule_id',$examScheduleId)
                ->join('students as s','s.id','=','exam_mark_ledgers.students_id')
                ->orderBy('s.reg_no','asc')
                ->get();

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



                if($ledgerExist){
                    $response['error'] = false;

                    $response['exist'] = view($this->view_path.'.includes.student_tr_rows', [
                        'exist' => $ledgerExist,
                        'absent_theory' => $trAbsentStudent,
                        'absent_practical' => $prAbsentStudent,
                        'markLimits' => $markLimits,
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