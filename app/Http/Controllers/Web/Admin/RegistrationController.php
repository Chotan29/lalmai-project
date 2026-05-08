<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Controllers\Web\Admin;
use App\Models\AlertSetting;
use App\Models\GeneralSetting;
use App\Models\Web\WebRegistration;
use App\Models\Web\WebRegistrationAcademicQualification;
use App\Models\Web\WebRegistrationProgramme;
use App\Models\Web\WebRegistrationSetting;
use App\Models\Web\WebRegistrationWorkExperience;
use App\Traits\SmsEmailScope;
use Carbon\Carbon;

use App\Http\Requests\Web\Registration\AddValidation;
use App\Http\Requests\Web\Registration\EditValidation;
use App\Models\Registration;
use Illuminate\Http\Request;
use View, AppHelper, Image, URL;

class RegistrationController extends AdminBaseController
{
    protected $base_route = 'web.admin.registration';
    protected $view_path = 'web.admin.registration';
    protected $panel = 'Registration';
    protected $folder_path;
    protected $folder_name = 'registration';
    protected $filter_query = [];

    use SmsEmailScope;

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        $data['rows'] = WebRegistration::select('id', 'web_registration_programmes_id','reg_no','reg_date','name','sex','date_of_birth','religion','caste','nationality','state','mother_tongue','blood_group','medicine_info','disease_info','guardian_name','guardian_relation','guardian_occupation','guardian_annual_income','address','tel','cell_1','cell_2','email','mailing_address','mailing_tel','mailing_cell_1','mailing_cell_2','mailing_email','student_image','student_signature','guardian_signature','status')
            ->where(function ($query) use ($request) {
                if ($request->has('web_registration_programmes_id')) {
                    $query->where('web_registration_programmes_id', '=', $request->web_registration_programmes_id);
                    $this->filter_query['web_registration_programmes_id'] = $request->web_registration_programmes_id;
                }

                if ($request->has('reg_no')) {
                    $query->where('reg_no', '=', $request->reg_no);
                    $this->filter_query['reg_no'] = $request->reg_no;
                }

                if ($request->has('start_date') && $request->has('end_date')) {
                    $query->whereBetween('reg_date', [$request->get('start_date'), $request->get('end_date')]);
                    $this->filter_query['start_date'] = $request->get('start_date');
                    $this->filter_query['end_date'] = $request->get('end_date');
                }
                elseif ($request->has('start_date')) {
                    $query->where('reg_date', '>=', $request->get('start_date'));
                    $this->filter_query['start_date'] = $request->get('start_date');
                }
                elseif($request->has('end_date')) {
                    $query->where('reg_date', '<=', $request->get('end_date'));
                    $this->filter_query['end_date'] = $request->get('end_date');
                }

                if ($request->has('name')) {
                    $query->where('name', 'like', '%'. $request->name.'%');
                    $this->filter_query['name'] = $request->name;
                }

                if ($request->has('sex')) {
                    $query->where('sex', 'like', '%'. $request->sex.'%');
                    $this->filter_query['sex'] = $request->sex;
                }

                if ($request->has('date_of_birth')) {
                    $query->where('date_of_birth', '=',Carbon::parse($request->date_of_birth)->format('Y-m-d'));
                    $this->filter_query['date_of_birth'] = Carbon::parse($request->date_of_birth)->format('Y-m-d');
                }

                if ($request->has('religion')) {
                    $query->where('religion', 'like', '%'.$request->religion.'%');
                    $this->filter_query['religion'] = $request->religion;
                }

                if ($request->has('caste')) {
                    $query->where('caste', 'like', '%'. $request->caste.'%');
                    $this->filter_query['caste'] = $request->caste;
                }

                if ($request->has('nationality')) {
                    $query->where('nationality', 'like', '%'. $request->nationality.'%');
                    $this->filter_query['nationality'] = $request->nationality;
                }

                if ($request->has('state')) {
                    $query->where('state', 'like', '%'. $request->state.'%');
                    $this->filter_query['state'] = $request->state;
                }

                if ($request->has('state')) {
                    $query->where('state', 'like', '%'. $request->state.'%');
                    $this->filter_query['state'] = $request->state;
                }

                if ($request->has('mother_tongue')) {
                    $query->where('mother_tongue', 'like', '%'. $request->mother_tongue.'%');
                    $this->filter_query['mother_tongue'] = $request->mother_tongue;
                }

                if ($request->has('blood_group')) {
                    $query->where('blood_group', 'like', '%'. $request->blood_group.'%');
                    $this->filter_query['blood_group'] = $request->blood_group;
                }

                if ($request->has('medicine_info')) {
                    $query->where('medicine_info', 'like', '%'. $request->medicine_info.'%');
                    $this->filter_query['medicine_info'] = $request->medicine_info;
                }

                if ($request->has('disease_info')) {
                    $query->where('disease_info', 'like', '%'. $request->disease_info.'%');
                    $this->filter_query['disease_info'] = $request->disease_info;
                }

                if ($request->has('guardian_name')) {
                    $query->where('guardian_name', 'like', '%'. $request->guardian_name.'%');
                    $this->filter_query['guardian_name'] = $request->guardian_name;
                }

                if ($request->has('guardian_relation')) {
                    $query->where('guardian_relation', 'like', '%'. $request->guardian_relation.'%');
                    $this->filter_query['guardian_relation'] = $request->guardian_relation;
                }

                if ($request->has('guardian_occupation')) {
                    $query->where('guardian_occupation', 'like', '%'. $request->guardian_occupation.'%');
                    $this->filter_query['guardian_occupation'] = $request->guardian_occupation;
                }

                if ($request->has('guardian_annual_income')) {
                    $query->where('guardian_annual_income', 'like', '%'. $request->guardian_annual_income.'%');
                    $this->filter_query['guardian_annual_income'] = $request->guardian_annual_income;
                }

                if ($request->has('annual_income_start') && $request->has('annual_income_end')) {
                    $query->whereBetween('guardian_annual_income', [$request->get('annual_income_start'), $request->get('annual_income_end')]);
                    $this->filter_query['annual_income_start'] = $request->get('annual_income_start');
                    $this->filter_query['annual_income_end'] = $request->get('annual_income_end');
                }
                elseif ($request->has('annual_income_start')) {
                    $query->where('guardian_annual_income', '>=', $request->get('annual_income_start'));
                    $this->filter_query['annual_income_start'] = $request->get('annual_income_start');
                }
                elseif($request->has('annual_income_end')) {
                    $query->where('guardian_annual_income', '<=', $request->get('annual_income_end'));
                    $this->filter_query['annual_income_end'] = $request->get('annual_income_end');
                }

                if ($request->has('address')) {
                    $query->where('address', 'like', '%'. $request->address.'%');
                    $this->filter_query['address'] = $request->address;
                }

                if ($request->has('tel')) {
                    $query->where('tel', 'like', '%'. $request->tel.'%');
                    $this->filter_query['tel'] = $request->tel;
                }

                if ($request->has('cell_1')) {
                    $query->where('cell_1', 'like', '%'. $request->cell_1.'%');
                    $this->filter_query['cell_1'] = $request->cell_1;
                }

                if ($request->has('cell_2')) {
                    $query->where('cell_2', 'like', '%'. $request->cell_2.'%');
                    $this->filter_query['cell_2'] = $request->cell_2;
                }

                if ($request->has('email')) {
                    $query->where('email', 'like', '%'. $request->email.'%');
                    $this->filter_query['email'] = $request->email;
                }

                if ($request->has('mailing_address')) {
                    $query->where('mailing_address', 'like', '%'. $request->mailing_address.'%');
                    $this->filter_query['mailing_address'] = $request->mailing_address;
                }

                if ($request->has('mailing_tel')) {
                    $query->where('mailing_tel', 'like', '%'. $request->mailing_tel.'%');
                    $this->filter_query['mailing_tel'] = $request->mailing_tel;
                }

                if ($request->has('mailing_cell_1')) {
                    $query->where('mailing_cell_1', 'like', '%'. $request->mailing_cell_1.'%');
                    $this->filter_query['mailing_cell_1'] = $request->mailing_cell_1;
                }

                if ($request->has('mailing_cell_2')) {
                    $query->where('mailing_cell_2', 'like', '%'. $request->mailing_cell_2.'%');
                    $this->filter_query['mailing_cell_2'] = $request->mailing_cell_2;
                }

                if ($request->has('mailing_email')) {
                    $query->where('mailing_email', 'like', '%'. $request->mailing_email.'%');
                    $this->filter_query['mailing_email'] = $request->mailing_email;
                }


                if ($request->has('create_start_date') && $request->has('create_end_date')) {
                    $query->whereBetween('created_at', [$request->get('create_start_date'), $request->get('create_end_date')]);
                    $this->filter_query['create_start_date'] = $request->get('create_start_date');
                    $this->filter_query['create_end_date'] = $request->get('create_end_date');
                }
                elseif ($request->has('create_start_date')) {
                    $query->where('created_at', '>=', $request->get('create_start_date'));
                    $this->filter_query['create_start_date'] = $request->get('create_start_date');
                }
                elseif($request->has('create_end_date')) {
                    $query->where('created_at', '<=', $request->get('create_end_date'));
                    $this->filter_query['create_end_date'] = $request->get('create_end_date');
                }

                if ($request->has('update_start_date') && $request->has('update_end_date')) {
                    $query->whereBetween('updated_at', [$request->get('update_start_date'), $request->get('update_end_date')]);
                    $this->filter_query['update_start_date'] = $request->get('update_start_date');
                    $this->filter_query['update_end_date'] = $request->get('update_end_date');
                }
                elseif ($request->has('update_start_date')) {
                    $query->where('updated_at', '>=', $request->get('update_start_date'));
                    $this->filter_query['update_start_date'] = $request->get('update_start_date');
                }
                elseif($request->has('update_end_date')) {
                    $query->where('updated_at', '<=', $request->get('update_end_date'));
                    $this->filter_query['update_end_date'] = $request->get('update_end_date');
                }

                if ($request->has('status')) {
                    $query->where('status', $request->status == 'active'?1:0);
                    $this->filter_query['status'] = $request->get('status');
                }

            })
            ->paginate($this->registration_pagination_limit);

