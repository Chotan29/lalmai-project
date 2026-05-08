<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\PrintOut;

use App\Http\Controllers\CollegeBaseController;
use App\Models\CertificateTemplate;
use App\Models\Exam;
use App\Models\ExamMarkLedger;
use App\Models\ExamSchedule;
use App\Models\Faculty;
use App\Models\GradingScale;
use App\Models\GradingType;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Traits\ExaminationScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use view, URL;
use ViewHelper;
class ExamPrintController extends CollegeBaseController
{

    use ExaminationScope;

    protected $base_route = 'print.exam';
    protected $view_path = 'print.exam';
    protected $panel = 'Examination Printing';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function admitCard(Request $request)
    {
        $data = [];
        $data['year'] = $request->get('years_id');
        $data['month'] = $request->get('months_id');
        $data['exam'] = $request->get('exams_id');
        $data['faculty'] = $request->get('target_faculty');
        $data['semester'] = $request->get('semester_select');
        $whereCondition = [
            ['years_id' , '=' , $request->get('years_id')],
            ['months_id' , '=' , $request->get('months_id')],
            ['exams_id', '=' , $request->get('exams_id')],
            ['faculty_id', '=' , $request->get('target_faculty')],
            ['semesters_id', '=' , $request->get('semester_select')]
        ];
        $data['subjects'] = ExamSchedule::select('exam_schedules.subjects_id',
            'exam_schedules.date', 'exam_schedules.start_time', 'exam_schedules.end_time',
            'exam_schedules.full_mark_theory', 'exam_schedules.pass_mark_theory',
            'exam_schedules.full_mark_practical',
            'exam_schedules.pass_mark_practical', 's.code', 's.title')
            ->where($whereCondition)
            ->join('subjects as s','s.id','=','exam_schedules.subjects_id')
            ->orderBy('sorting_order','asc')
            ->get();

        if($data['subjects']->count() == 0)
            return back()->with($this->message_warning, 'No any Subject Scheduled in your target exam. Please, Schedule exam first. ');

        $data['student'] = Student::select('id','reg_no','date_of_birth', 'first_name', 'middle_name', 'last_name','student_image','gender','blood_group' /*,'faculty', 'semester','status'*/)
            ->whereIn('id',$request->get('chkIds'))
            ->get();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        if($request->print_type == 1){
            return view(parent::loadDataToView($this->view_path.'.admit-card'), compact('data'));
        }else{
            return view(parent::loadDataToView($this->view_path.'.admit-card-with-schedule'), compact('data'));
        }

    }

    public function examRoutine(Request $request)
    {

        $data['general_setting'] = $this->getGeneralSetting();
        if(isset($data['general_setting']) && $data['general_setting'] != null){
        }else{
            request()->session()->flash($this->message_warning, 'Please, Setup your institution detail or contact your system administrator');
            return redirect()->route('home');
        }

        $data = [];
        $data['year'] = $request->get('years_id');
        $data['month'] = $request->get('months_id');
        $data['exam'] = $request->get('exams_id');
        $data['faculty'] = $request->get('target_faculty');
        $data['semester'] = $request->get('semester_select');

        $whereCondition = [
            ['years_id' , '=' , $request->get('years_id')],
            ['months_id' , '=' , $request->get('months_id')],
            ['exams_id', '=' , $request->get('exams_id')],
            ['faculty_id', '=' , $request->get('target_faculty')],
            ['semesters_id', '=' , $request->get('semester_select')]
        ];

        $data['subjects'] = ExamSchedule::select('exam_schedules.subjects_id',
            'exam_schedules.date', 'exam_schedules.start_time', 'exam_schedules.end_time',
            'exam_schedules.full_mark_theory', 'exam_schedules.pass_mark_theory',
            'exam_schedules.full_mark_practical',
            'exam_schedules.pass_mark_practical', 's.code', 's.title')
            ->where($whereCondition)
            ->join('subjects as s','s.id','=','exam_schedules.subjects_id')
            ->orderBy('sorting_order','asc')
            ->get();

        if($data['subjects']->count() == 0)
            return back()->with($this->message_warning, 'No any Subject Scheduled in your target exam. Please, Schedule exam first. ');

        return view(parent::loadDataToView($this->view_path.'.routine'), compact('data'));
    }

