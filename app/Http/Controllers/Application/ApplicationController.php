<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\Application;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Application\AddValidation;
use App\Http\Requests\Application\EditValidation;
use App\Models\Application;
use App\Models\ApplicationType;
use App\Models\Assignment;
use App\Models\AssignmentAnswer;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookStatus;
use App\Models\Faculty;
use App\Models\HomeWork;
use App\Models\Semester;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use URL;
use ViewHelper;
class ApplicationController extends CollegeBaseController
{
    protected $base_route = 'application';
    protected $view_path = 'application';
    protected $panel = 'Application';
    protected $folder_path;
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        if($request->all()) {
            if(auth()->user()->hasRole('staff')) {
                $id = auth()->user()->id;
                $data['application'] = Application::where('created_by',$id)
                    ->where(function ($query) use ($request) {
                        if ($request->year_id && $request->year_id > 0) {
                            $query->where('years_id', '=', $request->year);
                            $this->filter_query['years_id'] = $request->year;
                        }

                        if ($request->application_type && $request->application_type != "") {
                            $query->where('application_type', 'like', '%' . $request->application_type . '%');
                            $this->filter_query['application_type'] = $request->application_type;
                        }

                        if ($request->date_start != "" && $request->date_end != "") {
                            $query->whereBetween('date', [$request->date_start, $request->date_end]);
                            $this->filter_query['date_start'] = $request->date_start;
                            $this->filter_query['date_end'] = $request->date_end;
                        } elseif ($request->date_start != "") {
                            $query->where('date', '>=', $request->date_start);
                            $this->filter_query['date_start'] = $request->date_start;
                        }
                    })
                    ->latest()
                    ->get();
            }else{
                $data['application'] = Application::where(function ($query) use ($request) {
                        if ($request->year_id && $request->year_id > 0) {
                            $query->where('years_id', '=', $request->year);
                            $this->filter_query['years_id'] = $request->year;
                        }

                        if ($request->application_type && $request->application_type != "") {
                            $query->where('application_type_id', 'like', '%' . $request->application_type . '%');
                            $this->filter_query['application_type'] = $request->application_type;
                        }

                        if ($request->date_start != "" && $request->date_end != "") {
                            $query->whereBetween('date', [$request->date_start, $request->date_end]);
                            $this->filter_query['date_start'] = $request->date_start;
                            $this->filter_query['date_end'] = $request->date_end;
                        } elseif ($request->date_start != "") {
                            $query->where('date', '>=', $request->date_start);
                            $this->filter_query['date_start'] = $request->date_start;
                        }

                    })
                    ->latest()
                    ->get();
            }
        }else{
            if(auth()->user()->hasRole('staff')) {
                $id = auth()->user()->id;
                $data['application'] = Application::where('created_by',$id)
                    ->latest()
                    ->limit($this->pagination_limit)
                    ->get();
            }elseif(auth()->user()->hasRole('student')) {
                $id = auth()->user()->id;
                $data['application'] = Application::where('created_by',$id)
                    ->latest()
                    ->limit($this->pagination_limit)
                    ->get();
            }else {

                $data['application'] = Application::latest()
                    ->limit($this->pagination_limit)
                    ->get();
                //dd($data['application']);
            }
        }

        $data['years'] = $this->activeYears();
        $data['applicationType'] = $this->activeApplicationType();
       // dd($data['applicationType']);
        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function add(Request $request)
    {
        $data = [];
        $data['applicationType'] = $this->activeApplicationType();

        $data['url'] = URL::current();
        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {

        $year = Year::where('active_status',1)->first();
        if($year){
            $year = $year->id;
        }else{
            $request->session()->flash($this->message_warning, 'Missing Year! First you need to create Year and Active it.');
            return back();
        }

        if ($request->hasFile('attach_file')){
            $name = str_slug($request->get('subject'));
            $file = $request->file('attach_file');
            $file_name = rand(4585, 9857).'_'.$name.'.'.$file->getClientOriginalExtension();
            $file->move($this->folder_path, $file_name);
        }else{
            $file_name = "";
        }

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['years_id' => $year]);
        $today = Carbon::today(env('APP_TIMEZONE'))->format('Y-m-d');
        $request->date = $request->date?$request->date:$today;
        $request->request->add(['date' => $request->date?$request->date:$request->date]);
        $request->request->add(['end_date' => $request->end_date?$request->end_date:$request->date]);
        $request->request->add(['subject' => $request->subject]);
        $request->request->add(['message' => $request->message]);
        $request->request->add(['file' => $file_name]);

        $addapplication = Application::create($request->all());

        $request->session()->flash($this->message_success, $this->panel. ' Add Successfully.');

        if($request->add_application_another) {
            return back();
        }else{
            return redirect()->route($this->base_route.'.index');
        }
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        $data['row'] = Application::find($id);
        if (!$data['row'])
            return parent::invalidRequest();

        if(auth()->user()->hasRole('staff')) {
            $UserId = auth()->user()->id;
            if($data['row']->created_by != $UserId){
                return parent::invalidRequest();
            }
        }

        $data['applicationType'] = $this->activeApplicationType();

        $data['url'] = URL::current();
        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Application::find($id)){
            request()->session()->flash($this->message_warning, 'Invalid request!!');
            return redirect()->route($this->base_route.'.index');
        }

        if ($request->hasFile('attach_file')) {
            $name = str_slug($request->get('subject'));
            $file = $request->file('attach_file');
            $file_name = rand(4585, 9857).'_'.$name.'.'.$file->getClientOriginalExtension();
            $file->move($this->folder_path, $file_name);


            if (file_exists($this->folder_path.$row->file))
                @unlink($this->folder_path.$row->file);
        }

