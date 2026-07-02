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
use App\Mail\MailSubscribers;
use App\Models\Category;
use App\Models\ContactUsSetting;
use App\Models\GeneralSetting;
use App\Models\Web\WebBlog;
use App\Models\Web\WebBlogCategory;
use App\Models\Web\WebSubscriber;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Mail;

class SubscribersController extends WebsiteBaseController
{
    protected $message_success = 'message_success';
    protected $message_warning = 'message_warning';
    protected $message_danger = 'message_danger';
    protected $view_path = 'web.website.subscribers';

    public function index()
    {
        return redirect()->route('web.home');
    }

    public function store(Request $request)
    {

        $data['recent'] = WebBlog::select('title', 'slug', 'short_desc', 'publish_date', 'image')
            ->Active()
            ->limit(10)
            ->get();

        $data['categories'] = WebBlogCategory::select('id', 'title', 'slug')
            ->Active()
            ->get();


        if(!$row = WebSubscriber::where('email', $request->get('email'))->first()){
           $data['subscriber'] = $subscriber = WebSubscriber::create($request->all());

            Mail::to($request->get('email'))->send(new MailSubscribers([
                'id' => encrypt($subscriber->id),
                'email' => $subscriber->email,
                'subject' => 'Subscription Confirmation',
            ]));

            $request->session()->flash($this->message_success,$subscriber->email.' Successfully Subscribe Our Newsletter. Please Check Your Mail For Verify Email Id.');
            return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));

        }else{
             $request->session()->flash($this->message_danger,'Your Email Has Already Subscribed.');
            return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
        }
    }

    public function active(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebSubscriber::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, "Your Email : ".$row->email.' Subscription Activate Successfully.');
        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('row'));
    }

    public function inActive(request $request, $id)
    {
        if (!$row = WebSubscriber::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_warning, "Your Email : ".$row->email.' UnSubscribe Successfully.');
        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('row'));
    }


}
