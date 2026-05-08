<?php
namespace App\Traits;

use App\Models\Exam;
use App\Models\ExamMarkLedger;
use App\Models\ExamSchedule;
use App\Models\Faculty;
use App\Models\GradingScale;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;

trait ExaminationScope{

    public function activeExams()
    {
        $exams = Exam::Active()->orderBy('title')->pluck('title','id')->toArray();
        return array_prepend($exams,'Select Exams','0');
    }

    public function getExamById($id)
    {
        $exam = Exam::find($id);
        if ($exam) {
            return $exam->title;
        }else{
            return "Unknown";
        }
    }

    public function getSubjectById($id)
    {
        $subject = Subject::find($id);
        if ($subject) {
            return $subject->title;
        }else{
            return "Unknown";
        }
    }

    public function getSubjectCodeById($id)
    {
        $subject = Subject::find($id);
        if ($subject) {
            return $subject->code;
        }else{
            return "Unknown";
        }
    }

    public function getSubCreditById($id)
    {
        $subject = Subject::find($id);
        if ($subject) {
            return $subject->credit_hour;
        }else{
            return "Unknown";
        }
    }

    public function getFinalGrade($semester, $gpa_average)
    {
        $score ="*MG";
        $gradingType = $semester->gradingType()->first();
        if(!$gradingType) return $score;
        $gradingScale = $gradingType->gradingScale()->get();

       // dd($gradingScale->toArray());

        foreach ($gradingScale as $key => $grade){
            //dd($grade);
            if( $key ==0 && $gpa_average >= $gradingScale[$key]->grade_point){
                $score = $grade->name;
            }else{
                if($gpa_average >= $gradingScale[$key]->grade_point && $gpa_average < $gradingScale[$key-1]->grade_point){
                    $score = $grade->name;
                }
            }

        }
        return $score;
    }

    public function getGrade($semester, $percentage)
    {
        $score ="*MG";
        $gradingType = $semester->gradingType()->first();
        if(!$gradingType) return $score;
        $gradingScale = $gradingType->gradingScale()->get();

        foreach ($gradingScale as $grade){
            if($percentage > $grade->percentage_from && $percentage <= $grade->percentage_to){
                $score = $grade->name;
            }
        }
        return $score;
    }

    public function getPoint($semester, $percentage)
    {
        $score ="*MP";
        $gradingType = $semester->gradingType()->first();
        if(!$gradingType) return $score;
        $gradingScale = $gradingType->gradingScale()->get();
        foreach ($gradingScale as $grade){
            if($percentage > $grade->percentage_from && $percentage <= $grade->percentage_to){
                $score = $grade->grade_point;
            }
        }
        return $score;
    }

    public function getRemark($semester, $percentage)
    {
        $score ="";
        $gradingType = $semester->gradingType()->first();
        if(!$gradingType) return $score;
        $gradingScale = $gradingType->gradingScale()->get();
        foreach ($gradingScale as $grade){
            if($percentage >= $grade->percentage_from && $percentage <= $grade->percentage_to){
                $score = $grade->description;
            }
        }
        return $score;
    }

    public function getExamNameByScheduleId($id)
    {
        $examSchedule = ExamSchedule::find($id)->first();
        $exam = Exam::find($examSchedule->exams_id);
        if ($exam) {
            return $exam->title;
        }else{
            return "Unknown";
        }
    }

    public function getStudentRankingInExam($year, $month, $exam, $faculty, $semester,$studentId)
    {
        $year = $year;
        $month = $month;
        $exam = $exam;
        $faculty = $faculty;
        $semester = $semester;
        $studentId = $studentId;

        if($year && $month && $exam && $faculty && $semester) {
            $examScheduleCondition = [
                ['years_id', '=', $year],
                ['months_id', '=', $month],
                ['exams_id', '=', $exam],
                ['faculty_id', '=', $faculty],
                ['semesters_id', '=', $semester]
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

            }

        }

        if($data['ledger_exist']){
            $data['exam_schedule_id'] = implode(',',$examScheduleId);
        }

        $exam_schedule_id = $examScheduleId;
        $student_id = $data['ledger_exist']->pluck('student_id');

        $students = Student::select('id','reg_no', 'first_name','middle_name','last_name','date_of_birth',
            'faculty','semester')
            ->whereIn('id', $student_id)
            ->get();


        /*filter student with schedule subject mark ledger*/
        $filteredStudent  = $students->filter(function ($value, $key) use ($exam_schedule_id){
            $subject = $value->markLedger()
                ->select('exam_schedule_id', 'obtain_mark_theory', 'obtain_mark_practical','absent_theory','absent_practical')
                ->whereIn('exam_schedule_id', $exam_schedule_id)
                ->get();

            //filter subject and joint mark from schedules;
            $filteredSubject  = $subject->filter(function ($subject, $key) {
                $joinSub = $subject->examSchedule()
                    ->select('subjects_id','full_mark_theory', 'pass_mark_theory', 'full_mark_practical', 'pass_mark_practical','sorting_order')
                    ->first();

                $subject->subjects_id = $joinSub->subjects_id;
                $subject->sorting_order = $joinSub->sorting_order;
                $subject->full_mark_theory = $joinSub->full_mark_theory;
                $subject->pass_mark_theory = $joinSub->pass_mark_theory;
                $subject->full_mark_practical = $joinSub->full_mark_practical;
                $subject->pass_mark_practical = $joinSub->pass_mark_practical;
                $th = $subject->obtain_mark_theory;
                $pr = $subject->obtain_mark_practical;
                $absent_theory = $subject->absent_theory;
                $absent_practical = $subject->absent_practical;

                /*theory mark comparision*/
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

                /*Practical mark comparision*/
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

                $subject->total_obtain_mark = (is_numeric($th_new)?$th_new:0) + (is_numeric($pr_new)?$pr_new:0);

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

                return $subject;
            });

            //$value->subjects = $filteredSubject;
            $value->subjects = $filteredSubject->sortBy('sorting_order');

            /*calculate total mark & percentage*/
            $otm = array_pluck($value->subjects,'obtain_mark_theory');

            $filtered_otm  =  array_where($otm, function ($value, $key) {
                return is_numeric($value);
            });
            $obtainedMarkTh = array_sum($filtered_otm);

            $omp = array_pluck($value->subjects,'obtain_mark_practical');
            $filtered_otp  =  array_where($omp, function ($value, $key) {
                return is_numeric($value);
            });
            $obtainedMarkPr = array_sum($filtered_otp);

            $totalMark = $value->subjects->sum('full_mark_theory') + $value->subjects->sum('full_mark_practical');
            $obtainedMark = $obtainedMarkTh + $obtainedMarkPr;

            $value->total_mark_theory = $obtainedMarkTh;
            $value->total_mark_practical = $obtainedMarkPr;
            $value->total_obtain = $obtainedMark;
            /*caculate percentage*/
            $value->percentage = ($obtainedMark*100)/ $totalMark;

            //$value->rank = "1";
            $remark = $value->subjects->pluck('remark')->toArray();
            $pr_remark = $value->subjects->pluck('pr_remark')->toArray();
            if(in_array('*',$remark) || in_array('*',$pr_remark)){
                $remarkOut = "* Fail";
            }else {
                $remarkOut = "Pass";
            }

            $value->remark = $remarkOut;

            return $value;
        });

        $filteredStudent = $filteredStudent->sortByDesc('percentage');

        //for return ranking
        $rank = 1;
        $filteredPassStudent  = $students->filter(function ($value, $key) use (&$rank){
            if($value->remark == "Pass"){
                $value->rank = $rank;
                $rank++;
                return $value;
            }
        });

        $returnStudentRank = $filteredPassStudent->filter(function ($value, $key) use ($studentId){
            if($value->id == $studentId){
                $rank = $value->rank;
                return $rank;
            }
        });

        return implode(',',array_pluck($returnStudentRank,'rank'));

    }