    public function examMarkSheet(Request $request)
    {
        $data = [];


        //first for transcript
        if ($request->get('result-type') == 'transcript') {
            if ($request->has('chkIds')) {

                //$exam_schedule_id = explode(',',$request->get('exam_schedule_id'));
                $student_id = decrypt($request->get('chkIds'));
                // dd($student_id);

                $students = Student::select('id', 'reg_no', 'first_name', 'middle_name', 'last_name', 'date_of_birth',
                    'faculty', 'semester','batch')
                    ->where('id',$student_id)
                    ->get();


                $data['student']  = $students->filter(function ($studentDetail, $studentKey){
                    $groupBySemester = $ledgerExist = ExamMarkLedger::select('exam_mark_ledgers.exam_schedule_id', 'exam_mark_ledgers.students_id',
                        'exam_mark_ledgers.obtain_mark_theory', 'exam_mark_ledgers.obtain_mark_practical', 'exam_mark_ledgers.absent_theory', 'exam_mark_ledgers.absent_practical',
                        'exam_mark_ledgers.status', 's.id as student_id', 's.reg_no', 's.first_name', 's.middle_name', 's.last_name', 's.last_name',
                        'es.semesters_id','es.subjects_id','sub.title as SubjectTitle','sub.code')
                        ->where('s.id', $studentDetail->id)
                        //->where('exam_mark_ledgers.exam_schedule_id', $examSchedue->id)
                        //->groupBy('es.semesters_id')
                        //->groupBy(\DB::raw('es.semesters_id'))
                        ->join('students as s', 's.id', '=', 'exam_mark_ledgers.students_id')
                        ->join('exam_schedules as es', 'es.id', '=', 'exam_mark_ledgers.exam_schedule_id')
                        ->join('subjects as sub', 'sub.id', '=', 'es.subjects_id')
                        ->orderBy('exam_mark_ledgers.students_id', 'asc')
                        ->orderBy('sub.title', 'asc')
                        ->get();

                    $studentDetail->semestersList = Semester::whereIn('id',$groupBySemester->pluck('semesters_id')->unique())->orderBy('semester')->get()->pluck('semester','id');
                    $groupBySemester = $groupBySemester->groupby('semesters_id');

                    //$semester = Semester::find($groupBySemester->pluck('semesters_id'));
                    foreach ($groupBySemester as $key => $semesterLedger) {
                        $semester = Semester::find($semesterLedger[0]['semesters_id']);

                        $value = $filteredSubject[$key]  = $semesterLedger->filter(function ($subject, $key) use($semester) {
                            $joinSub = $subject->examSchedule()
                                ->select('subjects_id','full_mark_theory', 'pass_mark_theory', 'full_mark_practical', 'pass_mark_practical','sorting_order')
                                ->first();


                            if(!$joinSub) return back();

                            $subject->subjects_id = $joinSub->subjects_id;
                            $subject->sorting_order = $joinSub->sorting_order;
                            $subject->full_mark_theory =$full_mark_theory = $joinSub->full_mark_theory;
                            $subject->pass_mark_theory = $pass_mark_theory = $joinSub->pass_mark_theory;
                            $subject->full_mark_practical = $full_mark_practical = $joinSub->full_mark_practical;
                            $subject->pass_mark_practical = $pass_mark_practical = $joinSub->pass_mark_practical;
                            $th = $obtain_mark_theory = $subject->obtain_mark_theory;
                            $pr = $absent_theory = $subject->absent_theory;
                            $obtain_mark_practical = $subject->obtain_mark_practical;
                            $absent_practical = $subject->absent_practical;


                            /*th absent*/
                            if($absent_theory != 1) {
                                if ($full_mark_theory > 0) {
                                    $th_per = ($obtain_mark_theory * 100) / $full_mark_theory;
                                    $subject->obtain_score_theory = $th_per ==0?'*NG':$this->getGrade($semester, $th_per);
                                }
                            }else{
                                $subject->obtain_score_theory = "*AB";
                            }

                            /*pr absent*/
                            if($absent_practical != 1) {
                                if($full_mark_practical > 0) {
                                    $pr_per = ($obtain_mark_practical * 100) / $full_mark_practical;
                                    $subject->obtain_score_practical = $pr_per ==0?"*NG":$this->getGrade($semester, $pr_per);
                                }
                            }else{
                                $pr_per = 0;
                                $subject->obtain_score_practical = "*AB";
                            }

                            /*check absent on theory & practical*/
                            $absentBoth = false;
                            if($absent_theory == 1 && $absent_practical == 1){
                                $absentBoth = true;
                            }

                            //Final Grade
                            $subject->totalMark = $totalMark = $full_mark_theory + $full_mark_practical;
                            $subject->obtainedMark = $obtainedMark = $obtain_mark_theory + $obtain_mark_practical;
                            $subject->percentage = $percentage = ($obtainedMark*100)/ $totalMark;

                            //verify both th & pr absent
                            if($absentBoth == false) {
                                $subject->final_grade = $this->getGrade($semester, $percentage);
                                $subject->grade_point = number_format((float)$this->getPoint($semester, $percentage),2);
                                $subject->remark = $this->getRemark($semester, $percentage);
                            }else{
                                $subject->final_grade = "*MG";
                                $subject->grade_point = "*MP";
                                $subject->remark = "-";
                            }

                            /*theory mark comparison*/
                            if(isset($subject->pass_mark_theory) && $subject->pass_mark_theory != null){
                                if($absent_theory == 1) {
                                    $subject->obtain_mark_theory = "AB";
                                }else{
                                    if(!is_numeric($th)){
                                        $subject->obtain_mark_theory = "*";
                                    }
                                }
                            }else{
                                $subject->obtain_mark_theory = "-";
                            }

                            /*Practical mark compare*/
                            if(isset($subject->pass_mark_practical) && $subject->pass_mark_practical != null){
                                if($absent_practical == 1) {
                                    $subject->obtain_mark_practical = "AB";
                                }else{
                                    if(!is_numeric($pr)){
                                        $subject->obtain_mark_practical = "*";
                                    }
                                }
                            }else{
                                $subject->obtain_mark_practical= "-";
                            }

                            /*verify again the new obtain values are number or not*/
                            $th_new = $subject->obtain_mark_theory;
                            $pr_new = $subject->obtain_mark_practical;
                            //$subject->total_obtain_mark = (is_numeric($th_new)?$th_new:0) + (is_numeric($pr_new)?$pr_new:0);
                            if($th_new >= $subject->pass_mark_theory && $pr_new >= $subject->pass_mark_practical){
                                $subject->remark = '';
                                if($subject->full_mark_theory != null && $th_new > $subject->full_mark_theory){
                                    $subject->th_remark = '*N';
                                    $subject->remark = '*';
                                }

                                if($subject->full_mark_practica != null && $pr_new > $subject->full_mark_practical){
                                    $subject->pr_remark = '*N';
                                    $subject->remark = '*';
                                }

                            }else{
                                $subject->remark = '*';

                                if($th_new < $subject->pass_mark_theory){
                                    $subject->th_remark = '*';
                                }

                                if($pr_new < $subject->pass_mark_practical){
                                    $subject->pr_remark = '*';
                                }

                                if($th_new > $subject->full_mark_theory){
                                    $subject->th_remark = '*N';
                                }

                                if($pr_new > $subject->full_mark_practical){
                                    $subject->pr_remark = '*N';
                                }
                            }
                            //for university gpa
                            //grade_point
                            $subject->creditHour = Subject::find($subject->subjects_id)->credit_hour;
//                            if (is_numeric($subject->grade_point) && is_numeric($subject->creditHour)) {
//                                $subject->gradeWithCredit = $subject->grade_point * $subject->creditHour;
//                            } else {
//                                $subject->gradeWithCredit = 0;
//                            }
                            $subject->gradeWithCredit = (int)$subject->grade_point * (int)$subject->creditHour;
                            return $subject;
                        });

                        // $value->subjects = $filteredSubject->sortBy('sorting_order');

                        /*calculate total mark & percentage*/
                        $otm = array_pluck($value,'obtain_mark_theory');

                        $filtered_otm  =  array_where($otm, function ($value, $key) {
                            return is_numeric($value);
                        });
                        $obtainedMarkTh = array_sum($filtered_otm);

                        $omp = array_pluck($value,'obtain_mark_practical');
                        $filtered_otp  =  array_where($omp, function ($value, $key) {
                            return is_numeric($value);
                        });
                        $obtainedMarkPr = array_sum($filtered_otp);

                        $totalMark = $value->sum('full_mark_theory') + $value->sum('full_mark_practical');
                        $obtainedMark = $obtainedMarkTh + $obtainedMarkPr;

                        $value->total_mark_theory = $obtainedMarkTh;
                        $value->total_mark_practical = $obtainedMarkPr;
                        $value->total_obtain = $obtainedMark;
                        /*caculate percentage*/
                        $value->percentage = ($obtainedMark*100)/ $totalMark;

                        /*calculate grading Score*/
                        //verify both th & pr absent
                        if($value->percentage > 0) {
                            $value->grade_point = round($value->sum('grade_point')/ $value->count(),2);
                            $value->final_grade = $this->getFinalGrade($semester, $value->grade_point);
                            $value->remark = $this->getRemark($semester, $value->percentage);



                        }else{
                            $value->final_grade = "*MG";
                            $value->grade_point = "*MP";
                            $value->remark = "-";
                        }

                        $remark = $value->pluck('remark')->toArray();
                        $pr_remark = $value->pluck('pr_remark')->toArray();
                        if(in_array('*',$remark) || in_array('*',$pr_remark)){
                            $remarkOut = "* Fail";
                        }else {
                            $remarkOut = "Pass";
                        }

                        $value->remark = $remarkOut;

                        $creditHourSum[$key] = $value->sum('creditHour');
                        $gradeWithCreditSum[$key] = $value->sum('gradeWithCredit');
                        $gpaGrade[$key] = $gpa_grade = round($gradeWithCreditSum[$key] / $creditHourSum[$key],4) ;
                        $GradeLetter[$key] = $this->getFinalGrade($semester, $gpa_grade);

                        $semesterDetailLedger[$key] = $value->toArray();
                    }

                    $studentDetail->creditHourSum = $creditHourSum ;
                    $studentDetail->gradeWithCredit = $gradeWithCreditSum ;
                    $studentDetail->gpaGrade = $gpaGrade ;
                    $studentDetail->GradeLetter = $GradeLetter ;
                    $studentDetail->semesterLedger = $semesterDetailLedger ;

                    //transcript gpa calculation
                    $studentDetail->transcriptCHS = $transcriptCHS = array_sum($creditHourSum);
                    $studentDetail->transcriptGradeWithCredit = $transcriptGradeWithCredit = array_sum($gradeWithCreditSum);
                    $studentDetail->transcriptGPA = round($transcriptGradeWithCredit / $transcriptCHS,4);
                    $studentDetail->transcriptGL = $this->getFinalGrade($semester, $gpa_grade);

                    return $studentDetail;
                });
                $facultyDetail = Faculty::find($students[0]->faculty);
                $data['grade-scale-range'] = GradingScale::where('gradingType_id',$facultyDetail->gradingType_id)->get();
                //dd($data['grade-scale-range']);
                if($request->print_type ==1){
                    return view(parent::loadDataToView($this->view_path.'.transcript'), compact('data'));
                }

                if($request->print_type ==2){
                    $data['certificate_template'] = $certificateTemplate = CertificateTemplate::where('certificate','=','SEMESTER WISE FINAL RESULT')
                        ->first();

                    if (!$data['certificate_template'])
                        return parent::invalidRequest();
//
//                    $students = Student::select('id')->whereIn('id',$studIds)->get();
//
//                    $filteredStudent = $students->filter(function ($student, $key) use($certificateTemplate) {
//                        $data = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
//                            'students.faculty','students.semester','students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
//                            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group',  'students.religion',
//                            'students.caste','students.nationality', 'students.mother_tongue', 'students.email', 'students.extra_info',
//                            'students.status',
//                            'ai.address', 'ai.state', 'ai.country', 'ai.temp_address', 'ai.temp_state', 'ai.temp_country', 'ai.home_phone',
//                            'ai.mobile_1', 'ai.mobile_2',
//                            'pd.grandfather_first_name',
//                            'pd.grandfather_middle_name', 'pd.grandfather_last_name', 'pd.father_first_name', 'pd.father_middle_name',
//                            'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_office', 'pd.father_office_number',
//                            'pd.father_residence_number', 'pd.father_mobile_1', 'pd.father_mobile_2', 'pd.father_email', 'pd.mother_first_name',
//                            'pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation', 'pd.mother_office',
//                            'pd.mother_office_number', 'pd.mother_residence_number', 'pd.mother_mobile_1', 'pd.mother_mobile_2', 'pd.mother_email',
//                            'gd.id as guardian_id', 'gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
//                            'gd.guardian_eligibility', 'gd.guardian_occupation', 'gd.guardian_office', 'gd.guardian_office_number', 'gd.guardian_residence_number',
//                            'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email', 'gd.guardian_relation', 'gd.guardian_address',
//                            'students.student_image','students.student_signature', 'pd.father_image', 'pd.mother_image', 'gd.guardian_image',
//                            'pc.date_of_issue', 'pc.pc_num', 'pc.year',  'pc.gpa','pc.scale','pc.ref_text')
//                            ->where('students.id','=',$student->id)
//                            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
//                            ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
//                            ->join('student_guardians as sg', 'sg.students_id','=','students.id')
//                            ->join('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
//                            ->join('provisional_certificates as pc', 'pc.students_id', '=', 'students.id')
//                            ->first();
//
//                        $student->date_of_issue = $data->date_of_issue;
//                        $student->year = $data->year;
//                        $student->pc_num = $data->pc_num;
//                        $student->faculty = $data->faculty;
//                        $student->reg_no = $data->reg_no;
//                        $student->university_reg = $data->university_reg;
//                        $student->certificate = $certificateTemplate->certificate;
//
//                        $text = str_replace('{{date_of_issue}}', Carbon::parse($data->date_of_leaving)->format('d-m-Y'), $certificateTemplate->template);
//                        $text = str_replace('{{pc_num}}', $data->pc_num, $text);
//                        $text = str_replace('{{year}}', $data->year, $text);
//                        $text = str_replace('{{gpa}}', $data->gpa, $text);
//                        $text = str_replace('{{scale}}', $data->scale, $text);
//
//                        if($certificateTemplate->student_photo == 1){
//                            $student->student_image = $data->student_image?$data->student_image:"";
//                            $imageUrl=url('images/studentProfile').'/'.$student->student_image;
//                            $image = "<img class=\"img-thumbnail\" alt=\"\" src=\"$imageUrl\" width=\"150px\" />";
//
//                            $text = str_replace('{{student_image}}', $image, $text);
//                        }else{
//                            $text = str_replace('{{student_image}}', '', $text);
//                        }
//
//                        $certificateTemplate = $this->textReplace($data, $text);
//                        $student->certificate_template = $certificateTemplate;
//
//                        return $student;
//                    });
//
//                    return $data['students'] = $filteredStudent;

                    return view(parent::loadDataToView($this->view_path.'.semester-wise-final-result'), compact('data'));
                }
            }
        }


    //second other methods
        $examScheduleCondition = [
            ['years_id', '=', $request->get('year_id')],
            ['months_id', '=', $request->get('month_id')],
            ['exams_id', '=', $request->get('exams_id')],
            ['faculty_id', '=', $request->get('faculty_id')],
            ['semesters_id', '=', $request->get('semester_id')]
        ];

          /*Find Exam Schedule Id*/
                $examScheduleId = ExamSchedule::select('id')
                    ->where($examScheduleCondition)
                    ->get();
                $examScheduleId = array_pluck($examScheduleId, 'id');
                if(count($examScheduleId) > 0){
                    $data['ledger_exist'] = ExamMarkLedger::select('exam_mark_ledgers.exam_schedule_id', 'exam_mark_ledgers.students_id',
                        'exam_mark_ledgers.obtain_mark_theory', 'exam_mark_ledgers.obtain_mark_practical', 'exam_mark_ledgers.absent_theory','exam_mark_ledgers.absent_practical',
                        'exam_mark_ledgers.status', 's.id as student_id', 's.reg_no', 's.first_name', 's.middle_name', 's.last_name',
                        's.last_name')
                        ->where('exam_mark_ledgers.exam_schedule_id', $examScheduleId)
                        ->join('students as s', 's.id', '=', 'exam_mark_ledgers.students_id')
                        ->orderBy('exam_mark_ledgers.students_id','asc')
                        ->get();
                }else{
                    $request->session()->flash($this->message_warning, 'No any Examination Scheduled. for Your Target Exam. Please Schedule First.');
                    return redirect()->back();
                }


            if($data['ledger_exist']){
                $data['exam_schedule_id'] = implode(',',$examScheduleId);
            }
            $request->request->add(['exam_schedule_id' => $data['exam_schedule_id']]);

        if($request->has('result-type')){
            if($request->get('result-type')=='grading'){
                $data = $this->gradingSystem($request);
                return view(parent::loadDataToView($this->view_path.'.grading-sheet'), compact('data'));
            }
            elseif($request->get('result-type')=='university-grading'){
                $data = $this->universityGradingSystem($request);
                return view(parent::loadDataToView($this->view_path.'.university-grading-sheet'), compact('data'));
            }
            elseif($request->get('result-type') =='percentage'){
                $data = $this->percentageSystem($request);
                return view(parent::loadDataToView($this->view_path.'.mark-sheet'), compact('data'));
            }
            elseif($request->get('result-type') =='ledger'){
                $data = $this->examMarkLedger($request);
                return view(parent::loadDataToView($this->view_path.'.mark-ledger'), compact('data'));
            }
            else{
                return back();
            }
        }else{
            return back();
        }

    }

}

