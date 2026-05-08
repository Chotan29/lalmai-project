<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\Web\Admin\Info;
use App\Http\Controllers\Web\Admin\AdminBaseController;
use App\Models\Blog;
use App\Models\ContactDirectoryDetail;
use App\Models\ContactDirectoryGroup;
use App\Models\Notice;
use App\Models\Web\WebSubscriber;
use App\Models\SmsEmail;
use App\Models\Staff;
use App\Traits\SmsEmailScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SmsEmailController extends AdminBaseController
{
    protected $base_route = 'web.admin.info.smsemail';
    protected $view_path = 'web.admin.info.smsemail';
    protected $panel = 'SMS / Email';
    protected $filter_query = [];

    use SmsEmailScope;

    public function __construct()
    {

    }

    /*Message Traking*/
    public function index(Request $request)
    {
        $data = [];
        $data['rows'] = SmsEmail::where(function ($query) use ($request) {

            if ($request->has('group') && $request->get('group') !==null ) {
                $query->where('group', 'like', '%'.$request->group.'%');
                $this->filter_query['group'] = $request->group;
            }

            if ($request->has('subject') && $request->get('subject') !==null ) {
                $query->where('subject', 'like', '%'.$request->subject.'%');
                $this->filter_query['subject'] = $request->subject;
            }

            if ($request->has('message') && $request->get('message') !==null ) {
                $query->where('message', 'like', '%'.$request->message.'%');
                $this->filter_query['message'] = $request->message;
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [$request->get('start_date'), $request->get('end_date')]);
                $this->filter_query['start_date'] = $request->get('start_date');
                $this->filter_query['end_date'] = $request->get('end_date');
            }
            elseif ($request->has('start_date')) {
                $query->where('created_at', '>=', $request->get('start_date'));
                $this->filter_query['start_date'] = $request->get('start_date');
            }
            elseif($request->has('end_date')) {
                $query->where('created_at', '<=', $request->get('end_date'));
                $this->filter_query['end_date'] = $request->get('end_date');
            }

            if ($request->get('type') == 'email') {
                $query->where('email', '=', 1);
                $this->filter_query['email'] = $request->email;
            }

            if ($request->get('type') == 'sms') {
                $query->where('sms', '=', 1);
                $this->filter_query['sms'] = $request->sms;
            }

        })
            ->where('created_by',auth()->user()->id)
            ->latest()
            ->paginate($this->pagination_limit);



        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::WebsiteViewHelper($this->view_path . '.index'), compact('data'));

    }

    public function delete(Request $request, $id)
    {
        if (!$row = SmsEmail::find($id)) return parent::invalidRequest();

        $row->delete();

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {
                foreach ($request->get('chkIds') as $row_id) {
                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            $row = SmsEmail::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = SmsEmail::find($row_id);
                            $row->delete();
                            break;
                    }
                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')) . ' Successfully.');
                else
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')).' successfully.');

                return redirect()->route($this->base_route);

            } else {
                $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
                return redirect()->route($this->base_route);
            }

        } else return parent::invalidRequest();

    }

    /*Group Message*/
    public function create()
    {
        $data = [];
        $data['groups'] = ContactDirectoryGroup::orderBy('title')->get();
        return view(parent::WebsiteViewHelper($this->view_path . '.create'), compact('data'));

    }

    public function send(Request $request)
    {
        $emailIds = [];
        $contactNumbers = [];
        $subject = $request->subject;
        $message = $request->message;
        $emailMessage = $request->emailMessage;
        $group = [];

        /*get email id and contact number all active college admin*/
        if($request->group){
            $contactDetails = ContactDirectoryDetail::select('email','cell_1')->whereIn('contact_directory_groups_id',$request->group)->Active()->get();

            foreach($contactDetails as $contactDetail){
                /*chek email id is correct or not if correct add on array other wise not*/
                $filterMail = filter_var($contactDetail->email,FILTER_VALIDATE_EMAIL);
                if($filterMail){
                    $emailIds[] = $filterMail;
                }
                $contactNumbers[] = $contactDetail->cell_1;
            }

            //$group[] = 2;
            $group = ContactDirectoryGroup::whereIn('id',$request->group)->get()->pluck('title')->toArray();
            //$request->request->add(['admin' => in_array("admin", $request->role) ? 1 : 0]);
        }

        /*Now Send SMS On First Mobile Number*/
        if(in_array("sms",$request->type)){
            $contactNumbers = $this->contactFilter($contactNumbers);
            $smssuccess = $this->sendSMS($contactNumbers,$message);

            /*store*/
            $group = implode(',',$group);
            $request->request->add(['group' => $group]);
            $request->request->add(['ref' => $contactNumbers]);
            $request->request->add(['sms' => 1]);
            $request->request->add(['email' => 0]);
            $request->request->add(['message' => $message]);
            $request->request->add(['created_by' => auth()->user()->id]);
            SmsEmail::create($request->all());

            return back()->with($this->message_success, "SMS Send Successfully");
        }

        /*Now Send Email With Subject*/
        if(in_array("email",$request->type)){
            $emailIds = $this->emailFilter($emailIds);
            $emailSuccess = $this->sendEmail($emailIds, $subject, $emailMessage);
            if($emailSuccess !=null)
                return back();

            /*store*/
            $group = implode(',',$group);
            $request->request->add(['group' => $group]);
            $request->request->add(['ref' => $emailIds]);
            $request->request->add(['sms' => 0]);
            $request->request->add(['email' => 1]);
            $request->request->add(['message' => $emailMessage]);
            $request->request->add(['created_by' => auth()->user()->id]);
            SmsEmail::create($request->all());

            return back()->with($this->message_success, "Email Send Successfully");
        }
    }

    /*SMS & EMAIL FOR Staff*/
    public function staff(Request $request)
    {
        $this->panel = 'Staffs';
        $data = [];
        if($request->all()) {
            $data = [];
            $data['staff'] = Staff::where(function ($query) use ($request) {

                if ($request->has('name')) {
                    $query->where('name', 'like', '%'.$request->name.'%');
                    $this->filter_query['name'] = $request->name;
                }

                if ($request->has('position')) {
                    $query->where('position', 'like', '%'.$request->position.'%');
                    $this->filter_query['position'] = $request->position;
                }

                if ($request->has('address')) {
                    $query->where('address', 'like', '%'.$request->address.'%');
                    $this->filter_query['address'] = $request->address;
                }

                if ($request->has('tel')) {
                    $query->where('tel', 'like', '%'.$request->tel.'%');
                    $this->filter_query['tel'] = $request->tel;
                }

                if ($request->has('cel_1')) {
                    $query->where('cel_1', 'like', '%'.$request->cel_1.'%');
                    $this->filter_query['cel_1'] = $request->cel_1;
                }

                if ($request->has('cel_2')) {
                    $query->where('cel_2', 'like', '%'.$request->cel_2.'%');
                    $this->filter_query['cel_2'] = $request->cel_2;
                }

                if ($request->has('email')) {
                    $query->where('email', 'like', '%'.$request->email.'%');
                    $this->filter_query['email'] = $request->email;
                }

                if ($request->has('description')) {
                    $query->where('description', 'like', '%'.$request->description.'%');
                    $this->filter_query['description'] = $request->description;
                }

                if ($request->has('whatsapp_url')) {
                    $query->where('whatsapp_url', 'like', '%'.$request->whatsapp_url.'%');
                    $this->filter_query['whatsapp_url'] = $request->whatsapp_url;
                }

                if ($request->has('linkedIn_url')) {
                    $query->where('linkedIn_url', 'like', '%'.$request->linkedIn_url.'%');
                    $this->filter_query['linkedIn_url'] = $request->linkedIn_url;
                }

                if ($request->has('twitter_url')) {
                    $query->where('twitter_url', 'like', '%'.$request->twitter_url.'%');
                    $this->filter_query['twitter_url'] = $request->twitter_url;
                }

                if ($request->has('facebook_url')) {
                    $query->where('facebook_url', 'like', '%'.$request->facebook_url.'%');
                    $this->filter_query['facebook_url'] = $request->facebook_url;
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

                if ($request->has('rank')) {
                    $query->where('rank', '=', $request->rank);
                    $this->filter_query['rank'] = $request->rank;
                }

                if ($request->has('status')) {
                    $query->where('status', $request->status == 'active'?1:0);
                    $this->filter_query['status'] = $request->get('status');
                }

            })
                ->get();
        }

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::WebsiteViewHelper($this->view_path . '.staff.index'), compact('data'));

    }

    public function staffSend(Request $request)
    {
        $emailIds = [];
        $contactNumbers = [];
        $message = $request->message;
        $subject = $request->subject;
        $emailMessage = $request->emailMessage;
        $group = [];

        if ($request->has('chkIds')) {
            $ids = $request->get('chkIds');
            $staffs = Staff::select('email', 'cell_1')
                ->whereIn('id',$ids)
                ->where('status','=',1)
                ->get();

            foreach($staffs as $staff){
                /*chek email id is correct or not if correct add on array other wise not*/
                $emailIds[] = $staff->email;
                $contactNumbers[] = $staff->cell_1;
                $group[] = 5;
            }
        }else{
            $request->session()->flash($this->message_warning, 'Please, Select At Least One Target Record.');
            return redirect()->route($this->base_route);
        }

        if($request->type){
            /*Now Send SMS On First Mobile Number*/
            if(in_array("sms",$request->type)){
                $contactNumbers = $this->contactFilter($contactNumbers);
                $smssuccess = $this->sendSMS($contactNumbers,$message);

                /*store*/
                $request->request->add(['group' => 'Staffs']);
                $request->request->add(['ref' => $contactNumbers]);
                $request->request->add(['sms' => 1]);
                $request->request->add(['email' => 0]);
                $request->request->add(['message' => $message]);
                $request->request->add(['created_by' => auth()->user()->id]);
                SmsEmail::create($request->all());

                return back()->with($this->message_success, "SMS Send Successfully");
            }

            /*Now Send Email With Subject*/
            if(in_array("email",$request->type)){
                $emailIds = $this->emailFilter($emailIds);
                $emailSuccess = $this->sendEmail($emailIds, $subject, $emailMessage);
                if($emailSuccess !=null)
                    return back();

                /*store*/
                $request->request->add(['group' => 'Staffs']);
                $request->request->add(['ref' => $emailIds]);
                $request->request->add(['sms' => 0]);
                $request->request->add(['email' => 1]);
                $request->request->add(['message' => $emailMessage]);
                $request->request->add(['created_by' => auth()->user()->id]);
                SmsEmail::create($request->all());

                return back()->with($this->message_success, "Email Send Successfully");
            }
        }else{
            $request->session()->flash($this->message_warning, 'Please, Select Message Type');
            return redirect()->route($this->base_route);
        }
    }

    /*SMS & EMAIL FOR Individual'S*/
    public function individual(Request $request)
    {
        $data = [];
        return view(parent::WebsiteViewHelper($this->view_path . '.individual.index'), compact('data'));
    }

    public function individualSend(Request $request)
    {
        $emailIds = $request->email;;
        $contactNumbers = $request->number;
        $message = $request->message;
        $subject = $request->subject;
        $emailMessage = $request->emailMessage;
        $group = "";

        if($request->type){
            /*Now Send SMS On First Mobile Number*/
            if(in_array("sms",$request->type)){
                $contactNumbers = explode(',',$contactNumbers);
                $contactNumbers = $this->contactFilter($contactNumbers);
                $contactNumbers= str_replace(' ','',$contactNumbers);
                $smssuccess = $this->sendSMS($contactNumbers,$message);

                /*store*/
                $request->request->add(['group' => 'Individual']);
                $request->request->add(['ref' => $contactNumbers]);
                $request->request->add(['sms' => 1]);
                $request->request->add(['email' => 0]);
                $request->request->add(['message' => $message]);
                $request->request->add(['created_by' => auth()->user()->id]);
                SmsEmail::create($request->all());

                return back()->with($this->message_success, "SMS Send Successfully");
            }

            /*Now Send Email With Subject*/
            if(in_array("email",$request->type)){
                $emailIds = explode(',',$emailIds);;
                $emailIds = $this->emailFilter($emailIds);

                $emailIds = str_replace(' ', '',$emailIds);
                $emailSuccess = $this->sendEmail($emailIds, $subject, $emailMessage);
                if($emailSuccess != null)
                    return back();

                /*store*/
                $request->request->add(['group' => 'Individual']);
                $request->request->add(['ref' => $emailIds]);
                $request->request->add(['sms' => 0]);
                $request->request->add(['email' => 1]);
                $request->request->add(['message' => $emailMessage]);
                $request->request->add(['created_by' => auth()->user()->id]);
                SmsEmail::create($request->all());

                return back()->with($this->message_success, "Email Send Successfully");
            }

        }else{
            $request->session()->flash($this->message_warning, 'Please, Select Message Type');
            return redirect()->route($this->base_route);
        }

    }

    //Mail to subscriber
    public function subscriberMail(Request $request)
    {
        $data = [];
        return view(parent::WebsiteViewHelper($this->view_path . '.subscribers.index'), compact('data'));
    }

    public function subscriberMailSend(Request $request)
    {
        $emailIds = $request->email;;
        $contactNumbers = $request->number;
        $message = $request->message;
        $subject = $request->subject;
        $emailMessage = $request->emailMessage;
        $group = "";

        $subscribersEmails = WebSubscriber::active()->get()->pluck('email')->toArray();

        $emailIds = $subscribersEmails;;
        $emailIds = $this->emailFilter($emailIds);

        $emailIds = str_replace(' ', '',$emailIds);
        $emailSuccess = $this->sendEmail($emailIds, $subject, $emailMessage);
        if($emailSuccess != null)
            return back();

        /*store*/
        $request->request->add(['group' => 'Subscribers']);
        $request->request->add(['ref' => $emailIds]);
        $request->request->add(['sms' => 0]);
        $request->request->add(['email' => 1]);
        $request->request->add(['subject' => $subject]);
        $request->request->add(['message' => $emailMessage]);
        $request->request->add(['created_by' => auth()->user()->id]);
        SmsEmail::create($request->all());

        return back()->with($this->message_success, "Email Send Successfully");

    }

    public function mailBlogToSubscriber(Request $request,$id)
    {
        $id = decrypt($id);
        $blogDetail = Blog::select('title','image','detail_desc')->find($id);

        $subject = $blogDetail->title;
        $emailMessage = $blogDetail->detail_desc;

        $subscribersEmails = WebSubscriber::active()->get()->pluck('email')->toArray();

        //$emailIds
        //$emailIds = explode(',',$subscribersEmails);;
        $emailIds = $subscribersEmails;;
        $emailIds = $this->emailFilter($emailIds);

        $emailIds = str_replace(' ', '',$emailIds);
        $emailSuccess = $this->sendEmail($emailIds, $subject, $emailMessage);
        if($emailSuccess != null)
            return back();

        /*store*/
        $request->request->add(['group' => 'Blog Newsletter']);
        $request->request->add(['ref' => $emailIds]);
        $request->request->add(['sms' => 0]);
        $request->request->add(['email' => 1]);
        $request->request->add(['subject' => $subject]);
        $request->request->add(['message' => $emailMessage]);
        $request->request->add(['created_by' => auth()->user()->id]);
        SmsEmail::create($request->all());

        return back()->with($this->message_success, "Email Send Successfully");
    }

    public function mailNoticeToSubscriber(Request $request,$id)
    {
        $id = decrypt($id);
        $noticeDetail = Notice::select('title','image','description')->find($id);

        $subject = $noticeDetail->title;
        $emailMessage = $noticeDetail->description;

        $subscribersEmails = WebSubscriber::active()->get()->pluck('email')->toArray();

        //$emailIds
        //$emailIds = explode(',',$subscribersEmails);;
        $emailIds = $subscribersEmails;;
        $emailIds = $this->emailFilter($emailIds);

        $emailIds = str_replace(' ', '',$emailIds);
        $emailSuccess = $this->sendEmail($emailIds, $subject, $emailMessage);
        if($emailSuccess != null)
            return back();

        /*store*/
        $request->request->add(['group' => 'Notice Newsletter']);
        $request->request->add(['ref' => $emailIds]);
        $request->request->add(['sms' => 0]);
        $request->request->add(['email' => 1]);
        $request->request->add(['subject' => $subject]);
        $request->request->add(['message' => $emailMessage]);
        $request->request->add(['created_by' => auth()->user()->id]);
        SmsEmail::create($request->all());

        return back()->with($this->message_success, "Email Send Successfully");
    }

    public function mailEventToSubscriber(Request $request,$id)
    {
        $id = decrypt($id);
        $eventDetail = Event::select('title','image','description')->find($id);

        $subject = $eventDetail->title;
        $emailMessage = $eventDetail->description;

        $subscribersEmails = WebSubscriber::active()->get()->pluck('email')->toArray();

        //$emailIds
        //$emailIds = explode(',',$subscribersEmails);;
        $emailIds = $subscribersEmails;;
        $emailIds = $this->emailFilter($emailIds);

        $emailIds = str_replace(' ', '',$emailIds);
        $emailSuccess = $this->sendEmail($emailIds, $subject, $emailMessage);
        if($emailSuccess != null)
            return back();

        /*store*/
        $request->request->add(['group' => 'Event Newsletter']);
        $request->request->add(['ref' => $emailIds]);
        $request->request->add(['sms' => 0]);
        $request->request->add(['email' => 1]);
        $request->request->add(['subject' => $subject]);
        $request->request->add(['message' => $emailMessage]);
        $request->request->add(['created_by' => auth()->user()->id]);
        SmsEmail::create($request->all());

        return back()->with($this->message_success, "Email Send Successfully");
    }


}