    public function getStudentPositionInExam($year, $month, $exam, $faculty, $semester,$studentId)
    {
        $year = $year;
        $month = $month;
        $exam = $exam;
        $faculty = $faculty;
        $semester = $semester;
        $studentId = $studentId;

        if($year && $month && $exam && $faculty && $semester) {
            $examScheduleCondition = [
                ['years_id', '=', $year],
                ['months_id', '=', $month],
                ['exams_id', '=', $exam],
                ['faculty_id', '=', $faculty],
                ['semesters_id', '=', $semester]
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

            }

        }

        if($data['ledger_exist']){
            $data['exam_schedule_id'] = implode(',',$examScheduleId);
        }

        $exam_schedule_id = $examScheduleId;
        $student_id = $data['ledger_exist']->pluck('student_id');

        $students = Student::select('id','reg_no', 'first_name','middle_name','last_name','date_of_birth',
            'faculty','semester')
            ->whereIn('id', $student_id)
            ->get();


        /*filter student with schedule subject mark ledger*/
        $filteredStudent  = $students->filter(function ($value, $key) use ($exam_schedule_id){
            $subject = $value->markLedger()
                ->select('exam_schedule_id', 'obtain_mark_theory', 'obtain_mark_practical','absent_theory','absent_practical')
                ->whereIn('exam_schedule_id', $exam_schedule_id)
                ->get();

            //filter subject and joint mark from schedules;
            $filteredSubject  = $subject->filter(function ($subject, $key) {
                $joinSub = $subject->examSchedule()
                    ->select('subjects_id','full_mark_theory', 'pass_mark_theory', 'full_mark_practical', 'pass_mark_practical','sorting_order')
                    ->first();

                $subject->subjects_id = $joinSub->subjects_id;
                $subject->sorting_order = $joinSub->sorting_order;
                $subject->full_mark_theory = $joinSub->full_mark_theory;
                $subject->pass_mark_theory = $joinSub->pass_mark_theory;
                $subject->full_mark_practical = $joinSub->full_mark_practical;
                $subject->pass_mark_practical = $joinSub->pass_mark_practical;
                $th = $subject->obtain_mark_theory;
                $pr = $subject->obtain_mark_practical;
                $absent_theory = $subject->absent_theory;
                $absent_practical = $subject->absent_practical;

                /*theory mark comparision*/
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

                /*Practical mark comparision*/
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

                $subject->total_obtain_mark = (is_numeric($th_new)?$th_new:0) + (is_numeric($pr_new)?$pr_new:0);

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

                return $subject;
            });

            //$value->subjects = $filteredSubject;
            $value->subjects = $filteredSubject->sortBy('sorting_order');

            /*calculate total mark & percentage*/
            $otm = array_pluck($value->subjects,'obtain_mark_theory');

            $filtered_otm  =  array_where($otm, function ($value, $key) {
                return is_numeric($value);
            });
            $obtainedMarkTh = array_sum($filtered_otm);

            $omp = array_pluck($value->subjects,'obtain_mark_practical');
            $filtered_otp  =  array_where($omp, function ($value, $key) {
                return is_numeric($value);
            });
            $obtainedMarkPr = array_sum($filtered_otp);

            $totalMark = $value->subjects->sum('full_mark_theory') + $value->subjects->sum('full_mark_practical');
            $obtainedMark = $obtainedMarkTh + $obtainedMarkPr;

            $value->total_mark_theory = $obtainedMarkTh;
            $value->total_mark_practical = $obtainedMarkPr;
            $value->total_obtain = $obtainedMark;
            /*caculate percentage*/
            $value->percentage = ($obtainedMark*100)/ $totalMark;

            //$value->rank = "1";
            $remark = $value->subjects->pluck('remark')->toArray();
            $pr_remark = $value->subjects->pluck('pr_remark')->toArray();
            if(in_array('*',$remark) || in_array('*',$pr_remark)){
                $remarkOut = "* Fail";
            }else {
                $remarkOut = "Pass";
            }

            $value->remark = $remarkOut;

            return $value;
        });

        //$filteredStudent = $filteredStudent->sortByDesc('percentage');
        ////ranking customization keynya user;
        $rank = 0; $score = -1;
        $filteredStudent = $filteredStudent
            ->sortByDesc('total_obtain')->map(function($record) use (&$rank, &$score) {
                if ($score != $record->getAttribute('total_obtain')) {
                    $score = $record->getAttribute('total_obtain');
                    $rank++;
                }

                $record->Position = $rank;
                //$record->setAttribute('Position', $rank);
                //$record->setAttribute('subjects', $record->subjects);
                //return collect($record->getAttributes());
                return $record;
            });

        //$collection = collect($filteredStudent->subjects);
        //$union = $collection->union([3 => ['c'], 1 => ['b']]);
        //$union->all();
        // [1 => ['a'], 2 => ['b'], 3 => ['c']]
        //dd($filteredStudent->toArray());

        //$data['student'] = $filteredStudent;

        //for return ranking
        /*$rank = 1;
        $filteredPassStudent  = $students->filter(function ($value, $key) use ($rank){
            if($value->remark == "Pass"){
                $value->rank = $rank;
                $rank++;
                return $value;
            }
        });*/

        $returnStudentRank = $filteredStudent->filter(function ($value, $key) use ($studentId){
            if($value->id == $studentId){
                $rank = $value->Position;
                return $rank;
            }
        });

        //dd($returnStudentRank);

        return implode(',',array_pluck($returnStudentRank,'Position'));

    }

