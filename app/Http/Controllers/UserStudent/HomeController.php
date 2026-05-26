<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\UserStudent;
use App\Http\Requests\Application\AddValidation;
use App\Http\Requests\Student\PublicRegistration\EditValidation;

use App\Charts\FeePayDueChart;
use App\Http\Controllers\CollegeBaseController;
//use App\Http\Controllers\API\PaymentController;
use App\Models\AcademicInfo;
use App\Models\Application;
use App\Models\Assignment;
use App\Models\AssignmentAnswer;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\BookCategory;
use App\Models\BookMaster;
use App\Models\BookRequest;
use App\Models\BookStatus;
use App\Models\Document;
use App\Models\Download;
use App\Models\ExamSchedule;
use App\Models\FeeCollection;
use App\Models\FeeMaster;
use App\Models\GradingScale;
use App\Models\GuardianDetail;
use App\Models\LibraryCirculation;
use App\Models\LibraryMember;
use App\Models\Meeting;
use App\Models\Note;
use App\Models\Notice;
use App\Models\OnlinePayment;
use App\Models\ResidentHistory;
use App\Models\ClassRoutine;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\StudentBatch;
use App\Models\Subject;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\StudentStatus;
use App\Models\SubjectAttendance;
use App\Models\TransportHistory;
use App\Models\Year;
use App\Traits\ExaminationScope;
use App\Traits\LibraryScope;
use App\Traits\PaymentGatewayScope;
use App\Traits\StudentScopes;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use ViewHelper, URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class HomeController extends CollegeBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    use ExaminationScope;
    protected $base_route = 'dashboard';
    protected $view_path = 'user-student';
    protected $panel = 'Dashboard';
    protected $folder_path;
    protected $folder_name = 'studentProfile';
    protected $filter_query = [];

    use StudentScopes;
    use PaymentGatewayScope;
    use LibraryScope;

    public function __construct()
    {
        $this->middleware('auth');
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->panel = "Dashboard";
        $id = auth()->user()->hook_id;
        $data = [];
        $data['student'] = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
            'students.faculty','students.semester', 'students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group', 'students.nationality',
            'students.mother_tongue', 'students.email', 'students.extra_info', 'students.student_image', 'students.status',
            'ai.mobile_1', 'ai.mobile_2')
            ->where('students.id','=',$id)
            ->leftJoin('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->first();

        if (!$data['student']){
            request()->session()->flash($this->message_warning, "Not a Valid Student");
            return redirect()->route($this->base_route);
        }

        /*Notice*/
        $userRoleId = auth()->user()->roles()->first()->id;
        $now = date('Y-m-d');
        $data['notice_display'] = Notice::select('last_updated_by', 'title', 'message',  'publish_date', 'end_date',
            'display_group', 'status')
            ->where('display_group','like','%'.$userRoleId.'%')
            ->where('publish_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->latest()
            ->get();

        $feeMaster = FeeMaster::where('students_id',$data['student']->id)->sum('fee_amount');
        $feeCollection = FeeCollection::where('students_id',$data['student']->id)->sum('paid_amount');
        $dueFee = $feeMaster - $feeCollection;
        //get installment amount
        
        $data['current_unpaid_installment'] = $this->currentUnpaidInstallment($data['student']->reg_no);        


        /*chart*/
        $data['feeCompare'] = new FeePayDueChart('Paid','Due');
        $data['feeCompare']->dataset('Income', 'doughnut',[$feeCollection, $dueFee])
            ->options(['borderColor' => '#46b8da', 'backgroundColor'=>['#46b8da','#FF6384'] ]);


        return view(parent::loadDataToView($this->view_path.'.dashboard.index'), compact('data'));

    }

    // public function currentUnpaidInstallment($regNo)
    // {
    //  //get installment amount
    //     $response = app()->call('App\Http\Controllers\API\PaymentController@getStudentInfo', [
    //         'studentId' => $regNo
    //     ]);

    //     if($response->original['success']){
    //         return $response->original['payload']['installmentAmount'];
    //     }
    // }

    public function profile()
    {
        $this->panel = "Profile";
        $id = auth()->user()->hook_id;
        $data = [];
        $data['student'] = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
            'students.faculty','students.semester', 'students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group', 'students.religion', 'students.nationality',
            'students.mother_tongue', 'students.email', 'students.extra_info', 'students.student_image', 'students.status', 'pd.grandfather_first_name',
            'pd.grandfather_middle_name', 'pd.grandfather_last_name', 'pd.father_first_name', 'pd.father_middle_name',
            'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_office', 'pd.father_office_number',
            'pd.father_residence_number', 'pd.father_mobile_1', 'pd.father_mobile_2', 'pd.father_email', 'pd.mother_first_name',
            'pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation', 'pd.mother_office',
            'pd.mother_office_number', 'pd.mother_residence_number', 'pd.mother_mobile_1', 'pd.mother_mobile_2', 'pd.mother_email',
            'ai.address', 'ai.state', 'ai.country', 'ai.temp_address', 'ai.temp_state', 'ai.temp_country', 'ai.home_phone',
            'ai.mobile_1', 'ai.mobile_2', 'gd.id as guardian_id', 'gd.guardian_email','gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
            'gd.guardian_eligibility', 'gd.guardian_occupation', 'gd.guardian_office', 'gd.guardian_office_number', 'gd.guardian_residence_number',
            'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email', 'gd.guardian_relation', 'gd.guardian_address')
            ->where('students.id','=',$id)
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->join('student_guardians as sg', 'sg.students_id','=','students.id')
            ->join('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
            ->first();

        if (!$data['student']){
            request()->session()->flash($this->message_warning, "Not a Valid Student");
            return redirect()->route($this->base_route);
        }


        /*total Calculation on Table Foot*/
        $data['student']->fee_amount = $data['student']->feeMaster()->sum('fee_amount');
        $data['student']->discount = $data['student']->feeCollect()->sum('discount');
        $data['student']->fine = $data['student']->feeCollect()->sum('fine');
        $data['student']->paid_amount = $data['student']->feeCollect()->sum('paid_amount');
        $data['student']->balance =
            ($data['student']->fee_amount - ($data['student']->paid_amount + $data['student']->discount))+ $data['student']->fine;

        $data['document'] = Document::select('id', 'member_type','member_id', 'title', 'file','description', 'status')
            ->where('member_type','=','student')
            ->where('member_id','=',$data['student']->id)
            ->orderBy('created_by','desc')
            ->get();


        $data['note'] = Note::select('created_at', 'id', 'member_type','member_id','subject', 'note', 'status')
            ->where('member_type','=','student')
            ->where('member_id','=', $data['student']->id)
            ->orderBy('created_at','desc')
            ->get();

        $data['academicInfos'] = $data['student']->academicInfo()->orderBy('sorting_order','asc')->get();
        //login credential
        $data['student_login'] = User::where([['role_id',6],['hook_id',$data['student']->id]])->first();

        return view(parent::loadDataToView($this->view_path.'.detail.index'), compact('data'));
    }

    public function editProfile(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];

        $data['row'] = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
            'students.faculty','students.semester','students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group', 'students.religion', 'students.caste', 'students.nationality',
            'students.mother_tongue', 'students.email', 'students.extra_info','students.student_image', 'students.student_signature', 'students.status',
            'pd.grandfather_first_name',
            'pd.grandfather_middle_name', 'pd.grandfather_last_name', 'pd.father_first_name', 'pd.father_middle_name',
            'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_office', 'pd.father_office_number',
            'pd.father_residence_number', 'pd.father_mobile_1', 'pd.father_mobile_2', 'pd.father_email', 'pd.mother_first_name',
            'pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation', 'pd.mother_office',
            'pd.mother_office_number', 'pd.mother_residence_number', 'pd.mother_mobile_1', 'pd.mother_mobile_2', 'pd.mother_email',
            'pd.father_image', 'pd.mother_image',
            'ai.address', 'ai.state', 'ai.country', 'ai.temp_address', 'ai.temp_state', 'ai.temp_country', 'ai.home_phone',
            'ai.mobile_1', 'ai.mobile_2', 'gd.id as guardians_id', 'gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
            'gd.guardian_eligibility', 'gd.guardian_occupation', 'gd.guardian_office', 'gd.guardian_office_number',
            'gd.guardian_residence_number', 'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email',
            'gd.guardian_relation', 'gd.guardian_address', 'gd.guardian_image')
            ->where('students.id','=',$id)
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->join('student_guardians as sg', 'sg.students_id','=','students.id')
            ->join('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
            ->first();

        if (!$data['row'])
            return parent::invalidRequest();

        $data['faculties'] = $this->activeFaculties();
        //$data['academic_status'] = $this->activeStudentAcademicStatus();


        $semester = Semester::select('id', 'semester')->where('id','=',$data['row']->semester)->Active()->pluck('semester','id')->toArray();
        $data['semester'] = array_prepend($semester,'Select Semester',0);


        $academicStatus = StudentStatus::select('id', 'title')->Active()->pluck('title','id')->toArray();
        $data['academic_status'] = array_prepend($academicStatus,'Select Status',0);

        $studentBatch = StudentBatch::select('id', 'title')->Active()->pluck('title','id')->toArray();
        $data['batch'] = array_prepend($studentBatch,'Select Batch',0);

        $data['academicInfo'] = $data['row']->academicInfo()->orderBy('sorting_order','asc')->get();
        $data['academicInfo-html'] = view($this->view_path.'.registration.includes.forms.academic_tr_edit', [
            'academicInfos' => $data['academicInfo']
        ])->render();

        return view(parent::loadDataToView($this->view_path.'.registration.edit'), compact('data'));
    }

    public function updateProfile(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Student::find($id))
            return parent::invalidRequest();

        if ($request->hasFile('student_main_image')) {
            // remove old image from folder
            if (file_exists($this->folder_path.$row->student_image))
                @unlink($this->folder_path.$row->student_image);

            /*upload new student image*/
            $student_image = $request->file('student_main_image');
            $student_image_name = $request->reg_no.'.'.$student_image->getClientOriginalExtension();
            $student_image->move($this->folder_path, $student_image_name);
        }

        $request->request->add(['updated_by' => auth()->user()->id]);
        $request->request->add(['student_image' => isset($student_image_name)?$student_image_name:$row->student_image]);

        $student = $row->update($request->all());

        /*Update Associate Address Info*/
        $row->address()->update([
            'address'    =>  $request->address,
            'state'      =>  $request->state,
            'country'    =>  $request->country,
            'temp_address' =>  $request->temp_address,
            'temp_state' =>  $request->temp_state,
            'temp_country' =>  $request->temp_country,
            'home_phone'   =>  $request->home_phone,
            'mobile_1'   =>  $request->mobile_1,
            'mobile_2'   =>  $request->mobile_2

        ]);

        /*Update Associate Parents Info with Images*/
        $parent = $row->parents()->first();
        $guardian = $row->guardian()->first();

        $parential_image_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR;
        if ($request->hasFile('father_main_image')){
            // remove old image from folder
            if (file_exists($parential_image_path.$parent->father_image))
                @unlink($parential_image_path.$parent->father_image);

            $father_image = $request->file('father_main_image');
            $father_image_name = $row->reg_no.'_father.'.$father_image->getClientOriginalExtension();
            $father_image->move($parential_image_path, $father_image_name);
        }

        if ($request->hasFile('mother_main_image')){
            // remove old image from folder
            if (file_exists($parential_image_path.$parent->mother_image))
                @unlink($parential_image_path.$parent->mother_image);

            $mother_image = $request->file('mother_main_image');
            $mother_image_name = $row->reg_no.'_mother.'.$mother_image->getClientOriginalExtension();
            $mother_image->move($parential_image_path, $mother_image_name);
        }


        if ($request->hasFile('guardian_main_image')){
            // remove old image from folder
            if (file_exists($parential_image_path.$guardian->guardian_image))
                @unlink($parential_image_path.$guardian->guardian_image);

            $guardian_image = $request->file('guardian_main_image');
            $guardian_image_name = $row->reg_no.'_guardian.'.$guardian_image->getClientOriginalExtension();
            $guardian_image->move($parential_image_path, $guardian_image_name);
        }


        $father_image_name = isset($father_image_name)?$father_image_name:$parent->father_image;
        $mother_image_name = isset($mother_image_name)?$mother_image_name:$parent->mother_image;
        $guardian_image_name = isset($guardian_image_name)?$guardian_image_name:$guardian->guardian_image;


        $row->parents()->update([
            'grandfather_first_name'    =>  $request->grandfather_first_name,
            'grandfather_middle_name'   =>  $request->grandfather_middle_name,
            'grandfather_last_name'     =>  $request->grandfather_last_name,
            'father_first_name'         =>  $request->father_first_name,
            'father_middle_name'        =>  $request->father_middle_name,
            'father_last_name'          =>  $request->father_last_name,
            'father_eligibility'        =>  $request->father_eligibility,
            'father_occupation'         =>  $request->father_occupation,
            'father_office'             =>  $request->father_office,
            'father_office_number'      =>  $request->father_office_number,
            'father_residence_number'   =>  $request->father_residence_number,
            'father_mobile_1'           =>  $request->father_mobile_1,
            'father_mobile_2'           =>  $request->father_mobile_2,
            'father_email'              =>  $request->father_email,
            'mother_first_name'         =>  $request->mother_first_name,
            'mother_middle_name'        =>  $request->mother_middle_name,
            'mother_last_name'          =>  $request->mother_last_name,
            'mother_eligibility'        =>  $request->mother_eligibility,
            'mother_occupation'         =>  $request->mother_occupation,
            'mother_office'             =>  $request->mother_office,
            'mother_office_number'      =>  $request->mother_office_number,
            'mother_residence_number'   =>  $request->mother_residence_number,
            'mother_mobile_1'           =>  $request->mother_mobile_1,
            'mother_mobile_2'           =>  $request->mother_mobile_2,
            'mother_email'              =>  $request->mother_email,
            'father_image'              =>  $father_image_name,
            'mother_image'              =>  $mother_image_name

        ]);

        //if guardian link modified or not condition

        if($request->guardian_link_id == null){
            $sgd = $row->guardian()->first();
            $guardiansInfo = [
                'guardian_first_name'         =>  $request->guardian_first_name,
                'guardian_middle_name'        =>  $request->guardian_middle_name,
                'guardian_last_name'          =>  $request->guardian_last_name,
                'guardian_eligibility'        =>  $request->guardian_eligibility,
                'guardian_occupation'         =>  $request->guardian_occupation,
                'guardian_office'             =>  $request->guardian_office,
                'guardian_office_number'      =>  $request->guardian_office_number,
                'guardian_residence_number'   =>  $request->guardian_residence_number,
                'guardian_mobile_1'           =>  $request->guardian_mobile_1,
                'guardian_mobile_2'           =>  $request->guardian_mobile_2,
                'guardian_email'              =>  $request->guardian_email,
                'guardian_relation'           =>  $request->guardian_relation,
                'guardian_address'            =>  $request->guardian_address,
                'guardian_image'              =>  $guardian_image_name

            ];
            GuardianDetail::where('id',$sgd->guardians_id)->update($guardiansInfo);
        }else{
            $studentGuardian = StudentGuardian::where('students_id', $row->id)->update([
                'students_id' => $row->id,
                'guardians_id' => $request->guardian_link_id,
            ]);
        }


        /*Academic Info Start*/
        if ($row && $request->has('institution')) {
            foreach ($request->get('institution') as $key => $institute) {
                $academicInfoExist = AcademicInfo::where([['students_id','=',$row->id],['institution','=',$institute]])->first();
                if($academicInfoExist){
                    $academicInfoUpdate = [
                        'students_id' => $row->id,
                        'institution' => $institute,
                        'board' => $request->get('board')[$key],
                        'pass_year' => $request->get('pass_year')[$key],
                        'symbol_no' => $request->get('symbol_no')[$key],
                        'percentage' => $request->get('percentage')[$key],
                        'division_grade' => $request->get('division_grade')[$key],
                        'major_subjects' => $request->get('major_subjects')[$key],
                        'remark' => $request->get('remark')[$key],
                        'sorting_order' => $key+1,
                        'last_updated_by' => auth()->user()->id
                    ];
                    $academicInfoExist->update($academicInfoUpdate);
                }else{
                    AcademicInfo::create([
                        'students_id' => $row->id,
                        'institution' => $institute,
                        'board' => $request->get('board')[$key],
                        'pass_year' => $request->get('pass_year')[$key],
                        'symbol_no' => $request->get('symbol_no')[$key],
                        'percentage' => $request->get('percentage')[$key],
                        'division_grade' => $request->get('division_grade')[$key],
                        'major_subjects' => $request->get('major_subjects')[$key],
                        'remark' => $request->get('remark')[$key],
                        'sorting_order' => $key+1,
                        'created_by' => auth()->user()->id,
                    ]);
                }

            }
        }
        /*Academic Info End*/

        $request->session()->flash($this->message_success, ' Profile Updated Successfully.');
        //return redirect()->route($this->base_route);
        return back();

    }

    public function password(Request $request, $id)
    {
        //dd($request->all());
        if (!$row = User::find($id)) return parent::invalidRequest();

        if($request->password != $request->confirmPassword){
            $request->session()->flash($this->message_warning, 'Password & Confirm Password Not Match.');
            return redirect()->back();
        }

        if ($request->get('password')){
            $new_password= bcrypt($request->get('password'));
        }

        $request->request->add(['password' => isset($new_password)?$new_password:$row->password]);

        $row->update($request->all());

        $roles = [];
        $roles[] = [
            'user_id' => $row->id,
            'role_id' => $request->role_id
        ];

        $row->userRole()->sync($roles);

        $request->session()->flash($this->message_success, 'Login Detail Updated Successfully.');
        return redirect()->back();
    }

    public function fees()
    {
        $this->panel = "Fees";
        $id = auth()->user()->hook_id;
        $data = [];
        $today = Carbon::parse(today())->format('Y-m-d');
        $data['student'] = Student::select('students.id','students.reg_no','students.reg_date', 'students.first_name',
            'students.middle_name', 'students.last_name','students.faculty','students.semester','students.date_of_birth',
            'students.email', 'ai.mobile_1', 'pd.father_first_name', 'pd.father_middle_name', 'pd.father_last_name',
            'students.student_image','students.academic_status','students.status')
            ->where('students.id','=',$id)
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->first();


        $data['fee_master'] = $data['student']->feeMaster()->orderBy('fee_due_date','desc')->get();
        $data['fee_collection'] = $data['student']->feeCollect()->get();

        $data['student']->payment_today = $data['student']->feeCollect()->where('date','=',$today)->sum('paid_amount');

        /*total Calculation on Table Foot*/
        $data['student']->fee_amount = $data['student']->feeMaster()->sum('fee_amount');
        $data['student']->discount = $data['student']->feeCollect()->sum('discount');
        $data['student']->fine = $data['student']->feeCollect()->sum('fine');
        $data['student']->paid_amount = $data['student']->feeCollect()->sum('paid_amount');
        $data['student']->balance =
            ($data['student']->fee_amount - ($data['student']->paid_amount + $data['student']->discount))+ $data['student']->fine;

        $data['student']->currentURL = URL::current();
        
        //Previous Payment Record
        $data['onlinePayments'] = OnlinePayment::where('students_id','=',$id)->orderBy('date')->get();
    
        $data['current_unpaid_installment'] = $this->currentUnpaidInstallment($data['student']->reg_no);        
        $current_installment_detail = $this->currentUnpaidInstallmentDetail($data['student']->reg_no);   
        $data['current_installment_detail'] = $current_installment_detail['installments'];     

        if (
            $data['student']->balance > 0 &&
            (
                empty($data['current_installment_detail']) ||
                !isset($data['current_unpaid_installment']['installmentAmount']) ||
                (float) $data['current_unpaid_installment']['installmentAmount'] <= 0
            )
        ) {
            $fallbackInstallment = $this->buildStudentFeeInstallmentFallback($data['student']);

            if (!empty($fallbackInstallment['installments'])) {
                $data['current_installment_detail'] = $fallbackInstallment['installments'];
                $data['current_unpaid_installment'] = [
                    'studentId' => $data['student']->reg_no,
                    'studentName' => trim(implode(' ', array_filter([
                        $data['student']->first_name ?? '',
                        $data['student']->middle_name ?? '',
                        $data['student']->last_name ?? '',
                    ]))),
                    'installmentAmount' => $fallbackInstallment['current_payable_amount'],
                ];
            }
        }
        //dd($data['current_installment_detail']);

        return view(parent::loadDataToView($this->view_path.'.fees.index'), compact('data'));
    }

    protected function buildStudentFeeInstallmentFallback($student)
    {
        $feeMasters = FeeMaster::where('students_id', $student->id)
            ->where('semester', $student->semester)
            ->orderBy('id', 'desc')
            ->get();

        if ($feeMasters->isEmpty()) {
            $feeMasters = FeeMaster::where('students_id', $student->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        if ($feeMasters->isEmpty()) {
            return [
                'current_payable_amount' => 0,
                'installments' => [],
            ];
        }

        $latestFeeMaster = $feeMasters->first();
        $totalAmount = round($feeMasters->sum('fee_amount'), 2);

        $installmentPercentages = [1 => 30, 2 => 40, 3 => 30];
        $dueDates = [
            1 => $latestFeeMaster->fee_due_date,
            2 => $latestFeeMaster->fee_due_date2 ?: $latestFeeMaster->fee_due_date,
            3 => $latestFeeMaster->fee_due_date3 ?: $latestFeeMaster->fee_due_date2 ?: $latestFeeMaster->fee_due_date,
        ];

        $installments = [];
        $currentPayableAmount = 0;
        $currentDate = Carbon::now();

        foreach ($installmentPercentages as $number => $percentage) {
            $initialAmount = round(($totalAmount * $percentage) / 100, 2);
            $paidAmount = round($feeMasters->sum(function ($feeMaster) use ($number) {
                return $feeMaster->collections()
                    ->where('status', 1)
                    ->where('installment_number', $number)
                    ->sum('paid_amount');
            }), 2);
            $discountAmount = round($feeMasters->sum(function ($feeMaster) use ($number) {
                return $feeMaster->collections()
                    ->where('status', 1)
                    ->where('installment_number', $number)
                    ->sum('discount');
            }), 2);
            $fineAmount = round($feeMasters->sum(function ($feeMaster) use ($number) {
                return $feeMaster->collections()
                    ->where('status', 1)
                    ->where('installment_number', $number)
                    ->sum('fine');
            }), 2);

            $dueDate = $dueDates[$number] ?: now()->format('Y-m-d');
            $isOverdue = $currentDate->gt(Carbon::parse($dueDate));
            $dueAmount = max(0, round($initialAmount - ($paidAmount + $discountAmount), 2));
            $status = $dueAmount <= 0 ? 'paid' : ($isOverdue ? 'overdue' : 'pending');
            $finalDueAmount = round($dueAmount + $fineAmount, 2);

            if ($currentPayableAmount <= 0 && $finalDueAmount > 0) {
                $currentPayableAmount = $finalDueAmount;
            }

            $installments[] = [
                'number' => $number,
                'percentage' => $percentage,
                'initial_due_amount' => $initialAmount,
                'due_date' => $dueDate,
                'status' => $status,
                'is_overdue' => $isOverdue,
                'paid_amount' => $paidAmount,
                'discount_amount' => $discountAmount,
                'fine' => $fineAmount,
                'due_amount' => $finalDueAmount,
            ];
        }

        return [
            'current_payable_amount' => $currentPayableAmount,
            'installments' => $installments,
        ];
    }

    public function library()
    {
        $this->panel = "Library";
        $id = auth()->user()->hook_id;
        $data['lib_member'] = LibraryMember::where(['library_members.user_type' => 1, 'library_members.member_id' => $id])
            ->first();

        if($data['lib_member'] != null){
            $data['circulation'] = $data['lib_member']->libCirculation()->first();

            $data['books_taken'] = $data['lib_member']->libBookIssue()->select('book_issues.id', 'book_issues.member_id',
                'book_issues.book_id',  'book_issues.issued_on', 'book_issues.due_date', 'b.book_masters_id',
                'b.book_code', 'bm.title','bm.categories','bm.image')
                ->where('book_issues.status',1)
                ->join('books as b','b.id','=','book_issues.book_id')
                ->join('book_masters as bm','bm.id','=','b.book_masters_id')
                ->orderBy('book_issues.issued_on', 'desc')
                ->get();

            $data['books_history'] = $data['lib_member']->libBookIssue()->select('book_issues.id', 'book_issues.member_id',
                'book_issues.book_id',  'book_issues.issued_on', 'book_issues.due_date','book_issues.return_date', 'b.book_masters_id',
                'b.book_code', 'bm.title','bm.categories','bm.image')
                ->join('books as b','b.id','=','book_issues.book_id')
                ->join('book_masters as bm','bm.id','=','b.book_masters_id')
                ->orderBy('book_issues.issued_on', 'desc')
                ->get();
        }

        return view(parent::loadDataToView($this->view_path.'.library.index'), compact('data'));
    }

    public function bookList(Request $request)
    {
        $this->panel = "Library - Book";
        $id = auth()->user()->hook_id;
        $data = [];
        $data['books'] = BookMaster::select('id','code', 'title', 'image', 'categories', 'author', 'publisher', 'status')
            ->where(function ($query) use ($request) {

                if ($request->has('isbn_number')) {
                    $query->where('isbn_number', 'like', '%'.$request->isbn_number.'%');
                    $this->filter_query['isbn_number'] = $request->isbn_number;
                }

                if ($request->has('code')) {
                    $query->where('code', 'like', '%'.$request->code.'%');
                    $this->filter_query['code'] = $request->code;
                }

                if ($request->has('categories')) {
                    $query->where('categories', 'like', '%'.$request->categories.'%');
                    $this->filter_query['categories'] = $request->categories;
                }

                if ($request->has('title')) {
                    $query->where('title', 'like', '%'.$request->title.'%');
                    $this->filter_query['title'] = $request->title;
                }

                if ($request->has('author')) {
                    $query->where('author', 'like', '%'.$request->author.'%');
                    $this->filter_query['author'] = $request->author;
                }

                if ($request->has('language')) {
                    $query->where('language', 'like', '%'.$request->language.'%');
                    $this->filter_query['language'] = $request->language;
                }

                if ($request->has('publisher')) {
                    $query->where('publisher', 'like', '%'.$request->publisher.'%');
                    $this->filter_query['publisher'] = $request->publisher;
                }

                if ($request->has('publish_year')) {
                    $query->where('publish_year', 'like', '%'.$request->publish_year.'%');
                    $this->filter_query['publish_year'] = $request->publish_year;
                }

                if ($request->has('edition')) {
                    $query->where('edition', 'like', '%'.$request->edition.'%');
                    $this->filter_query['edition'] = $request->edition;
                }

                if ($request->has('edition_year')) {
                    $query->where('edition_year', 'like', '%'.$request->edition_year.'%');
                    $this->filter_query['edition_year'] = $request->edition_year;
                }

                if ($request->has('series')) {
                    $query->where('series', 'like', '%'.$request->series.'%');
                    $this->filter_query['series'] = $request->series;
                }

                if ($request->has('rack_location')) {
                    $query->where('rack_location', 'like', '%'.$request->rack_location.'%');
                    $this->filter_query['rack_location'] = $request->rack_location;
                }
            })
            ->orderBy('title','asc')
            ->get();

        $data['categories'] = $this->activeBookCategories();

        $data['lib_member'] = LibraryMember::where(['library_members.user_type' => 1, 'library_members.member_id' => $id])
            ->first();

        if($data['lib_member']){
            $data['book_request'] = BookMaster::select('book_masters.id','book_masters.code', 'book_masters.title', 'book_masters.image',
                'book_masters.categories', 'book_masters.author', 'book_masters.publisher',
                'br.created_at as requested_date')
                ->where('br.member_id',$data['lib_member']->id)
                ->orderBy('book_masters.title','asc')
                ->join('book_requests as br','br.book_masters_id','=','book_masters.id')
                ->get();

            $data['book_request_ids'] = $data['book_request']->pluck('id')->toArray();
        }else{
            $request->session()->flash($this->message_warning, 'You are not a valid member of Library. Please, contact Library Department for Membership.');
        }

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        return view(parent::loadDataToView($this->view_path.'.library.book-list.index'), compact('data'));
    }

    public function requestBook(Request $request, $bookId)
    {
        $this->panel = "Library- Book Request";
        $id = auth()->user()->hook_id;
        $bookId = decrypt($bookId);
        $data['lib_member'] = LibraryMember::where(['library_members.user_type' => 1, 'library_members.member_id' => $id])
            ->first();

        if($data['lib_member'] != null){
            $memberId = $data['lib_member']->id;

            $data['circulation'] = $data['lib_member']->libCirculation()->first();
            $issueLimitBooks = $data['circulation']->issue_limit_books;

            $data['books_taken'] = $data['lib_member']->libBookIssue()->select('book_issues.id', 'book_issues.member_id',
                'book_issues.book_id',  'book_issues.issued_on', 'book_issues.due_date', 'b.book_masters_id',
                'b.book_code', 'bm.title','bm.categories','bm.image')
                ->where('book_issues.status',1)
                ->join('books as b','b.id','=','book_issues.book_id')
                ->join('book_masters as bm','bm.id','=','b.book_masters_id')
                ->orderBy('book_issues.issued_on', 'desc')
                ->get();

            $currentlyTakenBooks = $data['books_taken']->count();
            $eligibleBookTaken = $issueLimitBooks - $currentlyTakenBooks;

            $bookRequestedBooks = BookRequest::where('member_id',$memberId)->count();
            //dd($bookRequestedBooks);
            $eligibleReqestBook = $issueLimitBooks - $bookRequestedBooks;

            if($eligibleReqestBook > 0  ){
                $bookRequested = BookRequest::where('member_id',$memberId)->where('book_masters_id',$bookId)->count();
                if($bookRequested > 0){
                    $request->session()->flash($this->message_warning, 'This book is Already Requested by You. Please, Request another book.');
                }else{
                    BookRequest::create([
                        'book_masters_id' => $bookId,
                        'member_id' => $memberId,
                        'created_by' => auth()->user()->id,
                    ]);

                    $request->session()->flash($this->message_success, 'Book Successfully Requested. Contact library department to take your requested books.');
                }

            }else{
                $request->session()->flash($this->message_warning, 'You were requested maximum books. You will not able to requesting more books now. ');
            }
        }else{
            $request->session()->flash($this->message_warning, 'You are not a valid member of Library. Please, contact Library Department for Membership.');
        }
        return back();
    }    


    public function hostel()
    {
        $this->panel = "Hostel";
        $id = auth()->user()->hook_id;
        $data = [];

        $data['history'] = ResidentHistory::select('resident_histories.years_id', 'resident_histories.hostels_id',
            'resident_histories.rooms_id', 'resident_histories.beds_id',
            'resident_histories.history_type','resident_histories.created_at')
            ->where(['r.user_type' => 1, 'r.member_id' => $id])
            ->join('residents as r', 'r.id', '=', 'resident_histories.residents_id')
            ->join('beds as b', 'b.id', '=', 'resident_histories.beds_id')
            ->latest()
            ->get();

        return view(parent::loadDataToView($this->view_path.'.hostel.index'), compact('data'));
    }

    public function transport()
    {
        $this->panel = "Transport";
        $id = auth()->user()->hook_id;
        $data = [];

        /*Transport History*/
        $data['transport_history'] = TransportHistory::select('transport_histories.id', 'transport_histories.years_id',
            'transport_histories.routes_id', 'transport_histories.vehicles_id',  'transport_histories.history_type',
            'transport_histories.created_at','tu.member_id','tu.user_type')
            ->where(['tu.user_type' => 1, 'tu.member_id' => $id])
            ->join('transport_users as tu','tu.id','=','transport_histories.travellers_id')
            ->latest()
            ->get();

        return view(parent::loadDataToView($this->view_path.'.transport.index'), compact('data'));
    }

    public function subject()
    {
        $this->panel = "Course";
        $id = auth()->user()->hook_id;
        $student = Student::select('semester')->where('id',$id)->first();
        $data = [];
        $data['semester'] = Semester::find($student->semester);
        $data['subject'] = $data['semester']->subjects()->orderBy('title')->get();

        return view(parent::loadDataToView($this->view_path.'.subject.index'), compact('data'));
    }

    public function notice()
    {
        $this->panel = "Notice";
        $data = [];
        $userRoleId = auth()->user()->roles()->first()->id;
        $data['rows'] = Notice::select('id', 'title', 'message', 'publish_date', 'end_date', 'display_group','status')
            ->where('display_group','like','%'.$userRoleId.'%')
            ->latest()
            ->get();

        return view(parent::loadDataToView($this->view_path.'.notice.index'), compact('data'));
    }

    public function download()
    {
        $this->panel = "Download";
        $id = auth()->user()->hook_id;
        $student = Student::select('semester')->where('id',$id)->first();
        $data = [];
        //$data['semester'] = Semester::find($student->semester);

        $data['download'] = Download::where(function ($query) use($student){
                            $query->where('semesters_id',$student->semester)
                                ->orWhere('semesters_id',null);
                        })
                        ->Active()
                        ->latest()
                        ->get();
        $data['download'] = Download::where('semesters_id',$student->semester)->Active()
                            ->latest()
                            ->get();

        return view(parent::loadDataToView($this->view_path.'.download.index'), compact('data'));
    }

    public function meeting()
    {
        $this->panel = "Meeting";
        $id = auth()->user()->hook_id;
        $student = Student::select('semester')->where('id',$id)->first();
        $data = [];
        //$data['semester'] = Semester::find($student->semester);

        /*$data['meetings'] = Meeting::where('semesters_id',$student->semester)
            ->Active()
            ->latest()
            ->get();*/

        $data['meetings'] = Meeting::where('semesters_id',$student->semester)
            ->get();

        return view(parent::loadDataToView($this->view_path.'.meeting.index'), compact('data'));
    }

    /*Exam group*/
    public function exams()
    {
        $this->panel = "Exams";
        $id = auth()->user()->hook_id;
        $data = [];
        $data['student'] = Student::find($id);
        $semester = Semester::find($data['student']->semester);
        $year = Year::where('active_status',1)->first();
        if(!$year) return back();

        $data['schedule_exams'] = ExamSchedule::select('years_id', 'months_id', 'exams_id', 'faculty_id', 'semesters_id', 'publish_status', 'status')
            //->where([['semesters_id',$semester->id],['years_id',$year->id]])
            ->where('semesters_id',$semester->id)
            ->groupBy('years_id', 'months_id', 'exams_id', 'faculty_id', 'semesters_id','publish_status', 'status')
            ->orderBy('years_id', 'desc')
            ->orderBy('months_id', 'asc')
            ->get();

        return view(parent::loadDataToView($this->view_path.'.exam.index'), compact('data'));
    }

    public function examSchedule(Request $request, $year=null,$month=null,$exam=null,$faculty=null,$semester=null)
    {
        $this->panel = "Exam Schedule";
        $id = auth()->user()->hook_id;
        $student_id = $id;
        $data = [];
        $whereCondition = [
            ['years_id', '=' , $year],
            ['months_id', '=' , $month],
            ['exams_id', '=' , $exam],
            ['faculty_id', '=' , $faculty],
            ['semesters_id', '=' , $semester],
        ];

        $examSchedule = ExamSchedule::where($whereCondition)
            ->get();

        $exam_schedule_id = array_pluck($examSchedule,'id');

        $data['subjects'] = ExamSchedule::select('exam_schedules.id','exam_schedules.subjects_id',
            'exam_schedules.date', 'exam_schedules.start_time', 'exam_schedules.end_time',
            'exam_schedules.full_mark_theory', 'exam_schedules.pass_mark_theory',
            'exam_schedules.full_mark_practical',
            'exam_schedules.pass_mark_practical', 's.code', 's.title')
            ->where([
                ['exam_schedules.years_id', '=' , $year],
                ['exam_schedules.months_id', '=' , $month],
                ['exam_schedules.exams_id', '=' , $exam],
                ['exam_schedules.faculty_id', '=' , $faculty],
                ['exam_schedules.semesters_id', '=' , $semester],
            ])
            ->join('subjects as s','s.id','=','exam_schedules.subjects_id')
            ->orderBy('exam_schedules.date','asc')
            ->get();

        if($data['subjects']->count() == 0)
            return back()->with($this->message_warning, 'No any Subject Scheduled in your target exam. Please, Schedule exam first. ');

        $data['year'] = $year;
        $data['month'] = $month;
        $data['exam'] = $exam;
        $data['faculty'] = $faculty;
        $data['semester'] = $semester;

        return view(parent::loadDataToView($this->view_path.'.exam.routine'), compact('data'));
    }

    public function admitCard(Request $request, $year=null,$month=null,$exam=null,$faculty=null,$semester=null)
    {
        $this->panel = "Admit Card";
        $id = auth()->user()->hook_id;
        $data = [];
        $whereCondition = [
            ['years_id', '=' , $year],
            ['months_id', '=' , $month],
            ['exams_id', '=' , $exam],
            ['faculty_id', '=' , $faculty],
            ['semesters_id', '=' , $semester],
        ];
        $data['subjects'] = ExamSchedule::where($whereCondition)
            ->get();

        if($data['subjects']->count() == 0)
            return back()->with($this->message_warning, 'No any Subject Scheduled in your target exam.');

        $data['student'] = Student::select('id','reg_no','date_of_birth', 'first_name', 'middle_name', 'last_name','student_image','gender','blood_group' ,'faculty', 'semester','status')
            ->where('id',$id)
            ->get();

        $data['year'] = $year;
        $data['month'] = $month;
        $data['exam'] = $exam;
        $data['faculty'] = $faculty;
        $data['semester'] = $semester;

        return view(parent::loadDataToView($this->view_path.'.exam.admit-card'), compact('data'));
    }

/*
    public function examScore(Request $request, $year=null,$month=null,$exam=null,$faculty=null,$semester=null)
    {
        $id = auth()->user()->hook_id;
        $student_id = $id;
        $data = [];
        $whereCondition = [
            ['years_id', '=' , $year],
            ['months_id', '=' , $month],
            ['exams_id', '=' , $exam],
            ['faculty_id', '=' , $faculty],
            ['semesters_id', '=' , $semester],
        ];

        $examSchedule = ExamSchedule::where($whereCondition)
            ->where('publish_status',1)
            ->get();

        if ($examSchedule->count() == 0)
            return back()->with($this->message_warning,'Result not published Yet. Please be patient.');

        $exam_schedule_id = array_pluck($examSchedule,'id');
        $semester = Semester::find($semester);

        $students = Student::select('id','reg_no', 'first_name','middle_name','last_name','date_of_birth',
            'faculty','semester')
            ->where('id', $student_id)
            ->get();

        //filter student with schedule subject mark ledger
        $filteredStudent  = $students->filter(function ($value, $key) use ($exam_schedule_id, $semester){
            $subject = $value->markLedger()
                ->select( 'exam_schedule_id',  'obtain_mark_theory', 'obtain_mark_practical','absent_theory','absent_practical')
                ->whereIn('exam_schedule_id', $exam_schedule_id)
                ->get();

            //filter subject and joint mark from schedules;
            $filteredSubject  = $subject->filter(function ($subject, $key) use($semester){
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
                $obtain_mark_theory = $subject->obtain_mark_theory;
                $absent_theory = $subject->absent_theory;
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

                return $subject;
            });

            //order subject order on schedule
            $value->subjects = $filteredSubject->sortBy('sorting_order');

            //calculate GPA
            //calculate total mark & percentage
            $gp_collection = array_pluck($value->subjects,'grade_point');

            $filtered_gp_collection  =  array_where($gp_collection, function ($value, $key) {
                return is_numeric($value);
            });

            $gradePoint = array_sum($filtered_gp_collection) / $subject->count();
            $value->gpa_point = number_format((float)$gradePoint, 2);

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
            //Calculate percentage
            $value->percentage = $percentage = ($obtainedMark*100)/ $totalMark;

            $value->gpa_average = $this->getGrade($semester, $percentage);
            $value->remark = $this->getRemark($semester, $percentage);

            return $value;

        });

        $data['student'] = $filteredStudent;

        $data['year'] = $year;
        $data['month'] = $month;
        $data['exam'] = $exam;
        $data['faculty'] = $faculty;
        $data['semester'] = $semester->id;

        return view(parent::loadDataToView($this->view_path.'.exam.grading-sheet'), compact('data'));
    }
    */

    public function examScore(Request $request, $year=null,$month=null,$exam=null,$faculty=null,$semester=null)
    {
        $id = auth()->user()->hook_id;
        $student_id = $id;
        $data = [];
        $whereCondition = [
            ['years_id', '=' , $year],
            ['months_id', '=' , $month],
            ['exams_id', '=' , $exam],
            ['faculty_id', '=' , $faculty],
            ['semesters_id', '=' , $semester],
        ];

        $examSchedule = ExamSchedule::where($whereCondition)
            ->where('publish_status',1)
            ->get();

        if ($examSchedule->count() == 0)
            return back()->with($this->message_warning,'Result not published Yet. Please be patient.');

        $exam_schedule_id = array_pluck($examSchedule,'id');
        $semester = Semester::find($semester);

        $students = Student::select('id','reg_no', 'first_name','middle_name','last_name','date_of_birth',
            'faculty','semester')
            ->where('id', $student_id)
            ->get();

        /*filter student with schedule subject mark ledger*/

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

        $data['student'] = $filteredStudent;
        //Detail of Grade Sheet
        $data['grade-scale-range'] = GradingScale::where('gradingType_id',$semester->gradingType_id)->get();

        $data['year'] = $year;
        $data['month'] = $month;
        $data['exam'] = $exam;
        $data['faculty'] = $faculty;
        $data['semester'] = $semester->id;
        //dd('heere');

        return view(parent::loadDataToView($this->view_path.'.exam.university-grading-sheet'), compact('data'));
    }


    /*assignment group*/
    public function assignment()
    {
        $this->panel = "Assignment";
        $id = auth()->user()->hook_id;
        $data = [];
        $data['student'] = Student::find($id);

        $data['assignment'] = Assignment::where('semesters_id',$data['student']->semester)
            ->latest()
            ->get();

        return view(parent::loadDataToView($this->view_path.'.assignment.index'), compact('data'));
    }

    public function addAnswer(Request $request, $id)
    {
        $this->panel = "Submit Assignment";
        $studentId = auth()->user()->hook_id;
        $data = [];
        $data['student'] = Student::find($studentId);
        $semester = Semester::find($data['student']->semester)->id;
        $year = Year::where('active_status',1)->first()->id;

        if(!$year) return back();

        $getAnswer = AssignmentAnswer::where('assignments_id',$id)->where('students_id', $studentId)->first();
        if($getAnswer){
            $request->session()->flash($this->message_warning,'Your Previous Answer Exist Please Edit Your Answer.');
            return redirect(route('user-student.assignment'));
        }

        $assignment = $data['assignment'] = Assignment::select('id','created_by', 'last_updated_by', 'years_id','semesters_id', 'subjects_id', 'publish_date',
            'end_date', 'title','description','file', 'status')
            ->where('id',$id)
            ->where('years_id',$year)
            ->where('semesters_id',$semester)
            ->first();

       //check available time period
       $this->checkSubmitTimeLimitation($request, $assignment);

        return view(parent::loadDataToView($this->view_path.'.assignment.answer.add'), compact('data'));
    }


    public function storeAnswer(Request $request)
    {
        $studentId = auth()->user()->hook_id;
        $data = [];
        $data['student'] = Student::find($studentId);
        $folder_path = public_path().DIRECTORY_SEPARATOR.'assignments'.DIRECTORY_SEPARATOR.'answers'.DIRECTORY_SEPARATOR;

        $assignment = Assignment::find($request->get('assignments_id'));

        if(!$assignment) return back();
        
       //check available time period
       $this->checkSubmitTimeLimitation($request, $assignment);

        $getAnswer = AssignmentAnswer::where('assignments_id',$assignment->id)->where('students_id', $studentId)->first();
        if($getAnswer){
            $request->session()->flash($this->message_warning,'Your Previous Answer Exist. Please Edit Your Answer.');
            return redirect(route('user-student.assignment'));
        }

        if ($request->hasFile('attach_file')){
            $name = str_slug($assignment->title);
            $file = $request->file('attach_file');
            $file_name = rand(4585, 9857).'_'.$name.'.'.$file->getClientOriginalExtension();
            $file->move($folder_path, $file_name);
        }else{
            $file_name = "";
        }

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['assignments_id' => $assignment->id]);
        $request->request->add(['students_id' => $data['student']->id]);
        $request->request->add(['file' => $file_name]);

        AssignmentAnswer::create($request->all());

        $request->session()->flash($this->message_success,'Answer Add Successfully.');

        return redirect(route('user-student.assignment'));
    }

    public function editAnswer(Request $request, $id)
    {
        $this->panel = "Edit Assignment";
        $studentId = auth()->user()->hook_id;
        $data = [];
        $data['student'] = Student::find($studentId);
        $semester = Semester::find($data['student']->semester)->id;
        $year = Year::where('active_status',1)->first()->id;

        if(!$year) return back();

        $data['row'] = AssignmentAnswer::where('assignments_id',$id)->where('students_id', $studentId)->first();

        if(!$data['row']) {
            $request->session()->flash($this->message_warning,'Answer Not Found. Please Create Your Answer First.');
            return redirect()->route('user-student.assignment');
        }

        if($data['row']->approve_status == 1){
            $request->session()->flash($this->message_warning,' Your Answer is Approved. So, You can not change the approved answer.');
            return redirect(route('user-student.assignment'));
        }

        if($data['row']->approve_status == 2){
            $request->session()->flash($this->message_danger,' Your Answer is Rejected. So, Contact You Subject Teacher.');
            return redirect(route('user-student.assignment'));
        }

        $assignment = $data['assignment'] = Assignment::select('id','created_by', 'last_updated_by', 'years_id','semesters_id', 'subjects_id', 'publish_date',
            'end_date', 'title','description','file', 'status')
            ->where('id',$id)
            ->where('years_id',$year)
            ->where('semesters_id',$semester)
            ->first();

        //check available time period
        if (!$this->checkSubmitTimeLimitation($request, $assignment)) return parent::invalidRequest();

        return view(parent::loadDataToView($this->view_path.'.assignment.answer.edit'), compact('data'));
    }

    public function updateAnswer(Request $request, $id)
    {

        if (!$row = AssignmentAnswer::find($id)) return parent::invalidRequest();

        $studentId = auth()->user()->hook_id;
        $data = [];
        $data['student'] = Student::find($studentId);
        $folder_path = public_path().DIRECTORY_SEPARATOR.'assignments'.DIRECTORY_SEPARATOR.'answers'.DIRECTORY_SEPARATOR;

        $assignment = Assignment::find($request->assignments_id);

        //check available time period
        if (!$this->checkSubmitTimeLimitation($request, $assignment)) return parent::invalidRequest();

        if ($request->hasFile('attach_file')){
            $name = str_slug($assignment->title);
            $file = $request->file('attach_file');
            $file_name = rand(4585, 9857).'_'.$name.'.'.$file->getClientOriginalExtension();
            $file->move($folder_path, $file_name);

            if (file_exists($folder_path.$row->file))
                @unlink($folder_path.$row->file);
        }else{
            $file_name = $row->file;
        }

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['assignments_id' => $assignment->id]);
        $request->request->add(['students_id' => $data['student']->id]);
        $request->request->add(['file' => $file_name]);

        $year = Year::where('active_status',1)->first()->id;

        $request->request->add(['years_id' => $year]);
        $request->request->add(['last_updated_by' => auth()->user()->id]);
        $request->request->add(['file' => isset($file_name)?$file_name:$row->file]);

        $row->update($request->all());

        $request->session()->flash($this->message_success,'Answer Updated Successfully.');
        return redirect(route('user-student.assignment'));
    }

    public function viewAssignmentAnswer(Request $request, $id, $answer)
    {
        $this->panel = "Assignment Detail";
        $data = [];
        $studentId = auth()->user()->hook_id;
        $data['student'] = Student::find($studentId);

        $data['assignment'] = Assignment::find($id);

        $data['answers'] = $data['assignment']->answers()->where('assignment_answers.id',$answer)
            ->select('assignment_answers.created_by','assignment_answers.last_updated_by','assignment_answers.id','assignment_answers.answer_text',
                'assignment_answers.file','assignment_answers.approve_status','assignment_answers.status','s.id as students_id')
            ->join('students as s','s.id','=','assignment_answers.students_id')
            ->first();

        if(!$data['answers']) {
            $request->session()->flash($this->message_warning,'Answer Not Found.');
            return redirect()->route('user-student.assignment');
        }

        $data['student'] = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
            'students.faculty','students.semester', 'students.academic_status', 'students.first_name', 'students.middle_name',
            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group', 'students.nationality',
            'students.mother_tongue', 'students.email', 'students.extra_info', 'students.student_image', 'students.status')
            ->where('students.id','=',$data['answers']->students_id)
            ->first();

        return view(parent::loadDataToView('user-student.assignment.view.index'), compact('data'));
    }

    public function checkSubmitTimeLimitation($request, $assignment){
        $currentDate = date('Y-m-d H:i:s');
       if($assignment->end_date <= $currentDate){
           $request->session()->flash($this->message_warning, 'You Can Not Submit Answer Because of Time Limitation ');
           return false;
      }else{
          return true;
      }

    }

