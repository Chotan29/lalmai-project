<?php

/*
 * Mr. Umesh Kumar Yadav
 */

namespace App\Http\Controllers\Student;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Student\PublicRegistration\AddValidation;
use App\Models\AcademicInfo;
use App\Models\Addressinfo;
use App\Models\AlertSetting;

use App\Models\Annexure;
use App\Models\Faculty;
use App\Models\GeneralSetting;
use App\Models\GuardianDetail;
use App\Models\OnlineRegistrationProgram;
use App\Models\OnlineRegistrationSetting;
use App\Models\OnlinePayment;
use App\Models\ParentDetail;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAddressinfo;
use App\Models\StudentAnnexure;
use App\Models\StudentBatch;
use App\Models\StudentDegree;
use App\Models\StudentExtraInfo;
use App\Models\StudentGuardian;
use App\Models\StudentParent;

use App\Models\StudentPlacement;
use App\Models\StudentScholarship;
use App\Models\StudentSubject;
use App\Models\Year;
use App\Models\Role;
use App\Traits\SmsEmailScope;
use App\Traits\UserScope;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Image, URL;
use ViewHelper;

class OnlineRegistrationController extends CollegeBaseController
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
        $data = [];
        $data['registration_setting'] = new OnlineRegistrationSetting();

        $data['show_registration_form'] = $this->checkRegistrationStatus();

        if($data['show_registration_form']){
            $data['blank_ins'] = new Student();

            //check Registration settings
            $data['registration_setting'] = OnlineRegistrationSetting::where('status', 'active')
                ->whereDate('start_date', '<=', Carbon::now())
                ->whereDate('end_date', '>=', Carbon::now())
                ->first() ?? OnlineRegistrationSetting::first() ?? new OnlineRegistrationSetting();

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
                // Fallback to active faculties so the public form does not render an empty dropdown.
                $allFaculty = Faculty::Active()->orderBy('faculty')->pluck('faculty','id')->toArray();
                $data['faculties'] = array_prepend($allFaculty,'Select Faculty/Program/Class','');
            }

            $studentBatch = StudentBatch::select('id', 'title')->Active()->pluck('title','id')->toArray();
            $data['batch'] = $studentBatch;
            $data['state'] = $this->activeState();
            $data['annexures'] = Annexure::select('id', 'title')->Active()->get();


        }else{
            request()->session()->flash($this->message_warning, 'Public Registration Closed.');
        }

        return view(parent::loadDataToView($this->view_path.'.register'), compact('data'));

    }

    /**
     * Check payment requirements and prepare for payment
     */
    public function preparePayment(Request $request)
    {
        try {
            $studentType = $request->input('student_type');
            
            if (!in_array($studentType, ['new', 'old'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid student type'
                ], 422);
            }

            $registrationSetting = OnlineRegistrationSetting::where('status', 'active')
                ->orWhere('status', 1)
                ->first() ?? OnlineRegistrationSetting::first();

            if (!$registrationSetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration is not configured'
                ], 422);
            }

            // Check if student type is enabled
            if ($studentType === 'new' && !$registrationSetting->new_student_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'New student registration is not currently enabled'
                ], 422);
            }

            if ($studentType === 'old' && !$registrationSetting->old_student_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Old student registration is not currently enabled'
                ], 422);
            }

            // Get the applicable fee
            $fee = $studentType === 'new' 
                ? $registrationSetting->new_student_registration_fee 
                : $registrationSetting->old_student_registration_fee;

            if ($registrationSetting->payment_required && $fee <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration fee not configured. Please contact admin.'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'payment_required' => $registrationSetting->payment_required,
                'amount' => $fee,
                'payment_method_options' => ['ssl', 'ucb']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }

    }

    public function register(AddValidation $request)
    {

        $selectedSubjects = $request->input('subject', []);
        if (!is_array($selectedSubjects)) {
            $selectedSubjects = [];
        }

        $selectedSubjects = array_values(array_unique(array_filter($selectedSubjects, function ($subjectId) {
            return (int) $subjectId > 0;
        })));

        $subjectLimitErrors = [];

        $semesterId = (int) $request->input('semester');
        if ($semesterId > 0 && count($selectedSubjects) > 0) {
            $semester = Semester::find($semesterId);

            if ($semester) {
                /*Per-semester dynamic limits set by admin (fallback 6 compulsory + 1 optional)*/
                $maxCompulsory = ($semester->max_compulsory_count === null || $semester->max_compulsory_count === '')
                    ? 6 : (int) $semester->max_compulsory_count;
                $maxOptional = ($semester->max_optional_count === null || $semester->max_optional_count === '')
                    ? 1 : (int) $semester->max_optional_count;
                $maxTotal = $maxCompulsory + $maxOptional;

                if (count($selectedSubjects) > $maxTotal) {
                    $subjectLimitErrors[] = 'You can select maximum ' . $maxTotal . ' subjects.';
                }

                $subjectRows = $semester->subjects()
                    ->select('subjects.id', 'subjects.sub_type')
                    ->whereIn('subjects.id', $selectedSubjects)
                    ->get();

                if ($subjectRows->count() !== count($selectedSubjects)) {
                    $subjectLimitErrors[] = 'One or more selected subjects are invalid for this semester.';
                } else {
                    $optionalCount = $subjectRows->filter(function ($subject) {
                        return strtolower(trim((string) $subject->sub_type)) === 'optional';
                    })->count();

                    $compulsoryCount = $subjectRows->count() - $optionalCount;

                    if ($optionalCount > $maxOptional) {
                        $subjectLimitErrors[] = 'You can select maximum ' . $maxOptional . ' optional subject(s).';
                    }

                    if ($compulsoryCount > $maxCompulsory) {
                        $subjectLimitErrors[] = 'You can select maximum ' . $maxCompulsory . ' compulsory subjects.';
                    }
                }
            }
        }

        if (!empty($subjectLimitErrors)) {
            $errorMessage = $subjectLimitErrors[0];

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => [
                        'subject[]' => $subjectLimitErrors,
                    ],
                ], 422);
            }

            return back()->withErrors(['subject' => $errorMessage])->withInput();
        }

        //subject validation if choose
        /*if($request->subject){
            $subjectSelected = count($request->subject);
            $allowSubject = $request->max_subjects_count;
            $errMessage = '';
            if($subjectSelected > $allowSubject){
                $errMessage = "You are not eligible to choose greater than ".$allowSubject." subjects.";

            }
            if($subjectSelected < $allowSubject){
                $errMessage = "Please Choose at least ".$allowSubject." subjects.";
            }


            //check choose subject and semester major different
            $errReturn = ['subject.max' => $errMessage];
            $validator = Validator::make($request->all(),
                [
                    'subject' => 'array|min:\'.$allowSubject|max:\'.$allowSubject',
                ],
                [
                    'subject.max' => $errMessage
                ]
            );

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator);
            }

        }*/

        /*Old (returning) students already have a college-issued Student ID and
          Registration Number, so they type their own instead of getting an
          auto-generated one. New students keep the auto-generated number.*/
        $isOldStudent = strtolower(trim((string) $request->input('student_type'))) === 'old';
        $typedStudentId = trim((string) $request->input('old_student_id'));
        $typedUniversityReg = trim((string) $request->input('old_university_reg'));

        if ($isOldStudent) {
            $idErrors = [];

            if ($typedStudentId === '') {
                $idErrors['old_student_id'] = ['Please enter your Student ID.'];
            } elseif (Student::where('reg_no', $typedStudentId)->orWhere('reg_no', Str::slug($typedStudentId))->exists()) {
                $idErrors['old_student_id'] = ['This Student ID is already registered. Please check your ID or contact the college office.'];
            }

            if ($typedUniversityReg !== '' && Student::where('university_reg', $typedUniversityReg)->exists()) {
                $idErrors['old_university_reg'] = ['This Registration Number is already registered.'];
            }

            if (!empty($idErrors)) {
                $firstError = reset($idErrors)[0];

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $firstError,
                        'errors' => $idErrors,
                    ], 422);
                }

                return back()->withErrors($idErrors)->withInput();
            }
        }

        $batchModel = StudentBatch::find($request->batch);
        $batchTitle = $batchModel->title;
        $regPrefix = Str::slug($batchTitle);

        $maxSerial = 0;
        $existingRegNumbers = Student::where('batch', $request->batch)
            ->whereNotNull('reg_no')
            ->pluck('reg_no');

        foreach ($existingRegNumbers as $existingRegNo) {
            if (strpos($existingRegNo, $regPrefix) !== 0) {
                continue;
            }

            if (preg_match('/(\d{1,4})$/', $existingRegNo, $matches)) {
                $maxSerial = max($maxSerial, (int) $matches[1]);
            }
        }

        $sn = $maxSerial + 1;

        do {
            $serial = substr("00000{$sn}", -4);
            $regNum = $batchTitle.'/'.$serial;
            $sluggedRegNum = Str::slug($regNum);
            $sn++;
        } while (Student::where('reg_no', $sluggedRegNum)->exists());

        /*Old student: keep the Student ID exactly as typed (no slug/serial)*/
        if ($isOldStudent && $typedStudentId !== '') {
            $regNum = $typedStudentId;
        }

        $request->request->add(['reg_no' => $regNum]);
        //reg generator End

        //upload student image
        if ($request->hasFile('student_main_image')){
            $student_image = $request->file('student_main_image');
            $student_image_name = Str::slug($batchTitle.'-'.$serial).'.'.$student_image->getClientOriginalExtension();
            $student_image->move(public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'studentProfile'.DIRECTORY_SEPARATOR, $student_image_name);
        }else{
            $student_image_name = "";
        }

        $year = Year::Active()->first()->title;
        //$regNum = $year.$request->faculty.$oldStudent->id;
        $request->request->add(['created_by' => 0]);
        /*Old student's typed ID is stored as-is; new student's generated one is slugged*/
        $request->request->add(['reg_no' => ($isOldStudent && $typedStudentId !== '') ? $typedStudentId : Str::slug($regNum)]);
        if ($isOldStudent && $typedUniversityReg !== '') {
            $request->request->add(['university_reg' => $typedUniversityReg]);
        }
        $request->request->add(['reg_date' => Carbon::today()->toDateString()]);
        //$request->request->add(['semester' => $semSec?$semSec:0]);
        $request->request->add(['academic_status' => 8]);
        $request->request->add(['student_image' => $student_image_name]);
        $request->request->add(['status' => 'in-active']);

       $student = Student::create($request->all());

        //if student created then store related data
        if($student){
            //create user
            $rolesId = Role::where('name','student')->first()->id;
            $password = str_random(8);
            $emailIds = $student->email;
            //check user is exist or not

            $existUser = User::where('email',$emailIds)->first();
            $name = isset($student->middle_name)?$student->first_name.' '.$student->middle_name.' '.$student->last_name:$student->first_name.' '.$student->last_name;

            if($existUser){
                $existUser->update([
                    'password' => Hash::make($password)
                ]);
                $user = $existUser; // Bug fix: update() returns bool, must keep the model instance

                //$subject = 'Login Detail Reset';
                //$message = 'Dear ' . $name.', Your login detail Updated in our system. Please Login with  <br> email: '.$emailIds.' And Password: '. $password ;
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

                //$subject = 'Login Detail Create';
                //$message = 'Dear ' . $name.', Your login detail created in our system. Please Login with  <br> email: '.$emailIds.' And Password: '. $password ;
            }

            $visitorUserId = $user->id ;

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
                        'created_by' => $visitorUserId,
                    ]);
                }
            }

            // Annexure List
            if ($request->has('annexure')) {
                foreach ($request->get('annexure') as $key => $annexure) {
                    StudentAnnexure::create([
                        'students_id' => $student->id,
                        'annexures_id' => $annexure,
                        'created_by' => $visitorUserId,
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
                        'created_by' => $visitorUserId,
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
                    'created_by' => $visitorUserId,
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
                        'created_by' => $visitorUserId,
                    ]
                );
            }else{
                $studentScholarship = new \App\Models\StudentScholarship();
                $studentScholarship->created_by = 1;
                $studentScholarship->students_id = $student->id;
                $studentScholarship->status = 1;
                $studentScholarship->save();
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
                        'created_by' => $visitorUserId,
                    ]
                );
            }else{
                $studentPlacement = new \App\Models\StudentPlacement();
                $studentPlacement->created_by = 1;
                $studentPlacement->students_id = $student->id;
                $studentPlacement->status = 1;
                $studentPlacement->save();
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
                        'created_by'                =>  $visitorUserId,
                    ]);
                }
            }

                        if ($request->has('degrees_id')) {
                //studentDegrees()
                $stuentDegrees = [];
                foreach ($request->get('degrees_id') as $key => $degree) {
//                    StudentDegree::create([
//                        'students_id'               =>  $student->id,
//                        'degrees_id'                 =>  $degree,
//                        'obtained_mark'             =>  $request->get('obtained_mark')[$key],
//                        'total_marks'               =>  $request->get('total_marks')[$key],
//                        'receive_in_college_date'   =>  $request->get('receive_in_college_date')[$key],
//                        'issue_date'                =>  $request->get('issue_date')[$key],
//                        'created_by'                =>  $visitorUserId,
//                    ]);
                    $stuentDegrees[$key] = [
                        'students_id'               =>  $student->id,
                        'degrees_id'                 =>  $degree,
                        'obtained_mark'             =>  $request->get('obtained_mark')[$key],
                        'total_marks'               =>  $request->get('total_marks')[$key],
                        'receive_in_college_date'   =>  $request->get('receive_in_college_date')[$key],
                        'issue_date'                =>  $request->get('issue_date')[$key],
                        'created_by'                =>  $visitorUserId,
                    ];
                }

                //studentDegrees

                $student->studentDegrees()->sync($stuentDegrees);
            }


            $PublishMessage = 'Dear, ' . $name.' Thank you for register with us. Your registration number is.'.$regNum;

            /*SMS & Email Alert — skip gracefully if AlertSetting not configured */
            $alert = AlertSetting::where('event','=','StudentRegistration')->first();
            if($alert) {
                $subject = $alert->subject;

                //SMS Template
                $message = $this->textReplace($student, $alert->template);
                //Email Template
                $emailMessage = $this->textReplace($student, $alert->email_template);
                //Module Specific Variable
                $message = str_replace('{{password}}', $password, $message);
                $emailMessage = str_replace('{{password}}', $password, $emailMessage);
                //Dear {{first_name}}, you are successfully registered in our institution with RegNo.{{reg_no}}. Your login password is {{password}} , Thank You.
                $notifEmails = [];
                $contactNumbers= [];
                $notifEmails[] = $student->email;
                $contactNumbers[] = $request->mobile_1;

                /*Now Send SMS On First Mobile Number*/
                if ($alert->sms == 1) {
                    $contactNumbers = $this->contactFilter($contactNumbers);
                    $smssuccess = $this->sendSMS($contactNumbers, $message);
                }

                /*Now Send Email With Subject*/
                if ($alert->email == 1) {
                    $notifEmails = $this->emailFilter($notifEmails);
                    $emailSuccess = $this->sendEmail($notifEmails, $subject, $emailMessage);
                }
            }
            //end sms email

            $request->session()->flash($this->message_success, $PublishMessage);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $PublishMessage,
                    'redirect_url' => route('online-registration.print', encrypt($student->id)),
                ]);
            }

            return redirect()->route('online-registration.print',encrypt($student->id));
        }else{
            $request->session()->flash($this->message_warning, $this->panel. ' Getting Err. While submitting.');

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $this->panel . ' Getting Err. While submitting.',
                ], 422);
            }

            return back();
        }