    //exam print common
    public function percentageSystem(Request $request)
    {
        if ($request->has('chkIds')) {
            $exam_schedule_id = explode(',',$request->get('exam_schedule_id'));
            $student_id = $request->get('chkIds');

            $students = Student::select('id','reg_no', 'first_name','middle_name','last_name','date_of_birth',
                'faculty','semester')
                ->whereIn('id', $student_id)
                ->get();

            /*filter student with schedule subject markledger*/
            $filteredStudent  = $students->filter(function ($value, $key) use ($exam_schedule_id){
                $subject = $value->markLedger()
                    ->select( 'exam_schedule_id',  'obtain_mark_theory', 'obtain_mark_practical','absent_theory','absent_practical')
                    ->whereIn('exam_schedule_id', $exam_schedule_id)
                    ->get();
                //filter subject and joint mark from schedules;
                $filteredSubject  = $subject->filter(function ($subject, $key) {
                    $joinSub = $subject->examSchedule()
                        ->select('subjects_id','full_mark_theory', 'pass_mark_theory', 'full_mark_practical', 'pass_mark_practical','sorting_order')
                        ->first();

                    $subject->subjects_id = $joinSub->subjects_id;
                    $subject->sorting_order = $joinSub->sorting_order;
                    $subject->full_mark_theory = $joinSub->full_mark_theory;
                    $subject->pass_mark_theory = $joinSub->pass_mark_theory;
                    $subject->full_mark_practical = $joinSub->full_mark_practical;
                    $subject->pass_mark_practical = $joinSub->pass_mark_practical;
                    $th = $subject->obtain_mark_theory;
                    $pr = $subject->obtain_mark_practical;
                    $absent_theory = $subject->absent_theory;
                    $absent_practical = $subject->absent_practical;

                    /*theory mark comparision*/
                    if(isset($subject->pass_mark_theory) && $subject->pass_mark_theory != null){
                        if($absent_theory == 1) {
                            $subject->obtain_mark_theory = "AB";
                        }else{
                            //dd($th);//35
                            if(!is_numeric($th)){
                                $subject->obtain_mark_theory = "*";
                            }
                        }
                    }else{
                        $subject->obtain_mark_theory = "-";
                    }

                    /*Practical mark comparision*/
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

                    $subject->total_obtain_mark = (is_numeric($th_new)?$th_new:0) + (is_numeric($pr_new)?$pr_new:0);

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

                    return $subject;
                });

                //$value->subjects = $filteredSubject;
                $value->subjects = $filteredSubject->sortBy('sorting_order');

                /*calculate total mark & percentage*/
                $otm = array_pluck($value->subjects,'obtain_mark_theory');

                $filtered_otm  =  array_where($otm, function ($value, $key) {
                    return is_numeric($value);
                });
                $obtainedMarkTh = array_sum($filtered_otm);

                $omp = array_pluck($value->subjects,'obtain_mark_practical');
                $filtered_otp  =  array_where($omp, function ($value, $key) {
                    return is_numeric($value);
                });
                $obtainedMarkPr = array_sum($filtered_otp);


                $totalMark = $value->subjects->sum('full_mark_theory') + $value->subjects->sum('full_mark_practical');
                $obtainedMark = $obtainedMarkTh + $obtainedMarkPr;

                $value->total_mark_theory = $obtainedMarkTh;
                $value->total_mark_practical = $obtainedMarkPr;
                $value->total_obtain = $obtainedMark;

                /*caculate percentage*/
                $value->percentage = ($obtainedMark*100)/ $totalMark;

                /*Find the Pass Fail Remark*/
                $remark = $value->subjects->pluck('remark')->toArray();
                $pr_remark = $value->subjects->pluck('pr_remark')->toArray();
                if(in_array('*',$remark) || in_array('*',$pr_remark)){
                    $remarkOut = "* Fail";
                }else {
                    $remarkOut = "Pass";
                }

                $value->remark = $remarkOut;

                return $value;
            });

            //$filteredStudent = $filteredStudent->sortByDesc('percentage');
            $rank = 0; $score = -1;
            $filteredStudent = $filteredStudent
                ->sortByDesc('total_obtain')->map(function($record) use (&$rank, &$score) {
                    if ($score != $record->getAttribute('total_obtain')) {
                        $score = $record->getAttribute('total_obtain');
                        $rank++;
                    }

                    $record->position = $rank;
                    //$record->setAttribute('Position', $rank);
                    //$record->setAttribute('subjects', $record->subjects);
                    //return collect($record->getAttributes());
                    return $record;
                });

            //Rank of Pass Student

            $rank = 0; $score = -1;
            $filteredStudent->filter(function ($record, $key) use (&$rank, &$score){
                if($record->remark == "Pass"){
                    /*$record->rank = $rank;
                    $rank++;*/
                    if ($score != $record->getAttribute('total_obtain')) {
                        $score = $record->getAttribute('total_obtain');
                        $rank++;
                    }

                    $record->rank = $rank;
                    return $record;
                }else{
                    $record->rank = 'X';
                }
                return $record;

            });

            $data['student'] = $filteredStudent;

        } else {
            $request->session()->flash($this->message_warning, 'Please, Check at least one Students row.');
            return back();
        }

        $data['exam'] = $request->get('exams_id');
        $data['year'] = $request->get('year_id');
        $data['month'] = $request->get('month_id');
        $data['faculty'] = $request->get('faculty_id');
        $data['semester'] = $request->get('semester_id');

        return $data;
    }