        $year = Year::where('active_status',1)->first()->id;

        $request->request->add(['years_id' => $year]);
        $request->request->add(['last_updated_by' => auth()->user()->id]);
        $request->request->add(['file' => isset($file_name)?$file_name:$row->file]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');

        return redirect()->route($this->base_route.'.index');
    }

    public function view(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if(auth()->user()->hasRole('staff')) {
            $UserId = auth()->user()->id;
            $data['application'] = Application::find($id);
            if($data['application']->created_by != $UserId){
                return parent::invalidRequest();
            }
        }else{
            $data['application'] = Application::find($id);
        }

        if(!$data['application']){
            return back();
        }else{
            $request->request->add(['status' => 'active']);
            $data['application']->update($request->all());
        }

        $data['application_type'] = ApplicationType::find($data['application']->application_type_id);
        return view(parent::loadDataToView($this->view_path.'.view'), compact('data'));
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Application::find($id)) return parent::invalidRequest();

        if(auth()->user()->hasRole('staff')) {
            $UserId = auth()->user()->id;
            if($row->created_by != $UserId){
                return parent::invalidRequest();
            }
        }

        // remove old file from folder
        if ($row->file && file_exists($this->folder_path.$row->file)) {
            @unlink($this->folder_path.$row->file);
        }

        $row->delete();
        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route.'.index');
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {
                foreach ($request->get('chkIds') as $row_id) {
                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            $row = Application::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = Application::find($row_id);
                            if(auth()->user()->hasRole('staff')) {
                                $UserId = auth()->user()->id;
                                if($row->created_by != $UserId){
                                    return parent::invalidRequest();
                                }
                            }

                            // remove old file from folder
                            if (file_exists($this->folder_path.$row->file))
                                @unlink($this->folder_path.$row->file);

                            $row->delete();
                            break;
                    }
                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')) . ' Successfully.');
                else
                    $request->session()->flash($this->message_success, ' Deleted successfully.');

                return redirect()->route($this->base_route.'.index');

            } else {
                $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
                return redirect()->route($this->base_route.'.index');
            }

        } else return parent::invalidRequest();

    }

    public function active(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Application::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' Active Successfully.');
        return back();
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Application::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' In-Active Successfully.');
        return back();
    }

    public function Approve(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Application::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);
        $request->request->add(['approve_status' => 1]);

        $row->update($request->all());

        $makeAttendance = AttendanceStatus::where('title','LEAVE')->first()->id;
        $this->makeAttendance($makeAttendance, $row);

        $request->session()->flash($this->message_success, $this->panel.'Approve Successfully.');
        return back();
    }

   // public function makeAttendance($attendees_type,$makeAttendance, $link_id, $startDate, $endDate)
    public function makeAttendance($makeAttendance, $row)
    {
        //get user linked id & Type
        $userID = $row->created_by;
        $userType = auth()->user()->find($userID);
        $userRoleId = $userType->role_id;
        $userHookId = $userType->hook_id;
        $attendeesType = 0;
        $linkId = 0;
        $attendableType = null;
        $regNo = null;
        //student
        if($userRoleId == $this->studentRoleId){
           // $userDetail = Student::find($userHookId);
           // $this->makeAttendance(1,$makeAttendance, $studentDetail->id, $row->date, $row->end_date);
            $linkId = $userHookId;
            $attendeesType = 1;
            $attendableType = 'student';
            $regNo = Student::where('id', $linkId)->value('reg_no');
        }
        //staff
        if($userRoleId == $this->staffRoleId){
            //$userDetail = Staff::find($userHookId);
            //$this->makeAttendance(2,$makeAttendance, $staffDetail->id, $row->date, $row->end_date);
            $linkId = $userHookId;
            $attendeesType = 2;
            $attendableType = 'staff';
            $regNo = Staff::where('id', $linkId)->value('reg_no');
        }

        if($linkId ==0 && $attendeesType==0){
            session()->flash($this->message_warning, 'Attendance is not Link to Any Staff & Student. It may not affect on Making Attendance.');
            return false;
        }else{
            $startDate = Carbon::parse( $row->date);
            $endDate= Carbon::parse($row->end_date);
            for ( $date = $startDate; $date <= $endDate; $date->addDays(1)) {
                $attendanceExist = Attendance::whereDate('date', $date->copy()->toDateString())
                    ->where('attendable_id', $linkId)
                    ->whereIn('attendable_type', [$attendableType, $attendeesType == 1 ? Student::class : Staff::class])
                    ->first();

                if ($attendanceExist) {
                    $attendanceExist->update([
                        'attendance_status_id' => $makeAttendance,
                        'reg_no' => $regNo,
                        'updated_by' => auth()->user()->id,
                    ]);
                } else {
                    Attendance::create([
                        'date' => $date->copy()->toDateString(),
                        'attendable_id' => $linkId,
                        'attendable_type' => $attendableType,
                        'reg_no' => $regNo,
                        'source' => 'manual',
                        'attendance_status_id' => $makeAttendance,
                        'created_by' => auth()->user()->id,
                    ]);
                }
            }
            return true;
        }
    }

    public function UnApprove(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Application::find($id)) return parent::invalidRequest();

        $request->request->add(['approve_status' => 0]);

        $row->update($request->all());

        //$makeAttendance = AttendanceStatus::where('title','LEAVE')->first()->id;
        $this->makeAttendance(0, $row);

        $request->session()->flash($this->message_success, $this->panel.' Un-Active Successfully.');
        return back();
    }


}
