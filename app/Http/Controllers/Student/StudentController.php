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

use App\Exports\StudentsExport;
use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Student\Registration\AddValidation;
use App\Http\Requests\Student\Registration\EditValidation;
use App\Jobs\AllEmail;
use App\Models\AcademicInfo;
use App\Models\Addressinfo;
use App\Models\AlertSetting;
use App\Models\Annexure;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\BookIssue;
use App\Models\CertificateTemplate;
use App\Models\CharacterCertificate;
use App\Models\Document;
use App\Models\ExamMarkLedger;
use App\Models\ExamSchedule;
use App\Models\Faculty;
use App\Models\FormSetting;
use App\Models\GuardianDetail;
use App\Models\LibraryMember;
use App\Models\Note;
use App\Models\OnlineRegistrationProgram;
use App\Models\ParentDetail;
use App\Models\ResidentHistory;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAddressinfo;
use App\Models\StudentAnnexure;
use App\Models\StudentDegree;
use App\Models\StudentExtraInfo;
use App\Models\StudentGuardian;
use App\Models\StudentParent;
use App\Models\StudentPlacement;
use App\Models\StudentScholarship;
use App\Models\StudentStatus;
use App\Models\StudentSubject;
use App\Models\SubjectAttendance;
use App\Models\TransportHistory;
use App\Models\Year;
use App\Models\Role;
use App\Traits\AcademicScope;
use App\Traits\CertificateScope;
use App\Traits\LibraryScope;
use App\Traits\SmsEmailScope;
use App\Traits\UserScope;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Image, URL;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade as PDF;
use ViewHelper;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Schema;

class StudentController extends CollegeBaseController
{
    protected $base_route = 'student';
    protected $view_path = 'student';
    protected $panel;
    protected $folder_path;
    protected $folder_name = 'studentProfile';
    protected $filter_query = [];

    use SmsEmailScope;
    use UserScope;
    use LibraryScope;
    use AcademicScope;
    use CertificateScope;