    public function gradingSystem(Request $request)
    {
        if ($request->has('chkIds')) {
            $exam_schedule_id = explode(',',$request->get('exam_schedule_id'));
            $student_id = $request->get('chkIds');

            $data['semester'] = $semester = Semester::find($request->get('semester_id'));

            $students = Student::select('id','reg_no', 'first_name','middle_name','last_name','date_of_birth',
                'faculty','semester')
                ->whereIn('id', $student_id)
                ->get();

            //filter student with schedule subject mark ledger


            $filteredStudent  = $students->filter(function ($value, $key) use ($semester, $exam_schedule_id){
                $subject = $value->markLedger()
                    ->select('exam_schedule_id', 'obtain_mark_theory', 'obtain_mark_practical','absent_theory','absent_practical')
                    ->whereIn('exam_schedule_id', $exam_schedule_id)
                    ->get()->unique('exam_schedule_id');

                //filter subject and joint mark from schedules;
                $filteredSubject  = $subject->filter(function ($subject, $key) use($semester) {
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

                    //th absent
                    if($absent_theory != 1) {
                        if ($full_mark_theory > 0) {
                            $th_per = ($obtain_mark_theory * 100) / $full_mark_theory;
                            $subject->obtain_score_theory = $th_per ==0?'*NG':$this->getGrade($semester, $th_per);
                        }
                    }else{
                        $subject->obtain_score_theory = "*AB";
                    }

                    //pr absent
                    if($absent_practical != 1) {
                        if($full_mark_practical > 0) {
                            $pr_per = ($obtain_mark_practical * 100) / $full_mark_practical;
                            $subject->obtain_score_practical = $pr_per ==0?"*NG":$this->getGrade($semester, $pr_per);
                        }
                    }else{
                        $pr_per = 0;
                        $subject->obtain_score_practical = "*AB";
                    }

                    //check absent on theory & practical
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

                    //theory mark comparison
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

                    //Practical mark compare
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

                    //verify again the new obtain values are number or not
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

                    return $subject;
                });

                $value->subjects = $filteredSubject->sortBy('sorting_order');

                //calculate total mark & percentage
                $otm = array_pluck($value->subjects,'obtain_mark_theory');

                $filtered_otm  =  array_where($otm, function ($value, $key) {
                    return is_numeric($value);
                });
                $obtainedMarkTh = array_sum($filtered_otm);

                $omp = array_pluck($value->subjects,'obtain_mark_practical');
                $filtered_otp  =  array_where($omp, function ($value, $key) {
                    return is_numeric($value);
                });
                $obtainedMarkPr = array_sum($filtered_otp);

                $totalMark = $value->subjects->sum('full_mark_theory') + $value->subjects->sum('full_mark_practical');
                $obtainedMark = $obtainedMarkTh + $obtainedMarkPr;

                $value->total_mark_theory = $obtainedMarkTh;
                $value->total_mark_practical = $obtainedMarkPr;
                $value->total_obtain = $obtainedMark;
                //caculate percentage
                $value->percentage = ($obtainedMark*100)/ $totalMark;


                //calculate grading Score
                //verify both th & pr absent
                if($value->percentage > 0) {
                    //$value->gpa_grade = $this->getGrade($semester, $value->percentage);
                    //$value->gpa_average = $this->getPoint($semester, $value->percentage);
                    $value->gpa_average = round($value->subjects->sum('grade_point')/ $value->subjects->count(),2);
                    $value->gpa_grade = $this->getFinalGrade($semester, $value->gpa_average);
                    $value->gpa_remark = $this->getRemark($semester, $value->percentage);
                }else{
                    $value->final_grade = "*MG";
                    $value->grade_point = "*MP";
                    $value->gpa_remark = "-";
                }


                $remark = $value->subjects->pluck('remark')->toArray();
                $pr_remark = $value->subjects->pluck('pr_remark')->toArray();
                if(in_array('*',$remark) || in_array('*',$pr_remark)){
                    $remarkOut = "* Fail";
                }else {
                    $remarkOut = "Pass";
                }

                $value->remark = $remarkOut;
                //dd($value->toArray());

                return $value;
            });

            $rank = 0; $score = -1;
            $filteredStudent = $filteredStudent
                ->sortByDesc('total_obtain')->map(function($record) use (&$rank, &$score) {
                    if ($score != $record->getAttribute('total_obtain')) {
                        $score = $record->getAttribute('total_obtain');
                        $rank++;
                    }

                    $record->position = $rank;
                    return $record;
                });

            //Rank of Pass Student
            $rank = 0; $score = -1;
            $filteredStudent->filter(function ($record, $key) use (&$rank, &$score){
                if($record->remark == "Pass"){
                    //$record->rank = $rank;
                    // $rank++;
                    if ($score != $record->getAttribute('total_obtain')) {
                        $score = $record->getAttribute('total_obtain');
                        $rank++;
                    }

                    $record->rank = $rank;
                    return $record;
                }else{
                    $record->rank = 'X';
                }
                return $record;

            });

            $data['student'] = $filteredStudent;
            // dd($filteredStudent->toArray());

            //Detail of Grade Sheet
            $data['grade-scale-range'] = GradingScale::where('gradingType_id',$semester->gradingType_id)->get();


        } else {
            $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
            return back();
        }

        $data['exam'] = $request->get('exams_id');
        $data['year'] = $request->get('year_id');
        $data['faculty'] = $request->get('faculty_id');
        $data['semester'] = $request->get('semester_id');

        return $data ;
    }


