<?php

/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\Student;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Student\PublicRegistration\AddValidation;
use App\Models\AcademicInfo;
use App\Models\Addressinfo;
use App\Models\AlertSetting;

use App\Models\Annexure;
use App\Models\Faculty;
use App\Models\FacultySemester;
use App\Models\GeneralSetting;
use App\Models\GuardianDetail;
use App\Models\OnlineRegistrationProgram;
use App\Models\OnlineRegistrationSetting;
use App\Models\ParentDetail;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAddressinfo;
use App\Models\StudentBatch;
use App\Models\StudentGuardian;
use App\Models\StudentParent;
use App\Models\StudentStatus;

use App\Models\Year;
use App\Models\Role;
use App\Traits\SmsEmailScope;
use App\Traits\UserScope;
use App\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Image, URL;
use ViewHelper;

class OnlineRegistrationControllerBackup extends CollegeBaseController
{
    protected $base_route = 'student.online-registration';
    protected $view_path = 'student.online-registration';
    protected $panel = 'Student';
    protected $folder_path;
    protected $folder_name = 'studentProfile';
    protected $filter_query = [];

    use SmsEmailScope;
    use UserScope;

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function registration()
    {
        if($this->checkRegistrationStatus()){
            $data = [];
            $data['blank_ins'] = new Student();

            //check Registration settions
            $data['registration_setting'] = OnlineRegistrationSetting::first();

            $existingProgram = OnlineRegistrationProgram::select('online_registration_programs.id','online_registration_programs.faculties_id',
                'online_registration_programs.start_date', 'online_registration_programs.end_date',
                'online_registration_programs.status','f.faculty','f.id as fac_id')
                ->where('online_registration_programs.start_date', '<=', Carbon::now())
                ->where('online_registration_programs.end_date', '>=', Carbon::now())
                ->join('faculties as f','f.id','=','online_registration_programs.faculties_id')
                ->get();

            if(isset($existingProgram) && $existingProgram->count() > 0){
                $facultyExceptId = $existingProgram->pluck('fac_id');
                $allFaculty = Faculty::whereIn('id',$facultyExceptId)->Active()->orderBy('faculty')->pluck('faculty','id')->toArray();
                $data['faculties'] = array_prepend($allFaculty,'Select Faculty/Program/Class','');
            }else{
                $data['faculties'] = [];
            }

            $studentBatch = StudentBatch::select('id', 'title')->where('active_status',1)->Active()->pluck('title','id')->toArray();
            $data['batch'] = $studentBatch;
            $data['state'] = $this->activeState();
            $data['annexures'] = Annexure::select('id', 'title','default')->Active()->get();

            return view(parent::loadDataToView($this->view_path.'.register'), compact('data'));
        }else{
            request()->session()->flash($this->message_warning, 'Public Registration Closed.');
            return redirect()->route('login');
        }
    }