/*Application group*/
    public function applicationIndex()
    {
        $this->panel = "Application";
        $id = auth()->user()->hook_id;
        $data = [];
        $data['student'] = Student::find($id);

        $data['application'] = Application::where('created_by',auth()->user()->id)
            ->latest()
            ->get();

        return view(parent::loadDataToView($this->view_path.'.application.index'), compact('data'));
    }

    public function applicationAdd(Request $request)
    {
        $this->panel = "Submit Application";
        $studentId = auth()->user()->hook_id;
        $data = [];
        $data['student'] = Student::find($studentId);
        $semester = Semester::find($data['student']->semester)->id;
        $year = Year::where('active_status',1)->first()->id;
        if(!$year) return back();

        $data['applicationType'] = $this->activeApplicationType();


//        $getAnswer = Application::where('created_by',$id)->first();
//        if($getAnswer){
//            $request->session()->flash($this->message_warning,'Your Previous Answer Exist Please Edit Your Answer.');
//            return redirect(route('user-student.assignment'));
//        }
//
//        $assignment = $data['assignment'] = Assignment::select('id','created_by', 'last_updated_by', 'years_id','semesters_id', 'subjects_id', 'publish_date',
//            'end_date', 'title','description','file', 'status')
//            ->where('id',$id)
//            ->where('years_id',$year)
//            ->where('semesters_id',$semester)
//            ->first();

        //check available time period
       // $this->checkSubmitTimeLimitation($request, $assignment);

        return view(parent::loadDataToView($this->view_path.'.application.add'), compact('data'));
    }


    public function applicationStore(AddValidation $request)
    {
     //   dd($request->all());
//     "_token" => "2sWreFVaTL0CjpMjrqolUoJUT304m0ly1vv4aHCJ"
//  "application_type_id" => "1"
//  "date" => "2023-01-24"
//  "end_date" => "2023-01-28"
//  "subject" => "test subject"
//  "message" => "sadfsadf"
//  "add_assignment" => "Save"
//  "attach_file" =>

        $studentId = auth()->user()->hook_id;
        $data = [];
        $data['student'] = Student::find($studentId);
        $folder_path = public_path().DIRECTORY_SEPARATOR.'applications'.DIRECTORY_SEPARATOR.'answers'.DIRECTORY_SEPARATOR;

        $year = Year::where('active_status',1)->first()->id;
        if ($request->hasFile('attach_file')){
            $name = str_slug($request->get('subject'));
            $file = $request->file('attach_file');
            $file_name = rand(4585, 9857).'_'.$name.'.'.$file->getClientOriginalExtension();
            $file->move($folder_path, $file_name);
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

        $request->session()->flash($this->message_success, 'Application Submit Successfully. Wait for Admin Response.');

        if($request->add_application_another) {
            return back();
        }else{
            return redirect()->route('user-student.application');
        }

    }

    public function applicationEdit(Request $request, $id)
    {
        $this->panel = "Edit Application";
        $studentId = auth()->user()->hook_id;
        $data = [];
        $data['student'] = Student::find($studentId);
        $semester = Semester::find($data['student']->semester)->id;
        $year = Year::where('active_status',1)->first()->id;

        if(!$year) return back();

        //dd($id);

        $data['row'] = Application::find(decrypt($id));

        $data['applicationType'] = $this->activeApplicationType();
//        $data['row'] = AssignmentAnswer::where('assignments_id',$id)->where('students_id', $studentId)->first();
//
//        if(!$data['row']) {
//            $request->session()->flash($this->message_warning,'Answer Not Found. Please Create Your Answer First.');
//            return redirect()->route('user-student.assignment');
//        }
//
//        if($data['row']->approve_status == 1){
//            $request->session()->flash($this->message_warning,' Your Answer is Approved. So, You can not change the approved answer.');
//            return redirect(route('user-student.assignment'));
//        }
//
//        if($data['row']->approve_status == 2){
//            $request->session()->flash($this->message_danger,' Your Answer is Rejected. So, Contact You Subject Teacher.');
//            return redirect(route('user-student.assignment'));
//        }
//
//        $assignment = $data['assignment'] = Assignment::select('id','created_by', 'last_updated_by', 'years_id','semesters_id', 'subjects_id', 'publish_date',
//            'end_date', 'title','description','file', 'status')
//            ->where('id',$id)
//            ->where('years_id',$year)
//            ->where('semesters_id',$semester)
//            ->first();

        //check available time period
      //  if (!$this->checkSubmitTimeLimitation($request, $assignment)) return parent::invalidRequest();
       // dd('hello');

        return view(parent::loadDataToView($this->view_path.'.application.edit'), compact('data'));
    }

    public function applicationUpdate(Request $request, $id)
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


        $request->session()->flash($this->message_success,'Application Updated Successfully.');
        return redirect(route('user-student.application'));
    }

    public function applicationDelete(Request $request, $id)
    {
        $this->panel = "Application Detail";
        $data = [];
        $studentId = auth()->user()->hook_id;
        $data['student'] = Student::find($studentId);

        $data['application'] = Application::find($id);

        return view(parent::loadDataToView('user-student.assignment.view.index'), compact('data'));
    }


    public function applicationView(Request $request, $id, $answer)
    {
        $this->panel = "Application Detail";
        $data = [];
        $studentId = auth()->user()->hook_id;
        $data['student'] = Student::find($studentId);

        $data['assignment'] = Assignment::find($id);

        $data['answers'] = $data['assignment']->answers()->where('assignment_answers.id',$answer)
            ->select('assignment_answers.created_by','assignment_answers.last_updated_by','assignment_answers.id','assignment_answers.answer_text',
                'assignment_answers.file','assignment_answers.approve_status','assignment_answers.status','s.id as students_id')
            ->join('students as s','s.id','=','assignment_answers.students_id')
            ->first();

        if(!$data['answers']) {
            $request->session()->flash($this->message_warning,'Answer Not Found.');
            return redirect()->route('user-student.assignment');
        }

        $data['student'] = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
            'students.faculty','students.semester', 'students.academic_status', 'students.first_name', 'students.middle_name',
            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group', 'students.nationality',
            'students.mother_tongue', 'students.email', 'students.extra_info', 'students.student_image', 'students.status')
            ->where('students.id','=',$data['answers']->students_id)
            ->first();

        return view(parent::loadDataToView('user-student.assignment.view.index'), compact('data'));
    }

    public function routine(Request $request)
    {
        $this->panel = "Class Routine";

        // Logged-in student
        $studentId = auth()->user()->hook_id;
        $student   = Student::select('id','faculty','semester','batch','first_name','middle_name','last_name')
                        ->findOrFail($studentId);

        // References
        $faculty  = Faculty::find($student->faculty);
        $semester = Semester::find($student->semester);
        $batch    = $student->batch ? StudentBatch::find($student->batch) : null;

        // Subject filter (GET ?subject_id=)
        $subjectIdRaw = $request->input('subject_id', null);
        $subjectId    = ($subjectIdRaw === null || $subjectIdRaw === '') ? null : (int) $subjectIdRaw;

        /**
         * Subject dropdown (avoid ambiguous 'id'):
         * Prefer Semester->subjects() relation if it exists, otherwise join pivot.
         */
        if ($semester && method_exists($semester, 'subjects')) {
            $subjects = $semester->subjects()
                ->select('subjects.id', 'subjects.title')   // qualify!
                ->orderBy('subjects.title')
                ->pluck('subjects.title', 'subjects.id');
        } else {
            $subjects = Subject::join('semester_subject as ss', 'subjects.id', '=', 'ss.subject_id')
                ->where('ss.semester_id', $student->semester)
                ->select('subjects.id', 'subjects.title')   // qualify!
                ->orderBy('subjects.title')
                ->pluck('subjects.title', 'subjects.id');
        }

        /**
         * Build routine query filtered by student's faculty/semester/batch.
         * Adjust column names here if your schema differs.
         */
        $query = ClassRoutine::with([
                'subject:id,title,code',
                'teacher:id,first_name,middle_name,last_name',
                'batch:id,title'
            ])
            ->where('faculty_id',  $student->faculty)
            ->where('semester_id', $student->semester)
            ->where('status', 1);

        if ($batch) {
            // If your column name differs (e.g., batch_id or student_batches_id), change here.
            $query->where('student_batch_id', $batch->id);
        }

        if ($subjectId) {
            // If your column is subjects_id, change to ->where('subjects_id', $subjectId)
            $query->where('subject_id', $subjectId);
        }

        // Order and fetch
        $routines = $query->orderBy('day_of_week')     // numeric 1..7 or string; change to 'day' if needed
                        ->orderBy('start_time')
                        ->get();

        // Normalize day label for grouping
        $dayMap = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'];
        foreach ($routines as $r) {
            $raw = $r->day_of_week ?? $r->day ?? null;
            $r->day_name = is_numeric($raw) ? ($dayMap[(int)$raw] ?? 'Monday') : ($raw ?: 'Monday');
        }

        // Group for view (['Monday' => [...], ...])
        $groupedByDay = $routines->groupBy('day_name');

        // Monday..Sunday display order
        $order = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

        return view(parent::loadDataToView($this->view_path.'.routine.index'), [
            'student'   => $student,
            'faculty'   => $faculty,
            'semester'  => $semester,
            'batch'     => $batch,
            'subjects'  => $subjects,
            'subjectId' => $subjectId,
            // provide BOTH for convenience
            'routines'  => $groupedByDay,
            'order'     => $order,
        ]);
    }


    public function attendance(Request $request)
    {
        $this->panel = "Attendance";

        $id       = auth()->user()->hook_id;
        $student  = Student::select('id','reg_no','first_name','middle_name','last_name','faculty','semester','reg_date')
                    ->findOrFail($id);

        // Build subject dropdown (optional)
        $subjects = [];
        if ($student->semester) {
            $sem = Semester::find($student->semester);
            if ($sem && method_exists($sem, 'subjects')) {
                $subjects = $sem->subjects()
                    ->select('subjects.id','subjects.title')
                    ->orderBy('subjects.title')
                    ->pluck('subjects.title','subjects.id');
            } else {
                $subjects = Subject::join('semester_subject as ss','subjects.id','=','ss.subject_id')
                    ->where('ss.semester_id',$student->semester)
                    ->select('subjects.id','subjects.title')
                    ->orderBy('subjects.title')
                    ->pluck('subjects.title','subjects.id');
            }
        }

        // Endpoints for the Blade
        $regularEndpoint = route('user-student.attendance.regular.json');
        $subjectEndpoint = route('user-student.attendance.subject.json');

        return view(
            parent::loadDataToView($this->view_path.'.attendance.index'),
            compact('student','subjects','regularEndpoint','subjectEndpoint')
        );
    }

    /** JSON for the table/cards: Regular (day-wise) */
    public function attendanceRegularJson(Request $request)
    {
        $studentId = auth()->user()->hook_id;
        $student   = Student::select('id','reg_no','reg_date')->findOrFail($studentId);

        [$start,$end,$period] = $this->parseRange($request, $student);

        // Pull attendance rows (polymorphic first, then fallback by reg_no)
        $rowsDb = $this->queryStudentAttendanceByMorph($student->id, $start, $end);
        if ($rowsDb->isEmpty()) {
            $reg = $this->studentPrimaryRegNo($student->id);
            if ($reg) $rowsDb = $this->queryStudentAttendanceByRegNo($reg, $start, $end);
        }

        // Status mapping + legend + colors (schema-aware)
        [$idToCode, $legend, $colors] = $this->fetchStatuses();

        $rows = [];
        $totalMinutes = 0;
        $totals = []; // keyed by code

        foreach ($rowsDb as $r) {
            $code = $idToCode[$r->attendance_status_id ?? 0] ?? '';          // try DB mapping
            if ($code === '') $code = $r->check_in_at ? 'P' : 'A';            // heuristic fallback

            $in   = $r->check_in_at  ? Carbon::parse($r->check_in_at)  : null;
            $out  = $r->check_out_at ? Carbon::parse($r->check_out_at) : null;

            $mins = 0;
            if ($in && $out && $out->greaterThan($in)) {
                $mins = $in->diffInMinutes($out);
                $totalMinutes += $mins;
            }

            $rows[] = [
                'date'   => Carbon::parse($r->date)->format('Y-m-d'),
                'status' => $code,
                'in'     => $in  ? $in->format('H:i')  : '',
                'out'    => $out ? $out->format('H:i') : '',
                'total'  => $mins ? $this->fmtHM($mins) : '',
            ];
            $totals[$code] = ($totals[$code] ?? 0) + 1;
        }

        // Attendance % (ignore H)
        $den = ($totals['P']??0) + ($totals['A']??0) + ($totals['L']??0) + ($totals['LV']??0) + ($totals['EL']??0);
        $percent = $den ? round((($totals['P']??0) / $den) * 100) : 0;

        return response()->json([
            'ok'    => true,
            'range' => ['start'=>$start->toDateString(),'end'=>$end->toDateString()],
            'period'=> $period,
            'rows'  => $rows,
            'totals'=> $totals,
            'percent' => $percent,
            'hours_total_hm' => $this->fmtHM($totalMinutes),
            'legend' => $legend,
            'colors' => $colors,
        ]);
    }

    /** JSON for the table/cards: Subject-wise */
    public function attendanceSubjectJson(Request $request)
    {
        $studentId = auth()->user()->hook_id;
        $student   = Student::select('id','reg_no','reg_date','semester')->findOrFail($studentId);

        [$start,$end,$period] = $this->parseRange($request, $student);
        $subjectId = $request->get('subject_id');

        $query = DB::table('subject_attendances as sa')
            ->select(['sa.date','sa.subject_id','sa.attendance_status_id','sa.in_at','sa.out_at'])
            ->where('sa.student_id', $student->id)
            ->whereDate('sa.date','>=',$start->toDateString())
            ->whereDate('sa.date','<=',$end->toDateString())
            ->orderBy('sa.date','asc');

        if (!empty($subjectId)) {
            $query->where('sa.subject_id', $subjectId);
        }

        $rowsDb = $query->get();

        // Subject titles
        $subjectTitles = Subject::pluck('title','id')->toArray();

        // Status mapping + legend + colors (schema-aware)
        [$idToCode, $legend, $colors] = $this->fetchStatuses();

        $rows = [];
        $totalMinutes = 0;
        $totals = [];
        foreach ($rowsDb as $r) {
            $code = $idToCode[$r->attendance_status_id ?? 0] ?? '';
            if ($code === '') $code = $r->in_at ? 'P' : 'A';

            $in  = $r->in_at  ? Carbon::parse($r->in_at)   : null;
            $out = $r->out_at ? Carbon::parse($r->out_at)  : null;

            $mins = 0;
            if ($in && $out && $out->greaterThan($in)) {
                $mins = $in->diffInMinutes($out);
                $totalMinutes += $mins;
            }

            $rows[] = [
                'date'    => Carbon::parse($r->date)->format('Y-m-d'),
                'subject' => $subjectTitles[$r->subject_id] ?? ('#'.$r->subject_id),
                'status'  => $code,
                'in'      => $in  ? $in->format('H:i')  : '',
                'out'     => $out ? $out->format('H:i') : '',
                'total'   => $mins ? $this->fmtHM($mins) : '',
            ];
            $totals[$code] = ($totals[$code] ?? 0) + 1;
        }

        $den = ($totals['P']??0) + ($totals['A']??0) + ($totals['L']??0) + ($totals['LV']??0) + ($totals['EL']??0);
        $percent = $den ? round((($totals['P']??0) / $den) * 100) : 0;

        return response()->json([
            'ok'    => true,
            'range' => ['start'=>$start->toDateString(),'end'=>$end->toDateString()],
            'period'=> $period,
            'rows'  => $rows,
            'totals'=> $totals,
            'percent' => $percent,
            'hours_total_hm' => $this->fmtHM($totalMinutes),
            'legend' => $legend,
            'colors' => $colors,
        ]);
    }

    /** OPTIONAL: Summary endpoints (if you still call them anywhere) */
    public function attendanceRegularSummary(Request $request)
    {
        try {
            $studentId = auth()->user()->hook_id;
            $period   = strtolower((string)$request->get('period','monthly'));
            $year     = (int)$request->get('year', 0);
            $month    = (int)$request->get('month', 0);
            $dateFrom = $request->get('date_from');
            $dateTo   = $request->get('date_to');

            [$start, $end] = $this->attResolveRange($period, $year, $month, $dateFrom, $dateTo);
            [$legend, $colors] = $this->attLegendAndColors();

            $rowsDb = $this->queryStudentAttendanceByMorph($studentId, $start, $end);
            if ($rowsDb->isEmpty()) {
                $reg = $this->studentPrimaryRegNo($studentId);
                if ($reg) $rowsDb = $this->queryStudentAttendanceByRegNo($reg, $start, $end);
            }

            // Map ids->codes
            [$idToCode] = $this->fetchStatuses();

            $rows = [];
            $totals = ['P'=>0,'A'=>0,'L'=>0,'EL'=>0,'LV'=>0,'H'=>0];
            $totalMins = 0;

            foreach ($rowsDb as $r) {
                $code = $idToCode[$r->attendance_status_id ?? 0] ?? ($r->check_in_at ? 'P' : 'A');

                $in  = $r->check_in_at ? Carbon::parse($r->check_in_at) : null;
                $out = $r->check_out_at ? Carbon::parse($r->check_out_at) : null;
                $mins = ($in && $out) ? $in->diffInMinutes($out) : 0;

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

            $den = ($totals['P']+$totals['A']+$totals['L']+$totals['EL']+$totals['LV']);
            $num = ($totals['P']+$totals['L']+$totals['EL']);
            $percent = $den > 0 ? round(($num*100)/$den) : 0;

            return response()->json([
                'ok'        => true,
                'range'     => ['start'=>$start->toDateString(),'end'=>$end->toDateString()],
                'legend'    => $legend,
                'colors'    => $colors,
                'totals'    => $totals,
                'percent'   => $percent,
                'total_mins'=> $totalMins,
                'total_hm'  => $this->fmtHM($totalMins),
                'rows'      => $rows,
            ]);

        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'msg'=>'Failed to load regular attendance.']);
        }
    }

    public function attendanceSubjectSummary(Request $request)
    {
        try {
            $studentId = auth()->user()->hook_id;

            $period   = strtolower((string)$request->get('period','monthly'));
            $year     = (int)$request->get('year', 0);
            $month    = (int)$request->get('month', 0);
            $dateFrom = $request->get('date_from');
            $dateTo   = $request->get('date_to');
            $subjectId= $request->get('subject_id');

            [$start, $end] = $this->attResolveRange($period, $year, $month, $dateFrom, $dateTo);
            [$legend, $colors] = $this->attLegendAndColors();

            [$idToCode] = $this->fetchStatuses();

            $q = DB::table('subject_attendances as sa')
                ->select([
                    'sa.id','sa.date','sa.student_id','sa.subject_id',
                    'sa.attendance_status_id','sa.in_at','sa.out_at',
                    'sub.code as subject_code','sub.title as subject_title'
                ])
                ->leftJoin('subjects as sub','sub.id','=','sa.subject_id')
                ->where('sa.student_id',$studentId)
                ->whereDate('sa.date','>=',$start->toDateString())
                ->whereDate('sa.date','<=',$end->toDateString())
                ->orderBy('sa.date','asc');

            if (!empty($subjectId) && (int)$subjectId > 0) {
                $q->where('sa.subject_id',(int)$subjectId);
            }

            $rowsDb = $q->get();

            $rows = [];
            $totals = ['P'=>0,'A'=>0,'L'=>0,'EL'=>0,'LV'=>0,'H'=>0];
            $totalMins = 0;

            foreach ($rowsDb as $r) {
                $code = $idToCode[$r->attendance_status_id ?? 0] ?? ($r->in_at ? 'P' : 'A');

                $in  = $r->in_at ? Carbon::parse($r->in_at) : null;
                $out = $r->out_at ? Carbon::parse($r->out_at) : null;
                $mins = ($in && $out) ? $in->diffInMinutes($out) : 0;

                $totalMins += $mins;
                if (isset($totals[$code])) $totals[$code]++;

                $rows[] = [
                    'date'   => Carbon::parse($r->date)->toDateString(),
                    'subject'=> [
                        'id'    => $r->subject_id,
                        'code'  => $r->subject_code,
                        'title' => $r->subject_title,
                    ],
                    'status' => $code,
                    'in'     => $in ? $in->format('H:i') : '',
                    'out'    => $out ? $out->format('H:i') : '',
                    'mins'   => $mins,
                    'total'  => $this->fmtHM($mins),
                ];
            }

            // Per-subject aggregates
            $subjectsAgg = collect($rows)->groupBy(fn($r)=>$r['subject']['id'])->map(function($items){
                $t=['P'=>0,'A'=>0,'L'=>0,'EL'=>0,'LV'=>0,'H'=>0];
                $mins=0;
                foreach($items as $r){ $mins += $r['mins']; if(isset($t[$r['status']])) $t[$r['status']]++; }
                $den = ($t['P']+$t['A']+$t['L']+$t['EL']+$t['LV']);
                $num = ($t['P']+$t['L']+$t['EL']);
                $pct = $den>0 ? round(($num*100)/$den) : 0;
                $head=$items->first()['subject'];
                return [
                    'id'=>$head['id'],'code'=>$head['code'],'title'=>$head['title'],
                    'totals'=>$t,'percent'=>$pct,'total_mins'=>$mins,'total_hm'=>$this->fmtHM($mins),
                ];
            })->values();

            // Overall %
            $den = ($totals['P']+$totals['A']+$totals['L']+$totals['EL']+$totals['LV']);
            $num = ($totals['P']+$totals['L']+$totals['EL']);
            $percent = $den > 0 ? round(($num*100)/$den) : 0;

            return response()->json([
                'ok'        => true,
                'range'     => ['start'=>$start->toDateString(),'end'=>$end->toDateString()],
                'legend'    => $legend,
                'colors'    => $colors,
                'totals'    => $totals,
                'percent'   => $percent,
                'total_mins'=> $totalMins,
                'total_hm'  => $this->fmtHM($totalMins),
                'rows'      => $rows,
                'subjects'  => $subjectsAgg,
            ]);

        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'msg'=>'Failed to load subject-wise attendance.']);
        }
    }

    /* ----------------- Helpers ----------------- */

    private function parseRange(Request $req, Student $student): array
    {
        $period = $req->get('period','monthly');
        $today  = Carbon::today(config('app.timezone'));
        $start  = $today->copy()->startOfMonth();
        $end    = $today->copy()->endOfMonth();

        if ($period === 'yearly') {
            $year  = (int)($req->get('year', $today->year));
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end   = Carbon::create($year,12,31)->endOfDay();
        } elseif ($period === 'custom') {
            $df = $req->get('date_from');
            $dt = $req->get('date_to');
            $start = $df ? Carbon::parse($df)->startOfDay() : $today->copy()->startOfMonth();
            $end   = $dt ? Carbon::parse($dt)->endOfDay()   : $today->copy()->endOfMonth();
            if ($end->lt($start)) [$start,$end] = [$end,$start];
        } elseif ($period === 'lifetime') {
            // earliest based on data; fallback to reg_date
            $minDate = DB::table('attendances')
                ->whereIn('attendable_type', $this->studentMorphTypes())
                ->where('attendable_id', $student->id)
                ->min('date');
            if ($minDate) $start = Carbon::parse($minDate)->startOfDay();
            else $start = $student->reg_date ? Carbon::parse($student->reg_date)->startOfDay() : $today->copy()->subYears(1)->startOfDay();
            $end = $today->copy()->endOfDay();
        } else { // monthly
            $year  = (int)($req->get('year', $today->year));
            $month = (int)($req->get('month', $today->month));
            $start = Carbon::create($year,$month,1)->startOfDay();
            $end   = Carbon::create($year,$month,1)->endOfMonth()->endOfDay();
        }

        return [$start,$end,$period];
    }

    /** Build colors for status codes */
    private function statusPalette(): array
    {
        return [
            'P'  => '#16a34a',
            'A'  => '#ef4444',
            'L'  => '#f59e0b',
            'LV' => '#0ea5e9',
            'EL' => '#6366f1',
            'H'  => '#64748b',
            'OT' => '#334155',
        ];
    }

    /** Map arbitrary label -> compact code (P/A/L/...) */
    private function codeFromTitle(string $title): string
    {
        $t = trim(Str::lower($title));
        if ($t==='p' || $t==='present') return 'P';
        if ($t==='a' || $t==='absent')  return 'A';
        if ($t==='l' || $t==='late')    return 'L';
        if ($t==='lv' || $t==='leave')  return 'LV';
        if ($t==='el' || str_contains($t,'excuse')) return 'EL';
        if ($t==='h' || $t==='holiday') return 'H';

        // fallback initials (max 3)
        $abbr = strtoupper(collect(preg_split('/\s+/', $title))->map(fn($p)=>Str::substr($p,0,1))->implode(''));
        return $abbr ?: 'OT';
    }

    /**
     * Schema-aware fetch of statuses.
     * Returns: [ id->code map, legend[], colorsMap ]
     */
    private function fetchStatuses(): array
    {
        $palette = $this->statusPalette();

        if (!Schema::hasTable('attendance_statuses')) {
            // default legend if table missing
            $legend  = [];
            foreach (['P','A','L','LV','EL','H'] as $c) {
                $legend[] = ['code'=>$c,'label'=>$c,'color'=>$palette[$c]];
            }
            return [[], $legend, array_column($legend,'color','code')];
        }

        $cols = Schema::getColumnListing('attendance_statuses');

        $idCol    = in_array('id',$cols) ? 'id' : (in_array('status_id',$cols) ? 'status_id' : null);
        $codeCol  = collect(['code','short_code','symbol','abbr'])->first(fn($c)=>in_array($c,$cols));
        $titleCol = collect(['title','name','label','status_title','display_name','remark'])->first(fn($c)=>in_array($c,$cols));
        $colorCol = in_array('color',$cols) ? 'color' : null;
        $orderCol = in_array('order',$cols) ? 'order' : null;

        $select = [];
        if ($idCol)    $select[] = $idCol.' as id';
        if ($codeCol)  $select[] = $codeCol.' as code';
        if ($titleCol) $select[] = $titleCol.' as title';
        if ($colorCol) $select[] = $colorCol.' as color';
        if ($orderCol) $select[] = $orderCol.' as ord';

        $rows = DB::table('attendance_statuses')
            ->select($select ?: ['*'])
            ->when($orderCol, fn($q)=>$q->orderBy($orderCol))
            ->get();

        $idToCode = [];
        $legendMap = [];
        foreach ($rows as $r) {
            $code  = isset($r->code)  ? strtoupper(trim((string)$r->code)) : '';
            $title = isset($r->title) ? (string)$r->title : '';
            $norm  = $code !== '' ? $code : $this->codeFromTitle($title ?: (string)($r->id ?? ''));

            $id = $r->id ?? null;
            if ($id !== null) $idToCode[$id] = $norm;

            $color = isset($r->color) && $r->color ? $r->color : ($palette[$norm] ?? $palette['OT']);
            $legendMap[$norm] = [
                'code'  => $norm,
                'label' => $title ?: $norm,
                'color' => $color,
            ];
        }

        if (empty($legendMap)) {
            foreach (['P','A','L','LV','EL','H'] as $c) {
                $legendMap[$c] = ['code'=>$c,'label'=>$c,'color'=>$palette[$c]];
            }
        }

        $legend = array_values($legendMap);
        $colors = [];
        foreach ($legend as $l) $colors[$l['code']] = $l['color'];

        return [$idToCode, $legend, $colors];
    }

    /** Legend+colors convenience (wraps fetchStatuses) */
    private function attLegendAndColors(): array
    {
        [, $legend, $colors] = $this->fetchStatuses();
        return [$legend, $colors];
    }

    private function normalizeStatus($v){
        $t=strtoupper(trim((string)$v));
        if ($t==='P'||str_contains($t,'PRESENT')) return 'P';
        if ($t==='A'||str_contains($t,'ABSENT'))  return 'A';
        if ($t==='L'||str_contains($t,'LATE'))    return 'L';
        if ($t==='EL'||str_contains($t,'EXCUSE')) return 'EL';
        if ($t==='LV'||str_contains($t,'LEAVE'))  return 'LV';
        if ($t==='H'||str_contains($t,'HOLIDAY')) return 'H';
        return '';
    }

    /** Minutes -> "Xh Ym" */
    private function hmFromMinutes(int $minutes): string
    {
        return $this->fmtHM($minutes);
    }

    private function fmtHM($mins): string
    {
        $m = max(0,(int)round($mins));
        $h = intdiv($m,60); $r=$m%60;
        return $h.'h '.str_pad((string)$r,2,'0',STR_PAD_LEFT).'m';
    }

    /* --- tiny helpers used by queries --- */
    private function firstExistingColumn(string $table, array $cands){
        foreach ($cands as $c) if (Schema::hasColumn($table,$c)) return $c;
        return null;
    }
    private function attResolveRange(string $period, int $year, int $month, $from, $to): array{
        $now = Carbon::now();
        switch ($period) {
            case 'yearly':
                $y = $year ?: (int)date('Y');
                return [Carbon::create($y,1,1)->startOfDay(), Carbon::create($y,12,31)->endOfDay()];
            case 'custom':
                $s = $from ? Carbon::parse($from)->startOfDay() : $now->copy()->startOfMonth();
                $e = $to   ? Carbon::parse($to)->endOfDay()   : $now->copy()->endOfMonth()->endOfDay();
                if ($e->lt($s)) [$s,$e]=[$e,$s];
                return [$s,$e];
            case 'lifetime':
                return [Carbon::create(2000,1,1)->startOfDay(), $now->endOfDay()];
            case 'monthly':
            default:
                $y = $year ?: (int)date('Y');
                $m = $month ?: (int)date('n');
                $s = Carbon::create($y,$m,1)->startOfDay();
                return [$s, $s->copy()->endOfMonth()->endOfDay()];
        }
    }
    private function studentMorphTypes(): array{
        $c=['App\\Models\\Student','App\\Student','student','Student'];
        if (class_exists('App\\Models\\Student')) array_unshift($c,'App\\Models\\Student');
        elseif (class_exists('App\\Student'))     array_unshift($c,'App\\Student');
        return array_values(array_unique($c));
    }
    private function studentPrimaryRegNo(int $studentId): ?string{
        $tab = Schema::hasTable('students') ? 'students' : (Schema::hasTable('users') ? 'users' : null);
        if (!$tab) return null;
        $idCol = Schema::hasColumn($tab,'id') ? 'id' : null; if(!$idCol) return null;
        $regCol = $this->firstExistingColumn($tab, ['reg_no','registration_no','university_reg','code']);
        if (!$regCol) return null;
        $row = DB::table($tab)->select("$regCol as reg")->where($idCol,$studentId)->first();
        return $row ? ($row->reg ?? null) : null;
    }
    private function queryStudentAttendanceByMorph(int $studentId, Carbon $start, Carbon $end)
    {
        return DB::table('attendances as a')
            ->select(['a.date','a.check_in_at','a.check_out_at','a.attendance_status_id'])
            ->whereIn('a.attendable_type', $this->studentMorphTypes())
            ->where('a.attendable_id', $studentId)
            ->whereDate('a.date','>=',$start->toDateString())
            ->whereDate('a.date','<=',$end->toDateString())
            ->orderBy('a.date','asc')
            ->get();
    }
    private function queryStudentAttendanceByRegNo(string $reg, Carbon $start, Carbon $end)
    {
        return DB::table('attendances as a')
            ->select(['a.date','a.check_in_at','a.check_out_at','a.attendance_status_id'])
            ->where('a.reg_no', $reg)
            ->whereDate('a.date','>=',$start->toDateString())
            ->whereDate('a.date','<=',$end->toDateString())
            ->orderBy('a.date','asc')
            ->get();
    }

}