    public function universityGradingSystem(Request $request)
    {
        if ($request->has('chkIds')) {
            $exam_schedule_id = explode(',',$request->get('exam_schedule_id'));
            $student_id = $request->get('chkIds');

            $data['semester'] = $semester = Semester::find($request->get('semester_id'));

            $students = Student::select('id','reg_no', 'first_name','middle_name','last_name','date_of_birth',
                'faculty','semester')
                ->whereIn('id', $student_id)
                ->get();

            //filter student with schedule subject mark ledger
            $filteredStudent  = $students->filter(function ($value, $key) use ($semester, $exam_schedule_id){
                $subject = $value->markLedger()
                                    ->select('exam_mark_ledgers.exam_schedule_id', 'exam_mark_ledgers.students_id',
                                        'exam_mark_ledgers.obtain_mark_theory', 'exam_mark_ledgers.obtain_mark_practical', 'exam_mark_ledgers.absent_theory', 'exam_mark_ledgers.absent_practical',
                                        'exam_mark_ledgers.status', 's.id as student_id', 's.reg_no', 's.first_name', 's.middle_name', 's.last_name', 's.last_name',
                                        'es.semesters_id','es.subjects_id','sub.title as SubjectTitle','sub.code')
                                    ->whereIn('exam_schedule_id', $exam_schedule_id)
                                    ->join('students as s', 's.id', '=', 'exam_mark_ledgers.students_id')
                                    ->join('exam_schedules as es', 'es.id', '=', 'exam_mark_ledgers.exam_schedule_id')
                                    ->join('subjects as sub', 'sub.id', '=', 'es.subjects_id')
                                    ->orderBy('exam_mark_ledgers.students_id', 'asc')
                                    ->orderBy('sub.title', 'asc')
                                    ->get();

                //filter subject and joint mark from schedules;
                $filteredSubject  = $subject->filter(function ($subject, $key) use($semester) {
                    //dd($subject);
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
                    //th absent
                    if($absent_theory != 1) {
                        if ($full_mark_theory > 0) {
                            $th_per = ($obtain_mark_theory * 100) / $full_mark_theory;


                            $subject->obtain_score_theory = $th_per ==0?'*NG':$this->getGrade($semester, $th_per);
                        }
                    }else{
                        $subject->obtain_score_theory = "*AB";
                    }
                    //pr absent
                    if($absent_practical != 1) {
                        if($full_mark_practical > 0) {
                            $pr_per = ($obtain_mark_practical * 100) / $full_mark_practical;

                            $subject->obtain_score_practical = $pr_per ==0?"*NG":$this->getGrade($semester, $pr_per);
                        }
                    }else{
                        $pr_per = 0;
                        $subject->obtain_score_practical = "*AB";
                    }

                    //check absent on theory & practical
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

                    //theory mark comparison
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

                    //Practical mark compare
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

                    //verify again the new obtain values are number or not
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
                    $subject->creditHour = number_format((float)Subject::find($subject->subjects_id)->credit_hour,2);
//                    if (is_numeric($subject->grade_point) && is_numeric($subject->creditHour)) {
//                        $subject->gradeWithCredit = $subject->grade_point * $subject->creditHour;
//                    } else {
//                        $subject->gradeWithCredit = 0;
//                    }
                    $subject->gradeWithCredit = $subject->grade_point * $subject->creditHour;
                    return $subject;
                });

                //$value->subjects = $filteredSubject->sortBy('sorting_order');
                $value->subjects = $filteredSubject;

                //calculate total mark & percentage
                $otm = array_pluck($value->subjects,'obtain_mark_theory');

                $filtered_otm  =  array_where($otm, function ($value, $key) {
                    return is_numeric($value);
                });
                $obtainedMarkTh = array_sum($filtered_otm);

                $omp = array_pluck($value->subjects,'obtain_mark_practical');
                $filtered_otp  =  array_where($omp, function ($value, $key) {
                    return is_numeric($value);
                });
                $obtainedMarkPr = array_sum($filtered_otp);

                $totalMark = $value->subjects->sum('full_mark_theory') + $value->subjects->sum('full_mark_practical');
                $obtainedMark = $obtainedMarkTh + $obtainedMarkPr;

                $value->total_mark_theory = $obtainedMarkTh;
                $value->total_mark_practical = $obtainedMarkPr;
                $value->total_obtain = $obtainedMark;
                //caculate percentage
                $value->percentage = ($obtainedMark*100)/ $totalMark;


                //calculate grading Score
                //verify both th & pr absent
                if($value->percentage > 0) {
                    //$value->gpa_grade = $this->getGrade($semester, $value->percentage);
                    //$value->gpa_average = $this->getPoint($semester, $value->percentage);
                    $value->gpa_average = round($value->subjects->sum('grade_point')/ $value->subjects->count(),2);
                    // $value->gpa_grade = $this->getFinalGrade($semester, $value->gpa_average);
                    $value->gpa_remark = $this->getRemark($semester, $value->percentage);
                    //gpa according to university (credit*obtainGP)/CreditHour

                    $value->creditHourSum = $creditHourSum = $value->subjects->sum('creditHour');
                    $gradeWithCreditSum = $value->subjects->sum('gradeWithCredit');
                    //  dd($gradeWithCreditSum , $creditHourSum);
                    $value->gpa_grade = number_format((float)$gradeWithCreditSum / $creditHourSum,2);
                    $value->gpa_gradeletter =  $this->getFinalGrade($semester, $value->gpa_grade);

                }else{
                    $value->final_grade = "*MG";
                    $value->grade_point = "*MP";
                    $value->gpa_remark = "-";
                }


                $remark = $value->subjects->pluck('remark')->toArray();
                $pr_remark = $value->subjects->pluck('pr_remark')->toArray();
                if(in_array('*',$remark) || in_array('*',$pr_remark)){
                    $remarkOut = "* Fail";
                }else {
                    $remarkOut = "Pass";
                }

                $value->remark = $remarkOut;

                return $value;
            });

            $rank = 0; $score = -1;
            $filteredStudent = $filteredStudent
                ->sortByDesc('total_obtain')->map(function($record) use (&$rank, &$score) {
                    if ($score != $record->getAttribute('total_obtain')) {
                        $score = $record->getAttribute('total_obtain');
                        $rank++;
                    }

                    $record->position = $rank;
                    return $record;
                });

            //Rank of Pass Student
            $rank = 0; $score = -1;
            $filteredStudent->filter(function ($record, $key) use (&$rank, &$score){
                if($record->remark == "Pass"){
                    //$record->rank = $rank;
                    // $rank++;
                    if ($score != $record->getAttribute('total_obtain')) {
                        $score = $record->getAttribute('total_obtain');
                        $rank++;
                    }

                    $record->rank = $rank;
                    return $record;
                }else{
                    $record->rank = 'X';
                }
                return $record;

            });

            $data['student'] = $filteredStudent;

            //Detail of Grade Sheet
            $data['grade-scale-range'] = GradingScale::where('gradingType_id',$semester->gradingType_id)->get();


        } else {
            $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
            return back();
        }

        $data['exam'] = $request->get('exams_id');
        $data['year'] = $request->get('year_id');
        $data['faculty'] = $request->get('faculty_id');
        $data['semester'] = $request->get('semester_id');



        return $data ;
    }