/*
       if($student){
           $request->request->add(['students_id' => $student->id]);
           $addressinfo = Addressinfo::create($request->all());
           $parentdetail = ParentDetail::create($request->all());

           $guardian = GuardianDetail::create($request->all());
           $studentGuardian = StudentGuardian::create([
               'students_id' => $student->id,
               'guardians_id' => $guardian->id,
           ]);

           //Major Subjects
           if ($student && $request->has('subject')) {
               $subjects = [];
               foreach ($request->get('subject') as $key => $subject) {
                   $subjects[$subject] = [
                       'students_id' => $student->id,
                       'subject_id' => $subject,
                   ];
               }

               $student->studentSubjects()->sync($subjects);
           }

           // Annexure List
           if ($student && $request->has('annexure')) {
               foreach ($request->get('annexure') as $key => $annexure) {
                   StudentAnnexure::create([
                       'students_id' => $student->id,
                       'annexures_id' => $annexure,
                       'created_by' => 0,
                   ]);
               }
           }


           // Academic Info
           if ($student && $request->has('board')) {
               foreach ($request->get('board') as $key => $board) {
                   AcademicInfo::create([
                       'students_id' => $student->id,
                       'institution' => $request->get('institution')[$key],
                       'board' => $board,
                       'pass_year' => $request->get('pass_year')[$key],
                       'major_subjects' => $request->get('major_subjects')[$key],
                       'mark_obtained' => $request->get('mark_obtained')[$key],
                       'maximum_mark' => $request->get('maximum_mark')[$key],
                       'percentage' => $request->get('percentage')[$key],
                       'created_by' => 0,
                   ]);
               }
           }


           //create login access
           $name = isset($request->middle_name)?$request->first_name.' '.$request->middle_name.' '.$request->last_name:$request->first_name.' '.$request->last_name;


           $PublishMessage = 'Dear, ' . $name.' Thank you for register with us. Your registration number is.'.$regNum;
           //Dear {{first_name}}, you are successfully registered in our institution with RegNo.{{reg_no}}. Your login password is {{password}} ,Thank You.

           //SMS & Email Alert
           $alert = AlertSetting::select('sms','email','subject','template')->where('event','=','StudentRegistration')->first();
           if(!$alert){

           }else{
               //Dear {{first_name}}, you are successfully registered in our institution with RegNo.{{reg_no}}. Your login with password: {{password}} ,Thank You.
               $subject = $alert->subject;
               $message = $alert->template;
               $message = str_replace('{{first_name}}', $student->first_name, $message );
               $message = str_replace('{{reg_no}}', $student->reg_no, $message );
               $message = str_replace('{{password}}', isset($password)?$password:'', $message );
               $emailIds[] = $student->email;
               $contactNumbers[] = $addressinfo->mobile_1;

               //Now Send SMS On First Mobile Number
               if($alert->sms == 1){
                   $contactNumbers = $this->contactFilter($contactNumbers);
                   $smssuccess = $this->sendSMS($contactNumbers,$message);
               }

               //Now Send Email With Subject
               if($alert->email == 1){
                   $emailIds = $this->emailFilter($emailIds);
                   //sending email
                   $emailSuccess = $this->sendEmail($emailIds, $subject, $message);
               }
           }

           //end sms email
           $request->session()->flash($this->message_success, $PublishMessage);
           return redirect()->route('online-registration.print',encrypt($student->id));

       }else{
           $request->session()->flash($this->message_warning, 'Some thing is Wrong. Please Check all Required Fields.');
           return back();
       }
*/
    }

    public function checkRegistrationStatus()
    {
        $generalSetting = GeneralSetting::select('public_registration')->first();

        if (isset($generalSetting) && $generalSetting->public_registration == 1) {
            return true;
        }

        $registrationSetting = OnlineRegistrationSetting::where('status', 'active')
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereDate('end_date', '>=', Carbon::now())
            ->first();

        return $registrationSetting ? true : false;
    }

    public function findSubject(Request $request)
    {
        if (Config::get('edufirmconfig.student.online_registration.tabs.general_info.subject_info') == 1) {
            $semester = Semester::find($request->get('semester_id'));

            if (!$semester) {
                return response()->json([
                    'error' => 'Invalid semester selected. Please choose the semester again.'
                ]);
            }

            $subjects = $semester->subjects()
                ->select('subjects.id as subject_id', 'subjects.title as subject_title', 'subjects.sub_type as subject_type')
                ->orderBy('subjects.title')
                ->get();

            if ($subjects->isEmpty()) {
                return response()->json([
                    'error' => 'No subject is assigned to this semester yet. Please contact the administrator.'
                ]);
            }

            /*Per-semester dynamic limits set by admin. Fallbacks keep old behaviour
              (6 compulsory + 1 optional = 7) when a semester has no value yet.*/
            $maxCompulsory = $semester->max_compulsory_count;
            $maxOptional = $semester->max_optional_count;
            $maxCompulsory = ($maxCompulsory === null || $maxCompulsory === '') ? 6 : (int) $maxCompulsory;
            $maxOptional = ($maxOptional === null || $maxOptional === '') ? 1 : (int) $maxOptional;

            $totalMax = $maxCompulsory + $maxOptional;
            $numOfSubject = $semester->major_subject_count ?: $totalMax;

            return response()->json([
                'subjects' => view($this->view_path . '.includes.forms.fetch-subjects', [
                    'subjects' => $subjects,
                    'numOfSubject' => $numOfSubject,
                    'maxCompulsory' => $maxCompulsory,
                    'maxOptional' => $maxOptional,
                    'totalMax' => $totalMax,
                ])->render(),
                'success' => 'Please, Select Maximum ' . $totalMax . ' Subjects in Subject List.'
            ]);
        }

        return response()->json([
            'error' => 'Subject selection is not enabled right now.'
        ]);

    }

    public function findRegistration(Request $request)
    {
        $data = [];

        //dd($request->all());

//        $searchParameter = [
//            "faculty" =>    $request->faculty,
//            "semester" =>   $request->semester,
//            "batch" =>  $request->batch,
//            //"reg_no" => $request->reg_no,
//            "national_id_1" => $request->national_id_1,
//            "date_of_birth" =>  $request->date_of_birth,
//        ];

        if($request->all()) {
            $data['student'] = Student::select('id', 'national_id_1','reg_no', 'reg_date',
                'faculty', 'semester', 'batch', 'first_name', 'middle_name', 'last_name')
                ->where(function ($query) use ($request) {
                    if ($request->has('reg_no') && $request->get('reg_no') !=null) {
                        $query->where('reg_no', 'like', '%' . $request->reg_no . '%');
                        $this->filter_query['reg_no'] = $request->reg_no;
                    }

                    if ($request->has('date_of_birth')) {
                        $query->where('date_of_birth', '=', $request->get('date_of_birth'));
                        $this->filter_query['national_id_1'] = $request->date_of_birth;
                    }

                    if ($request->get('faculty') > 0) {
                        $query->where('faculty', '=', $request->faculty);
                        $this->filter_query['faculty'] = $request->faculty;
                    }

                    if ($request->get('semester_select') > 0) {
                        $query->where('semester', '=',  $request->semester_select);
                        $this->filter_query['semester'] = $request->semester_select;
                    }

                    if ($request->get('batch') > 0) {
                        $query->where('batch', '=',  $request->batch);
                        $this->filter_query['batch'] = $request->batch;
                    }

                    if ($request->has('national_id_1')) {
                        $query->where('national_id_1', 'like', '%' . $request->national_id_1 . '%');
                        $this->filter_query['national_id_1'] = $request->national_id_1;
                    }

                    if ($request->has('date_of_birth')) {
                        $query->where('date_of_birth', '=', $request->get('date_of_birth'));
                        $this->filter_query['national_id_1'] = $request->date_of_birth;
                    }



                })
                //->where($searchParameter)
                ->get();

            if(isset($data['student']) && $data['student']->count() >0){

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
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            \Log::error('[REGISTRATION_PAYMENT_DEBUG] Decryption failed for print route', ['error' => $e->getMessage()]);
            request()->session()->flash($this->message_warning, "Invalid student reference. Please try again.");
            return redirect()->route($this->base_route);
        }

        \Log::info('[REGISTRATION_PAYMENT_DEBUG] print() called with student_id', ['decrypted_id' => $id, 'id_type' => gettype($id)]);

        // First, try to find the student with a simple query to verify it exists
        $studentExists = Student::where('students.id', '=', $id)->first();
        if (!$studentExists) {
            \Log::error('[REGISTRATION_PAYMENT_DEBUG] Student not found in print()', ['student_id' => $id, 'searched_table' => 'students', 'id_type' => gettype($id)]);
            request()->session()->flash($this->message_warning, "Not a Valid Student (ID: {$id}). This student record may have been deleted or is not yet created. Please verify the student exists in the system.");
            return redirect()->route($this->base_route);
        }

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
//            'students.national_id_type', 'students.national_id_number', 'students.extra_id_card_type','students.extra_id_card_number',
        )
            ->where('students.id','=',$id)
            ->leftJoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->leftJoin('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->leftJoin('student_guardians as sg', 'sg.students_id','=','students.id')
            ->leftJoin('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
            ->first();

        if (!$data['student']){
            \Log::error('[REGISTRATION_PAYMENT_DEBUG] Query returned no student despite existence check', ['student_id' => $id]);
            request()->session()->flash($this->message_warning, "Not a Valid Student");
            return redirect()->route($this->base_route);
        }

        $data['academicInfos'] = $data['student']->academicInfo()->orderBy('sorting_order','asc')->get();
        $data['appliedSubjects'] = $data['student']->majorSubject()->get();
        $data['annexure'] = $data['student']->annexure()->get();
        $data['onlinePayment'] = OnlinePayment::where('students_id', $id)->latest()->first();
        $data['url'] = URL::current();
        return view(parent::loadDataToView('print.student.registration'), compact('data'));
    }

    public function pdf($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            \Log::error('[REGISTRATION_PAYMENT_DEBUG] Decryption failed for pdf route', ['error' => $e->getMessage()]);
            request()->session()->flash($this->message_warning, "Invalid student reference. Please try again.");
            return redirect()->route($this->base_route);
        }

        \Log::info('[REGISTRATION_PAYMENT_DEBUG] pdf() called with student_id', ['decrypted_id' => $id, 'id_type' => gettype($id)]);

        // First, try to find the student with a simple query to verify it exists
        $studentExists = Student::where('students.id', '=', $id)->first();
        if (!$studentExists) {
            \Log::error('[REGISTRATION_PAYMENT_DEBUG] Student not found in pdf()', ['student_id' => $id, 'searched_table' => 'students', 'id_type' => gettype($id)]);
            request()->session()->flash($this->message_warning, "Not a Valid Student (ID: {$id}). This student record may have been deleted or is not yet created. Please verify the student exists in the system.");
            return redirect()->route($this->base_route);
        }

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
            ->leftJoin('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->leftJoin('addressinfos as ai', 'ai.students_id', '=', 'students.id')
            ->leftJoin('student_guardians as sg', 'sg.students_id','=','students.id')
            ->leftJoin('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
            ->first();


        if (!$data['student']){
            \Log::error('[REGISTRATION_PAYMENT_DEBUG] Query returned no student in pdf() despite existence check', ['student_id' => $id]);
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
        return $pdf->download('student.pdf');
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
                    ->orderBy('semesters.semester')
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

    public function checkEmail(Request $request)
    {
        $email = strtolower(trim((string) $request->input('email')));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid email address.'
            ], 422);
        }

        $exists = Student::whereRaw('LOWER(email) = ?', [$email])->exists();

        return response()->json([
            'success' => true,
            'exists' => $exists,
            'message' => $exists
                ? 'This email address already exists in registration records.'
                : 'Email is available.'
        ]);
    }

}
