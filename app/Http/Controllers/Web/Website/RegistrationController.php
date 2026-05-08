<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\Web\Website;

use App\Http\Requests\Web\Registration\FindValidation;
use App\Models\AlertSetting;
use App\Models\GeneralSetting;
use App\Models\Web\WebContactUsSetting;
use App\Models\Web\WebRegistration;
use App\Models\Web\WebRegistrationAcademicQualification;
use App\Models\Web\WebRegistrationProgramme;
use App\Models\Web\WebRegistrationSetting;
use App\Models\Web\WebRegistrationWorkExperience;
use App\Traits\SmsEmailScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use PDF;

class RegistrationController extends WebsiteBaseController
{

    protected $view_path = 'web.website.online-registration';
    protected $folder_path;
    protected $folder_name = 'registration';
    use SmsEmailScope;

    public function __construct()
    {

        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;

    }

    public function index(Request $request)
    {
        $this->middleware('page-status');

        $data = [];
        $data['programmes'] = WebRegistrationProgramme::where('status',1)->orderBy('title')->pluck('title','id')->toArray();

        $data['reg_setting'] = $registrationSetting = WebRegistrationSetting::first();
        if(!$data['reg_setting'])
            return redirect()->route('web.home');

        $data['periodValid'] = (Carbon::today() > $registrationSetting->start_date && Carbon::today() <= $registrationSetting->end_date)?true:false;

        if(!$data['periodValid'])
            $request->session()->flash($this->message_warning, 'Registration Time Not Valid. Apply Between :'.$registrationSetting->start_date.' To '.$registrationSetting->end_date);

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

    public function store(Request $request)
    {
        //make unique registration number with function
        $regNo = $this->randomNum('REG',6);

        if ($request->hasFile('student_main_image')){
            $student_image = $request->file('student_main_image');
            $student_image_name = $regNo.'_studentImage.'.$student_image->getClientOriginalExtension();
            $student_image->move($this->folder_path, $student_image_name);
        }else{
            $student_image_name = "";
        }

        if ($request->hasFile('student_signature_main_image')){
            $student_signature_image = $request->file('student_signature_main_image');
            $student_signature_image_name = $regNo.'_signature.'.$student_signature_image->getClientOriginalExtension();
            $student_signature_image->move($this->folder_path, $student_signature_image_name);
        }else{
            $student_signature_image_name = "";
        }

        if ($request->hasFile('guardian_signature_main_image')){
            $guardian_signature_image = $request->file('guardian_signature_main_image');
            $guardian_signature_image_name = $regNo.'_guardian_signature.'.$guardian_signature_image->getClientOriginalExtension();
            $guardian_signature_image->move($this->folder_path, $student_signature_image_name);
        }else{
            $guardian_signature_image_name = "";
        }

        $request->request->add(['reg_no' => $regNo]);
        $request->request->add(['reg_date' => Carbon::parse($request->reg_date)->format('Y-m-d')]);
        $request->request->add(['date_of_birth' => Carbon::parse($request->date_of_birth)->format('Y-m-d')]);
        $request->request->add(['student_image' => $student_image_name]);
        $request->request->add(['student_signature' => $student_signature_image_name]);
        $request->request->add(['guardian_signature' => $guardian_signature_image_name]);

        $registration = WebRegistration::create($request->all());

        /*Academic Qualification Start*/
            if ($registration && $request->has('examination')) {
            foreach ($request->get('examination') as $key => $exam) {
                WebRegistrationAcademicQualification::create([
                    'registrations_id' => $registration->id,
                    'examination' => $exam,
                    'institution' => $request->get('institution')[$key],
                    'board_university' => $request->get('board_university')[$key],
                    'year_of_pass' => $request->get('year_of_pass')[$key],
                    'percentage_grade' => $request->get('percentage_grade')[$key],
                ]);
            }
        }
        /*Academic Qualification End*/

        /*Work Experience Start*/
            if ($registration && $request->has('experience_info')) {
            foreach ($request->get('experience_info') as $key => $experience) {
                WebRegistrationWorkExperience::create([
                    'registrations_id' => $registration->id,
                    'experience_info' => $experience,
                ]);
            }
        }
        /*Work Experience End*/

        if($registration){
            /*SMS & Email Alert*/
            $alert = AlertSetting::select('sms','email','subject','template')->where('event','=','Registration')->first();
            if($alert){
                //Dear {{first_name}}, you are successfully registered in our institution with RegNo.{{reg_no}}. Thank You.
                $subject = $alert->subject;
                $message = $alert->template;
                $message = str_replace('{{name}}', $registration->name, $message );
                $message = str_replace('{{reg_no}}', $registration->reg_no, $message );

                /*Now Send SMS On First Mobile Number*/
                if($alert->sms == 1 && isset($registration->cell_1)){
                    $contactNumbers[] = $registration->cell_1;

                    $contactNumbers = $this->contactFilter($contactNumbers);
                    $smssuccess = $this->sendSMS($contactNumbers,$message);
                }

                /*Now Send Email With Subject*/
                if($alert->email == 1 && isset($registration->email)){
                    $emailIds[] = $registration->email;
                    $emailIds = $this->emailFilter($emailIds);
                    /*sending email*/
                    $emailSuccess = $this->sendEmail($emailIds, $subject, $message);
                }
            }
            //end sms email
        }

        $request->session()->flash($this->message_success, 'Registration Submit Successfully. Your Reg. No. is -'.$regNo);
        return redirect()->back();
    }

    public function findApplication()
    {
        $data = [];
        $data['programmes'] = WebRegistrationProgramme::where('status',1)->orderBy('title')->pluck('title','id')->toArray();
        return view(parent::WebsiteViewHelper($this->view_path.'.find-registration'), compact('data'));
    }

    public function printApplication(FindValidation $request)
    {

        $data = [];
        $data['row'] = WebRegistration::select('id', 'web_registration_programmes_id','reg_no','reg_date','name','sex','date_of_birth',
            'religion','caste','nationality','state','mother_tongue','blood_group','medicine_info','disease_info','guardian_name',
            'guardian_relation','guardian_occupation','guardian_annual_income','address','tel','cell_1','cell_2','email',
            'mailing_address','mailing_tel','mailing_cell_1','mailing_cell_2','mailing_email','student_image','student_signature',
            'guardian_signature','status')
            ->where(function ($query) use ($request) {

                if ($request->has('web_registration_programmes_id')) {
                    $query->where('web_registration_programmes_id', '=', $request->web_registration_programmes_id);
                    $this->filter_query['web_registration_programmes_id'] = $request->web_registration_programmes_id;
                }

                if ($request->has('reg_no') && $request->has('reg_no') !="") {
                    $query->where('reg_no', '=', $request->reg_no);
                    $this->filter_query['reg_no'] = $request->reg_no;
                }

                if ($request->has('name')) {
                    $query->where('name', 'like', '%'.$request->name.'%');
                    $this->filter_query['name'] = $request->name;
                }

                if ($request->has('date_of_birth')) {
                    $query->where('date_of_birth', '=', Carbon::parse($request->date_of_birth)->format('Y-m-d'));
                    $this->filter_query['date_of_birth'] = Carbon::parse($request->date_of_birth)->format('Y-m-d');
                }
            })
            ->first();


        if (!$data['row']){
            $request->session()->flash($this->message_warning,'Application Not Match. Please Enter Correct Info or Register New Application');
            return redirect()->back();
        }

        $data['academicInfos'] = $data['row']->academicInfo()->get();
        $data['workExperiences'] = $data['row']->workExperience()->get();
        $data['general_setting'] = GeneralSetting::select('institute', 'salogan','copyright', 'address','phone','email','website', 'email', 'logo',
            'facebook', 'twitter', 'linkedIn', 'youtube', 'instagram', 'whatsApp', 'skype', 'pinterest','wordpress')->first();
        $data['reg_setting'] = WebRegistrationSetting::first();
        $data['base_route'] = $this->view_path.'.view';

        //$pdf = PDF::loadView(parent::WebsiteViewHelper($this->view_path.'.view'), compact('data'));
        //return $pdf->download($data['row']->reg_no.'_Registration.pdf');
        //return $pdf->stream();

        return view(parent::WebsiteViewHelper($this->view_path.'.view'), compact('data'));
    }

    public function academicInfoHtml()
    {
        $response['html'] = view('web.admin.registration.includes.academic_tr')->render();
        return response()->json(json_encode($response));
    }

    public function workExperienceHtml()
    {
        $response['html'] = view('web.admin.registration.includes.workexperience_tr')->render();
        return response()->json(json_encode($response));
    }
}