    public function examMarkLedger(Request $request)
    {

        if ($request->has('chkIds')) {
            $exam_schedule_id = explode(',', $request->get('exam_schedule_id'));
            $student_id = $request->get('chkIds');
            $semester = Semester::find($request->get('semester_id'));


            $students = Student::select('id','reg_no', 'first_name','middle_name','last_name','date_of_birth',
                'faculty','semester')
                ->whereIn('id', $student_id)
                ->get();

            /*filter student with schedule subject mark ledger*/
            $filteredStudent  = $students->filter(function ($value, $key) use ($semester, $exam_schedule_id){
                //dd($exam_schedule_id);
                $subject = $value->markLedger()
                    ->select('exam_schedule_id', 'obtain_mark_theory', 'obtain_mark_practical','absent_theory','absent_practical')
                    ->whereIn('exam_schedule_id', $exam_schedule_id)
                    ->get()->unique('exam_schedule_id');

                //filter subject and joint mark from schedules;
                $filteredSubject  = $subject->filter(function ($subject, $key) use($semester) {
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

                    return $subject;
                });

                //$value->subjects = $filteredSubject;

                $value->subjects = $filteredSubject->sortBy('sorting_order');

                /*calculate total mark & percentage*/
                $otm = array_pluck($value->subjects,'obtain_mark_theory');

                $filtered_otm  =  array_where($otm, function ($value, $key) {
                    return is_numeric($value);
                });
                $obtainedMarkTh = array_sum($filtered_otm);

                $omp = array_pluck($value->subjects,'obtain_mark_practical');
                $filtered_otp  =  array_where($omp, function ($value, $key) {
                    return is_numeric($value);
                });
                $obtainedMarkPr = array_sum($filtered_otp);

                $totalMark = $value->subjects->sum('full_mark_theory') + $value->subjects->sum('full_mark_practical');
                $obtainedMark = $obtainedMarkTh + $obtainedMarkPr;

                $value->total_mark_theory = $obtainedMarkTh;
                $value->total_mark_practical = $obtainedMarkPr;
                $value->total_obtain = $obtainedMark;
                /*caculate percentage*/
                $value->percentage = ($obtainedMark*100)/ $totalMark;

                /*calculate grading Score*/
                //verify both th & pr absent
                if($value->percentage > 0) {
                    // $value->final_grade = $this->getGrade($semester, $value->percentage);
                    //$value->grade_point = $this->getPoint($semester, $value->percentage);
                    // $value->grade_point = round($value->subjects->sum('grade_point')/ $value->subjects->count(),2);
                    //$value->gpa_grade = $this->getGrade($semester, $value->percentage);
                    //$value->gpa_average = $this->getPoint($semester, $value->percentage);
                    $value->grade_point = round($value->subjects->sum('grade_point')/ $value->subjects->count(),2);
                    $value->final_grade = $this->getFinalGrade($semester, $value->grade_point);
                    $value->remark = $this->getRemark($semester, $value->percentage);
                }else{
                    $value->final_grade = "*MG";
                    $value->grade_point = "*MP";
                    $value->remark = "-";
                }

                $remark = $value->subjects->pluck('remark')->toArray();
                $pr_remark = $value->subjects->pluck('pr_remark')->toArray();
                if(in_array('*',$remark) || in_array('*',$pr_remark)){
                    $remarkOut = "* Fail";
                }else {
                    $remarkOut = "Pass";
                }

                $value->remark = $remarkOut;

                return $value;
            });

            //ranking customization kenya user;
            $rank = 0; $score = -1;
            $filteredStudent = $filteredStudent
                ->sortByDesc('total_obtain')->map(function($record) use (&$rank, &$score) {
                    if ($score != $record->getAttribute('total_obtain')) {
                        $score = $record->getAttribute('total_obtain');
                        $rank++;
                    }

                    $record->position = $rank;
                    return $record;
                });

            //Rank of Pass Student
            $rank = 0; $score = -1;
            $filteredStudent->filter(function ($record, $key) use (&$rank, &$score){
                //dd($record->subjects->toArray());
                if($record->remark == "Pass"){
                    /*$record->rank = $rank;
                    $rank++;*/
                    if ($score != $record->getAttribute('total_obtain')) {
                        $score = $record->getAttribute('total_obtain');
                        $rank++;
                    }

                    $record->rank = $rank;
                    return $record;
                }else{
                    $record->rank = 'X';
                }
                return $record;

            });

            $data['student'] = $filteredStudent;

        } else {
            $request->session()->flash($this->message_warning, 'Please, Check at least one Students row.');
            return back();
        }

        $data['exam'] = $request->get('exams_id');
        $data['year'] = $request->get('year_id');
        $data['month'] = $request->get('month_id');
        $data['faculty'] = $request->get('faculty_id');
        $data['semester'] = $request->get('semester_id');
        return $data;
    }





//    global exam print

// transcript print with template
    public function bulkTranscriptPrint($certificateTemplate, $studIds)
    {
        $students = Student::select('id')->whereIn('id',$studIds)->get();

        $filteredStudent = $students->filter(function ($student, $key) use($certificateTemplate) {
            $data = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
                'students.faculty','students.semester','students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
                'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group',  'students.religion',
                'students.caste','students.nationality', 'students.mother_tongue', 'students.email', 'students.extra_info',
                'students.status',
                'ai.address', 'ai.state', 'ai.country', 'ai.temp_address', 'ai.temp_state', 'ai.temp_country', 'ai.home_phone',
                'ai.mobile_1', 'ai.mobile_2',
                'pd.grandfather_first_name',
                'pd.grandfather_middle_name', 'pd.grandfather_last_name', 'pd.father_first_name', 'pd.father_middle_name',
                'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_office', 'pd.father_office_number',
                'pd.father_residence_number', 'pd.father_mobile_1', 'pd.father_mobile_2', 'pd.father_email', 'pd.mother_first_name',
                'pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation', 'pd.mother_office',
                'pd.mother_office_number', 'pd.mother_residence_number', 'pd.mother_mobile_1', 'pd.mother_mobile_2', 'pd.mother_email',
                'gd.id as guardian_id', 'gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
                'gd.guardian_eligibility', 'gd.guardian_occupation', 'gd.guardian_office', 'gd.guardian_office_number', 'gd.guardian_residence_number',
                'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email', 'gd.guardian_relation', 'gd.guardian_address',
                'students.student_image','students.student_signature', 'pd.father_image', 'pd.mother_image', 'gd.guardian_image',
                'tc.id as certificate_id', 'tc.date_of_issue','tc.trc_num', 'tc.year','tc.duration','tc.credit_required',
                'tc.gpa','tc.verification_code', 'tc.mark_sheet_sn', 'tc.provisional_certificate_num',
                'tc.ref_text')
                ->where('students.id','=',$student->id)
                ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
                ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                ->join('student_guardians as sg', 'sg.students_id','=','students.id')
                ->join('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
                ->join('transcript_certificates as tc', 'tc.students_id', '=', 'students.id')
                ->first();

            $student->date_of_issue = $data->date_of_issue;
            $student->year = $data->year;
            $student->trc_num = $data->trc_num;
            $student->faculty = $data->faculty;
            $student->reg_no = $data->reg_no;
            $student->university_reg = $data->university_reg;
            $student->certificate = $certificateTemplate->certificate;

            $text = str_replace('{{date_of_issue}}', Carbon::parse($data->date_of_leaving)->format('d-m-Y'), $certificateTemplate->template);
            $text = str_replace('{{trc_num}}', $data->trc_num, $text);
            $text = str_replace('{{year}}', $data->year, $text);
            $text = str_replace('{{duration}}', $data->duration, $text);
            $text = str_replace('{{credit_required}}', $data->credit_required, $text);
            //semester wise
            $text = str_replace('{{gpa}}', $data->gpa, $text);
            $text = str_replace('{{verification_code}}', $data->verification_code, $text);
            $text = str_replace('{{mark_sheet_sn}}', $data->mark_sheet_sn, $text);
            $text = str_replace('{{provisional_certificate_num}}', $data->provisional_certificate_num, $text);

//            if($certificateTemplate->student_photo == 1){
//                $student->student_image = $data->student_image?$data->student_image:"";
//                $imageUrl=url('images/studentProfile').'/'.$student->student_image;
//                $image = "<img class=\"img-thumbnail\" alt=\"\" src=\"$imageUrl\" width=\"150px\" />";
//
//                $text = str_replace('{{student_image}}', $image, $text);
//            }else{
//                $text = str_replace('{{student_image}}', '', $text);
//            }

            $certificateTemplate = $this->textReplace($data, $text);
            $student->certificate_template = $certificateTemplate;

            //transcript start
            $groupBySemester = $ledgerExist = ExamMarkLedger::select('exam_mark_ledgers.exam_schedule_id', 'exam_mark_ledgers.students_id',
                'exam_mark_ledgers.obtain_mark_theory', 'exam_mark_ledgers.obtain_mark_practical', 'exam_mark_ledgers.absent_theory', 'exam_mark_ledgers.absent_practical',
                'exam_mark_ledgers.status', 's.id as student_id', 's.reg_no', 's.first_name', 's.middle_name', 's.last_name', 's.last_name',
                'es.semesters_id','es.subjects_id','sub.title as SubjectTitle','sub.code')
                ->where('s.id', $student->id)
                //->where('exam_mark_ledgers.exam_schedule_id', $examSchedue->id)
                //->groupBy('es.semesters_id')
                //->groupBy(\DB::raw('es.semesters_id'))
                ->join('students as s', 's.id', '=', 'exam_mark_ledgers.students_id')
                ->join('exam_schedules as es', 'es.id', '=', 'exam_mark_ledgers.exam_schedule_id')
                ->join('subjects as sub', 'sub.id', '=', 'es.subjects_id')
                ->orderBy('exam_mark_ledgers.students_id', 'asc')
                ->orderBy('sub.code', 'asc')
                ->get();

            $student->semestersList = Semester::whereIn('id',$groupBySemester->pluck('semesters_id')->unique())->orderBy('semester')->get()->pluck('semester','id');
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
                        $subject->grade_point =0 /*"*MP"*/;
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
//                    $subject->creditHour = Subject::find($subject->subjects_id)->credit_hour;
                    $subject->creditHour = number_format((float)Subject::find($subject->subjects_id)->credit_hour,2);
                    //                            if (is_numeric($subject->grade_point) && is_numeric($subject->creditHour)) {
//                                $subject->gradeWithCredit = $subject->grade_point * $subject->creditHour;
//                            } else {
//                                $subject->gradeWithCredit = 0;
//                            }
                    //$subject->gradeWithCredit = number_format((float)$subject->grade_point * $subject->creditHour,2);
                    //dd($subject->grade_point , $subject->creditHour);umesh
                    $subject->gradeWithCredit = $subject->grade_point * $subject->creditHour;
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
                    $value->grade_point = number_format($value->sum('grade_point')/ $value->count(),2);
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
                $gpaGrade[$key] = $gpa_grade = number_format((float)$gradeWithCreditSum[$key] / $creditHourSum[$key],2);
                $GradeLetter[$key] = $this->getFinalGrade($semester, $gpa_grade);

                $semesterDetailLedger[$key] = $value->toArray();
            }

            $student->creditHourSum = $creditHourSum ;
            $student->gradeWithCredit = $gradeWithCreditSum ;
            $student->gpaGrade = $gpaGrade ;
            $student->GradeLetter = $GradeLetter ;
            $student->semesterLedger = $semesterDetailLedger ;

            //transcript gpa calculation
            $student->transcriptCHS = $transcriptCHS = array_sum($creditHourSum);
            $student->transcriptGradeWithCredit = $transcriptGradeWithCredit = array_sum($gradeWithCreditSum);
            $student->transcriptGPA = number_format((float)$transcriptGradeWithCredit / $transcriptCHS,2);
            //$student->transcriptGPA = round($transcriptGradeWithCredit / $transcriptCHS,4);
            $student->transcriptGL = $this->getFinalGrade($semester, $gpa_grade);

            //transcript end
            $facultyDetail = Faculty::find($student->faculty);
            $student->gradeScaleRange = GradingScale::where('gradingType_id',$facultyDetail->gradingType_id)->get();

            return $student;
        });

