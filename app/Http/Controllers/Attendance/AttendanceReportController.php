<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\CollegeBaseController;
use App\Models\AlertSetting;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\Month;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Year;
use App\Traits\AcademicScope;
use App\Traits\SmsEmailScope;
use App\Traits\StudentScopes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;
use View, URL;

class AttendanceReportController extends CollegeBaseController
{
    protected $base_route = 'attendance.student.report';
    protected $view_path = 'attendance.student.report';
    protected $panel = 'Student Attendance Report';
    protected $filter_query = [];

    use AcademicScope;
    use SmsEmailScope;
    use StudentScopes;

    public function __construct()
    {

    }

    public function student(Request $request)
    {
        $data = [];
        $data['tag'] ='';
        if($request->all()) {
            if(auth()->user()->hasRole('staff')) {
                $id = auth()->user()->id;
                $staffId = auth()->user()->hook_id;

                if($request->has('semester_select')){
                    $semesterStaff = Semester::where('staff_id',$staffId)->where('id',$request->semester_select)->first();

                    if(isset($semesterStaff)) {
                        $students = $this->buildLegacyStudentAttendanceRows($request, $id, $semesterStaff->id);
                    }else{
                        $request->session()->flash($this->message_warning, 'You are not a valid Staff for target Semester/Section.');
                    }
                }else{
                    $request->session()->flash($this->message_warning, 'Please Filter Attendance with Semester/Section.');
                }

            }else{
                if($request->attendance_status || $request->attendance_date){
                    $students = $this->buildLegacyStudentAttendanceRows($request, null);

                    $data['tag'] = 'date-status';
                    $data['date'] = $request->attendance_date;
                }else{
                    $students = $this->buildLegacyStudentAttendanceRows($request, null);

                    $data['tag'] = 'month-filter';
                }

            }
        }


        $attendanceStatus = AttendanceStatus::get();
        if(isset($students)){
            $filteredStudent = $students->filter(function ($student, $key) use($attendanceStatus) {
                for ($day = 1; $day <= 32; $day++) {
                    $dayCode = "day_".$day;
                    foreach ($attendanceStatus as $attenStatus){
                        if($student->$dayCode == $attenStatus->id){
                            $attenTitle = $attenStatus->title;
                            $student->$attenTitle = $student->$attenTitle + 1;
                        }
                    }
                }

                return $student;
            });



            $data['student'] = $filteredStudent;
        }

        $data['attendanceStatus'] = $attendanceStatus;
        $attendanceStatusFilter = AttendanceStatus::Active()->orderBy('title')->pluck('title','id')->toArray();/*$attendanceStatus;*/
        $data['attendanceStatusFilter'] = array_prepend($attendanceStatusFilter,'Select Status','0');
        $data['years'] = $this->activeYears();
        $data['months'] = $this->activeMonths();
        $data['faculties'] = $this->activeFaculties();
        $data['batch'] = $this->activeBatch();
        $data['academic_status'] = $this->activeStudentAcademicStatus();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function staff(Request $request)
    {
        return Back();
    }

    protected function buildLegacyStudentAttendanceRows(Request $request, $createdBy = null, $forceSemesterId = null)
    {
        $studentQuery = Student::select(
            'students.id',
            'students.reg_no',
            'students.first_name',
            'students.middle_name',
            'students.last_name',
            'students.faculty',
            'students.semester'
        )->where(function ($query) use ($request) {
            $this->commonStudentFilterCondition($query, $request);
        });

        if ($forceSemesterId) {
            $studentQuery->where('students.semester', $forceSemesterId);
        }

        $studentRows = $studentQuery->get();

        if ($studentRows->isEmpty()) {
            return collect();
        }

        $attendanceQuery = Attendance::whereIn('attendable_type', [Student::class, 'student'])
            ->whereIn('attendable_id', $studentRows->pluck('id')->all())
            ->orderBy('date', 'asc');

        if ($createdBy) {
            $attendanceQuery->where('created_by', $createdBy);
        }

        if ($request->has('year') && $request->get('year') != 0) {
            $year = Year::find($request->year);
            if ($year) {
                $attendanceQuery->whereYear('date', $year->title);
                $this->filter_query['year'] = $request->year;
            }
        }

        if ($request->has('month') && $request->get('month') != 0) {
            $attendanceQuery->whereMonth('date', $request->month);
            $this->filter_query['month'] = $request->month;
        }

        if ($request->attendance_date) {
            $attendanceQuery->whereDate('date', $request->attendance_date);
            $this->filter_query['attendance_date'] = $request->attendance_date;
        }

        if ($request->get('attendance_status') > 0) {
            $attendanceQuery->where('attendance_status_id', $request->attendance_status);
            $this->filter_query['attendance_status'] = $request->attendance_status;
        }

        $entityMap = $studentRows->keyBy('id')->map(function ($student) {
            return [
                'students_id' => $student->id,
                'reg_no' => $student->reg_no,
                'first_name' => $student->first_name,
                'middle_name' => $student->middle_name,
                'last_name' => $student->last_name,
                'faculty' => $student->faculty,
                'semester' => $student->semester,
            ];
        })->all();

        return Attendance::legacyMonthlyCollection($attendanceQuery->get(), 1, $entityMap);
    }

}