        $data['programmes'] = WebRegistrationProgramme::where('status',1)->orderBy('title')->pluck('title','id')->toArray();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

    public function add()
    {
        $data = [];
        $data['programmes'] = WebRegistrationProgramme::where('status',1)->orderBy('title')->pluck('title','id')->toArray();

        return view(parent::WebsiteViewHelper($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
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
                    'web_registrations_id' => $registration->id,
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
                    'web_registrations_id' => $registration->id,
                    'experience_info' => $experience,
                ]);
            }
        }
        /*Work Experience End*/


        if($registration){
            /*SMS & Email Alert*/
            $alert = AlertSetting::select('sms','email','subject','template')->where('event','=','Online Registration')->first();
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

    public function view($id)
    {
        $id = decrypt($id);
        $data = [];
        $data['row'] = WebRegistration::find($id);

        $data['academicInfos'] = $data['row']->academicInfo()->get();
        $data['workExperiences'] = $data['row']->workExperience()->get();
        $data['general_setting'] = GeneralSetting::select('institute', 'salogan','copyright', 'address','phone','email','website', 'email', 'logo',
            'facebook', 'twitter', 'linkedIn', 'youtube', 'instagram', 'whatsApp', 'skype', 'pinterest','wordpress')->first();

        $data['reg_setting'] = WebRegistrationSetting::first();

        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.view'), compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = WebRegistration::find($id))
            return parent::invalidRequest();


        $data['programmes'] = WebRegistrationProgramme::where('status',1)->orderBy('title')->pluck('title','id')->toArray();

        //$data['academicInfo'] = WebRegistrationAcademicQualification::where('web_registrations_id',$id)->get();
        $data['academicInfos'] = $data['row']->academicInfo();

        $data['academicInfo-html'] = view($this->view_path.'.includes.academic_tr_edit', [
            'academicInfos' => $data['academicInfos']
        ])->render();

        //$data['workExperience'] = WebRegistrationWorkExperience::where('web_registrations_id',$id)->get();
        $data['workExperiences'] = $data['row']->workExperience();

        $data['workExperience-html'] = view($this->view_path.'.includes.workexperience_tr_edit', [
            'workExperiences' => $data['workExperiences']
        ])->render();

        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebRegistration::find($id)) return parent::invalidRequest();


        if ($request->hasFile('student_main_image')) {
            // remove old image from folder
            if (file_exists($this->folder_path.$row->student_image))
                @unlink($this->folder_path.$row->student_image);

            /*upload new student image*/
            $student_image = $request->file('student_main_image');
            $student_image_name = $row->reg_no.'_studentImage.'.$student_image->getClientOriginalExtension();
            $student_image->move($this->folder_path, $student_image_name);
        }

        if ($request->hasFile('student_signature_main_image')) {
            // remove old image from folder
            if (file_exists($this->folder_path.$row->student_signature))
                @unlink($this->folder_path.$row->student_signature);

            /*upload new student image*/
            $student_signature = $request->file('student_signature_main_image');
            $student_signature_name = $row->reg_no.'_signature.'.$student_signature->getClientOriginalExtension();
            $student_signature->move($this->folder_path, $student_signature_name);
        }


        if ($request->hasFile('guardian_signature_main_image')) {
            // remove old image from folder
            if (file_exists($this->folder_path.$row->guardian_signature))
                @unlink($this->folder_path.$row->guardian_signature);

            /*upload new student image*/
            $guardian_signature = $request->file('guardian_signature_main_image');
            $guardian_signature_name = $row->reg_no.'_guardian_signature.'.$guardian_signature->getClientOriginalExtension();
            $guardian_signature->move($this->folder_path, $guardian_signature_name);
        }

        $request->request->add(['id' => isset($id)?$id:$row->id]);
        $request->request->add(['web_registration_programmes_id' => isset($request->web_registration_programmes_id)?$request->web_registration_programmes_id:$row->web_registration_programmes_id]);
        $request->request->add(['reg_date' => Carbon::parse($request->reg_date)->format('Y-m-d')]);
        $request->request->add(['date_of_birth' => Carbon::parse($request->date_of_birth)->format('Y-m-d')]);
        $request->request->add(['student_image' => isset($student_image_name)?$student_image_name:$row->student_image]);
        $request->request->add(['student_signature' => isset($student_signature_name)?$student_signature_name:$row->student_signature]);
        $request->request->add(['guardian_signature' => isset($guardian_signature_name)?$guardian_signature_name:$row->guardian_signature]);

        $data = $row->update($request->all());


        /*Academic Info Start*/
        if ($row && $request->has('examination')) {
            foreach ($request->get('examination') as $key => $exam) {
                $academicInfoExist = WebRegistrationAcademicQualification::where([['web_registrations_id','=',$row->id],['examination','=',$exam]])->first();
                if($academicInfoExist){
                    $academicInfoUpdate = [
                        'web_registrations_id' => $id,
                        'examination' => $exam,
                        'institution' => $request->get('institution')[$key],
                        'board_university' => $request->get('board_university')[$key],
                        'year_of_pass' => $request->get('year_of_pass')[$key],
                        'percentage_grade' => $request->get('percentage_grade')[$key],
                    ];
                    $academicInfoExist->update($academicInfoUpdate);
                }else{
                    WebRegistrationAcademicQualification::create([
                        'web_registrations_id' => $id,
                        'examination' => $exam,
                        'institution' => $request->get('institution')[$key],
                        'board_university' => $request->get('board_university')[$key],
                        'year_of_pass' => $request->get('year_of_pass')[$key],
                        'percentage_grade' => $request->get('percentage_grade')[$key],
                    ]);
                }

            }
        }
        /*Academic Info End*/

        /*Work Experience Start*/
        if ($row && $request->has('experience_info')) {
            $workExperienceExist = WebRegistrationWorkExperience::where([['web_registrations_id','=',$id]])->delete();

            foreach ($request->get('experience_info') as $key => $experience) {
                WebRegistrationWorkExperience::create([
                    'web_registrations_id' => $id,
                    'experience_info' => $experience,
                ]);
            }

        }
        /*Work Experience End*/

        $request->session()->flash($this->message_success, $this->panel.' successfully updated.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebRegistration::find($id)) return parent::invalidRequest();

        // remove old image from folder
        if (file_exists($this->folder_path.$row->student_image))
            @unlink($this->folder_path.$row->student_image);

        if (file_exists($this->folder_path.$row->student_signature))
            @unlink($this->folder_path.$row->student_signature);

        if (file_exists($this->folder_path.$row->guardian_signature))
            @unlink($this->folder_path.$row->guardian_signature);

        $row->delete();

        $request->session()->flash($this->message_success, $this->panel.' successfully deleted.');
        //return redirect()->route($this->base_route);
        return back();

    }

    public function active(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebRegistration::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $registration = $row->update($request->all());


        if($registration){
            /*SMS & Email Alert*/
            $alert = AlertSetting::select('sms','email','subject','template')->where('event','=','Online Registration Approve')->first();
            if($alert){
                //Dear {{first_name}}, you are successfully registered in our institution with RegNo.{{reg_no}}. Thank You.
                $subject = $alert->subject;
                $message = $alert->template;
                $message = str_replace('{{name}}', $row->name, $message );
                $message = str_replace('{{reg_no}}', $row->reg_no, $message );

                /*Now Send SMS On First Mobile Number*/
                if($alert->sms == 1 && isset($row->cell_1)){
                    $contactNumbers[] = $row->cell_1;

                    $contactNumbers = $this->contactFilter($contactNumbers);
                    $smssuccess = $this->sendSMS($contactNumbers,$message);
                }

                /*Now Send Email With Subject*/
                if($alert->email == 1 && isset($row->email)){
                    $emailIds[] = $row->email;
                    $emailIds = $this->emailFilter($emailIds);
                    /*sending email*/
                    $emailSuccess = $this->sendEmail($emailIds, $subject, $message);
                }
            }
            //end sms email
        }

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' Approve Successfully.');
        //return redirect()->route($this->base_route);
        return back();
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebRegistration::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);
        $registration = $row->update($request->all());


        if($registration){
            /*SMS & Email Alert*/
            $alert = AlertSetting::select('sms','email','subject','template')->where('event','=','Online Registration Reject')->first();
            if($alert){
                //Dear {{first_name}}, you are successfully registered in our institution with RegNo.{{reg_no}}. Thank You.
                $subject = $alert->subject;
                $message = $alert->template;
                $message = str_replace('{{name}}', $row->name, $message );
                $message = str_replace('{{reg_no}}', $row->reg_no, $message );

                /*Now Send SMS On First Mobile Number*/
                if($alert->sms == 1 && isset($row->cell_1)){
                    $contactNumbers[] = $row->cell_1;

                    $contactNumbers = $this->contactFilter($contactNumbers);
                    $smssuccess = $this->sendSMS($contactNumbers,$message);
                }

                /*Now Send Email With Subject*/
                if($alert->email == 1 && isset($row->email)){
                    $emailIds[] = $row->email;
                    $emailIds = $this->emailFilter($emailIds);
                    /*sending email*/
                    $emailSuccess = $this->sendEmail($emailIds, $subject, $message);
                }
            }
            //end sms email
        }

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' Rejected Successfully.');
        //return redirect()->route($this->base_route);
        return back();
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['Approve', 'Reject', 'Delete'])) {

            if ($request->has('chkIds')) {

                foreach ($request->get('chkIds') as $row_id) {
                    $id = decrypt($row_id);
                    $row = WebRegistration::find($id);
                    switch ($request->get('bulk_action')) {
                        case 'Approve':
                            if ($row) {
                                $this->active($request,$row_id);
                            }
                            break;
                        case 'Reject':
                            if ($row) {
                                $this->inActive($request,$row_id);
                            }
                            break;
                        case 'Delete':
                            // remove old image from folder
                            if (file_exists($this->folder_path.$row->student_image))
                                @unlink($this->folder_path.$row->student_image);

                            if (file_exists($this->folder_path.$row->student_signature))
                                @unlink($this->folder_path.$row->student_signature);

                            if (file_exists($this->folder_path.$row->guardian_signature))
                                @unlink($this->folder_path.$row->guardian_signature);

                            $row->delete();
                            break;
                    }

                }

                if ($request->get('bulk_action') == 'Approve' || $request->get('bulk_action') == 'Reject')
                    $request->session()->flash($this->message_success, 'Bulk Action '.$request->get('bulk_action').' Successful.');
                else
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')).' successfully.');

                return redirect()->route($this->base_route);

            } else {
                $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
                return redirect()->route($this->base_route);
            }
        } else return parent::invalidRequest();
    }

    public function academicInfoHtml()
    {
        $response['html'] = view($this->view_path.'.includes.academic_tr')->render();
        return response()->json(json_encode($response));
    }

    public function deleteAcademicInfo(Request $request, $id)
    {
        if (!$row = WebRegistrationAcademicQualification::find($id)) return parent::invalidRequest();

        $row->delete();

        $request->session()->flash($this->message_success,'Academic Qualification Deleted Successfully.');
        return redirect()->back();
    }

    public function workExperienceHtml()
    {
        $response['html'] = view($this->view_path.'.includes.workexperience_tr')->render();
        return response()->json(json_encode($response));
    }

    public function deleteWorkExperience(Request $request, $id)
    {
        if (!$row = WebRegistrationWorkExperience::find($id)) return parent::invalidRequest();

        $row->delete();

        $request->session()->flash($this->message_success,'Work Experience Deleted Successfully.');
        return redirect()->back();
    }

}