        //dd($filteredStudent);
        return $data['students'] = $filteredStudent;

    }

    public function getTranscriptData($studIds)
    {
        $certificateTemplate = 'TRANSCRIPT';
        //$students = Student::select('id')->whereIn('id',$studIds)->get();
        //DD($studIds);

        //START
        $student = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
            'students.faculty','students.semester','students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
            'students.last_name', 'students.date_of_birth', 'students.gender', /*'students.blood_group',  'students.religion',
            'students.caste','students.nationality', 'students.mother_tongue', 'students.email', 'students.extra_info',
            'students.status',*/
            'f.faculty as faculty_title', 'f.faculty_code', 'f.gradingType_id','f.scale', 'f.sorting', 'f.duration','f.credit_required', 'f.registration_validate'
         )
            ->where('students.id','=',$studIds)
            ->join('faculties as f', 'f.id', '=', 'students.faculty')
            ->first();

        /*$student->date_of_issue = $data->date_of_issue;
        $student->year = $data->year;
        $student->trc_num = $data->trc_num;
        $student->faculty = $data->faculty;
        $student->reg_no = $data->reg_no;
        $student->university_reg = $data->university_reg;
        $student->certificate = $certificateTemplate->certificate;

        $text = str_replace('{{date_of_issue}}', Carbon::parse($data->date_of_leaving)->format('d-m-Y'), $certificateTemplate->template);
        $text = str_replace('{{trc_num}}', $data->trc_num, $text);
        $text = str_replace('{{year}}', $data->year, $text);
        $text = str_replace('{{duration}}', $data->duration, $text);
        $text = str_replace('{{credit_required}}', $data->credit_required, $text);
        //semester wise
        $text = str_replace('{{gpa}}', $data->gpa, $text);
        $text = str_replace('{{verification_code}}', $data->verification_code, $text);
        $text = str_replace('{{mark_sheet_sn}}', $data->mark_sheet_sn, $text);
        $text = str_replace('{{provisional_certificate_num}}', $data->provisional_certificate_num, $text);*/

