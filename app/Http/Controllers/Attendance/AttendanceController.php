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
use App\Models\Attendance;
use App\Models\AttendanceMaster;
use App\Models\Faculty;
use App\Models\FeeHead;
use App\Models\Month;
use App\Models\Staff;
use App\Models\Year;
use Illuminate\Http\Request;
use URL;
use ViewHelper;
class AttendanceController extends CollegeBaseController
{
    protected $base_route = 'attendance';
    protected $view_path = 'attendance';
    protected $panel = 'Attendance';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $data = [];
        $data['staff'] = collect();

        if($request->all()) {
            $staffQuery = Staff::select('staff.id as staffs_id', 'staff.id', 'staff.reg_no', 'staff.first_name', 'staff.middle_name', 'staff.last_name');

            if ($request->has('reg_no') && $request->get('reg_no') != null) {
                $staffQuery->where('staff.reg_no', $request->reg_no);
                $this->filter_query['reg_no'] = $request->reg_no;
            }

            $staff = $staffQuery->get();

            $attendanceQuery = Attendance::whereIn('attendable_type', [Staff::class, 'staff'])
                ->whereIn('attendable_id', $staff->pluck('id')->all())
                ->orderBy('date', 'asc');

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

            $entityMap = $staff->keyBy('id')->map(function ($member) {
                return [
                    'staffs_id' => $member->staffs_id,
                    'reg_no' => $member->reg_no,
                    'first_name' => $member->first_name,
                    'middle_name' => $member->middle_name,
                    'last_name' => $member->last_name,
                ];
            })->all();

            $data['staff'] = Attendance::legacyMonthlyCollection($attendanceQuery->get(), 2, $entityMap);
        }

        $data['year'] = [];
        $data['year'][0] = 'Select Year';
        foreach (Year::select('id', 'title')->get() as $year) {
            $data['year'][$year->id] = $year->title;
        }

        $data['month'] = [];
        $data['month'][0] = 'Select Month';
        foreach (Month::select('id', 'title')->orderBy('id','asc')->get() as $month) {
            $data['month'][$month->id] = $month->title;
        }

        $data['faculties'] = $this->activeFaculties();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function findMonth(Request $request)
    {
        $response = [];
        $response['error'] = true;

        if ($request->has('year_id')) {
            $response['months'] = AttendanceMaster::select('attendance_masters.id','attendance_masters.month', 'm.title')
                ->where('year','=',$request->year_id)
                ->join('months as m','m.id','=','attendance_masters.month')
                ->get();

            if ($response['months']) {
                $response['error'] = false;
            } else
                $response['message'] = 'Invalid request!!';

        } else
            $response['message'] = 'Invalid request!!';

        return response()->json(json_encode($response));
    }

}