    public function __construct()
    {
        $this->panel = __('student_staff.child.student.name');
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    protected function normalizeAcademicBoard($value)
    {
        return strtoupper(preg_replace('/\s+/', '', trim((string) $value)));
    }

    protected function academicInfoCompletenessScore($academicInfo)
    {
        $score = 0;
        $fields = [
            'institution',
            'roll_no',
            'pass_year',
            'major_subjects',
            'mark_obtained',
            'maximum_mark',
            'percentage',
            'grade_point',
            'grade_letter',
        ];

        foreach ($fields as $field) {
            $value = $academicInfo->{$field} ?? null;
            if ($value !== null && trim((string) $value) !== '' && trim((string) $value) !== '0') {
                $score++;
            }
        }

        return $score;
    }

    protected function mergeAcademicInfoRows($primary, $secondary)
    {
        $fields = [
            'institution',
            'roll_no',
            'pass_year',
            'major_subjects',
            'mark_obtained',
            'maximum_mark',
            'percentage',
            'grade_point',
            'grade_letter',
        ];

        $updates = [];

        foreach ($fields as $field) {
            $primaryValue = $primary->{$field} ?? null;
            $secondaryValue = $secondary->{$field} ?? null;

            if (($primaryValue === null || trim((string) $primaryValue) === '' || trim((string) $primaryValue) === '0')
                && $secondaryValue !== null
                && trim((string) $secondaryValue) !== '') {
                $updates[$field] = $secondaryValue;
            }
        }

        if (!empty($updates)) {
            $primary->update($updates);
            foreach ($updates as $field => $value) {
                $primary->{$field} = $value;
            }
        }
    }

    protected function deduplicateAcademicInfoRows(Student $student)
    {
        $academicInfos = $student->academicInfo()->orderBy('sorting_order', 'asc')->orderBy('id', 'asc')->get();

        if ($academicInfos->count() <= 1) {
            return;
        }

        $grouped = $academicInfos->groupBy(function ($academicInfo) {
            return $this->normalizeAcademicBoard($academicInfo->board);
        });

        foreach ($grouped as $normalizedBoard => $rows) {
            if ($normalizedBoard === '' || $rows->count() <= 1) {
                continue;
            }

            $sortedRows = $rows->sort(function ($left, $right) {
                $leftScore = $this->academicInfoCompletenessScore($left);
                $rightScore = $this->academicInfoCompletenessScore($right);

                if ($leftScore === $rightScore) {
                    return $left->id <=> $right->id;
                }

                return $rightScore <=> $leftScore;
            })->values();

            $primary = $sortedRows->shift();

            foreach ($sortedRows as $duplicate) {
                $this->mergeAcademicInfoRows($primary, $duplicate);
                $duplicate->delete();
            }
        }
    }

    public function index(Request $request)
    {
        $data = [];
        if($request->all()) {
            $data['student'] = Student::select('students.id', 'students.reg_no', 'students.reg_date',
                'students.faculty', 'students.semester', 'students.batch', 'students.academic_status',
                'students.first_name', 'students.middle_name', 'students.last_name', 'students.status',
                'f.faculty','s.semester','ss.title as academic_status')
                ->where(function ($query) use ($request) {
                    $this->commonStudentFilterCondition($query, $request);
                    //fast_finder
                    if ($request->has('fast_finder') && $request->get('fast_finder') !=null) {
                        $query->where('students.reg_no', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.university_reg', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.first_name', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.middle_name', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.last_name', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.gender', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.blood_group', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.nationality', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.national_id_1', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.national_id_2', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.national_id_3', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.national_id_4', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.religion', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.caste', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.mother_tongue', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('students.email', 'like', '%' . $request->fast_finder . '%')
                                ->orWhere('ai.mobile_1', 'like' , '%'.$request->fast_finder.'%')
                                ->orWhere('ai.mobile_2', 'like' , '%'.$request->fast_finder.'%');
                    }
                })
                ->join('addressinfos as ai','ai.students_id','=','students.id')
                ->join('faculties as f','f.id','=','students.faculty')
                ->join('semesters as s','s.id','=','students.semester')
                ->join('student_statuses as ss','ss.id','=','students.academic_status')
                //->get();
                ->paginate(env('PAGINATION_LIMIT',$this->pagination_limit));
        }else{
            $data['student'] = Student::select('students.id', 'students.reg_no', 'students.faculty', 'students.semester',
                'students.academic_status', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.status',
                'f.faculty','s.semester','ss.title as academic_status')
                ->orderBy('students.id', 'desc')
                ->join('faculties as f','f.id','=','students.faculty')
                ->join('semesters as s','s.id','=','students.semester')
                ->join('student_statuses as ss','ss.id','=','students.academic_status')
                ->limit($this->defaultDataFetch)
                //->get();
                ->paginate(env('PAGINATION_LIMIT',$this->pagination_limit));

        }

        $data['faculties'] = $this->activeFaculties();
        $data['batch'] = $this->activeBatch();
        $data['academic_status'] = $this->activeStudentAcademicStatus();

        $data['certificate-template'] =  $this->activeCertificateTemplates();

        //for Quick services Creation
        /*Hostel List*/
        $data['hostels'] = $this->activeHostel();
        /*Transport Route List*/
        $data['routes'] = $this->activeTransportRoutes();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function registration()
    {
        $data = [];
        $data['blank_ins'] = new Student();

        $data['faculties'] = $this->activeFaculties();
        $data['semester'] = $this->activeSemester();
        $data['batch'] = $this->activeBatch();
        $data['academic_status'] = $this->activeStudentAcademicStatus();
        $data['state'] = $this->activeState();
        $data['scholarship'] = $this->activeScholarship();
        $data['placement'] = $this->activePlacement();
        $data['degrees'] = $this->activeDegrees();
        $data['annexures'] = Annexure::select('id', 'title')->Active()->get();

        return view(parent::loadDataToView($this->view_path.'.registration.register'), compact('data'));
    }

    public function register(AddValidation $request)
    {
        if(!isset($request->reg_no)){
            //RegNo Generator Start
            $oldStudent = Student::where('faculty',$request->faculty)->orderBy('id', 'DESC')->first();
            if (!$oldStudent){
                $sn = 1;
            }else{
                $oldReg = intval(substr($oldStudent->reg_no,-4));
                $sn = $oldReg + 1;
            }

            $sn = substr("00000{$sn}", -4);
            $year = intval(substr(Year::where('active_status','=',1)->first()->title,-2));
            $faculty = Faculty::find(intval($request->faculty));
            $facultyCode = $faculty->faculty_code;
            //$regNum = $faculty.'-'.$year.'-'.$sn;
            $regNum = $facultyCode.$year.$sn;
            //reg generator End
            $request->request->add(['reg_no' => $regNum]);
        }else{
            $request->request->add(['reg_no' => $request->reg_no]);
        }

        if ($request->hasFile('student_main_image')){
            $student_image_name = parent::uploadAttendanceProfileImage(
                $request,
                'student_main_image',
                public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR,
                $request->reg_no
            );
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

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['semester' => $request->get('semester')]);
        $request->request->add(['student_image' => $student_image_name]);
        $request->request->add(['student_signature' => $student_signature_image_name]);

        $student = Student::create($request->all());
        //if student created then store related data
        if($student){

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

            if($request->guardian_link_id == null){
            $guardian = GuardianDetail::create($request->all());
            $studentGuardian = StudentGuardian::create([
                'students_id' => $student->id,
                'guardians_id' => $guardian->id,
            ]);
        }else{
            $studentGuardian = StudentGuardian::create([
                'students_id' => $student->id,
                'guardians_id' => $request->guardian_link_id,
            ]);
        }

            //Major Subjects
            if ($request->has('subject')) {
                foreach ($request->get('subject') as $key => $subject) {
                    StudentSubject::create([
                        'students_id' => $student->id,
                        'subjects_id' => $subject,
                        'created_by' => auth()->user()->id,
                    ]);
                }
            }

            // Annexure List
            if ($request->has('annexure')) {
                foreach ($request->get('annexure') as $key => $annexure) {
                    StudentAnnexure::create([
                        'students_id' => $student->id,
                        'annexures_id' => $annexure,
                        'created_by' => auth()->user()->id,
                    ]);
                }
            }

            // Academic Info
            if ($request->has('board')) {
                foreach ($request->get('board') as $key => $board) {
                    AcademicInfo::create([
                        'students_id' => $student->id,
                        'institution' => $request->get('institution')[$key],
                        'board' => $board,
                        'pass_year' => $request->get('pass_year')[$key],
                        'roll_no' => $request->get('roll_no')[$key],
                        'major_subjects' => $request->get('major_subjects')[$key],
                        'mark_obtained' => $request->get('mark_obtained')[$key],
                        'maximum_mark' => $request->get('maximum_mark')[$key],
                        'percentage' => $request->get('percentage')[$key],
                        'grade_point' => $request->get('grade_point')[$key],
                        'grade_letter' => $request->get('grade_letter')[$key],
                        'created_by' => auth()->user()->id,
                    ]);
                }
            }

            //extra info
            $extraInfo = StudentExtraInfo::create(
                [
                    'students_id' => $student->id,
                    "total_fee_per_year" => $request->get('total_fee_per_year'),
                    "bank_name" => $request->get('bank_name'),
                    "bank_code" => $request->get('bank_code'),
                    "bank_account_number" => $request->get('bank_account_number'),
                    "admission_portal_login_id" => $request->get('admission_portal_login_id'),
                    "admission_portal_login_password" => $request->get('admission_portal_login_password'),
                    'created_by' => auth()->user()->id,
                ]
            );


            //$this->createExtraInfo($student->id);

            if($request->get('scholarship')) {
                $studentScholarship = StudentScholarship::create(
                    [
                        'students_id' => $student->id,
                        "scholarships_id" => $request->get('scholarship'),
                        "scholarship_application_id" => $request->get('scholarship_application_id'),
                        "scholarship_user_name" => $request->get('scholarship_user_name'),
                        "scholarship_password" => $request->get('scholarship_password'),
                        'created_by' => auth()->user()->id,
                    ]
                );
            }else{
                $this->createScholarship($student->id);
            }

            if($request->get('placement_company')){
                $studentPlacement = StudentPlacement::create(
                    [
                        'students_id' => $student->id,
                        "placements_id" => $request->get('placement_company'),
                        "placement_salary" => $request->get('placement_salary'),
                        "placement_date" => $request->get('placement_date'),
                        "placement_location" => $request->get('placement_location'),
                        "placement_designation" => $request->get('placement_designation'),
                        'created_by' => auth()->user()->id,
                    ]
                );
            }else{
                $this->createPlacement($student->id);
            }

            //dmcs record keeping
            if ($request->has('obtained_mark')) {
                foreach ($request->get('obtained_mark') as $key => $obtained_mark) {
                    StudentDegree::create([
                        'students_id'               =>  $student->id,
                        'degrees_id'                 =>  $request->get('degrees_id')[$key],
                        'obtained_mark'             =>  $obtained_mark,
                        'total_marks'               =>  $request->get('total_marks')[$key],
                        'receive_in_college_date'   =>  $request->get('receive_in_college_date')[$key],
                        'issue_date'                =>  $request->get('issue_date')[$key],
                        'created_by'                =>  auth()->user()->id,
                    ]);
                }
            }

            //            if ($request->has('degrees_id')) {
//                //studentDegrees()
//                $stuentDegrees = [];
//                foreach ($request->get('degrees_id') as $key => $degree) {
////                    StudentDegree::create([
////                        'students_id'               =>  $student->id,
////                        'degrees_id'                 =>  $degree,
////                        'obtained_mark'             =>  $request->get('obtained_mark')[$key],
////                        'total_marks'               =>  $request->get('total_marks')[$key],
////                        'receive_in_college_date'   =>  $request->get('receive_in_college_date')[$key],
////                        'issue_date'                =>  $request->get('issue_date')[$key],
////                        'created_by'                =>  auth()->user()->id,
////                    ]);
//                    $stuentDegrees[$key] = [
//                        'students_id'               =>  $student->id,
//                        'degrees_id'                 =>  $degree,
//                        'obtained_mark'             =>  $request->get('obtained_mark')[$key],
//                        'total_marks'               =>  $request->get('total_marks')[$key],
//                        'receive_in_college_date'   =>  $request->get('receive_in_college_date')[$key],
//                        'issue_date'                =>  $request->get('issue_date')[$key],
//                        'created_by'                =>  auth()->user()->id,
//                    ];
//                }
//
//                //studentDegrees
//
//                $student->studentDegrees()->sync($stuentDegrees);
//            }

            //create user
            $rolesId = Role::where('name','student')->first()->id;
            $password = str_random(8);
            $emailIds = $student->email;
            //check user is exist or not
            $existUser = User::where('email',$emailIds)->first();
            $name = isset($student->middle_name)?$student->first_name.' '.$student->middle_name.' '.$student->last_name:$student->first_name.' '.$student->last_name;

            if($existUser){
                $user = $existUser->update([
                    'password' => $password
                ]);

                //$subject = 'Login Detail Reset';
                //$message = 'Dear ' . $name.', Your login detail Updated in our system. Please Login with  <br> email: '.$emailIds.' And Password: '. $password ;
            }else{
                $user = User::create([
                    'role_id' => $rolesId,
                    'hook_id' => $student->id,
                    'name' => $name,
                    'email' => $emailIds,
                    'password' => $password,
                    'status' => 'active'
                ]);
                $roles = [];
                $roles[] = [
                    'user_id' => $user->id,
                    'role_id' => $rolesId
                ];

                $user->userRole()->sync($roles);

                //$subject = 'Login Detail Create';
                //$message = 'Dear ' . $name.', Your login detail created in our system. Please Login with  <br> email: '.$emailIds.' And Password: '. $password ;
            }


            /*SMS & Email Alert*/
            $alert = AlertSetting::where('event','=','StudentRegistration')->first();
            if(!$alert) {
                return back()->with($this->message_warning, "Alert Setting not Setup. Contact Admin For More Detail.");
            }else {
                $subject = $alert->subject;
                //SMS Template
                $message = $this->textReplace($student, $alert->template);
                //Email Template
                $emailMessage = $this->textReplace($student, $alert->email_template);
                //Module Specific Variable
                $message = str_replace('{{password}}', $password, $message);
                $emailMessage = str_replace('{{password}}', $password, $emailMessage);
                //Dear {{first_name}}, you are successfully registered in our institution with RegNo.{{reg_no}}. Your login password is {{password}} , Thank You.
                $emailIds = $student->email;
                $contactNumbers = $student->mobile_1;

                /*Now Send SMS On First Mobile Number*/
                if ($alert->sms == 1) {
                    //$contactNumbers = $this->contactFilter($contactNumbers);
                    $smssuccess = $this->sendSMS($contactNumbers, $message);
                }

                /*Now Send Email With Subject*/
                if ($alert->email == 1) {
                    //$emailIds = $this->emailFilter($emailIds);
                    $emailSuccess = $this->sendEmail($emailIds, $subject, $emailMessage);
                }
            }
            //end sms email

            $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');
        }else{
            $request->session()->flash($this->message_warning, $this->panel. ' Getting Err. While Creating.');
        }

        if($request->add_student_another) {
            return back();
        }else{
            return redirect()->route($this->base_route);
        }
    }

    public function view($id)
    {
        $id =decrypt($id);
        $data = [];
        $data['student'] = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
            'students.faculty', 'students.semester', 'students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group', 'students.nationality',
            'students.national_id_1', 'students.national_id_2', 'students.national_id_3','students.national_id_4','students.religion', 'students.caste',
            'students.mother_tongue', 'students.email', 'students.extra_info', 'students.student_image', 'students.status',

            'pd.grandfather_first_name', 'pd.grandfather_middle_name', 'pd.grandfather_last_name', 'pd.father_first_name',
            'pd.father_middle_name', 'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation',
            'pd.father_office', 'pd.father_office_number', 'pd.father_residence_number', 'pd.father_mobile_1',
            'pd.father_mobile_2', 'pd.father_email', 'pd.mother_first_name', 'pd.mother_middle_name', 'pd.mother_last_name',
            'pd.mother_eligibility', 'pd.mother_occupation', 'pd.mother_office', 'pd.mother_office_number',
            'pd.mother_residence_number', 'pd.mother_mobile_1', 'pd.mother_mobile_2', 'pd.mother_email',

            'gd.id as guardian_id','gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
            'gd.guardian_eligibility', 'gd.guardian_occupation', 'gd.guardian_office', 'gd.guardian_office_number',
            'gd.guardian_residence_number', 'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email',
            'gd.guardian_relation', 'gd.guardian_address',

            'ai.address', 'ai.state', 'ai.country', 'ai.temp_address', 'ai.temp_state', 'ai.temp_country', 'ai.home_phone',
            'ai.mobile_1', 'ai.mobile_2',

            'ei.total_fee_per_year', 'ei.bank_name', 'ei.bank_code', 'ei.bank_account_number',
            'ei.admission_portal_login_id', 'ei.admission_portal_login_password',

            'ss.scholarships_id', 'ss.scholarship_application_id', 'ss.scholarship_user_name', 'ss.scholarship_password',

            'pl.placements_id', 'pl.placement_date', 'pl.placement_salary', 'pl.placement_location', 'pl.placement_designation'

        )
            ->where('students.id','=',$id)
            ->leftJoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->leftJoin('student_guardians as sg', 'sg.students_id','=','students.id')
            ->leftJoin('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
            ->leftJoin('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->leftJoin('student_extra_infos as ei', 'ei.students_id','=','students.id')
            ->leftJoin('student_scholarships as ss', 'ss.students_id','=','students.id')
            ->leftJoin('student_placements as pl', 'pl.students_id','=','students.id')
            ->first();


        if (!$data['student']){
            request()->session()->flash($this->message_warning, "Not a Valid Student");
            return redirect()->route($this->base_route);
        }

        //degrees
        $data['degrees'] = $data['student']->studentDegrees()->join('degrees as d','d.id','=','student_degrees.degrees_id')->get();

        $data['fee_master'] = $data['student']->feeMaster()->orderBy('fee_due_date','asc')->get();
        $data['fee_collection'] = $data['student']->feeCollect()->get();

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

        //attendance start
    try {
        // Pre-fill both cards with the same initial range as the UI default (monthly)
        $initialReq = new Request(['period' => 'monthly']);

        // Use the JSON endpoints so we get totals, percent, legend, colors, total_hm, etc.
        $regJson = $this->attendanceSummary($id, $initialReq);
        $subJson = $this->subjectAttendanceSummary($id, $initialReq);

        $data['attendance_summary_initial'] =
            json_decode($regJson->getContent(), true);

        // IMPORTANT: the blade expects this exact key name
        $data['subject_attendance_summary_initial'] =
            json_decode($subJson->getContent(), true);

        // Subject options for the subject-wise dropdown (used by the blade)
        $data['subjects'] = $this->subjectOptionsForStudent((int)$id);

    } catch (\Throwable $e) {
        // Don’t break the detail page if anything fails — safe defaults + legend
        $legend = $this->attStatusPalette();

        $data['attendance_summary_initial'] = [
            'ok' => true,
            'rows' => [],
            'totals' => ['P'=>0,'A'=>0,'L'=>0,'LV'=>0,'EL'=>0,'H'=>0],
            'range' => ['start' => null, 'end' => null],
            'legend' => $legend,
            'colors' => [],
            'percent' => 0,
            'total_minutes' => 0,
            'total_hm' => '0h 00m',
        ];

        $data['subject_attendance_summary_initial'] = [
            'ok' => true,
            'rows' => [],
            'totals' => ['P'=>0,'A'=>0,'L'=>0,'LV'=>0,'EL'=>0,'H'=>0],
            'range' => ['start' => null, 'end' => null],
            'legend' => $legend,
            'colors' => [],
            'percent' => 0,
            'total_minutes' => 0,
            'total_hm' => '0h 00m',
        ];

        $data['subjects'] = [];
    }


        //library

        $data['lib_member'] = LibraryMember::where([
            'library_members.user_type' => 1,
            'library_members.member_id' => $data['student']->id,
        ])
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

        $data['note'] = Note::select('created_at', 'id', 'member_type','member_id','subject', 'note', 'status')
            ->where('member_type','=','student')
            ->where('member_id','=', $data['student']->id)
            ->orderBy('created_at','desc')
            ->get();

        $data['hostel_history'] = ResidentHistory::select('resident_histories.years_id', 'resident_histories.hostels_id',
            'resident_histories.rooms_id', 'resident_histories.beds_id',
            'resident_histories.history_type','resident_histories.created_at')
            ->where([['r.user_type','=', 1],['r.member_id','=',$data['student']->id]])
            ->join('residents as r', 'r.id', '=', 'resident_histories.residents_id')
            ->join('beds as b', 'b.id', '=', 'resident_histories.beds_id')
            ->orderBy('resident_histories.created_at')
            ->get();

        $data['academicInfos'] = $data['student']->academicInfo()->orderBy('sorting_order','asc')->get();

        /*Exam Score*/
        /*filter student with schedule subject mark ledger*/
        /*$subject = $data['student']->markLedger()
            //->select( 'exam_schedule_id',  'obtain_mark_theory', 'obtain_mark_practical','absent')
            ->get();

        //filter subject and joint mark from schedules;
        $filteredSubject  = $subject->filter(function ($subject, $key) {
            $joinSub = $subject->examSchedule()
                ->first();

            if($joinSub){
                $subject->subjects_id = $joinSub->subjects_id;
                $subject->full_mark_theory = $joinSub->full_mark_theory;
                $subject->pass_mark_theory = $joinSub->pass_mark_theory;
                $subject->full_mark_practical = $joinSub->full_mark_practical;
                $subject->pass_mark_practical = $joinSub->pass_mark_practical;

                //attach exam detail
                $subject->years_id = $joinSub->years_id;
                $subject->months_id = $joinSub->months_id;
                $subject->exams_id = $joinSub->exams_id;
                $subject->faculty_id = $joinSub->faculty_id;
                $subject->semesters_id = $joinSub->semesters_id;
                //more
                $th = $subject->obtain_mark_theory;
                $pr = $subject->obtain_mark_practical;
                $absent_theory = $subject->absent_theory;
                $absent_practical = $subject->absent_practical;

                //theory mark comparision
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

                //Practical mark comparision
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

                $subject->total_obtain_mark = (is_numeric($th_new)?$th_new:0) + (is_numeric($pr_new)?$pr_new:0);

                if($th_new >= $subject->pass_mark_theory && $pr_new >= $subject->pass_mark_practical){
                    $subject->remark = '';
                    if($th_new > $subject->full_mark_theory){
                        $subject->th_remark = '*N';
                        $subject->remark = '*';
                    }

                    if($pr_new > $subject->full_mark_practical){
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
            }
        });

        $data['student']->markLedger->subjects = $filteredSubject;

        $data['examScore'] = $data['student']->markLedger->subjects->groupBY('months_id');
        */

        //$id = auth()->user()->hook_id;
       // $data['student'] = Student::find($id);
        //$semester = Semester::find($data['student']->semester);
//        dd($data['student']);
        $year = Year::where('active_status',1)->first();
        if(!$year) return back();

        $data['schedule_exams'] = ExamSchedule::select('exam_schedules.years_id', 'exam_schedules.months_id', 'exam_schedules.exams_id',
            'exam_schedules.faculty_id', 'exam_schedules.semesters_id')
            ->where('s.id',$data['student']->id)
            ->join('exam_mark_ledgers as ml','ml.exam_schedule_id','=','exam_schedules.id')
            ->join('students as s','s.id','=','ml.students_id')
            ->groupBy('exam_schedules.years_id', 'exam_schedules.months_id', 'exam_schedules.exams_id',
                'exam_schedules.faculty_id', 'exam_schedules.semesters_id')
            ->orderBy('years_id', 'asc')
            ->orderBy('months_id', 'asc')
            ->orderBy('faculty_id', 'asc')
            ->orderBy('semesters_id', 'asc')
            ->get();

    //'exam_schedules.id as exam_schedule_id',
    //       dd($data['schedule_exams']->toArray());

        /*'created_by', 'last_updated_by', 'exam_schedule_id','students_id', 'obtain_mark_theory',
        'absent_theory','obtain_mark_practical','absent_practical', 'sorting_order', 'status'*/
     /*   $data['exam_participation'] = $data['student']->markLedger()
//                                                        ->select('exam_mark_ledgers.id as leger_id','es.years_id', 'es.months_id', 'es.exams_id', 'es.faculty_id', 'es.semesters_id')
                                                        //->groupBy('exam_schedule_id','obtain_mark_theory', 'absent_theory','obtain_mark_practical','absent_practical')
                                                        ->groupBy('es.years_id', 'es.months_id', 'es.exams_id', 'es.faculty_id', 'es.semesters_id')
                                                        ->join('exam_schedules as es','es.id','=','exam_mark_ledgers.exam_schedule_id')
                                                        ->get();*/
       // dd($data['schedule_exams']->toArray());
        //dd($data['exam_participation']->toArray());
//        $data['exam_participation'] = ExamMarkLedger::select(
//                                        'exam_schedule_id','students_id', 'obtain_mark_theory', 'absent_theory','obtain_mark_practical','absent_practical'
//                                        )
//            ->where('id')
//                                        ->groupBy('exam_schedule_id','students_id', 'obtain_mark_theory', 'absent_theory','obtain_mark_practical','absent_practical')
//                                        //->join('')
//                                        ->get();
        //dd($data['exam_participation']->toArray());
//        "exam_schedule_id" => 7
//    "students_id" => 1402
//    "obtain_mark_theory" => 0
//    "absent_theory" => 0
//    "obtain_mark_practical" => 0
//    "absent_practical" => 0

        /*Certificate History*/
        $data['certificate_history'] = $data['student']->certificateHistory()->get();
        //dd($data['certificate_history']);

        /*Transport History*/
        $data['transport_history'] = TransportHistory::select('transport_histories.id', 'transport_histories.years_id',
            'transport_histories.routes_id', 'transport_histories.vehicles_id',  'transport_histories.history_type',
            'transport_histories.created_at','tu.member_id','tu.user_type')
            ->where([['tu.user_type','=', 1],['tu.member_id','=',$data['student']->id]])
            ->join('transport_users as tu','tu.id','=','transport_histories.travellers_id')
            ->orderBy('transport_histories.created_at')
            ->get();

        $data['annexure'] = $data['student']->annexure()->get();

        //login credential
        $data['student_login'] = User::where([['role_id',6],['hook_id',$data['student']->id]])->first();
        $data['guardian_login'] = User::where([['role_id',7],['hook_id',$data['student']->guardian_id]])->first();
        $data['url'] = URL::current();
        return view(parent::loadDataToView($this->view_path.'.detail.index'), compact('data'));
    }


    public function edit(Request $request, $id)
    {
        $id =decrypt($id);
        $data = [];
        $data['row'] = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
            'students.faculty','students.semester','students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group', 'students.religion', 'students.caste',
            'students.nationality', 'students.national_id_1', 'students.national_id_2', 'students.national_id_3','students.national_id_4',
            'students.mother_tongue', 'students.email', 'students.extra_info','students.student_image', 'students.student_signature', 'students.status',

            'pd.grandfather_first_name', 'pd.grandfather_middle_name', 'pd.grandfather_last_name', 'pd.father_first_name', 'pd.father_middle_name',
            'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_office', 'pd.father_office_number',
            'pd.father_residence_number', 'pd.father_mobile_1', 'pd.father_mobile_2', 'pd.father_email', 'pd.mother_first_name',
            'pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation', 'pd.mother_office',
            'pd.mother_office_number', 'pd.mother_residence_number', 'pd.mother_mobile_1', 'pd.mother_mobile_2', 'pd.mother_email',
            'pd.father_image', 'pd.mother_image',

            'ai.address', 'ai.state', 'ai.country','ai.postal_code', 'ai.temp_address',
            'ai.temp_state', 'ai.temp_country', 'ai.temp_postal_code', 'ai.home_phone', 'ai.mobile_1', 'ai.mobile_2',

             'gd.id as guardians_id', 'gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
            'gd.guardian_eligibility', 'gd.guardian_occupation', 'gd.guardian_office', 'gd.guardian_office_number',
            'gd.guardian_residence_number', 'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email',
            'gd.guardian_relation', 'gd.guardian_address', 'gd.guardian_image',

            'ei.total_fee_per_year', 'ei.bank_name', 'ei.bank_code', 'ei.bank_account_number',
            'ei.admission_portal_login_id', 'ei.admission_portal_login_password',

            'ss.scholarships_id', 'ss.scholarship_application_id', 'ss.scholarship_user_name', 'ss.scholarship_password',

            'pl.placements_id', 'pl.placement_date', 'pl.placement_salary', 'pl.placement_location', 'pl.placement_designation',

            //'sd.degrees_id', 'sd.obtained_mark', 'sd.total_marks', 'sd.receive_in_college_date', 'sd.issue_date'
            )
            ->where('students.id','=',$id)
            ->leftJoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->leftJoin('student_guardians as sg', 'sg.students_id','=','students.id')
            ->leftJoin('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
            ->leftJoin('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->leftJoin('student_extra_infos as ei', 'ei.students_id','=','students.id')
            ->leftJoin('student_scholarships as ss', 'ss.students_id','=','students.id')
            ->leftJoin('student_placements as pl', 'pl.students_id','=','students.id')
           // ->join('student_degrees as sd', 'sd.students_id','=','students.id')
            ->first();
            
          //  dd($data['row']);

        if (!$data['row'])
            return parent::invalidRequest();

        $this->deduplicateAcademicInfoRows($data['row']);

        $data['faculties'] = $this->activeFaculties();
        $data['semester'] = $this->activeSemester();
        $data['batch'] = $this->activeBatch();
        $data['academic_status'] = $this->activeStudentAcademicStatus();

        $data['state'] = $this->activeState();
        $data['scholarship'] = $this->activeScholarship();
        $data['placement'] = $this->activePlacement();
        $data['degrees'] = $this->activeDegrees();

        //academic info exist
        $data['academicInfo'] = $data['row']->academicInfo()->orderBy('sorting_order','asc')->get();
        $data['academicInfo-html'] = view($this->view_path.'.registration.includes.forms.academic_tr_edit', [
            'academicInfos' => $data['academicInfo']
        ])->render();

        //academic info not exist and except exist
        $semester = Semester::find($data['row']->semester);
        if(isset($data['academicInfo']) && $data['academicInfo']->count()>0){
            $existingAcademicBoards = $data['academicInfo']
                ->pluck('board')
                ->filter()
                ->map(function ($board) {
                    return strtoupper(preg_replace('/\s+/', '', trim((string) $board)));
                })
                ->unique()
                ->values();

            $academicInfoRow = $semester->programNeedAcademicLevel('academic_info_levels.id as academicInfolevelId','academic_info_levels.title','academic_info_levels.status as academicInfoleveStatus')
                ->Active()
                ->get()
                ->reject(function ($level) use ($existingAcademicBoards) {
                    $normalizedTitle = strtoupper(preg_replace('/\s+/', '', trim((string) $level->title)));
                    return $existingAcademicBoards->contains($normalizedTitle);
                })
                ->values();
            $data['academicInfoRows-html'] = view($this->view_path.'.registration.includes.forms.academic_tr', [
                'academicInfoRow' => $academicInfoRow
            ])->render();
        }else{
            $academicInfoRow = $semester->programNeedAcademicLevel('academic_info_levels.id as academicInfolevelId','academic_info_levels.title','academic_info_levels.status as academicInfoleveStatus')->Active()->get();
            $data['academicInfoRows-html'] = view($this->view_path.'.registration.includes.forms.academic_tr', [
                'academicInfoRow' => $academicInfoRow
            ])->render();
        }


        //semesters Subjects
        $semesterModel = Semester::find($data['row']->semester);
        $data['max_subjects_count'] =  $semesterModel ? $semesterModel->major_subject_count : 0;
        $data['subjects'] =  $semesterModel ? $semesterModel->subjects()->orderBy('subjects.title')->get() : collect();
        //$data['existing_subjects'] = $data['row']->majorSubject()->join('subjects as s','s.id','=','student_subjects.subjects_id')->pluck('s.title', 's.id')->toArray();
        $data['existing_subjects'] = $data['row']->majorSubject()->join('subjects as s','s.id','=','student_subject.subjects_id')->pluck('s.title', 's.id')->toArray();
        $data['selected_subject_ids'] = array_map('intval', array_keys($data['existing_subjects']));
        $data['subjects_html'] = view('student.online-registration.includes.forms.fetch-subjects', [
            'subjects' => $data['subjects'],
            'numOfSubject' => $data['max_subjects_count'] ?: $data['subjects']->count(),
            'selectedSubjectIds' => $data['selected_subject_ids'],
        ])->render();

        $data['annexures'] = Annexure::select('id', 'title')->Active()->get();
        $data['existing_annexure'] = $data['row']->annexure()->join('annexures as an','an.id','=','student_annexures.annexures_id')->pluck('an.title', 'an.id')->toArray();

        //degrees
        $data['degrees'] = $data['row']->studentDegrees()->join('degrees as d','d.id','=','student_degrees.degrees_id')->get();

        $semesterFee = Semester::find($data['row']->semester)->semester_fee;
        //$subjectsFee = $data['row']->majorSubject()->join('subjects','subjects.id','=','student_subject.subject_id')->sum('course_fee');
        //$subjectsFee = $data['row']->majorSubject()->join('subjects','subjects.id','=','student_subject.subjects_id')->sum('course_fee');
        //$data['admission_fee'] = $semesterFee + $subjectsFee;
        return view(parent::loadDataToView($this->view_path.'.registration.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        
        $id = decrypt($id);
        if (!$row = Student::find($id))
            return parent::invalidRequest();

        $selectedSubjects = array_values(array_unique(array_filter((array) $request->get('subject'), function ($subjectId) {
            return (int) $subjectId > 0;
        })));

        if (empty($selectedSubjects)) {
            return back()->withInput()->withErrors([
                'subject' => 'Please select at least 1 subject.'
            ]);
        }

        $semester = Semester::find($row->semester);
        $maxAllowedSubjects = min((int) ($semester->major_subject_count ?? count($selectedSubjects)), 7);
        $semesterSubjects = $semester
            ? $semester->subjects()->select('subjects.id', 'subjects.sub_type')->whereIn('subjects.id', $selectedSubjects)->get()
            : collect();

        if ($semesterSubjects->count() !== count($selectedSubjects)) {
            return back()->withInput()->withErrors([
                'subject' => 'Selected subjects must belong to the student semester.'
            ]);
        }

        $optionalCount = $semesterSubjects->filter(function ($subject) {
            return strtolower(trim((string) ($subject->sub_type ?? ''))) === 'optional';
        })->count();
        $compulsoryCount = $semesterSubjects->count() - $optionalCount;

        if (count($selectedSubjects) > $maxAllowedSubjects) {
            return back()->withInput()->withErrors([
                'subject' => 'You can select maximum '.$maxAllowedSubjects.' subjects.'
            ]);
        }

        if ($optionalCount > 1) {
            return back()->withInput()->withErrors([
                'subject' => 'You can select maximum 1 optional subject.'
            ]);
        }

        if ($compulsoryCount > 6) {
            return back()->withInput()->withErrors([
                'subject' => 'You can select maximum 6 compulsory subjects.'
            ]);
        }


        if($row) {

            if ($request->hasFile('student_main_image')) {
                // remove old image from folder
                if (file_exists($this->folder_path . $row->student_image))
                    @unlink($this->folder_path . $row->student_image);

                /*upload new student image*/
                $student_image_name = parent::uploadAttendanceProfileImage(
                    $request,
                    'student_main_image',
                    $this->folder_path,
                    $request->reg_no
                );
            }

            if ($request->hasFile('student_signature_main_image')) {
                // remove old image from folder
                if (file_exists($this->folder_path . $row->student_signature))
                    @unlink($this->folder_path . $row->student_signature);

                /*upload new student signature*/
                $student_signature = $request->file('student_signature_main_image');
                $student_signature_name = $request->reg_no . '_signature.' . $student_signature->getClientOriginalExtension();
                $student_signature->move($this->folder_path, $student_signature_name);
            }

            $request->request->add(['updated_by' => auth()->user()->id]);
            $request->request->add(['student_image' => isset($student_image_name) ? $student_image_name : $row->student_image]);
            $request->request->add(['student_signature' => isset($student_signature_name) ? $student_signature_name : $row->student_signature]);

            $student = $row->update($request->all());

            /*Update Associate Address Info*/
            $row->address()->update([
                'address' => $request->address,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'temp_address' => $request->temp_address,
                'temp_state' => $request->temp_state,
                'temp_postal_code' => $request->temp_postal_code,
                'temp_country' => $request->temp_country,
                'home_phone' => $request->home_phone,
                'mobile_1' => $request->mobile_1,
                'mobile_2' => $request->mobile_2

            ]);

            /*Update Associate Parents Info with Images*/
            $parent = $row->parents()->first();
            $guardian = $row->guardian()->first();

            $parential_image_path = public_path() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'parents' . DIRECTORY_SEPARATOR;
            if ($request->hasFile('father_main_image')) {
                // remove old image from folder
                if (file_exists($parential_image_path . $parent->father_image))
                    @unlink($parential_image_path . $parent->father_image);

                $father_image = $request->file('father_main_image');
                $father_image_name = $row->reg_no . '_father.' . $father_image->getClientOriginalExtension();
                $father_image->move($parential_image_path, $father_image_name);
            }

            if ($request->hasFile('mother_main_image')) {
                // remove old image from folder
                if (file_exists($parential_image_path . $parent->mother_image))
                    @unlink($parential_image_path . $parent->mother_image);

                $mother_image = $request->file('mother_main_image');
                $mother_image_name = $row->reg_no . '_mother.' . $mother_image->getClientOriginalExtension();
                $mother_image->move($parential_image_path, $mother_image_name);
            }


            if ($request->hasFile('guardian_main_image')) {
                // remove old image from folder
                if ($guardian && $guardian->guardian_image && file_exists($parential_image_path . $guardian->guardian_image))
                    @unlink($parential_image_path . $guardian->guardian_image);

                $guardian_image = $request->file('guardian_main_image');
                $guardian_image_name = $row->reg_no . '_guardian.' . $guardian_image->getClientOriginalExtension();
                $guardian_image->move($parential_image_path, $guardian_image_name);
            }

            $father_image_name = isset($father_image_name) ? $father_image_name : $parent->father_image;
            $mother_image_name = isset($mother_image_name) ? $mother_image_name : $parent->mother_image;
            $guardian_image_name = isset($guardian_image_name) ? $guardian_image_name : ($guardian ? $guardian->guardian_image : null);

            $row->parents()->update([
                'grandfather_first_name' => $request->grandfather_first_name,
                'grandfather_middle_name' => $request->grandfather_middle_name,
                'grandfather_last_name' => $request->grandfather_last_name,
                'father_first_name' => $request->father_first_name,
                'father_middle_name' => $request->father_middle_name,
                'father_last_name' => $request->father_last_name,
                'father_eligibility' => $request->father_eligibility,
                'father_occupation' => $request->father_occupation,
                'father_office' => $request->father_office,
                'father_office_number' => $request->father_office_number,
                'father_residence_number' => $request->father_residence_number,
                'father_mobile_1' => $request->father_mobile_1,
                'father_mobile_2' => $request->father_mobile_2,
                'father_email' => $request->father_email,
                'mother_first_name' => $request->mother_first_name,
                'mother_middle_name' => $request->mother_middle_name,
                'mother_last_name' => $request->mother_last_name,
                'mother_eligibility' => $request->mother_eligibility,
                'mother_occupation' => $request->mother_occupation,
                'mother_office' => $request->mother_office,
                'mother_office_number' => $request->mother_office_number,
                'mother_residence_number' => $request->mother_residence_number,
                'mother_mobile_1' => $request->mother_mobile_1,
                'mother_mobile_2' => $request->mother_mobile_2,
                'mother_email' => $request->mother_email,
                'father_image' => $father_image_name,
                'mother_image' => $mother_image_name

            ]);

            //if guardian link modified or not condition
            if ($request->guardian_link_id == null) {
                $sgd = $row->guardian()->first();
                $guardiansInfo = [
                    'guardian_first_name' => $request->guardian_first_name,
                    'guardian_middle_name' => $request->guardian_middle_name,
                    'guardian_last_name' => $request->guardian_last_name,
                    'guardian_eligibility' => $request->guardian_eligibility,
                    'guardian_occupation' => $request->guardian_occupation,
                    'guardian_office' => $request->guardian_office,
                    'guardian_office_number' => $request->guardian_office_number,
                    'guardian_residence_number' => $request->guardian_residence_number,
                    'guardian_mobile_1' => $request->guardian_mobile_1,
                    'guardian_mobile_2' => $request->guardian_mobile_2,
                    'guardian_email' => $request->guardian_email,
                    'guardian_relation' => $request->guardian_relation,
                    'guardian_address' => $request->guardian_address,
                    'guardian_image' => $guardian_image_name
                ];
                if ($sgd) {
                    GuardianDetail::where('id', $sgd->guardians_id)->update($guardiansInfo);
                }
            } else {
                $studentGuardian = StudentGuardian::where('students_id', $row->id)->update([
                    'students_id' => $row->id,
                    'guardians_id' => $request->guardian_link_id,
                ]);
            }

            //Academic Info Start
            if ($request->has('institution')) {
                foreach ($request->get('institution') as $key => $institution) {
                    $board = trim((string) ($request->get('board')[$key] ?? ''));
                    $institution = trim((string) $institution);
                    $rollNo = trim((string) ($request->get('roll_no')[$key] ?? ''));
                    $passYear = trim((string) ($request->get('pass_year')[$key] ?? ''));
                    $majorSubjects = trim((string) ($request->get('major_subjects')[$key] ?? ''));
                    $markObtained = $request->get('mark_obtained')[$key] ?? null;
                    $maximumMark = $request->get('maximum_mark')[$key] ?? null;
                    $percentage = $request->get('percentage')[$key] ?? null;
                    $gradePoint = trim((string) ($request->get('grade_point')[$key] ?? ''));
                    $gradeLetter = trim((string) ($request->get('grade_letter')[$key] ?? ''));

                    $isAcademicRowEmpty = $institution === ''
                        && $rollNo === ''
                        && $passYear === ''
                        && $majorSubjects === ''
                        && ($markObtained === null || $markObtained === '')
                        && ($maximumMark === null || $maximumMark === '')
                        && ($percentage === null || $percentage === '')
                        && $gradePoint === ''
                        && $gradeLetter === '';

                    if ($isAcademicRowEmpty) {
                        continue;
                    }

                    $academicInfoId = $request->get('academic_info_id')[$key] ?? null;
                    $academicInfoExist = null;

                    if ($academicInfoId) {
                        $academicInfoExist = $row->academicInfo()->where('id', $academicInfoId)->first();
                    }

                    if (!$academicInfoExist && $board !== '') {
                        $academicInfoExist = $row->academicInfo()->where('board', $board)->first();
                    }

                    if ($academicInfoExist) {
                        $academicInfoUpdate = [
                            'students_id' => $row->id,
                            'institution' => $institution,
                            'board' => $board,
                            'roll_no' => $rollNo,
                            'pass_year' => $passYear,
                            'major_subjects' => $majorSubjects,
                            'mark_obtained' => $markObtained,
                            'maximum_mark' => $maximumMark,
                            'percentage' => $percentage,
                            'grade_point' => $gradePoint,
                            'grade_letter' => $gradeLetter,
                            'sorting_order' => $key + 1,
                            'last_updated_by' => auth()->user()->id
                        ];
                        $academicInfoExist->update($academicInfoUpdate);
                    } else {
                        AcademicInfo::create([
                            'students_id' => $row->id,
                            'institution' => $institution,
                            'board' => $board,
                            'roll_no' => $rollNo,
                            'pass_year' => $passYear,
                            'major_subjects' => $majorSubjects,
                            'mark_obtained' => $markObtained,
                            'maximum_mark' => $maximumMark,
                            'percentage' => $percentage,
                            'grade_point' => $gradePoint,
                            'grade_letter' => $gradeLetter,
                            'sorting_order' => $key + 1,
                            'created_by' => auth()->user()->id,
                        ]);
                    }
                }
            }

            $this->deduplicateAcademicInfoRows($row);

            //student subjects
            if($request->has('subject')) {
                $subjects = $selectedSubjects;

                // dd($subjects);

                $row->studentSubjects()->sync($subjects);
                //            foreach ($request->get('subject') as $key => $subject) {
                //                $majorSubjectExist = $row->majorSubject()->select('id')->where('subjects_id',$subject)->first();
                //
                //                if($majorSubjectExist){
                //                    $updateSubject = [
                //                        'students_id' => $row->id,
                //                        'subjects_id' => $subject,
                //                        'last_updated_by' => auth()->user()->id
                //                    ];
                //                    $majorSubjectExist->update($updateSubject);
                //                }else{
                //                    StudentSubject::create([
                //                        'students_id' => $row->id,
                //                        'subjects_id' => $subject,
                //                        'created_by' => auth()->user()->id,
                //                    ]);
                //                }
                //            }
                //
                //            //remove extra
                //            DB::table('student_subjects')->whereNotIn('subjects_id', $request->get('subject'))->delete();
            }

            // Annexure List
            if ($request->get('annexure')) {
                foreach ($request->get('annexure') as $key => $annexure) {
                    $majorSubjectExist = $row->annexure()->select('id')->where('annexures_id', $annexure)->first();
                    if ($majorSubjectExist) {
                        $academicInfoUpdate = [
                            'students_id' => $row->id,
                            'annexures_id' => $annexure,
                            'last_updated_by' => auth()->user()->id
                        ];
                        $majorSubjectExist->update($academicInfoUpdate);
                    } else {
                        StudentAnnexure::create([
                            'students_id' => $row->id,
                            'annexures_id' => $annexure,
                            'created_by' => auth()->user()->id,
                        ]);
                    }
                }
                //remove extra
                DB::table('student_annexures')->whereNotIn('annexures_id', $request->get('annexure'))->delete();
            }

            //extra info
            $existingExtraInfo = $row->studentExtraInfo()->first();
            $extraInfo = [
                'students_id' => $row->id,
                "total_fee_per_year" => $request->get('total_fee_per_year'),
                "bank_name" => $request->get('bank_name'),
                "bank_code" => $request->get('bank_code'),
                "bank_account_number" => $request->get('bank_account_number'),
                "admission_portal_login_id" => $request->get('admission_portal_login_id'),
                "admission_portal_login_password" => $request->get('admission_portal_login_password'),
                'created_by' => auth()->user()->id,
            ];
            if ($existingExtraInfo) {
                $existingExtraInfo->update($extraInfo);
            } else {
                StudentExtraInfo::create($extraInfo);

            }

            //scholarship
            $existingStudentScholarship = $row->studentScholarship()->first();
            $studentScholarship = [
                'students_id' => $row->id,
                "scholarships_id" => $request->get('scholarship'),
                "scholarship_application_id" => $request->get('scholarship_application_id'),
                "scholarship_user_name" => $request->get('scholarship_user_name'),
                "scholarship_password" => $request->get('scholarship_password'),
                'created_by' => auth()->user()->id,
            ];
            if ($existingStudentScholarship) {
                $existingStudentScholarship->update($studentScholarship);
            } else {
                StudentScholarship::create($studentScholarship);
            }

            //Placement
            $existingStudentPlacement = $row->studentPlacement()->first();
            $studentPlacement = [
                'students_id' => $row->id,
                "placements_id" => $request->get('placement_company'),
                "placement_salary" => $request->get('placement_salary'),
                "placement_date" => $request->get('placement_date'),
                "placement_location" => $request->get('placement_location'),
                "placement_designation" => $request->get('placement_designation'),
                'created_by' => auth()->user()->id,
            ];
            if ($existingStudentPlacement) {
                $existingStudentPlacement->update($studentPlacement);
            } else {
                StudentPlacement::create($studentPlacement);
            }

            //dmcs record keeping
//            if ($request->has('institution')) {
//                foreach ($request->get('institution') as $key => $institution) {
//                    $academicInfoId = isset($request->get('academic_info_id')[$key]) ? $request->get('academic_info_id')[$key] : $key + 1;
//                    $academicInfoExist = AcademicInfo::where('id', $academicInfoId)->first();
//
//                    if ($academicInfoExist) {
//                        $academicInfoUpdate = [
//                            'students_id' => $row->id,
//                            'institution' => $institution,
//                            'board' => $request->get('board')[$key],
//                            'pass_year' => $request->get('pass_year')[$key],
//                            'major_subjects' => $request->get('major_subjects')[$key],
//                            'mark_obtained' => $request->get('mark_obtained')[$key],
//                            'maximum_mark' => $request->get('maximum_mark')[$key],
//                            'percentage' => $request->get('percentage')[$key],
//                            'sorting_order' => $key + 1,
//                            'last_updated_by' => auth()->user()->id
//                        ];
//                        $academicInfoExist->update($academicInfoUpdate);
//                    } else {
//                        AcademicInfo::create([
//                            'students_id' => $row->id,
//                            'institution' => $institution,
//                            'board' => $request->get('board')[$key],
//                            'pass_year' => $request->get('pass_year')[$key],
//                            'major_subjects' => $request->get('major_subjects')[$key],
//                            'mark_obtained' => $request->get('mark_obtained')[$key],
//                            'maximum_mark' => $request->get('maximum_mark')[$key],
//                            'percentage' => $request->get('percentage')[$key],
//                            'sorting_order' => $key + 1,
//                            'created_by' => auth()->user()->id,
//                        ]);
//                    }
//                }
//            }

            //            if ($request->has('degrees_id')) {
//                $stuentDegrees = [];
//                foreach ($request->get('degrees_id') as $key => $degree) {
//                    $stuentDegrees[$degree] = [
//                        'students_id' => $row->id,
//                        'degrees_id' => $degree,
//                        'obtained_mark' => $request->get('obtained_mark')[$key],
//                        'total_marks' => $request->get('total_marks')[$key],
//                        'receive_in_college_date' => $request->get('receive_in_college_date')[$key],
//                        'issue_date' => $request->get('issue_date')[$key],
//                        'created_by' => auth()->user()->id,
//                    ];
//                }
//                if ($stuentDegrees) {
//                    $row->studentDegrees()->sync($stuentDegrees);
//                    //$student->studentDegrees()->sync($stuentDegrees);
//                }
//            }

            // Student Degree List
            if ($request->get('degrees_id')) {
                foreach ($request->get('degrees_id') as $key => $degrees) {
                    $stuentDegrees = $row->studentDegrees()->select('id')->where('degrees_id', $degrees)->first();
                    if ($stuentDegrees) {
                        $exitCount = true;
                        $degreeUpdate = [
                            'students_id' => $row->id,
                            'degrees_id' => $degrees,
                            'obtained_mark' => $request->get('obtained_mark')[$key],
                            'total_marks' => $request->get('total_marks')[$key],
                            'receive_in_college_date' => $request->get('receive_in_college_date')[$key],
                            'issue_date' => $request->get('issue_date')[$key],
                            'last_updated_by' => auth()->user()->id,
                        ];
                        $stuentDegrees->update($degreeUpdate);
                    } else {
                        StudentDegree::create([
                            'students_id' => $row->id,
                            'degrees_id' => $degrees,
                            'obtained_mark' => $request->get('obtained_mark')[$key],
                            'total_marks' => $request->get('total_marks')[$key],
                            'receive_in_college_date' => $request->get('receive_in_college_date')[$key],
                            'issue_date' => $request->get('issue_date')[$key],
                            'created_by' => auth()->user()->id,
                        ]);
                    }
                }
            }

            $request->session()->flash($this->message_success, $this->panel . ' Info Updated Successfully.');
        }else{
            $request->session()->flash($this->message_warning, $this->panel . ' Update Fetch Err.');
        }
        return back();

    }

    //Soft Delete With History Erase Message
    /*public function delete(Request $request, $id)
    {
        $id =decrypt($id);
        $errCount = 0;
        $errors = [];
        if (!$row = Student::find($id)) return parent::invalidRequest();

        //User
            $rolesId = Role::where('name','student')->first()->id;
            $userInfo = User::where(['role_id' => $rolesId, 'hook_id'=> $id])->first();
            if($userInfo) {
                $errCount = $errCount+1;
                $errors[] = "User Found, Please Delete.";
            }

        //Document & Notes
            //Documents
            $document = $row->studentDocuments()->get();
            if($document->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Documents Found, Please Delete.";
            }

        //Notes
            $notes = $row->studentNotes()->get();
            if($notes->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Notes Found, Please Delete.";
            }

        //Assignment
            $assignmentAnswer = $row->assignmentAnswers()->get();
            if($assignmentAnswer->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Assignment Answer Found, Please Delete.";
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

        //Certificates
            //Certificate History
            $certificateHistories = $row->certificateHistory()->get();
            if($certificateHistories->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Certificate History Found, Please Delete.";
            }

            //attendance certificate
            $attendanceCertificates = $row->attendanceCertificate()->get();
            if($attendanceCertificates->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Attendance Certificate Found, Please Delete.";
            }

            //bonafied Certificate
            $bonafideCertificates = $row->bonafideCertificate()->get();
            if($bonafideCertificates->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Bonafied Certificate Found, Please Delete.";
            }

            //Course Completion Certificate
            $courseCompletionCertificates = $row->courseCompletionCertificate()->get();
            if($courseCompletionCertificates->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Course Completion Certificate Found, Please Delete.";
            }

            //Transfer Certificate
            $transferCertificates = $row->transferCertificate()->get();
            if($transferCertificates->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Transfer Certificate Found, Please Delete.";
            }

            //Character Certificate
            $characterCertificates = $row->characterCertificate()->get();
            if($characterCertificates->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Character Certificate Found, Please Delete.";
            }

        //exam mark ledger
            $examMarkLedger = $row->markLedger()->get();
            if($examMarkLedger->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Mark Ledger Found, Please Delete.";
            }

        //attendance (regular & Subject)
            //Regular Attendance
            $attendacne = $row->regularAttendance()->get();
            if($attendacne->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Regular Attendance Found, Please Delete.";
            }

            //Subject Attendance
            $subjectAttendance = $row->subjectAttendances()->get();
            if($subjectAttendance->count() > 0){
                $errCount = $errCount+1;
                $errors[] = "Subject Attendance Found, Please Delete.";
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

        //Fee Master, Fee Collection
            $feeMaster = $row->feeMaster()->get();
            if($feeMaster->count() > 0){
                $feeCollection = $row->feeCollect()->get();
                if($feeCollection->count() > 0){
                    $errCount = $errCount+1;
                    $errors[] = "Fee Collection Found, Please Delete.";
                }
                $errCount = $errCount+1;
                $errors[] = "Fee Master Found, Please Delete.";
            }


        //Academic Info
        $academicInfo = $row->academicInfo()->get();
        if($academicInfo->count() > 0){
            $errCount = $errCount+1;
            $errors[] = "Academic Info Found, Please Delete.";
        }

        //parent Info
            $parentInfo = $row->parents()->first();
            if(isset($parentInfo)){
                $parentInfo->delete();
                $errCount = $errCount+1;
                $errors[] = "Parent Info Found, Please Delete.";
            }

        //address info
            $addressInfo = $row->address()->first();
            if(isset($addressInfo)){
                $addressInfo->delete();
                $errCount = $errCount+1;
                $errors[] = "Address Info Found, Please Delete.";
            }

        //guardian info
            $guardian = $row->guardian()->first();
            if(isset($guardian)){
                //$guardian->delete();
                $guardianDetail = GuardianDetail::find($guardian->id);
            if($guardianDetail){
                $errCount = $errCount+1;
                $errors[] = "Guardian Detail Info Found, Please Delete.";
            }

        }

        if($errCount > 0){
            $request->session()->flash($this->message_warning, $this->panel.' not delete. If you want to erase student data, please check err below and request administrator to delete all the data first.');
            return back()->withErrors($errors);
        }else{
            //remove images
            if (file_exists($this->folder_path.$row->student_image))
                @unlink($this->folder_path.$row->student_image);

            if (file_exists($this->folder_path.$row->student_signature))
                @unlink($this->folder_path.$row->student_signature);

            $this->parent_folder_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR;
            if (file_exists($this->parent_folder_path.$row->reg_no.'_father'.'.*'))
                @unlink($this->parent_folder_path.$row->reg_no.'_father'.'.*');

            if (file_exists($this->parent_folder_path.$row->reg_no.'_mother'.'.*'))
                @unlink($this->parent_folder_path.$row->reg_no.'_mother'.'.*');

            if (file_exists($this->parent_folder_path.$row->reg_no.'_guardian'.'.*'))
                @unlink($this->parent_folder_path.$row->reg_no.'_guardian'.'.*');

            $row->delete();
            $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        }

        //return redirect()->route($this->base_route);
        return back();
    }*/

    //Force Delete All History No Any Message
    public function delete(Request $request, $id)
    {

        $id =decrypt($id);
        $errCount = 0;
        $errors = [];
        if (!$row = Student::find($id)) return parent::invalidRequest();

        //User
        $rolesId = Role::where('name','student')->first()->id;
        $userInfo = User::where(['role_id' => $rolesId, 'hook_id'=> $id])->first();
        if(isset($userInfo) && $userInfo !== null) {
            $userInfo->delete();
        }

        //Academic Info
        $academicInfos = $row->academicInfo()->get();
        if(isset($academicInfos) && $academicInfos->count() > 0){
            foreach ($academicInfos as $academicInfo){
                $academicInfo->delete();
            }
        }

        //parent Info
        $parentInfo = $row->parents()->first();
        if(isset($parentInfo)){
            $parentInfo->delete();
        }

        //address info
        $addressInfo = $row->address()->first();
        if(isset($addressInfo)){
            $addressInfo->delete();
        }

        //guardian info
        $guardian = $row->guardian()->first();
        if(isset($guardian)){
            $guardian->delete();
            $guardianDetail = GuardianDetail::find($guardian->id);
            if($guardianDetail){
                $guardianDetail->delete();
            }

        }

        //Document & Notes
        //Documents
        $documents = $row->studentDocuments()->get();
        if(isset($documents) && $documents->count() > 0){
            foreach ($documents as $document){
                $document->delete();
            }
        }

        //Notes
        $notes = $row->studentNotes()->get();
        if(isset($notes) && $notes->count() > 0){
            foreach ($notes as $note){
                $note->delete();
            }
        }

        //subjects: detach only student mapping, do not delete master subjects
        $row->studentSubjects()->detach();

        $majorSubjects = $row->majorSubject()->get();
        if(isset($majorSubjects) && $majorSubjects->count() > 0){
            foreach ($majorSubjects as $majorSubject){
                $majorSubject->delete();
            }
        }

        //extraInfo
        $extraInfo = $row->studentScholarship()->get();
        if(isset($extraInfo) && $extraInfo->count() > 0){
            foreach ($extraInfo as $extraInfo){
            $extraInfo->delete();
            }
        }
        //Scholarship
        $scholarships = $row->studentScholarship()->get();
        if(isset($scholarships) && $scholarships->count() > 0){
            foreach ($scholarships as $scholarship){
                $scholarship->delete();
            }
        }

        //Placement
        $placements = $row->studentPlacement()->get();
        if(isset($placements) && $placements->count() > 0){
            foreach ($placements as $placement){
                $placement->delete();
            }
        }

        //Degrees
        $degrees = $row->studentNotes()->get();
        if(isset($degrees) && $degrees->count() > 0){
            foreach ($degrees as $degree){
                $degree->delete();
            }
        }

        //Assignment
        $assignmentAnswers = $row->assignmentAnswers()->get();
        if(isset($assignmentAnswers) && $assignmentAnswers->count() > 0){
            foreach ($assignmentAnswers as $assignmentAnswer){
                $assignmentAnswer->delete();
            }
        }

        //Transport
        $transportUser = $row->transportUser()->first();
        if(isset($transportUser) && $transportUser->count() > 0){
            $transportHistories = TransportHistory::where('travellers_id',$transportUser->id)->get();
            if( isset($transportHistories) && $transportHistories->count() > 0){
                foreach ($transportHistories as $transportHistory){
                    $transportHistory->delete();
                }
            }
            $transportUser->delete();
        }

        //Hostel
        $hostelResident = $row->hostelResident()->first();
        if(isset($hostelResident) && $hostelResident->count() > 0){
            $residentHistories = ResidentHistory::where('residents_id',$hostelResident->id)->get();
            if(isset($residentHistories) && $residentHistories->count() > 0){
                foreach ($residentHistories as $residentHistory){
                    $residentHistory->delete();
                }
            }
            $hostelResident->delete();
        }

        //Certificates
        //Certificate History
        $certificateHistories = $row->certificateHistory()->get();
        if(isset($certificateHistories) && $certificateHistories->count() > 0){
            foreach ($certificateHistories as $certificateHistory){
                $certificateHistory->delete();
            }
        }

        //attendance certificate
        $attendanceCertificates = $row->attendanceCertificate()->first();
        if(isset($attendanceCertificates) && $attendanceCertificates->count() > 0){
            $attendanceCertificates->delete();
        }

        //bonafied Certificate
        $bonafideCertificates = $row->bonafideCertificate()->first();
        if(isset($bonafideCertificates) && $bonafideCertificates->count() > 0){
            $bonafideCertificates->delete();
        }

        //Course Completion Certificate
        $courseCompletionCertificates = $row->courseCompletionCertificate()->first();
        if(isset($courseCompletionCertificates) && $courseCompletionCertificates->count() > 0){
            $courseCompletionCertificates->delete();
        }

        //Transfer Certificate
        $transferCertificates = $row->transferCertificate()->first();
        if(isset($transferCertificates) && $transferCertificates->count() > 0){
            $transferCertificates->delete();
        }

        //Character Certificate
        $characterCertificates = $row->characterCertificate()->first();
        if(isset($characterCertificates) && $characterCertificates->count() > 0){
            $characterCertificates->delete();
        }

        //MEDIUM OF INSTRUCTION
        $moiCertificates = $row->MOICertificate()->first();
        if(isset($moiCertificates) && $moiCertificates->count() > 0){
            $moiCertificates->delete();
        }
        //NIRGAMUTARA
        $nirgamUtaraCertificates = $row->nirgamUtaraCertificate()->first();
        if(isset($nirgamUtaraCertificates) && $nirgamUtaraCertificates->count() > 0){
            $nirgamUtaraCertificates->delete();
        }

        //PROVISIONAL
        $provisionalCertificates = $row->provisionalCertificate()->first();
        if(isset($provisionalCertificates) && $provisionalCertificates->count() > 0){
            $provisionalCertificates->delete();
        }

        //TESTIMONIAL
        $testimonialCertificates = $row->testimonialCertificate()->first();
        if(isset($testimonialCertificates) && $testimonialCertificates->count() > 0){
            $testimonialCertificates->delete();
        }

        //TRANSCRIPT
        $transcriptCertificates = $row->transcriptCertificate()->first();
        if(isset($transcriptCertificates) && $transcriptCertificates->count() > 0){
            $transcriptCertificates->delete();
        }

        //exam mark ledger
        $examMarkLedger = $row->markLedger()->get();
        if(isset($examMarkLedger) && $examMarkLedger->count() > 0){
            foreach ($examMarkLedger as $mark){
                $mark->delete();
            }
        }

        //attendance (regular & Subject)
        //Regular Attendance
        $attendances = $row->regularAttendance()->get();
        if(isset($attendances) && $attendances->count() > 0){
            foreach ($attendances as $attendance){
                $attendance->delete();
            }
        }

        //Subject Attendance
        $subjectAttendances = $row->subjectAttendances()->get();
        if(isset($subjectAttendances) && $subjectAttendances->count() > 0){
            foreach ($subjectAttendances as $subjectAttendance){
                $subjectAttendance->delete();
            }
        }

        //library Membership & History
        $libraryMember = $row->libraryMember()->first();
        if(isset($libraryMember) && $libraryMember->count() > 0){
            $libraryMember->delete();
        }

        //Fee Master, Fee Collection
        $feeCollections = $row->feeCollect()->get();
        if(isset($feeCollections) && $feeCollections->count() > 0){
            foreach ($feeCollections as $collection){
                $collection->delete();
            }
        }


        $feeMasters = $row->feeMaster()->get();
        if(isset($feeMasters) && $feeMasters->count() > 0){
            foreach ($feeMasters as $feeMaster){
                $feeMaster->delete();
            }
        }



        if($errCount > 0){
            $request->session()->flash($this->message_warning, $this->panel.' not delete. If you want to erase student data, please check err below and request administrator to delete all the data first.');
            return back()->withErrors($errors);
        }else{
            //remove images
            if (file_exists($this->folder_path.$row->student_image))
                @unlink($this->folder_path.$row->student_image);

            if (file_exists($this->folder_path.$row->student_signature))
                @unlink($this->folder_path.$row->student_signature);

            /*$this->parent_folder_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'parents'.DIRECTORY_SEPARATOR;
            if (file_exists($this->parent_folder_path.$row->reg_no.'_father'.'.*'))
                @unlink($this->parent_folder_path.$row->reg_no.'_father'.'.*');

            if (file_exists($this->parent_folder_path.$row->reg_no.'_mother'.'.*'))
                @unlink($this->parent_folder_path.$row->reg_no.'_mother'.'.*');

            if (file_exists($this->parent_folder_path.$row->reg_no.'_guardian'.'.*'))
                @unlink($this->parent_folder_path.$row->reg_no.'_guardian'.'.*');*/

            $row->delete();
            $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        }

        //return redirect()->route($this->base_route);
        return back();
    }

    public function active(request $request, $id)
    {
        $encId = $id;
        $id = decrypt($id);
        if (!$row = Student::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route.'.view', ['id' => $encId]);
    }

    public function inActive(request $request, $id)
    {
        $encId = $id;
        $id = decrypt($id);
        if (!$row = Student::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);
        $row->update($request->all());

        //in active student login detail
        $rolesId = Role::where('name','student')->first()->id;
        $login_detail = User::where([['role_id',$rolesId],['hook_id',$row->id]])->first();
        if($login_detail){
            $request->request->add(['status' => 'in-active']);
            $login_detail->update($request->all());
        }

        // in active guardian login detail
        $login_detail = User::where([['role_id',7],['hook_id',$row->id]])->first();
        if($login_detail) {
            $request->request->add(['status' => 'in-active']);
            $login_detail->update($request->all());
        }

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route.'.view', ['id' => $encId]);
    }

    public function findSemester(Request $request)
    {
        $response = [];
        $response['error'] = true;

        if ($request->has('faculty_id')) {
            $faculty = Faculty::find($request->get('faculty_id'));
            if ($faculty) {
                $semesterList = $faculty->semesters()
                    ->select('semesters.id', 'semesters.semester', 'semesters.slug')
                    ->get();

                if ($semesterList->isNotEmpty()) {
                    $response['semester'] = $semesterList->values();
                    $response['error'] = false;
                    $response['message'] = 'Semester/Sec. Available For This Faculty/Program/Class.';
                } else {
                    $response['message'] = 'No Any Semester Assign on This Faculty/Program/Class.';
                }
            } else {
                $response['message'] = 'No Any Semester Assign on This Faculty/Program/Class.';
            }
        } else {
            $response['message'] = 'Invalid request!!';
        }
        return response()->json($response);
    }

    public function transfer(Request $request)
    {
        $data = [];
        if($request->all()) {
            $data['student'] = Student::select('id', 'reg_no', 'reg_date', 'first_name', 'middle_name', 'last_name',
                'faculty', 'semester','academic_status', 'status')
                ->where(function ($query) use ($request) {
                    $this->commonStudentFilterCondition($query, $request);
                })
                ->get();
        }

        $data['faculties'] = $this->activeFaculties();
        $data['batch'] = $this->activeBatch();
        $data['academic_status'] = $this->activeStudentAcademicStatus();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.transfer.index'), compact('data'));
    }

    public function transfering(Request $request)
    {
        if($request->faculty > 0 && $request->semester_select > 0){
            if ($request->has('chkIds')) {
                $studIds = $request->get('chkIds');
                $students = Student::whereIn('id',$studIds)->get();
                //filter student & update new data & send alert if active
                /*Here We prepare message, contact number and email ids*/
                $emailIds = [];
                $contactNumbers = [];
                $alert = AlertSetting::select('sms','email','subject','template')->where('event','=','StudentTransfer')->first();

                $filteredStudent  = $students->filter(function ($student, $key) use ($alert, $emailIds, $contactNumbers, $request){
                    $updateData = [
                        'faculty' => $request->get('faculty'),
                        'semester' => $request->get('semester_select'),
                        'academic_status' => $request->get('student_status'),
                        'batch' => $request->get('student_batch')
                    ];
                    $updateStudent = $student->update($updateData);
                    $semesterName = $this->getSemesterById($request->get('semester_select'));

                    if(!$alert) {

                    }else{
                        //send alert
                        //Dear {{first_name}}, We would like to inform you are successfully transferred to {{semester}}. Thank You.
                        $subject = $alert->subject;
                        $message = $alert->template;
                        $message = str_replace('{{first_name}}', $student->first_name, $message );
                        $message = str_replace('{{semester}}', $semesterName, $message );
                        $emailIds[] = $student->email;
                        $contactNumbers[] = $this->getStudentMobileNumber($student->id);

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
                });
            }else {
                $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
                return redirect()->route($this->base_route.'.transfer');
            }

            $faculty_title = ViewHelper::getFacultyTitle( $request->faculty );
            $semester_title = ViewHelper::getSemesterTitle( $request->semester_select );
            $request->session()->flash($this->message_success, 'Students Transfer on : '.$faculty_title.' / '.$semester_title.' Successfully.');

        }else{
            $request->session()->flash($this->message_success, 'Please Choose Faculty and Semester Correctly.');
        }
        return redirect()->route($this->base_route.'.transfer');
    }

    public function academicInfoHtml(Request $request)
    {
        $semester = Semester::find($request->semester_id);
        if (!$semester) {
            return response()->json(json_encode([
                'html' => ''
            ]));
        }

        $academicInfoRow = $semester->programNeedAcademicLevel()->Active()->get();

        $response['html'] = view($this->view_path.'.registration.includes.forms.academic_tr', [
            'academicInfoRow' => $academicInfoRow
        ])->render();

        return response()->json(json_encode($response));
    }

    public function deleteAcademicInfo(Request $request, $id)
    {
        if (!$row = AcademicInfo::find($id)) return parent::invalidRequest();

        $row->delete();

        $request->session()->flash($this->message_success,'Academic Info Deleted Successfully.');
        return redirect()->back();
    }

    /*guardian's info link*/
    public function guardianNameAutocomplete(Request $request)
    {
        if ($request->has('q')) {

            $guardians = GuardianDetail::select('id','guardian_first_name',
                'guardian_middle_name', 'guardian_last_name', 'guardian_eligibility',
                'guardian_occupation', 'guardian_office', 'guardian_office_number',
                'guardian_residence_number', 'guardian_mobile_1', 'guardian_mobile_2',
                'guardian_email', 'guardian_relation', 'guardian_address','guardian_image')
                ->where('guardian_first_name', 'like', '%'.$request->get('q').'%')
                ->orWhere('guardian_middle_name', 'like', '%'.$request->get('q').'%')
                ->orWhere('guardian_last_name', 'like', '%'.$request->get('q').'%')
                ->orWhere('guardian_mobile_1', 'like', '%'.$request->get('q').'%')
                ->orWhere('guardian_mobile_2', 'like', '%'.$request->get('q').'%')
                ->orWhere('guardian_email', 'like', '%'.$request->get('q').'%')
                ->get();

            $response = [];
            foreach ($guardians as $guardian) {
                $guardianName = $guardian->guardian_first_name.' '.$guardian->guardian_middle_name.' '.$guardian->guardian_last_name;
                $response[] = ['id' => $guardian->id, 'text' => $guardianName.'- [MoNo.-'.$guardian->guardian_mobile_1.'] - [Email-'.$guardian->guardian_email.']'];
            }

            return json_encode($response);
        }

        abort(501);
    }

    public function guardianInfo(Request $request)
    {
        $response = [];
        $response['error'] = true;
        if ($request->has('id')) {
            if ($guardianInfo = GuardianDetail::find($request->get('id'))) {
                $response['error'] = false;
                $response['html'] = view($this->view_path.'.registration..includes.forms.pull-guardian-info', [
                    'guardianInfo' => $guardianInfo,
                ])->render();
                $response['message'] = 'Operation successful.';

            } else{
                $response['message'] = $request->get('subject_id').'Invalid request!!';
            }
        } else{
            $response['message'] = $request->get('id').'Invalid request!!';
        }

        return response()->json(json_encode($response));
    }

    /*bulk import*/
    public function importStudent()
    {
        return view(parent::loadDataToView($this->view_path.'.registration.import'));
    }

    public function handleImportStudent(Request $request)
    {
       // dd($request->all());
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

            //Faculty id
             $faculty = Faculty::where('faculty',$row['faculty'])->first();
             if($faculty){
                 $facultyId = $faculty->id;
             }else{
                 $facultyId = "";
             }

            //Semester id
             $semester = Semester::where('semester',$row['semester'])->first();
             if($semester){
                 $semesterId = $semester->id;
             }else{
                 $semesterId = "";
             }
            //Academic Status
            $academicStatus = StudentStatus::where('id',$row['academic_status'])->first();
            $academicStatus;
            if($academicStatus){
                $academicStatusId = $academicStatus->id;
            }else{
                $academicStatusId = 1; //1 for new Admission
            }

            //RegNo Generator Start
                if(!isset($row['reg_no']) || $row['reg_no'] == "" ){
					$oldStudent = Student::where('faculty',$row['faculty'])->orderBy('id', 'DESC')->first();
                    if (!$oldStudent){
                        $sn = 1;
                    }else{
                        $oldReg = intval(substr($oldStudent->reg_no,-4));
                        $sn = $oldReg + 1;
                    }

                   // dd($oldStudent);
                    $sn = substr("00000{$sn}", - 4);
                    $year = intval(substr(Year::where('active_status','=',1)->first()->title,-2));
                    $faculty = Faculty::find(intval($row['faculty']));
                    $facultyCode = $faculty->faculty_code;
                    //$regNum = $faculty.'-'.$year.'-'.$sn;
                    $regNum = $facultyCode.$year.$sn;
                    $row['reg_no'] = $regNum;
                }else{
                    //$row['reg_no'] = '';
                }

            //reg generator End
            //Student validation
            /*Manage Date's Format*/

            $validator = Validator::make($row, [
                'reg_no'                        => 'required  | max:25 | unique:students,reg_no',
                'reg_date'                      => 'required | date',
                'faculty'                       => 'required | exists:faculties,id',
                'semester'                      => 'required | exists:semesters,id',
                'first_name'                    => 'required | max:50',
                'last_name'                     => 'max:25',
                'date_of_birth'                 => 'required | date',
                'gender'                        => 'required',
                'religion'                      => 'max:15',
                'caste'                         => 'max:15',
                'nationality'                   => 'required | max:25',
                'address'                       => 'required | max:100',
                'state'                         => 'required | max:25',
                'country'                       => 'required | max:25',
                'temp_address'                  => 'max:100',
                'temp_state'                    => 'max:25',
                'temp_country'                  => 'max:25',
                /*'email'                         => 'required | max:100 | unique:students,email',*/
                'extra_info'                    => 'max:100',
                'home_phone'                    => 'max:25',
                'mobile_1'                      => 'max:25',
                'mobile_2'                      => 'max:25',
                'grandfather_first_name'        => 'max:25',
                'grandfather_middle_name'       => 'max:25',
                'grandfather_last_name'         => 'max:25',
                'father_first_name'             => 'max:25',
                'father_middle_name'            => 'max:25',
                'father_last_name'              => 'max:25',
                'father_eligibility'            => 'max:50',
                'father_occupation'             => 'max:50',
                'father_office'                 => 'max:100',
                'father_office_number'          => 'max:25',
                'father_residence_number'       => 'max:25',
                'father_mobile_1'               => 'max:25',
                'father_mobile_2'               => 'max:25',
                'father_email'                  => 'max:100',
                'mother_first_name'             => 'max:25',
                'mother_middle_name'            => 'max:25',
                'mother_last_name'              => 'max:25',
                'mother_eligibility'            => 'max:50',
                'mother_occupation'             => 'max:50',
                'mother_office'                 => 'max:100',
                'mother_office_number'          => 'max:25',
                'mother_residence_number'       => 'max:25',
                'mother_mobile_1'               => 'max:25',
                'mother_mobile_2'               => 'max:25',
                'mother_email'                  => 'max:100',
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator);
            }


            $reg_date = Carbon::parse($row['reg_date'])->format('Y-m-d');
            $date_of_birth = Carbon::parse($row['date_of_birth'])->format('Y-m-d');

            //Student import
            $student = Student::create([
                "reg_no"                => $row['reg_no'],
                "reg_date"              => $reg_date,
                "university_reg"        => $row['university_reg'],
                "faculty"               => $row['faculty'],
                "semester"              => $row['semester'],
                "academic_status"       => $row['academic_status'],
                "batch"                 => $row['batch'],
                "first_name"            => $row['first_name'],
                "middle_name"           => $row['middle_name'],
                "last_name"             => $row['last_name'],
                "date_of_birth"         => $date_of_birth,
                "gender"                => $row['gender'],
                "blood_group"           => $row['blood_group'],
                "religion"              => $row['religion'],
                "caste"                 => $row['caste'],
                "nationality"           => $row['nationality'],
                "national_id_1"         => $row['national_id_1'],
                "national_id_2"         => $row['national_id_2'],
                "national_id_3"         => $row['national_id_3'],
                "national_id_4"         => $row['national_id_4'],
                "mother_tongue"         => $row['mother_tongue'],
                "email"                 => $row['email'],
                "extra_info"            => $row['extra_info'],
                'created_by'            => auth()->user()->id
            ]);

            if($student){
                //address info
                Addressinfo::create([
                    "students_id"           => $student->id,
                    "home_phone"            => $row['home_phone'],
                    "mobile_1"              => $row['mobile_1'],
                    "mobile_2"              => $row['mobile_2'],
                    "address"               => $row['address'],
                    "state"                 => $row['state'],
                    "country"               => $row['country'],
                    "temp_address"          => $row['temp_address'],
                    "temp_state"            => $row['temp_state'],
                    "temp_country"          => $row['temp_country'],
                    'created_by'            => auth()->user()->id
                ]);

                //parents detail
                ParentDetail::create([
                    "students_id"               => $student->id,
                    "home_phone"                => $row['home_phone'],
                    "grandfather_first_name"    => $row['grandfather_first_name'],
                    "grandfather_middle_name"   => $row['grandfather_middle_name'],
                    "grandfather_last_name"     => $row['grandfather_last_name'],
                    "father_first_name"         => $row['father_first_name'],
                    "father_middle_name"        => $row['father_middle_name'],
                    "father_last_name"          => $row['father_last_name'],
                    "father_eligibility"        => $row['father_eligibility'],
                    "father_occupation"         => $row['father_occupation'],
                    "father_office"             => $row['father_office'],
                    "father_office_number"      => $row['father_office_number'],
                    "father_residence_number"   => $row['father_residence_number'],
                    "father_mobile_1"           => $row['father_mobile_1'],
                    "father_mobile_2"           => $row['father_mobile_2'],
                    "father_email"              => $row['father_email'],
                    "mother_first_name"         => $row['mother_first_name'],
                    "mother_middle_name"        => $row['mother_middle_name'],
                    "mother_last_name"          => $row['mother_last_name'],
                    "mother_eligibility"        => $row['mother_eligibility'],
                    "mother_occupation"         => $row['mother_occupation'],
                    "mother_office"             => $row['mother_office'],
                    "mother_office_number"      => $row['mother_office_number'],
                    "mother_residence_number"   => $row['mother_residence_number'],
                    "mother_mobile_1"           => $row['mother_mobile_1'],
                    "mother_mobile_2"           => $row['mother_mobile_2'],
                    "mother_email"              => $row['mother_email'],
                    'created_by'                => auth()->user()->id
                ]);

                //Guardian detail
                $guardian = GuardianDetail::create([
                    "students_id"                 => $student->id,
                    "guardian_first_name"         => $row['guardian_first_name'],
                    "guardian_middle_name"        => $row['guardian_middle_name'],
                    "guardian_last_name"          => $row['guardian_last_name'],
                    "guardian_eligibility"        => $row['guardian_eligibility'],
                    "guardian_occupation"         => $row['guardian_occupation'],
                    "guardian_office"             => $row['guardian_office'],
                    "guardian_office_number"      => $row['guardian_office_number'],
                    "guardian_residence_number"   => $row['guardian_residence_number'],
                    "guardian_mobile_1"           => $row['guardian_mobile_1'],
                    "guardian_mobile_2"           => $row['guardian_mobile_2'],
                    "guardian_email"              => $row['guardian_email'],
                    "guardian_relation"           => $row['guardian_relation'],
                    "guardian_address"            => $row['guardian_address'],
                    'created_by'                  => auth()->user()->id
                ]);

                /*student guardian link*/

                if($guardian){
                    StudentGuardian::create([
                        'students_id' => $student->id,
                        'guardians_id' => $guardian->id,
                    ]);
                }

                $this->createExtraInfo($student->id);
                $this->createScholarship($student->id);
                $this->createPlacement($student->id);

            }

        }

        $request->session()->flash($this->message_success,'Students imported Successfully');
        return redirect()->route($this->base_route);
    }

    public function createExtraInfo($StudentId)
    {
        $studentExtraInfo = new \App\Models\StudentExtraInfo();
        $studentExtraInfo->created_by = 1;
        $studentExtraInfo->students_id = $StudentId;
        $studentExtraInfo->status = 1;
        $studentExtraInfo->save();
    }

    public function createScholarship($StudentId)
    {
        //student_scholarships
        //'students_id', 'scholarships_id', 'scholarship_application_id', 'scholarship_user_name',
        // 'scholarship_password', 'status'
        $studentScholarship = new \App\Models\StudentScholarship();
        $studentScholarship->created_by = 1;
        $studentScholarship->students_id = $StudentId;
        $studentScholarship->status = 1;
        $studentScholarship->save();
    }

    public function createPlacement($StudentId)
    {
        $studentPlacement = new \App\Models\StudentPlacement();
        $studentPlacement->created_by = 1;
        $studentPlacement->students_id = $StudentId;
        $studentPlacement->status = 1;
        $studentPlacement->save();
    }

    /*student name auto complete*/
    public function studentNameAutocomplete(Request $request)
    {
        if ($request->has('q')) {
            $students = Student::select('students.id', 'students.reg_no', 'students.university_reg',
                'students.first_name', 'students.middle_name', 'students.last_name', 'students.semester','students.email',
                'ai.mobile_1', 'ai.mobile_2')
                ->where('students.reg_no', 'like', '%'.$request->get('q').'%')
                ->orWhere('students.university_reg', 'like', '%'.$request->get('q').'%')
                ->orWhere('students.first_name', 'like', '%'.$request->get('q').'%')
                ->orWhere('students.middle_name', 'like', '%'.$request->get('q').'%')
                ->orWhere('students.last_name', 'like', '%'.$request->get('q').'%')
                ->orWhere('students.email', 'like', '%'.$request->get('q').'%')
                ->orWhere('ai.mobile_1', 'like', '%'.$request->get('q').'%')
                ->orWhere('ai.mobile_2', 'like', '%'.$request->get('q').'%')
                ->join('addressinfos as ai','ai.students_id','=','students.id')
                ->get();

            $response = [];
            foreach ($students as $student) {
                $response[] = ['id' => $student->id, 'text' => $student->reg_no.' | '.$student->first_name.' '.$student->middle_name.' '.$student->last_name.' | '.$this->getSemesterById($student->semester).' | '.$student->mobile_1];
            }

            return json_encode($response);
        }

        abort(501);
    }

    public function bulkAction(Request $request)
    {
        $bulkActions = [
            'export-excel', 'generate-pdf', 'active', 'in-active', 'delete', 'delete-all',
            'print-certificate', 'create-reset-login', 'create-reset-library-member'
        ];

        //dd($request->all(), $bulkActions);

        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), $bulkActions)) {
            if ($request->get('bulk_action') == 'export-excel') {
                $dataIds = collect($request->get('chkIds'))->map(fn($id) => decrypt($id))->toArray();
                return Excel::download(new StudentsExport($dataIds), 'Student_Detail_' . Carbon::now()->format('d-m-Y') . '.xlsx');
            }

            if ($request->get('bulk_action') == 'print-certificate') {
                $template = CertificateTemplate::find($request->get('certificate-template'));

                /*Dedicated pixel-perfect ID card design (front+back, 54x86mm)*/
                if ($template && in_array(strtoupper(trim($template->certificate)), ['ID CARD', 'STUDENT ID CARD'])) {
                    return app(\App\Http\Controllers\PrintOut\CertificatePrintController::class)
                        ->idCardPrint($request, $template);
                }

                $request->merge(['certificate' => $template->id]);
                $data = $this->printCertificate($request);
                $certificateLayoutPath = $template ? $this->CertificateViewPath($template->certificate) : 'print.certificate.generate';

                return $data ? view($certificateLayoutPath, compact('data'))
                            : redirect()->route($this->base_route)->with($this->message_warning, 'Certificate Not Issued of Selected Student. Please Issue First');
            }

            if ($request->get('bulk_action') == 'delete-all') {
                if (!Auth::check() || !Auth::user()->hasRole('super-admin')) {
                    return redirect()->route($this->base_route)
                        ->with($this->message_warning, 'Only super-admin can delete all students.');
                }

                @set_time_limit(0);
                @ini_set('max_execution_time', '0');

                $deleted = 0;
                $chunkSize = 500;

                DB::disableQueryLog();

                do {
                    $ids = Student::query()
                        ->orderBy('id')
                        ->limit($chunkSize)
                        ->pluck('id');

                    if ($ids->isEmpty()) {
                        break;
                    }

                    $deleted += Student::whereIn('id', $ids)->delete();
                } while (true);

                return redirect()->route($this->base_route)
                    ->with($this->message_success, $deleted . ' Students deleted successfully.');
            }

            if (!$request->has('chkIds')) {
                return redirect()->route($this->base_route)->with($this->message_warning, 'Please, check at least one ' . $this->panel);
            }

            // Initialize counters for success/failure
            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];

            // Process all other bulk actions
                foreach ($request->get('chkIds') as $row_id) {
                    $id = decrypt($row_id);
                    $student = Student::find($id);
                    if (!$student) continue;

                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            $student->status = $request->get('bulk_action');
                            $student->save();
                            $successCount++;
                            break;

                        case 'delete':
                            $student->delete();
                            $successCount++;
                            break;

                        case 'generate-pdf':
                            $this->generatePdf($id);
                            $successCount++;
                            break;

                        case 'create-reset-login':
                            $this->createResetLogin($row_id);
                            $successCount++;
                            break;

                        case 'create-reset-library-member':
                            $this->createLibraryMember($row_id);
                            $successCount++;
                            break;                   
                        
                    }
                }

            // Prepare success message
            $successMessages = [
                'active' => 'Activated',
                'in-active' => 'Inactivated',
                'create-reset-login' => 'Student Login Created/Reset Successfully.',
                'create-reset-library-member' => 'Library Member Created Successfully.',
            ];

            $message = $successMessages[$request->get('bulk_action')] ?? 'Action performed successfully.';

            // Add error details if any
            if ($errorCount > 0) {
                $message .= ". Issues: " . implode('; ', array_slice($errorMessages, 0, 5));
                if (count($errorMessages) > 5) {
                    $message .= " and " . (count($errorMessages) - 5) . " more";
                }
            }

            return redirect()->route($this->base_route)
                ->with($errorCount > 0 ? $this->message_warning : $this->message_success, $message);
        }

        return parent::invalidRequest();
    }

    


    public function printCertificate($request)
    {
        if($request->get('chkIds')){
                foreach ($request->get('chkIds') as $studentId){
                    $studIds [] = decrypt($studentId);
                }
            }



            //$students = Student::/*select('id')->*/whereIn('id',$studIds)->get();
            $students = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
                'students.faculty','students.semester','students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
                'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group',  'students.religion',
                'students.caste','students.nationality',
                'students.mother_tongue', 'students.email', 'students.extra_info', 'students.status',
                'ai.address', 'ai.state', 'ai.country', 'ai.temp_address', 'ai.temp_state', 'ai.temp_country', 'ai.home_phone',
                'ai.mobile_1', 'ai.mobile_2', 'pd.grandfather_first_name',
                'pd.grandfather_middle_name', 'pd.grandfather_last_name', 'pd.father_first_name', 'pd.father_middle_name',
                'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_office', 'pd.father_office_number',
                'pd.father_residence_number', 'pd.father_mobile_1', 'pd.father_mobile_2', 'pd.father_email', 'pd.mother_first_name',
                'pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation', 'pd.mother_office',
                'pd.mother_office_number', 'pd.mother_residence_number', 'pd.mother_mobile_1', 'pd.mother_mobile_2', 'pd.mother_email',
                'gd.id as guardian_id', 'gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
                'gd.guardian_eligibility', 'gd.guardian_occupation', 'gd.guardian_office', 'gd.guardian_office_number', 'gd.guardian_residence_number',
                'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email', 'gd.guardian_relation', 'gd.guardian_address',
                'students.student_image','students.student_signature', 'pd.father_image', 'pd.mother_image', 'gd.guardian_image')
                ->whereIn('students.id',$studIds)
                ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
                ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                ->leftJoin('student_guardians as sg', 'sg.students_id','=','students.id')
                ->leftJoin('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
                ->get();

            if ($students->count()>0){
                $data['certificate_template'] = $certificateTemplate = CertificateTemplate::find($request->certificate);
                $filteredStudent = $students->filter(function ($student, $key) use($certificateTemplate) {
                    $issuedStatus = $this->studentCertificateIssuedStatus($student,$certificateTemplate->certificate);
                    if(isset($issuedStatus)){
                        $data = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
                            'students.faculty','students.semester','students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
                            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group',  'students.religion',
                            'students.caste','students.nationality',
                            'students.mother_tongue', 'students.email', 'students.extra_info', 'students.status',
                            'ai.address', 'ai.state', 'ai.country', 'ai.temp_address', 'ai.temp_state', 'ai.temp_country', 'ai.home_phone',
                            'ai.mobile_1', 'ai.mobile_2', 'pd.grandfather_first_name',
                            'pd.grandfather_middle_name', 'pd.grandfather_last_name', 'pd.father_first_name', 'pd.father_middle_name',
                            'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_office', 'pd.father_office_number',
                            'pd.father_residence_number', 'pd.father_mobile_1', 'pd.father_mobile_2', 'pd.father_email', 'pd.mother_first_name',
                            'pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation', 'pd.mother_office',
                            'pd.mother_office_number', 'pd.mother_residence_number', 'pd.mother_mobile_1', 'pd.mother_mobile_2', 'pd.mother_email',
                            'gd.id as guardian_id', 'gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
                            'gd.guardian_eligibility', 'gd.guardian_occupation', 'gd.guardian_office', 'gd.guardian_office_number', 'gd.guardian_residence_number',
                            'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email', 'gd.guardian_relation', 'gd.guardian_address',
                            'students.student_image','students.student_signature', 'pd.father_image', 'pd.mother_image', 'gd.guardian_image')
                            ->where('students.id','=',$student->id)
                            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
                            ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                            ->join('student_guardians as sg', 'sg.students_id','=','students.id')
                            ->join('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
                            ->first();

                        if($certificateTemplate->student_photo == 1){
                            $student->student_image = $data->student_image?$data->student_image:"";
                        }else{
                            $student->student_image = "";
                        }
                        $student->student_signature = $data->student_signature?$data->student_signature:"";

                        $student->certificate = $certificateTemplate->certificate;
                        $certificateTemplate = $this->studentCertificateTextReplace($student,$certificateTemplate);
                        $student->certificate_template = $certificateTemplate;

                        return $student;
                    }else{
                        session()->flash($this->message_warning, 'Certificate Not Issued. First Your Need To Issue From Certificate Manager.');
                        return redirect(route($this->base_route));
                    }
                });
                $data['student'] = $filteredStudent;
                return $data;
            }else{
                $data['student'] = [];
            }
    }

    public function createResetLogin($id)
    {
        $student = Student::find($id);
        if($student && $student->email !=""){
            //create login access
            $name = isset($student->middle_name)?$student->first_name.' '.$student->middle_name.' '.$student->last_name:$student->first_name.' '.$student->last_name;
            $rolesId = Role::where('name','student')->first()->id;
            $password = str_random(8);
            $emailIds = $student->email;
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
                    'hook_id' => $student->id,
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
                $message = 'Dear ' . $name.', Your login detail created in our system.<br> Please Login with  <br> email: '.$emailIds.' <br>Password: '. $password ;
            }

            if($user){
                $emailSuccess = $this->sendEmail($emailIds, $subject, $message);
            }
        }

        return back();
    }