//            if($certificateTemplate->student_photo == 1){
//                $student->student_image = $data->student_image?$data->student_image:"";
//                $imageUrl=url('images/studentProfile').'/'.$student->student_image;
//                $image = "<img class=\"img-thumbnail\" alt=\"\" src=\"$imageUrl\" width=\"150px\" />";
//
//                $text = str_replace('{{student_image}}', $image, $text);
//            }else{
//                $text = str_replace('{{student_image}}', '', $text);
//            }

        /*$certificateTemplate = $this->textReplace($data, $text);
        $student->certificate_template = $certificateTemplate;*/

        //transcript start
        $groupBySemester = $ledgerExist = ExamMarkLedger::select('exam_mark_ledgers.exam_schedule_id', 'exam_mark_ledgers.students_id',
            'exam_mark_ledgers.obtain_mark_theory', 'exam_mark_ledgers.obtain_mark_practical', 'exam_mark_ledgers.absent_theory', 'exam_mark_ledgers.absent_practical',
            'exam_mark_ledgers.status', 's.id as student_id', 's.reg_no', 's.first_name', 's.middle_name', 's.last_name', 's.last_name',
            'es.semesters_id','es.subjects_id','sub.title as SubjectTitle','sub.code')
            ->where('s.id', $studIds)
            //->where('exam_mark_ledgers.exam_schedule_id', $examSchedue->id)
            //->groupBy('es.semesters_id')
            //->groupBy(\DB::raw('es.semesters_id'))
            ->join('students as s', 's.id', '=', 'exam_mark_ledgers.students_id')
            ->join('exam_schedules as es', 'es.id', '=', 'exam_mark_ledgers.exam_schedule_id')
            ->join('subjects as sub', 'sub.id', '=', 'es.subjects_id')
            ->orderBy('exam_mark_ledgers.students_id', 'asc')
            ->orderBy('sub.code', 'asc')
            ->get();

        $student->semestersList = Semester::whereIn('id',$groupBySemester->pluck('semesters_id')->unique())->orderBy('semester')->get()->pluck('semester','id');
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
                    $subject->grade_point =0 /*"*MP"*/;
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
//                    $subject->creditHour = Subject::find($subject->subjects_id)->credit_hour;
                $subject->creditHour = number_format((float)Subject::find($subject->subjects_id)->credit_hour,2);
                //                            if (is_numeric($subject->grade_point) && is_numeric($subject->creditHour)) {
//                                $subject->gradeWithCredit = $subject->grade_point * $subject->creditHour;
//                            } else {
//                                $subject->gradeWithCredit = 0;
//                            }
                //$subject->gradeWithCredit = number_format((float)$subject->grade_point * $subject->creditHour,2);
                //dd($subject->grade_point , $subject->creditHour);umesh
                $subject->gradeWithCredit = $subject->grade_point * $subject->creditHour;
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
                $value->grade_point = number_format($value->sum('grade_point')/ $value->count(),2);
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
            $gpaGrade[$key] = $gpa_grade = number_format((float)$gradeWithCreditSum[$key] / $creditHourSum[$key],2);
            $GradeLetter[$key] = $this->getFinalGrade($semester, $gpa_grade);

            $semesterDetailLedger[$key] = $value->toArray();
        }

        $student->creditHourSum = $creditHourSum ;
        $student->gradeWithCredit = $gradeWithCreditSum ;
        $student->gpaGrade = $gpaGrade ;
        $student->GradeLetter = $GradeLetter ;
        $student->semesterLedger = $semesterDetailLedger ;

        //transcript gpa calculation
        $student->transcriptCHS = $transcriptCHS = array_sum($creditHourSum);
        $student->transcriptGradeWithCredit = $transcriptGradeWithCredit = array_sum($gradeWithCreditSum);
        $student->transcriptGPA = number_format((float)$transcriptGradeWithCredit / $transcriptCHS,2);
        //$student->transcriptGPA = round($transcriptGradeWithCredit / $transcriptCHS,4);
        $student->transcriptGL = $this->getFinalGrade($semester, $gpa_grade);

        //transcript end
        //$facultyDetail = Faculty::find($student->faculty);
        //$student->gradeScaleRange = GradingScale::where('gradingType_id',$facultyDetail->gradingType_id)->get();
        //END
        //DD($student);

        //dd($filteredStudent);
        return $student;

    }




}