    public function register(AddValidation $request)
    {
        //RegNo Generator Start
            $oldStudent = Student::where('faculty',$request->faculty)->orderBy('id', 'desc')->first();
            if (!$oldStudent){
                $sn = 1;
            }else{
                $oldReg = intval(substr($oldStudent->reg_no,-4));
                $sn = $oldReg + 1;
            }

            $sn = substr("00000{$sn}", - 4);
            $year = intval(substr(Year::where('active_status','=',1)->first()->title,-2));
            $faculty = Faculty::find(intval($request->faculty));
            $facultyCode = $faculty->faculty_code;
            //$regNum = $faculty.'-'.$year.'-'.$sn;
            $regNum = $facultyCode.$year.$sn;
            $request->request->add(['reg_no' => $regNum]);
            //reg generator End


        $year = Year::where('active_status','=',1)->first()->title;
        //$regNum = $year.$request->faculty.$oldStudent->id;
        $request->request->add(['created_by' => 0]);
        //$request->request->add(['reg_no' => $regNum]);
        //$request->request->add(['semester' => $semSec?$semSec:0]);
        $request->request->add(['academic_status' => 8]);
        $request->request->add(['status' => 'in-active']);


        if ($request->hasFile('student_main_image')){
            $student_image = $request->file('student_main_image');
            $student_image_name = $request->reg_no.'.'.$student_image->getClientOriginalExtension();
            $student_image->move(public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR, $student_image_name);
        }else{
            $student_image_name = "";
        }

        if ($request->hasFile('student_signature_main_image')){
            $student_signature_image = $request->file('student_signature_main_image');
            $student_signature_image_name = $request->reg_no.'_signature.'.$student_signature_image->getClientOriginalExtension();
            $student_signature_image->move(public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR, $student_signature_image_name);
        }else{
            $student_signature_image_name = "";
        }

        $request->request->add(['student_image' => $student_image_name]);
        $request->request->add(['student_signature' => $student_signature_image_name]);

        $student = Student::create($request->all());

        $parential_image_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR;

        if ($request->hasFile('father_main_image')){
            $father_image = $request->file('father_main_image');
            $father_image_name = $student->reg_no.'_father.'.$father_image->getClientOriginalExtension();
            $father_image->move($parential_image_path, $father_image_name);
        }else{
            $father_image_name = "";
        }

        if ($request->hasFile('mother_main_image')){
            $mother_image = $request->file('mother_main_image');
            $mother_image_name = $student->reg_no.'_mother.'.$mother_image->getClientOriginalExtension();
            $mother_image->move($parential_image_path, $mother_image_name);
        }else{
            $mother_image_name = "";
        }

        if ($request->hasFile('guardian_main_image')){
            $guardian_image = $request->file('guardian_main_image');
            $guardian_image_name = $student->reg_no.'_guardian.'.$guardian_image->getClientOriginalExtension();
            $guardian_image->move($parential_image_path, $guardian_image_name);
        }else{
            $guardian_image_name = "";
        }

        $request->request->add(['father_image' => $father_image_name]);
        $request->request->add(['mother_image' => $mother_image_name]);
        $request->request->add(['guardian_image' => $guardian_image_name]);

        $request->request->add(['students_id' => $student->id]);
        $addressinfo = Addressinfo::create($request->all());
        $parentdetail = ParentDetail::create($request->all());

        $guardian = GuardianDetail::create($request->all());
        $studentGuardian = StudentGuardian::create([
            'students_id' => $student->id,
            'guardians_id' => $guardian->id,
        ]);

        //create login access
        $name = isset($request->middle_name)?$request->first_name.' '.$request->middle_name.' '.$request->last_name:$request->first_name.' '.$request->last_name;
        $rolesId = Role::where('name','student')->first()->id;

        $request->request->add(['role_id' => $rolesId]);
        $request->request->add(['hook_id' => $student->id]);
        $request->request->add(['name' => $name]);
        $password = str_random(8);
        $request->request->add(['password' => Hash::make($password)]);
        $request->request->add(['status' => 'active']);

        $user = User::create($request->all());
        $roles = [];
        $roles[] = [
            'user_id' => $user->id,
            'role_id' => $rolesId
        ];

        $user->userRole()->sync($roles);

        $PublishMessage = 'Dear, ' . $name.' Thank you for register with us. Your registration detail and login password sent on your registered mobile & mail.' ;

        /*SMS & Email Alert*/
        $alert = AlertSetting::select('sms','email','subject','template')->where('event','=','StudentRegistration')->first();
        if(!$alert){

        }else{
            //Dear {{first_name}}, you are successfully registered in our institution with RegNo.{{reg_no}}. Your login with password: {{password}} ,Thank You.
            $subject = $alert->subject;
            $message = $alert->template;
            $message = str_replace('{{first_name}}', $student->first_name, $message );
            $message = str_replace('{{reg_no}}', $student->reg_no, $message );
            $message = str_replace('{{password}}', $password, $message );
            $emailIds[] = $student->email;
            $contactNumbers[] = $addressinfo->mobile_1;

            /*Now Send SMS On First Mobile Number*/
            if($alert->sms == 1){
                $contactNumbers = $this->contactFilter($contactNumbers);
                $smssuccess = $this->sendSMS($contactNumbers,$message);
            }

            /*Now Send Email With Subject*/
            if($alert->email == 1){
                $emailIds = $this->emailFilter($emailIds);
                /*sending email*/
                $emailSuccess = $this->sendEmail($emailIds, $subject, $message);
            }
        }

        //end sms email
        $request->session()->flash($this->message_success, $PublishMessage);
        return redirect()->route('login');
    }

    public function checkRegistrationStatus()
    {
        $data['general_setting'] = GeneralSetting::select('public_registration')->first();

        if(isset($data['general_setting']) && $data['general_setting']->public_registration ==1){
            return true;
        }else{
            return false;
        }
    }

    public function findSubject(Request $request)
    {
        $semester = Semester::where('id',$request->get('semester_id'))->first();
        /*Find Subject Title with associated Ids*/
        $collectSubject = $semester->subjects()->select('subjects.id as subject_id','subjects.title as subject_title')->orderby('subjects.title')->get();
        $subjects = $collectSubject;

        $numOfSubject = $semester->major_subject_count;

        if ($subjects && isset($numOfSubject)) {
            $response['subjects'] = view($this->view_path.'.includes.forms.fetch-subjects', [
                'subjects' => $subjects,
                'numOfSubject' => isset($numOfSubject)?$numOfSubject:0
            ])->render();

            $response['success'] = 'Please, Select Maximum '.$numOfSubject.' Subjects in Subject List.';
        }else {
            $response['error'] = 'No Any Subject Found. Please Contact Your Administrator.';
        }

        return response()->json(json_encode($response));
    }

