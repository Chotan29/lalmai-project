<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\Staff;

use App\Exports\StaffsExport;
use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Staff\Registration\AddValidation;
use App\Http\Requests\Staff\Registration\EditValidation;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\Attendence;
use App\Models\BookIssue;
use App\Models\Document;
use App\Models\Hostel;
use App\Models\LibraryMember;
use App\Models\Note;
use App\Models\ResidentHistory;
use App\Models\Route;
use App\Models\Staff;
use App\Models\StaffDesignation;
use App\Models\Student;
use App\Models\TransactionHead;
use App\Models\TransportHistory;
use App\Models\Role;
use App\Traits\LibraryScope;
use App\Traits\SmsEmailScope;
use App\Traits\UserScope;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Image, URL;
use LaravelQRCode\Facades\QRCode;
use Libern\QRCodeReader\QRCodeReader;
use Maatwebsite\Excel\Facades\Excel;
use ViewHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StaffController extends CollegeBaseController
{
    protected $base_route = 'staff';
    protected $view_path = 'staff';
    protected $panel = 'Staffs';
    protected $folder_path;
    protected $folder_name = 'staff';
    protected $filter_query = [];

    use SmsEmailScope;
    use UserScope;
    use LibraryScope;

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        $data['staff'] = Staff::select('staff.id','staff.reg_no', 'staff.first_name', 'staff.middle_name', 'staff.last_name',
             'staff.mobile_1','staff.designation', 'staff.qualification', 'staff.status',
            'd.title as designation')
            ->where(function ($query) use ($request) {
                $this->commonStaffFilterCondition($query, $request);
            })
            ->join('staff_designations as d','d.id','=','staff.designation')
            ->paginate(env('PAGINATION_LIMIT',$this->pagination_limit));

        $data['designation'] = $this->staffDesignationList();

        //for Quick services Creation
        /*Hostel List*/
        $data['hostels'] = $this->activeHostel();
        /*Transport Route List*/
        $data['routes'] = $this->activeTransportRoutes();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function add()
    {
        $data = [];
        $data['designations'] = $this->staffDesignationList();
        $data['state'] = $this->activeState();
        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        if ($request->hasFile('main_image')){
            $image_name = parent::uploadImages($request, 'main_image');
        }else{
            $image_name = "";
        }

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['staff_image' => $image_name]);

        $staff = Staff::create($request->all());

        if($staff) {
            //Create Customer Ledger
            $request->request->add(['tr_head' => $request->first_name.' '.$request->middle_name.' '.$request->last_name.' ' . ' [' . $staff->reg_no . ']']);
            $request->request->add(['ref_id' => $staff->id]);
            $request->request->add(['acc_id' => $this->staffAccCategory]);
            $trHead = TransactionHead::create($request->all());

            //Manage Opening Balance of Customer
            if ($trHead && $request->amount > 0) {
                if ($request->get('account_type') == "dr_amt") {
                    $drAmount = $request->amount;
                    $crAmount = 0;
                } elseif ($request->get('account_type') == "cr_amt") {
                    $drAmount = 0;
                    $crAmount = $request->amount;
                } else {

                }

                $data = [
                    'date' => Carbon::today(),
                    'tr_head_id' => $trHead->id,
                    'dr_amount' => $drAmount,
                    'cr_amount' => $crAmount,
                    'description' => 'Opening Balance',
                    'created_by' => auth()->user()->id
                ];

                Transaction::create($data);
            }
        }

        $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');

        if($request->add_staff_another) {
            return back();
        }else{
            return redirect()->route($this->base_route);
        }

    }

    public function view($id)
    {
        $id = decrypt($id);
        $data = [];
        $data['staff'] = Staff::where('id','=',$id)
            ->first();

        if (!$data['staff']){
            request()->session()->flash($this->message_warning, "Not a Valid Staff");
            return redirect()->route($this->base_route);
        }

        $data['payroll_master'] = $data['staff']->payrollMaster()->orderBy('due_date','desc')->get();
        $data['pay_salary'] = $data['staff']->paySalary()->get();

        /*total Calculation on Table Foot*/
        $data['staff']->amount = $data['staff']->payrollMaster()->sum('amount');
        $data['staff']->allowance = $data['staff']->paySalary()->sum('allowance');
        $data['staff']->fine = $data['staff']->paySalary()->sum('fine');
        $data['staff']->paid_amount = $data['staff']->paySalary()->sum('paid_amount');
        $data['staff']->balance =
            ($data['staff']->amount + $data['staff']->allowance) - ($data['staff']->paid_amount + $data['staff']->fine) ;

        $data['lib_member'] = LibraryMember::where(['library_members.user_type' => 2, 'library_members.member_id' => $data['staff']->id])
            ->first();

        if($data['lib_member'] != null){
            $data['books_history'] = $data['lib_member']->libBookIssue()->select('book_issues.id', 'book_issues.member_id',
                'book_issues.book_id',  'book_issues.issued_on', 'book_issues.due_date','book_issues.return_date', 'b.book_masters_id',
                'b.book_code', 'bm.title','bm.categories')
                ->join('books as b','b.id','=','book_issues.book_id')
                ->join('book_masters as bm','bm.id','=','b.book_masters_id')
                ->orderBy('book_issues.issued_on', 'desc')
                ->get();
        }

        // $attendanceCollection = Attendance::select('attendances.id', 'attendances.attendees_type', 'attendances.link_id',
        //     'attendances.years_id', 'attendances.months_id', 'attendances.day_1', 'attendances.day_2', 'attendances.day_3',
        //     'attendances.day_4', 'attendances.day_5', 'attendances.day_6', 'attendances.day_7', 'attendances.day_8',
        //     'attendances.day_9', 'attendances.day_10', 'attendances.day_11', 'attendances.day_12', 'attendances.day_13',
        //     'attendances.day_14', 'attendances.day_15', 'attendances.day_16', 'attendances.day_17', 'attendances.day_18',
        //     'attendances.day_19', 'attendances.day_20', 'attendances.day_21', 'attendances.day_22', 'attendances.day_23',
        //     'attendances.day_24', 'attendances.day_25', 'attendances.day_26', 'attendances.day_27', 'attendances.day_28',
        //     'attendances.day_29', 'attendances.day_30', 'attendances.day_31', 'attendances.day_32')
        //     ->where('attendances.attendees_type', 2)
        //     ->where('attendances.link_id',$data['staff']->id)
        //     ->join('staff as s', 's.id', '=', 'attendances.link_id')
        //     ->orderBy('attendances.years_id','asc')
        //     ->orderBy('attendances.months_id','asc')
        //     ->get();

        // $attendanceStatus = AttendanceStatus::get();
        // $filteredAttendance = $attendanceCollection->filter(function ($attendance, $key) use($attendanceStatus) {
        //     for ($day = 1; $day <= 32; $day++) {
        //         $dayCode = "day_".$day;
        //         foreach ($attendanceStatus as $attenStatus){
        //             if($attendance->$dayCode == $attenStatus->id){
        //                 $attenTitle = $attenStatus->title;
        //                 $attendance->$attenTitle = $attendance->$attenTitle + 1;
        //             }
        //         }
        //     }

        //     return $attendance;
        // });

        // $data['attendance'] = $filteredAttendance;
        // $data['attendanceStatus'] = $attendanceStatus;
       

        $data['note'] = Note::select('created_at', 'id', 'member_type','member_id','subject', 'note', 'status')
            ->where('member_type','=','staff')
            ->where('member_id','=', $data['staff']->id)
            ->orderBy('created_at','desc')
            ->get();

        $data['document'] = Document::select('id', 'member_type','member_id', 'title', 'file','description', 'status')
            ->where('member_type','=','staff')
            ->where('member_id','=',$data['staff']->id)
            ->orderBy('created_by','desc')
            ->get();

        $data['history'] = ResidentHistory::select('resident_histories.years_id', 'resident_histories.hostels_id',
            'resident_histories.rooms_id', 'resident_histories.beds_id',
            'resident_histories.history_type','resident_histories.created_at')
            ->where([['r.user_type','=', 2],['r.member_id','=',$data['staff']->id]])
            ->join('residents as r', 'r.id', '=', 'resident_histories.residents_id')
            ->join('beds as b', 'b.id', '=', 'resident_histories.beds_id')
            ->orderBy('resident_histories.created_at')
            ->get();


        /*Transport History*/
        $data['transport_history'] = TransportHistory::select('transport_histories.id', 'transport_histories.years_id',
            'transport_histories.routes_id', 'transport_histories.vehicles_id',  'transport_histories.history_type',
            'transport_histories.created_at','tu.user_type')
            ->where([['tu.user_type','=', 2],['tu.member_id','=',$data['staff']->id]])
            ->join('transport_users as tu','tu.id','=','transport_histories.travellers_id')
            ->orderBy('transport_histories.created_at')
            ->get();

        //transaction
        $data['transactionHead'] = TransactionHead::where(['acc_id' => $this->staffAccCategory, 'ref_id' => $id])->first();

        if($data['transactionHead']) {
            $transaction = $data['transactionHead']->tR()
                ->orderBy('date')
                ->get();

            $adjustment = [];
            $filteredTransaction  = $transaction->filter(function ($value, $key)use($transaction, $adjustment){
                $balance = $value->dr_amount - $value->cr_amount;

                if($key > 0) {
                    $value->balance = $transaction[$key-1]->balance + $balance;
                }else{
                    $value->balance = $value->dr_amount - $value->cr_amount;
                }
                return $value;
            });

            $data['transaction'] = $filteredTransaction;
        }

        //login credential
        $data['staff_login'] = User::where([['role_id',5],['hook_id',$data['staff']->id]])->first();

        $data['url'] = URL::current();
        return view(parent::loadDataToView($this->view_path.'.detail.index'), compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = Staff::find($id)) return parent::invalidRequest();

        $data['designations'] = $this->staffDesignationList();
        $data['state'] = $this->activeState();

        return view(parent::loadDataToView($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$row = Staff::find($id))
            return parent::invalidRequest();

        if ($request->hasFile('main_image')){
            $image_name = parent::uploadImages($request, 'main_image');

            // remove old image from folder
            if (file_exists($this->folder_path.$row->staff_image))
                @unlink($this->folder_path.$row->staff_image);
        }

        $request->request->add(['last_updated_by' => auth()->user()->id]);
        $request->request->add(['staff_image' => isset($image_name)?$image_name:$row->staff_image]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel. ' Updated Successfully.');
        return redirect()->route($this->base_route);

    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        $errCount = 0;
        $errors = [];
        if (!$row = Staff::find($id)) return parent::invalidRequest();

        //User
        $userInfo = User::where(['role_id' => 5, 'hook_id'=> $id])->first();

        //Document & Notes
            //Documents
            $document = $row->staffDocuments()->get();
            if($document->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Documents Found, Please Delete.";
            }

            //Notes
            $notes = $row->staffNotes()->get();
            if($notes->count() > 0){
            $errCount = $errCount+1;
            $errors[] = "Notes Found, Please Delete.";
        }

        //Payroll Master, Salary Pay
        $payrollMaster = $row->payrollMaster()->get();
        if($payrollMaster->count() > 0){
            $paySalary = $row->paySalary()->get();
            if($paySalary->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Salary Paid Found, Please Delete.";
            }
            $errCount = $errCount+1;
            $errors[] = "Salary Master Found, Please Delete.";
        }

        //Transport
        $transportUser = $row->transportUser()->get();
        if($transportUser->count() > 0){
            $transportHistory = TransportHistory::where('travellers_id',$transportUser->first()->id)->get();
            if($transportHistory->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Transport History Found, Please Delete.";
            }
            $errCount = $errCount+1;
            $errors[] = "Transport User Found, Please Delete.";
        }

        //Hostel
        $hostelResident = $row->hostelResident()->get();
        if($hostelResident->count() > 0){
            $residentHistory = ResidentHistory::where('residents_id',$hostelResident->first()->id)->get();
            if($residentHistory->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Hostel Resident History Found, Please Delete.";
            }
            $errCount = $errCount+1;
            $errors[] = "Hostel Resident Found, Please Delete.";
        }

        //attendance (regular & Subject)
        //Regular Attendance
        $attendacne = $row->regularAttendance()->get();
        if($attendacne->count() > 0){
            $errCount = $errCount+1;
            $errors[] = "Regular Attendance Found, Please Delete.";
        }

        //library Membership & History
        $libraryMember = $row->libraryMember()->get();
        if($libraryMember->count() > 0){
            $bookIssue = BookIssue::where('member_id',$libraryMember->first()->id)->get();
            if($bookIssue->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Book Issue Found, Please Delete.";
            }
            $errCount = $errCount+1;
            $errors[] = "Library Member Found, Please Delete.";
        }

        if($errCount > 0){
            $request->session()->flash($this->message_warning, $this->panel.' not delete. If you want to erase Staff data, please check err below and delete all the data first.');
            return back()->withErrors($errors);
        }else{
            if ($userInfo) {
                $userInfo->delete();
            }

            //remove images
            if (file_exists($this->folder_path.$row->staff_image))
                @unlink($this->folder_path.$row->staff_image);

            $row->delete();
            $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        }

        return redirect()->route($this->base_route);
    }

    public function active(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Staff::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Staff::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }

    /*bulk import*/
    public function importStaff()
    {
        return view(parent::loadDataToView($this->view_path.'.import'));
    }

    public function handleImportStaff(Request $request)
    {
        //file present or not validation
        $validator = Validator::make($request->all(), [
            'file' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator);
        }

        $file = $request->file('file');
        $csvData = file_get_contents($file);
        $rows = array_map("str_getcsv", explode("\n", $csvData));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            if (count($header) != count($row)) {
                continue;
            }

            $row = array_combine($header, $row);
            //Designation id
            $designation = StaffDesignation::where('title',$row['designation'])->first();
            if($designation){
                $designationId = $designation->id;
            }else{
                $designation = StaffDesignation::create([
                    'title' => strtoupper($row['designation']),
                    'created_by' => auth()->user()->id
                ]);

                $designationId = $designation->id;
            }

            //Staff validation
            $validator = Validator::make($row, [
                'reg_no'                => 'required  | unique:staff,reg_no',
                'join_date'              => 'required',
                'designation'           => 'required',
                'first_name'            => 'required',
                'last_name'             => 'required',
                'date_of_birth'         => 'required',
                'gender'                => 'required',
                'qualification'         => 'required',
                'mobile_1'              => 'required',
                'main_image'           => 'mimes:jpeg,bmp,png',
            ]);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator);
            }


            /*Manage Date's Format*/
            $join_date = Carbon::parse($row['join_date'])->format('Y-m-d');
            $date_of_birth =  Carbon::parse($row['date_of_birth'])->format('Y-m-d');

            //Staff import
            $staff = Staff::create([
              "reg_no"              => $row['reg_no'],
              "join_date"           => $join_date,
              "designation"         => $designationId,
              "first_name"          => $row['first_name'],
              "middle_name"         => $row['middle_name'],
              "last_name"           => $row['last_name'],
              "father_name"         => $row['father_name'],
              "mother_name"         => $row['mother_name'],
              "date_of_birth"       => $date_of_birth,
              "gender"              => $row['gender'],
              "blood_group"         => $row['blood_group'],
              "nationality"         => $row['nationality'],
              "mother_tongue"       => $row['mother_tongue'],
              "email"               => $row['email'],
              "home_phone"          => $row['home_phone'],
              "mobile_1"            => $row['mobile_1'],
              "mobile_2"            => $row['mobile_2'],
              "address"             => $row['address'],
              "state"               => $row['state'],
              "country"             => $row['country'],
              "temp_address"        => $row['temp_address'],
              "temp_state"          => $row['temp_state'],
              "temp_country"        => $row['temp_country'],
              "qualification"       => $row['qualification'],
              "experience"          => $row['experience'],
              "experience_info"     => $row['experience_info'],
              "other_info"          => $row['other_info'],
              'created_by'          => auth()->user()->id

            ]);

            if($staff) {
                //Create Ledger
                $request->request->add(['tr_head' => $row['first_name'].' '.$row['middle_name'].' '.$row['last_name'].' ' . ' [' . $staff->reg_no . ']']);
                $request->request->add(['ref_id' => $staff->id]);
                $request->request->add(['acc_id' => $this->staffAccCategory]);
                $request->request->add(['created_by' => auth()->user()->id]);
                $trHead = TransactionHead::create($request->all());

                //Manage Opening Balance of Customer
                /*if ($trHead && $request->amount > 0) {
                    if ($request->get('account_type') == "dr_amt") {
                        $drAmount = $request->amount;
                        $crAmount = 0;
                    } elseif ($request->get('account_type') == "cr_amt") {
                        $drAmount = 0;
                        $crAmount = $request->amount;
                    } else {

                    }

                    $data = [
                        'date' => Carbon::today(),
                        'tr_head_id' => $trHead->id,
                        'dr_amount' => $drAmount,
                        'cr_amount' => $crAmount,
                        'description' => 'Opening Balance',
                        'created_by' => auth()->user()->id
                    ];

                    Transaction::create($data);
                }*/
            }

        }

        $request->session()->flash($this->message_success,'Staffs imported Successfully');
        return redirect()->route($this->base_route);

    }


    public function bulkAction(Request $request)
    {
        $bulkActions = ['export-excel', 'active', 'in-active', 'delete', 'create-reset-login', 'create-reset-library-member'];
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), $bulkActions)) {

            if ($request->has('chkIds')) {
                if($request->get('bulk_action') == 'export-excel'){
                    $dataIds = [];
                    foreach ($request->get('chkIds') as $row_id) {
                        $dataIds[] = decrypt($row_id);
                    }
                    return Excel::download(new StaffsExport($dataIds), 'Staff_Detail_'.Carbon::now()->format('d-m-Y').'.xlsx');
                }else{
                    foreach ($request->get('chkIds') as $row_id) {
                        switch ($request->get('bulk_action')) {
                            case 'active':
                            case 'in-active':
                                $row = Staff::find($row_id);
                                if ($row) {
                                    $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                    $row->save();
                                }
                                break;
                            case 'delete':
                                $data = $this->delete($request, $row_id);
                                break;
                            case 'create-reset-login':
                                $this->createResetLogin($row_id);
                                break;
                            case 'create-reset-library-member':
                                $this->createLibraryMember($row_id);
                                break;
                        }
                    }

                    if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active') {
                        $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')) . ' Successfully.');
                    }elseif($request->get('bulk_action') == 'create-reset-login'){
                        $request->session()->flash($this->message_success, 'Student Login Create/Reset Successfully.');
                    }elseif($request->get('bulk_action') == 'create-reset-library-member'){
                        $request->session()->flash($this->message_success, 'Library Member Created Successfully.');
                    }else {
                        $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')).' successfully.');
                    }

                    return redirect()->route($this->base_route);
                }

            } else {
                $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
                return redirect()->route($this->base_route);
            }

            return redirect()->route($this->base_route);

        } else return parent::invalidRequest();

    }

    public function createResetLogin($id)
    {
        $staff = Staff::find($id);
    
        if($staff){
            //create login access
            $name = isset($staff->middle_name)?$staff->first_name.' '.$staff->middle_name.' '.$staff->last_name:$staff->first_name.' '.$staff->last_name;
            $rolesId = Role::where('name','staff')->first()->id;
            $password = str_random(8);
            $emailIds = $staff->email;
            //check user is exist or not
            $existUser = User::where('email',$emailIds)->first();
            
            if($existUser){
                $user = $existUser->update([
                    'password' => Hash::make($password)
                ]);

                $subject = 'Login Detail Reset';
                $message = 'Dear ' . $name.', Your login detail Updated in our system. Please Login with  <br> email: '.$emailIds.' And Password: '. $password ;
            }else{
                $user = User::create([
                    'role_id' => $rolesId,
                    'hook_id' => $staff->id,
                    'name' => $name,
                    'email' => $emailIds,
                    'password' => Hash::make($password),
                    'status' => 'active'
                ]);
                $roles = [];
                $roles[] = [
                    'user_id' => $user->id,
                    'role_id' => $rolesId
                ];

                $user->userRole()->sync($roles);

                $subject = 'Login Detail Create';
                $message = 'Dear ' . $name.', Your login detail created in our system. Please Login with  <br> email: '.$emailIds.' And Password: '. $password ;
            }

            if($user){
                $emailSuccess = $this->sendEmail($emailIds, $subject, $message);
            }
        }

        return back();
    }

    public function createLibraryMember($id)
    {
        $data = Staff::find($id);
        if($data){
            $currentMember = LibraryMember::where(['user_type' => 2, 'member_id' => $data->id])->orderBy('id','desc')->first();
            if(!$currentMember){
                $member = LibraryMember::create([
                    'member_id' => $data->id,
                    'user_type' => 2,
                    'created_by' => auth()->user()->id,
                ]);

                $memberId = $member->member_id;
                $userType = $member->user_type;
                $this->sendLibraryRegistrationAlert($memberId,$userType);

            }
        }

        return back();
    }


    /**
     * AJAX: Staff regular attendance summary
     * GET /staff/{id}/attendance/summary?period=monthly|yearly|custom|lifetime&year=&month=&date_from=&date_to=
     */
    /* ================= Staff Regular Attendance (AJAX JSON) ================ */
    public function attendanceSummary($id, Request $request)
    {
        try {
            $staffId = is_numeric($id) ? (int)$id : (int)decrypt($id);

            if (!Schema::hasTable('attendances')) {
                return response()->json(['ok'=>false,'msg'=>'Attendance table not found.']);
            }

            [$start, $end] = $this->resolveStaffRangeFromRequest($request, $staffId);
            [$legend, $colors] = $this->attLegendAndColors();

            // status table/columns (flex)
            $statusTab = Schema::hasTable('attendance_statuses') ? 'attendance_statuses' : null;
            $statusId  = $statusTab ? $this->firstExistingColumn($statusTab, ['id','status_id']) : null;
            $statusCol = $statusTab ? $this->firstExistingColumn($statusTab, ['code','short_code','symbol','abbr','title','name','status']) : null;

            // 1) polymorphic
            $rowsDb = $this->queryStaffAttendanceByMorph($staffId, $start, $end, $statusTab, $statusId, $statusCol);

            // 2) fallback: reg_no if no rows
            if ($rowsDb->isEmpty()) {
                $reg = $this->staffPrimaryRegNo($staffId);
                if ($reg) {
                    $rowsDb = $this->queryStaffAttendanceByRegNo($reg, $start, $end, $statusTab, $statusId, $statusCol);
                }
            }

            $rows = [];
            $totals = ['P'=>0,'A'=>0,'L'=>0,'EL'=>0,'LV'=>0,'H'=>0];
            $totalMins = 0;

            foreach ($rowsDb as $r) {
                $code = '';
                if ($statusTab && $statusCol) {
                    $code = $this->normalizeStatus($r->status_code ?? '');
                }
                if ($code === '') {
                    // default when status missing: P if in/out exists, else A
                    $code = $r->check_in_at ? 'P' : 'A';
                }

                $in  = $r->check_in_at ? Carbon::parse($r->check_in_at) : null;
                $out = $r->check_out_at ? Carbon::parse($r->check_out_at) : null;
                $mins = ($in && $out && $out->gte($in)) ? $in->diffInMinutes($out) : 0;

                $totalMins += $mins;
                if (isset($totals[$code])) $totals[$code]++;

                $rows[] = [
                    'date'   => Carbon::parse($r->date)->toDateString(),
                    'status' => $code,
                    'in'     => $in ? $in->format('H:i') : '',
                    'out'    => $out ? $out->format('H:i') : '',
                    'mins'   => $mins,
                    'total'  => $this->fmtHM($mins),
                ];
            }

            // Attendance % (exclude H from denominator; count L and EL as present-like)
            $den = ($totals['P']+$totals['A']+$totals['L']+$totals['EL']+$totals['LV']);
            $num = ($totals['P']+$totals['L']+$totals['EL']);
            $percent = $den > 0 ? round(($num*100)/$den) : 0;

            return response()->json([
                'ok'          => true,
                'range'       => ['start'=>$start->toDateString(), 'end'=>$end->toDateString()],
                'legend'      => $legend,
                'colors'      => $colors,
                'totals'      => $totals,
                'percent'     => $percent,
                'total_mins'  => $totalMins,
                'total_hm'    => $this->fmtHM($totalMins),
                'rows'        => $rows,
            ]);

        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'msg'=>'Failed to load staff attendance.']);
        }
    }

    /* ---------------- helpers just for staff attendance ---------------- */

    private function resolveStaffRangeFromRequest(Request $request, int $staffId): array
    {
        $period = strtolower((string)$request->get('period','monthly'));
        $year   = (int)$request->get('year', (int)date('Y'));
        $month  = (int)$request->get('month', (int)date('n'));
        $from   = $request->get('date_from');
        $to     = $request->get('date_to');

        $now = Carbon::now();

        switch ($period) {
            case 'yearly':
                $s = Carbon::create($year, 1, 1)->startOfDay();
                $e = Carbon::create($year,12,31)->endOfDay();
                break;
            case 'custom':
                $s = $from ? Carbon::parse($from)->startOfDay() : $now->copy()->startOfMonth();
                $e = $to   ? Carbon::parse($to)->endOfDay()   : $now->copy()->endOfMonth()->endOfDay();
                if ($e->lt($s)) [$s,$e]=[$e,$s];
                break;
            case 'lifetime':
                $min = $this->earliestStaffAttendanceDate($staffId);
                $s = ($min ?: Carbon::create(2000,1,1))->copy()->startOfDay();
                $e = $now->copy()->endOfDay();
                break;
            case 'monthly':
            default:
                $s = Carbon::create($year,$month,1)->startOfDay();
                $e = $s->copy()->endOfMonth()->endOfDay();
                break;
        }
        return [$s,$e];
    }

    private function earliestStaffAttendanceDate(int $staffId): ?Carbon
    {
        // Try polymorphic
        $d1 = DB::table('attendances')
            ->whereIn('attendable_type', $this->staffMorphTypes())
            ->where('attendable_id', $staffId)
            ->min('date');
        $min = $d1 ? Carbon::parse($d1) : null;

        // Fallback: by reg_no
        if (!$min) {
            $reg = $this->staffPrimaryRegNo($staffId);
            if ($reg) {
                $d2 = DB::table('attendances')->where('reg_no', $reg)->min('date');
                if ($d2) $min = Carbon::parse($d2);
            }
        }
        return $min;
    }

    private function firstExistingColumn(string $table, array $cands)
    {
        foreach ($cands as $c) if (Schema::hasColumn($table,$c)) return $c;
        return null;
    }

    private function attLegendAndColors(): array
    {
        if (Schema::hasTable('attendance_statuses')) {
            $rows = DB::table('attendance_statuses')
                ->select(['code','label','color','order'])
                ->orderByRaw('COALESCE(`order`,999)')
                ->get();
            $legend=[]; $colors=[];
            foreach ($rows as $r) {
                $code = strtoupper((string)($r->code ?? $r->label ?? ''));
                if ($code==='') continue;
                $legend[]=['code'=>$code,'label'=>$r->label ?: $code,'color'=>$r->color ?: '#111827'];
                $colors[$code]=$r->color ?: '#111827';
            }
            foreach ([['H','Holiday','#0EA5E9'],['LV','Leave','#7C3AED'],['EL','Excused','#3B82F6'],['L','Late','#F59E0B'],['A','Absent','#EF4444'],['P','Present','#10B981']] as $e){
                if(!isset($colors[$e[0]])){ $legend[]=['code'=>$e[0],'label'=>$e[1],'color'=>$e[2]]; $colors[$e[0]]=$e[2]; }
            }
            return [$legend,$colors];
        }
        $legend=[
            ['code'=>'P','label'=>'Present','color'=>'#10B981'],
            ['code'=>'A','label'=>'Absent','color'=>'#EF4444'],
            ['code'=>'L','label'=>'Late','color'=>'#F59E0B'],
            ['code'=>'EL','label'=>'Excused','color'=>'#3B82F6'],
            ['code'=>'LV','label'=>'Leave','color'=>'#7C3AED'],
            ['code'=>'H','label'=>'Holiday','color'=>'#0EA5E9'],
        ];
        $colors=[]; foreach($legend as $l) $colors[$l['code']]=$l['color'];
        return [$legend,$colors];
    }

    private function normalizeStatus($v)
    {
        $t = strtoupper(trim((string)$v));
        if ($t==='P'||str_contains($t,'PRESENT')) return 'P';
        if ($t==='A'||str_contains($t,'ABSENT'))  return 'A';
        if ($t==='L'||str_contains($t,'LATE'))    return 'L';
        if ($t==='HL') return 'HL';
        if ($t==='LV'||str_contains($t,'LEAVE'))  return 'LV';
        if ($t==='EL'||str_contains($t,'EXCUSE')) return 'EL';
        if ($t==='H'||str_contains($t,'HOLIDAY')) return 'H';
        return '';
    }

    private function fmtHM($mins): string
    {
        $m = max(0,(int)round($mins));
        $h = intdiv($m,60); $r=$m%60;
        return $h.'h '.str_pad((string)$r,2,'0',STR_PAD_LEFT).'m';
    }

    private function staffMorphTypes(): array
    {
        $cands = ['App\\Models\\Staff','App\\Staff','staff','Staff','App\\Models\\Employee','App\\Employee','employee','Employee'];
        if (class_exists('App\\Models\\Staff')) array_unshift($cands,'App\\Models\\Staff');
        elseif (class_exists('App\\Staff'))     array_unshift($cands,'App\\Staff');
        return array_values(array_unique($cands));
    }

    private function staffPrimaryRegNo(int $staffId): ?string
    {
        $staffTab = Schema::hasTable('staff') ? 'staff' : (Schema::hasTable('employees') ? 'employees' : (Schema::hasTable('users') ? 'users' : null));
        if (!$staffTab) return null;
        $idCol = Schema::hasColumn($staffTab,'id') ? 'id' : null; if(!$idCol) return null;
        $regCol = $this->firstExistingColumn($staffTab, ['reg_no','employee_code','employee_id','code','staff_code','registration_no']);
        if (!$regCol) return null;
        $row = DB::table($staffTab)->select("$regCol as reg")->where($idCol,$staffId)->first();
        return $row ? ($row->reg ?? null) : null;
    }

    private function queryStaffAttendanceByMorph(int $staffId, Carbon $start, Carbon $end, ?string $statusTab, ?string $statusId, ?string $statusCol)
    {
        $q = DB::table('attendances as a')
            ->select(['a.date','a.check_in_at','a.check_out_at','a.attendance_status_id'])
            ->whereIn('a.attendable_type', $this->staffMorphTypes())
            ->where('a.attendable_id', $staffId)
            ->whereDate('a.date','>=',$start->toDateString())
            ->whereDate('a.date','<=',$end->toDateString())
            ->orderBy('a.date','asc');

        if ($statusTab && $statusId && $statusCol) {
            $q->addSelect(DB::raw("COALESCE(s.`$statusCol`,'') as status_code"))
            ->leftJoin("$statusTab as s","s.$statusId",'=','a.attendance_status_id');
        } else {
            $q->addSelect(DB::raw("'' as status_code"));
        }

        return $q->get();
    }

    private function queryStaffAttendanceByRegNo(string $reg, Carbon $start, Carbon $end, ?string $statusTab, ?string $statusId, ?string $statusCol)
    {
        $q = DB::table('attendances as a')
            ->select(['a.date','a.check_in_at','a.check_out_at','a.attendance_status_id'])
            ->where('a.reg_no', $reg)
            ->whereDate('a.date','>=',$start->toDateString())
            ->whereDate('a.date','<=',$end->toDateString())
            ->orderBy('a.date','asc');

        if ($statusTab && $statusId && $statusCol) {
            $q->addSelect(DB::raw("COALESCE(s.`$statusCol`,'') as status_code"))
            ->leftJoin("$statusTab as s","s.$statusId",'=','a.attendance_status_id');
        } else {
            $q->addSelect(DB::raw("'' as status_code"));
        }

        return $q->get();
    }
}