    public function createLibraryMember($id)
    {
        $data = Student::find($id);
        if($data){
            $currentMember = LibraryMember::where(['user_type' => 1, 'member_id' => $data->id])->orderBy('id','desc')->first();
            if(!$currentMember){
                $member = LibraryMember::create([
                                                    'member_id' => $data->id,
                                                    'user_type' => 1,
                                                    'created_by' => auth()->user()->id,
                                                ]);
                $memberId = $member->member_id;
                $userType = $member->user_type;
                $this->sendLibraryRegistrationAlert($memberId,$userType);

            }
        }

        return back();
    }

    public function generatePdf($id)
    {
        $id = decrypt($id);
        $data = [];
        $data['student'] = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
            'students.faculty','students.semester','students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group',  'students.religion', 'students.caste',
            'students.nationality',
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
            'students.admission_date','students.admission_no', 'students.admission_course_fee','students.national_id_1','students.national_id_2',
            'students.special_category','students.weightage_claim',
//           'students.national_id_type', 'students.national_id_number', 'students.extra_id_card_type','students.extra_id_card_number',
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

        $data['generalSetting'] = $this->getGeneralSetting();
        $data['academicInfos'] = $data['student']->academicInfo()->orderBy('sorting_order','asc')->get();
        $data['appliedSubjects'] = $data['student']->majorSubject()->get();
        $data['annexure'] = $data['student']->annexure()->get();
        $data['url'] = URL::current();
       // return view(parent::loadDataToView('print.student.registration'), compact('data'));

        $pdf = Pdf::loadView('pdf.test', compact('data'));
        return $pdf->download($data['student']->reg_no.'_'.$data['student']->first_name.'.pdf');

        //        $pdf = Pdf::loadView(parent::loadDataToView('pdf.student.registration'), compact('data'));
        //        dd($pdf);
        //        return $pdf->download('student.pdf');

        //        $id = decrypt($id);
        //        $data = [];
        //        $data['student'] = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
        //            'students.faculty','students.semester','students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
        //            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group',  'students.religion', 'students.caste','students.nationality',
        //            'students.mother_tongue', 'students.email', 'students.extra_info', 'students.status', 'pd.grandfather_first_name',
        //            'pd.grandfather_middle_name', 'pd.grandfather_last_name', 'pd.father_first_name', 'pd.father_middle_name',
        //            'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_office', 'pd.father_office_number',
        //            'pd.father_residence_number', 'pd.father_mobile_1', 'pd.father_mobile_2', 'pd.father_email', 'pd.mother_first_name',
        //            'pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation', 'pd.mother_office',
        //            'pd.mother_office_number', 'pd.mother_residence_number', 'pd.mother_mobile_1', 'pd.mother_mobile_2', 'pd.mother_email',
        //            'ai.address', 'ai.state', 'ai.country', 'ai.temp_address', 'ai.temp_state', 'ai.temp_country', 'ai.home_phone',
        //            'ai.mobile_1', 'ai.mobile_2', 'gd.id as guardian_id', 'gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
        //            'gd.guardian_eligibility', 'gd.guardian_occupation', 'gd.guardian_office', 'gd.guardian_office_number', 'gd.guardian_residence_number',
        //            'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email', 'gd.guardian_relation', 'gd.guardian_address',
        //            'students.student_image','students.student_signature', 'pd.father_image', 'pd.mother_image', 'gd.guardian_image')
        //            ->where('students.id','=',$id)
        //            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
        //            ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
        //            ->join('student_guardians as sg', 'sg.students_id','=','students.id')
        //            ->join('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
        //            ->first();
        //
        //        if (!$data['student']){
        //            request()->session()->flash($this->message_warning, "Not a Valid Student");
        //            return redirect()->route($this->base_route);
        //        }
        //
        //        //$data['academicInfos'] = $data['student']->academicInfo()->orderBy('sorting_order','asc')->get();
        //        $data['generalSetting'] = $this->getGeneralSetting();
        //        $data['academicInfos'] = $data['student']->academicInfo()->orderBy('sorting_order','asc')->get();
        //        $data['appliedSubjects'] = $data['student']->majorSubject()->get();
        //        $data['annexure'] = $data['student']->annexure()->get();

        //dd($data['student']->toArray());

        //    use Barryvdh\DomPDF\Facade\Pdf;
        //
        //    $pdf = Pdf::loadView('pdf.invoice', $data);
        //    return $pdf->download('invoice.pdf');
        //dd($data);
//return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));


        //        $pdf = PDF::loadView('student.generate-pdf.index');
        //        dd($pdf);
        //        return $pdf->download('registration_detail.pdf');

        //        $data = [];
//        $pdf = Pdf::loadView('pdf.test', $data);
//        return $pdf->download('invoice.pdf');
        //$pdf = PDF::loadView('student.generate-pdf.index', array('data' => $data));
        //return $pdf->download($data['student']->reg_num.'_registration_detail.pdf');

        //dd('here');
        // retreive all records from db
       // $data = Employee::all();
        // share data to view
        //view()->share('employee',$data);
        //$pdf = PDF::loadView('pdf_view', $data);
        // download PDF file with download method
        //return $pdf->download('pdf_file.pdf');

        /* $pdf = App::make('dompdf.wrapper');
         $pdf->loadHTML('<h1>Test</h1>');
         return $pdf->stream();*/

        /*
         * $pdf = \App::make('dompdf.wrapper');
            $pdf->loadView('invoices.credit_note', compact('credit_notes'));
            $pdf->save(public_path($path));
         * */

        /*
         * $report =  Report::findOrFail($report);
        $pdf = PDF::loadView('Home.report')->setPaper('a4', 'portrait');
        $fileName = $report->issue_number;
        return $pdf->stream($fileName.'.pdf');
             * */

        /*      $pdf = App::make('dompdf.wrapper');
                $pdf->loadView('student.generate-pdf.index', array('data' => $data));
                $pdf->download('invoice.pdf');
        */
        //resources/views/student/generate-pdf/index.blade.php
    }

    
    /* ========== Regular (daily) attendance summary + rows ========== */
    public function attendanceSummary($id, Request $request)
    {
        try {
            $studentId = ctype_digit((string)$id) ? (int)$id : decrypt($id);
            [$start, $end] = $this->resolveRangeFromRequest($request, $studentId);

            if (!Schema::hasTable('attendances')) {
                return response()->json(['ok'=>false,'msg'=>'Attendance table not found.']);
            }

            $codeById = $this->attendanceStatusCodeMap();

            $rowsDb = DB::table('attendances')
                ->select(['date','attendance_status_id','check_in_at','check_out_at'])
                ->where('attendable_type', $this->studentMorphType())
                ->where('attendable_id', $studentId)
                ->whereDate('date', '>=', $start->toDateString())
                ->whereDate('date', '<=', $end->toDateString())
                ->orderBy('date','asc')
                ->get();

            $totals = ['P'=>0,'A'=>0,'L'=>0,'HL'=>0,'LV'=>0,'EL'=>0,'H'=>0];
            $totalMinutes = 0;
            $rows = [];

            foreach ($rowsDb as $r) {
                $code = $this->normalizeStatusCode($codeById[$r->attendance_status_id] ?? '');
                if (isset($totals[$code])) $totals[$code]++;

                $in  = $r->check_in_at  ? Carbon::parse($r->check_in_at)  : null;
                $out = $r->check_out_at ? Carbon::parse($r->check_out_at) : null;
                $mins = ($in && $out) ? max(0, $in->diffInMinutes($out, false)) : 0;
                $totalMinutes += $mins;

                $rows[] = [
                    'date'      => Carbon::parse($r->date)->toDateString(),
                    'status'    => $code,
                    'in'        => $in  ? $in->format('H:i')  : '',
                    'out'       => $out ? $out->format('H:i') : '',
                    'minutes'   => $mins,
                    'hours_hm'  => $this->formatMinutesHM($mins),
                ];
            }

            // Optional percent for UI (P + L + EL) / (P + A + L + LV + EL)
            $den = ($totals['P']+$totals['A']+$totals['L']+$totals['LV']+$totals['EL']) ?: 1;
            $pct = round((($totals['P']+$totals['L']+$totals['EL']) / $den) * 100);

            return response()->json([
                'ok'    => true,
                'range' => ['start'=>$start->toDateString(),'end'=>$end->toDateString()],
                'totals'=> $totals,
                'total_minutes' => $totalMinutes,
                'total_hm'      => $this->formatMinutesHM($totalMinutes),
                'percent'       => $pct,
                'legend'        => $this->attStatusPalette(),
                'colors'        => collect($this->attStatusPalette())->mapWithKeys(fn($l)=>[$l['code']=>$l['color']])->all(),
                'rows'          => $rows,
            ]);

        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'msg'=>'Failed to load attendance.']);
        }
    }

    /* ========== Subject-wise attendance summary + rows ========== */
    public function subjectAttendanceSummary($id, Request $request)
    {
        try {
            $studentId = ctype_digit((string)$id) ? (int)$id : decrypt($id);
            [$start, $end] = $this->resolveRangeFromRequest($request, $studentId);

            if (!Schema::hasTable('subject_attendances')) {
                return response()->json(['ok'=>false,'msg'=>'Subject attendance table not found.']);
            }

            $subjectId = $request->get('subject_id');
            $codeById  = $this->attendanceStatusCodeMap();

            $q = DB::table('subject_attendances')
                ->select(['date','attendance_status_id','in_at','out_at','subject_id'])
                ->where('student_id', $studentId)
                ->whereDate('date', '>=', $start->toDateString())
                ->whereDate('date', '<=', $end->toDateString())
                ->orderBy('date','asc');

            if (!empty($subjectId)) {
                $q->where('subject_id', (int)$subjectId);
            }

            $rowsDb = $q->get();

            $totals = ['P'=>0,'A'=>0,'L'=>0,'HL'=>0,'LV'=>0,'EL'=>0,'H'=>0];
            $totalMinutes = 0;
            $rows = [];

            foreach ($rowsDb as $r) {
                $code = $this->normalizeStatusCode($codeById[$r->attendance_status_id] ?? '');
                if (isset($totals[$code])) $totals[$code]++;

                $in  = $r->in_at  ? Carbon::parse($r->in_at)  : null;
                $out = $r->out_at ? Carbon::parse($r->out_at) : null;
                $mins = ($in && $out) ? max(0, $in->diffInMinutes($out, false)) : 0;
                $totalMinutes += $mins;

                $rows[] = [
                    'date'      => Carbon::parse($r->date)->toDateString(),
                    'status'    => $code,
                    'in'        => $in  ? $in->format('H:i')  : '',
                    'out'       => $out ? $out->format('H:i') : '',
                    'minutes'   => $mins,
                    'hours_hm'  => $this->formatMinutesHM($mins),
                ];
            }

            $den = ($totals['P']+$totals['A']+$totals['L']+$totals['LV']+$totals['EL']) ?: 1;
            $pct = round((($totals['P']+$totals['L']+$totals['EL']) / $den) * 100);

            return response()->json([
                'ok'    => true,
                'range' => ['start'=>$start->toDateString(),'end'=>$end->toDateString()],
                'totals'=> $totals,
                'total_minutes' => $totalMinutes,
                'total_hm'      => $this->formatMinutesHM($totalMinutes),
                'percent'       => $pct,
                'legend'        => $this->attStatusPalette(),
                'colors'        => collect($this->attStatusPalette())->mapWithKeys(fn($l)=>[$l['code']=>$l['color']])->all(),
                'rows'          => $rows,
            ]);

        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'msg'=>'Failed to load subject attendance.']);
        }
    }

    private function resolveRangeFromRequest(Request $request, ?int $studentId = null): array
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
                if ($e->lt($s)) [$s,$e] = [$e,$s];
                break;
            case 'lifetime':
                $min = $studentId ? $this->earliestAttendanceDateFor($studentId) : null;
                $s = ($min ?: Carbon::create(2000,1,1))->startOfDay();
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

    private function earliestAttendanceDateFor(int $studentId): ?Carbon
    {
        $min1 = null;
        if (Schema::hasTable('attendances')) {
            $d1 = DB::table('attendances')
                ->where('attendable_type', $this->studentMorphType())
                ->where('attendable_id', $studentId)
                ->min('date');
            if ($d1) $min1 = Carbon::parse($d1);
        }
        $min2 = null;
        if (Schema::hasTable('subject_attendances')) {
            $d2 = DB::table('subject_attendances')
                ->where('student_id', $studentId)
                ->min('date');
            if ($d2) $min2 = Carbon::parse($d2);
        }
        if ($min1 && $min2) return $min1->lt($min2) ? $min1 : $min2;
        return $min1 ?: $min2;
    }

    private function attendanceStatusCodeMap(): array
    {
        if (!Schema::hasTable('attendance_statuses')) return [];
        $rows = DB::table('attendance_statuses')->select(['id','code'])->get();
        $map = [];
        foreach ($rows as $r) $map[$r->id] = strtoupper(trim((string)$r->code));
        return $map;
    }

    private function attStatusPalette(): array
    {
        if (Schema::hasTable('attendance_statuses')) {
            $rows = DB::table('attendance_statuses')
                ->select(['code','label','color','order'])
                ->orderByRaw('COALESCE(`order`, 999)')
                ->get();
            $out=[];
            foreach($rows as $r){
                $out[]=[
                    'code'=>strtoupper((string)$r->code),
                    'label'=>$r->label ?: strtoupper((string)$r->code),
                    'color'=>$r->color ?: '#111827',
                ];
            }
            // ensure common codes
            foreach ([['H','Holiday','#0EA5E9'],['LV','Leave','#7C3AED'],['EL','Excused','#3B82F6'],['L','Late','#F59E0B'],['A','Absent','#EF4444'],['P','Present','#10B981']] as $e) {
                if (!collect($out)->pluck('code')->contains($e[0])) $out[]=['code'=>$e[0],'label'=>$e[1],'color'=>$e[2]];
            }
            return $out;
        }
        return [
            ['code'=>'P','label'=>'Present','color'=>'#10B981'],
            ['code'=>'A','label'=>'Absent','color'=>'#EF4444'],
            ['code'=>'L','label'=>'Late','color'=>'#F59E0B'],
            ['code'=>'EL','label'=>'Excused','color'=>'#3B82F6'],
            ['code'=>'LV','label'=>'Leave','color'=>'#7C3AED'],
            ['code'=>'H','label'=>'Holiday','color'=>'#0EA5E9'],
        ];
    }

    private function normalizeStatusCode($v)
    {
        $t=strtoupper(trim((string)$v));
        if($t==='P'||str_contains($t,'PRESENT')) return 'P';
        if($t==='A'||str_contains($t,'ABSENT'))  return 'A';
        if($t==='L'||str_contains($t,'LATE'))    return 'L';
        if($t==='HL') return 'HL';
        if($t==='LV'||str_contains($t,'LEAVE'))  return 'LV';
        if($t==='EL'||str_contains($t,'EXCUSED'))return 'EL';
        if($t==='H'||str_contains($t,'HOLIDAY')) return 'H';
        if($t==='1'||$t==='TRUE') return 'P';
        if($t==='0'||$t==='FALSE') return 'A';
        return '';
    }

    private function formatMinutesHM(int $mins): string
    {
        $h = intdiv(max(0,$mins),60);
        $m = max(0,$mins) % 60;
        return $h.'h '.str_pad((string)$m,2,'0',STR_PAD_LEFT).'m';
    }

    private function studentMorphType(): string
    {
        return class_exists('App\\Models\\Student') ? 'App\\Models\\Student'
            : (class_exists('App\\Student') ? 'App\\Student' : 'student');
    }

    private function attStudentMorphTypes(): array
    {
        $c = ['App\\Models\\Student','App\\Student','student','Student'];
        if (class_exists('App\\Models\\Student')) array_unshift($c,'App\\Models\\Student');
        elseif (class_exists('App\\Student'))     array_unshift($c,'App\\Student');
        return array_values(array_unique($c));
    }

    private function subjectOptionsForStudent(int $studentId): array
    {
        if (Schema::hasTable('subject_attendances') && Schema::hasTable('subjects')) {
            $ids = DB::table('subject_attendances')
                ->where('student_id', $studentId)
                ->distinct()->pluck('subject_id')->filter()->values();
            if ($ids->count()) {
                return DB::table('subjects')
                    ->whereIn('id', $ids)
                    ->orderBy('title')
                    ->pluck('title','id')
                    ->toArray();
            }
        }
        if (Schema::hasTable('students') && Schema::hasTable('semester_subject') && Schema::hasTable('subjects')) {
            $stu = DB::table('students')->select(['semester'])->where('id',$studentId)->first();
            if ($stu && $stu->semester) {
                return DB::table('subjects as s')
                    ->join('semester_subject as ss','s.id','=','ss.subject_id')
                    ->where('ss.semester_id', $stu->semester)
                    ->orderBy('s.title')
                    ->pluck('s.title','s.id')
                    ->toArray();
            }
        }
        return [];
    }


}