    public function findRegistration(Request $request)
    {
        $data = [];

        if($request->all()) {
            $searchParameter = [
                "faculty" =>    $request->faculty,
                "semester" =>   $request->semester,
                "batch" =>  $request->batch,
                //"reg_no" => $request->reg_no,
                "national_id_1" => $request->national_id_1,
                "date_of_birth" =>  $request->date_of_birth,
            ];

            $data['student'] = Student::select('id', 'national_id_1','reg_no', 'reg_date','reg_fee','admission_course_fee as balance',
                'faculty', 'semester', 'batch', 'first_name', 'middle_name', 'last_name','admission_course_fee')
                ->where($searchParameter)
                ->first();


            if(isset($data['student']) && $data['student']->count() > 0){

            }else{
                request()->session()->flash($this->message_warning, "Not Match ! Registration with your detail not match. Please, check and find again.");
            }
        }

        $data['faculties'] = $this->activeFaculties();
        $data['batch'] = $this->activeBatch();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.find-registration'), compact('data'));


    }

    public function print($id)
    {
        $id = decrypt($id);
        $data = [];
        $data['student'] = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
            'students.faculty','students.semester','students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group',  'students.religion', 'students.caste',
            'students.nationality', 'students.national_id_1', 'students.national_id_2', 'students.national_id_3','students.national_id_4',
            'students.mother_tongue', 'students.email', 'students.extra_info', 'students.status', 'pd.grandfather_first_name',
            'pd.grandfather_middle_name', 'pd.grandfather_last_name', 'pd.father_first_name', 'pd.father_middle_name',
            'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_office', 'pd.father_office_number',
            'pd.father_residence_number', 'pd.father_mobile_1', 'pd.father_mobile_2', 'pd.father_email', 'pd.mother_first_name',
            'pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation', 'pd.mother_office',
            'pd.mother_office_number', 'pd.mother_residence_number', 'pd.mother_mobile_1', 'pd.mother_mobile_2', 'pd.mother_email',
            'ai.address', 'ai.state', 'ai.country','ai.postal_code', 'ai.temp_address', 'ai.temp_state', 'ai.temp_country','ai.temp_postal_code', 'ai.home_phone',
            'ai.mobile_1', 'ai.mobile_2', 'gd.id as guardian_id', 'gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
            'gd.guardian_eligibility', 'gd.guardian_occupation', 'gd.guardian_office', 'gd.guardian_office_number', 'gd.guardian_residence_number',
            'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email', 'gd.guardian_relation', 'gd.guardian_address',
            'students.student_image','students.student_signature', 'pd.father_image', 'pd.mother_image', 'gd.guardian_image',

            'students.reg_fee','students.sbi_collect_no','students.bank_ref_no','students.payment_date','students.university_enrollment_no',
            'students.admission_date','students.admission_no', 'students.admission_course_fee',

//            'students.special_category','students.weightage_claim','students.national_id_1','students.national_id_2',
        )
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

        $data['academicInfos'] = $data['student']->academicInfo()->orderBy('sorting_order','asc')->get();
        $data['appliedSubjects'] = $data['student']->majorSubject()->get();
        $data['annexure'] = $data['student']->annexure()->get();
        $data['url'] = URL::current();
        return view(parent::loadDataToView('print.student.registration'), compact('data'));
    }

    public function findSemester(Request $request)
    {
        $response = [];
        $response['error'] = true;

        $existingProgram = OnlineRegistrationProgram::select('online_registration_programs.id',
            'online_registration_programs.faculties_id','online_registration_programs.semesters_id',
            'online_registration_programs.start_date', 'online_registration_programs.end_date',
            'online_registration_programs.status','f.faculty','s.semester')
            ->where('online_registration_programs.start_date', '<=', Carbon::now())
            ->where('online_registration_programs.end_date', '>=', Carbon::now())
            ->join('faculties as f','f.id','=','online_registration_programs.faculties_id')
            ->join('semesters as s','s.id','=','online_registration_programs.semesters_id')
            ->get();

        $semesterExceptId = $existingProgram->pluck('semesters_id');

        if ($request->has('faculty_id')) {
            $faculty = Faculty::find($request->get('faculty_id'));
            if ($faculty) {
                $response['semester'] = $faculty->semester()->whereIn('semesters.id',$semesterExceptId)->select('semesters.id', 'semesters.semester', 'semesters.slug')->get();

                $response['error'] = false;
                $response['success'] = 'Semester/Sec. Available For This Faculty/Program/Class.';
            } else {
                $response['error'] = 'No Any Semester Assign on This Faculty/Program/Class.';
            }
        } else {
            $response['message'] = 'Invalid request!!';
        }

        return response()->json(json_encode($response));
    }

}
