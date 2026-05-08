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
use App\Models\ContactDetail;
use App\Models\ContactDirectoryDetail;
use App\Models\ContactDirectoryGroup;
use App\Models\ContactGroup;
use App\Models\ContactUsSetting;
use App\Models\GeneralSetting;
use App\Models\RegistrationAcademicQualification;
use App\Models\RegistrationProgramme;
use App\Models\RegistrationSetting;
use App\Models\RegistrationWorkExperience;
use App\Traits\SmsEmailScope;
use Carbon\Carbon;


use App\Http\Requests\Web\ContactDetail\AddValidation;
use App\Http\Requests\Web\ContactDetail\EditValidation;
use App\Models\Registration;
use Illuminate\Http\Request;
use View, AppHelper, Image, URL;
use Illuminate\Support\Facades\Validator;

class ContactDetailController extends AdminBaseController
{
    protected $base_route = 'web.admin.contact';
    protected $view_path = 'web.admin.contact-detail';
    protected $panel = 'Contacts';
    protected $folder_path;
    protected $folder_name = 'contact-directory';
    protected $filter_query = [];

    use SmsEmailScope;

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        $data['rows'] = ContactDirectoryDetail::select('id','contact_directory_groups_id','name','address','tel','cell_1','status')
            ->where(function ($query) use ($request) {
                if ($request->has('contact_directory_groups_id')) {
                    $query->where('contact_directory_groups_id', '=', $request->contact_directory_groups_id);
                    $this->filter_query['contact_directory_groups_id'] = $request->contact_directory_groups_id;
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

        $data['group'] = ContactDirectoryGroup::where('status',1)->orderBy('title')->pluck('title','id')->toArray();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

    public function add()
    {
        $data = [];
        $data['group'] = ContactDirectoryGroup::where('status',1)->orderBy('title')->pluck('title','id')->toArray();

        return view(parent::WebsiteViewHelper($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $regNo = $this->randomNum('CON',6);

        if ($request->hasFile('main_image')){
            $image = $request->file('main_image');
            $image_name = $regNo.'_'.$request->get('name').'.'.$image->getClientOriginalExtension();
            $image->move($this->folder_path, $image_name);
        }else{
            $image_name = "";
        }

        $request->request->add(['reg_no' => $regNo]);
        $request->request->add(['reg_date' => Carbon::parse($request->reg_date)->format('Y-m-d')]);
        $request->request->add(['date_of_birth' => Carbon::parse($request->date_of_birth)->format('Y-m-d')]);
        $request->request->add(['image' => $image_name]);

        $registration = ContactDirectoryDetail::create($request->all());

       $request->session()->flash($this->message_success, 'Contact Created Successfully');
        return redirect()->back();
    }

    public function view($id)
    {
        $id = decrypt($id);
        $data = [];
        $data['row'] = ContactDirectoryDetail::find($id);

        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.view'), compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = ContactDirectoryDetail::find($id))
            return parent::invalidRequest();

        $data['group'] = ContactDirectoryGroup::where('status',1)->orderBy('title')->pluck('title','id')->toArray();

        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$row = ContactDirectoryDetail::find($id)) return parent::invalidRequest();

        if ($request->hasFile('main_image')) {
            // remove old image from folder
            if (file_exists($this->folder_path.$row->image))
                @unlink($this->folder_path.$row->image);

            /*upload new contact image*/
            $image = $request->file('main_image');
            $image_name = $row->reg_no.'_'.$request->get('name').'.'.$image->getClientOriginalExtension();
            $image->move($this->folder_path, $image_name);
        }

        $request->request->add(['id' => isset($id)?$id:$row->id]);
        $request->request->add(['contact_directory_groups_id' => isset($request->contact_directory_groups_id)?$request->contact_directory_groups_id:$row->contact_directory_groups_id]);
        $request->request->add(['reg_date' => Carbon::parse($request->reg_date)->format('Y-m-d')]);
        $request->request->add(['date_of_birth' => Carbon::parse($request->date_of_birth)->format('Y-m-d')]);
        $request->request->add(['image' => isset($image_name)?$image_name:$row->image]);

        $data = $row->update($request->all());


        $request->session()->flash($this->message_success, $this->panel.' successfully updated.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = ContactDirectoryDetail::find($id)) return parent::invalidRequest();

        // remove old image from folder
        if (file_exists($this->folder_path.$row->image))
            @unlink($this->folder_path.$row->image);

        $row->delete();

        $request->session()->flash($this->message_success, $this->panel.' successfully deleted.');
        return back();

    }

    public function active(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = ContactDirectoryDetail::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $registration = $row->update($request->all());


        if($registration){
            /*SMS & Email Alert*/
            $alert = AlertSetting::select('sms','email','subject','template')->where('event','=','Registration Approve')->first();
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
        if (!$row = ContactDirectoryDetail::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);
        $registration = $row->update($request->all());


        if($registration){
            /*SMS & Email Alert*/
            $alert = AlertSetting::select('sms','email','subject','template')->where('event','=','Registration Reject')->first();
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
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {

                foreach ($request->get('chkIds') as $row_id) {
                    $row_id = decrypt($row_id);
                    $row = ContactDirectoryDetail::find($row_id);
                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            // remove old image from folder
                            if (file_exists($this->folder_path.$row->image))
                                @unlink($this->folder_path.$row->image);

                            $row->delete();
                            break;
                    }

                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')).' Successfully.');
                else
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')).' successfully.');

                return redirect()->route($this->base_route);

            } else {
                $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
                return redirect()->route($this->base_route);
            }

        } else return parent::invalidRequest();


    }

    /*bulk import*/
    public function importContactDetail()
    {
        return view(parent::WebsiteViewHelper($this->view_path.'.import'));
    }

    public function handleImportContactDetail(Request $request)
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

            //Email validation
            $validator = Validator::make($row, [
                'contact_group'             => 'required',
                'name'                     => 'required | max:100',
            ]);

            //Group id
            $contactGroup = ContactDirectoryGroup::where('title',$row['contact_group'])->first();
            if($contactGroup){
                $contactGroupId = $contactGroup->id;
            }else{
                $contactGroup = ContactDirectoryGroup::create([
                    'title' => strtoupper($row['contact_group']),
                    'created_by' => auth()->user()->id
                ]);

                $contactGroupId = $contactGroup->id;
            }


            if ($validator->fails()) {
                /* return redirect()
                     ->back()
                     ->withErrors($validator);*/
                $request->session()->flash($this->message_warning, 'Some Contact Already Exist in Our Contact Detail Directory/ Contact Group, Name is Missing.');
            }else{
                $dob = isset($row['date_of_birth'])?Carbon::parse($row['date_of_birth'])->format('Y-m-d'):'';
                //ContactDetail import
                $customer = ContactDirectoryDetail::create([
                    "contact_directory_groups_id" => $contactGroupId,
                    "name"              => isset($row['name'])?$row['name']:'',
                    "sex"               => isset($row['sex'])?$row['sex']:'',
                    "date_of_birth"     => $dob,
                    "religion"          => isset($row['religion'])?$row['religion']:'',
                    "caste"             => isset($row['caste'])?$row['caste']:'',
                    "nationality"       => isset($row['nationality'])?$row['nationality']:'',
                    "mother_tongue"     => isset($row['mother_tongue'])?$row['mother_tongue']:'',
                    "blood_group"       => isset($row['blood_group'])?$row['blood_group']:'',
                    "address"           => isset($row['address'])?$row['address']:'',
                    "tel"               => isset($row['tel'])?$row['tel']:'',
                    "cell_1"            => isset($row['cell_1'])?$row['cell_1']:'',
                    "cell_2"            => isset($row['cell_2'])?$row['cell_2']:'',
                    "email"             => isset($row['email'])?$row['email']:'',
                    "mailing_address"   => isset($row['mailing_address'])?$row['mailing_address']:'',
                    "mailing_tel"       => isset($row['mailing_tel'])?$row['mailing_tel']:'',
                    "mailing_cell_1"    => isset($row['mailing_cell_1'])?$row['mailing_cell_1']:'',
                    "mailing_cell_2"    => isset($row['mailing_cell_2'])?$row['mailing_cell_2']:'',
                    "mailing_email"     => isset($row['mailing_email'])?$row['mailing_email']:'',
                    'created_by'        => auth()->user()->id
                ]);

                $request->session()->flash($this->message_success, 'Contact Detail imported Successfully.');

            }


        }

        return redirect()->route($this->base_route);
    }


}