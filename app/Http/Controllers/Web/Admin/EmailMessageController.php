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
use App\Mail\EmailNewsLetter;
use App\Models\ContactUsSetting;
use App\Models\emailMessage;
use App\Models\Subscriber;


use App\Http\Requests\Web\emailMessage\AddValidation;
use Illuminate\Http\Request;
use View, AppHelper, Image, URL, ViewHelper;
use Illuminate\Support\Facades\Mail;

class EmailMessageController extends AdminBaseController
{
    protected $base_route = 'web.admin.emailMessage';
    protected $view_path = 'web.admin.emailMessage';
    protected $panel = 'Email Message';
    protected $folder_path;
    protected $folder_name = 'email';
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        $data['rows'] = emailMessage::select('id', 'created_by', 'last_updated_by', 'title', 'description', 'link', 'image')
            ->where(function ($query) use ($request) {

                if ($request->has('title')) {
                    $query->where('title', 'like', '%'.$request->title.'%');
                    $this->filter_query['title'] = $request->title;
                }

                if ($request->has('description')) {
                    $query->where('description', 'like', '%'.$request->description.'%');
                    $this->filter_query['description'] = $request->description;
                }

                if ($request->has('link')) {
                    $query->where('link', 'like', '%'.$request->link.'%');
                    $this->filter_query['link'] = $request->link;
                }

            })
            ->paginate($this->pagination_limit);
            
        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

    public function add()
    {
        $data = [];
        return view(parent::WebsiteViewHelper($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        if ($request->hasFile('main_image'))
            $image_name = parent::uploadImages($request, 'main_image');
        else
            $image_name = "";

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['image' => $image_name]);

        $composeEmail = emailMessage::create($request->all());

        $contact_info = ContactUsSetting::select('address', 'phone', 'email', 'facebook_link', 'twitter_link',
            'googlePlus_link')->first();

        $subscriber = Subscriber::select('email')->where('status', '1')->get();

        Mail::to('nepalcomputercare@gmail.com')
            ->bcc($subscriber)
            ->send(new EmailNewsLetter([
            'title'             => $composeEmail->title,
            'description'       => $composeEmail->description,
            'link'              => $composeEmail->link,
            'image'             => $composeEmail->image,
            'facebook_link'     => $contact_info->facebook_link,
            'twitter_link'      => $contact_info->twitter_link,
            'googlePlus_link'   => $contact_info->googlePlus_link,
            'phone'             => $contact_info->phone,
            'email'             => $contact_info->email,
        ]));

        $request->session()->flash($this->message_success, $this->panel. ' successfully added.');
        return redirect()->route($this->base_route);
    }

    public function view($id)
    {
        $id = decrypt($id);
        $data = [];
        $data['row'] = emailMessage::find($id);
        $data['base_route'] = $this->base_route;        
        return view(parent::WebsiteViewHelper($this->view_path.'.view'), compact('data'));
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = emailMessage::find($id)) return parent::invalidRequest();

        // remove old image from folder
        if ($row->image && file_exists($this->folder_path.$row->image)) {
            @unlink($this->folder_path.$row->image);
            foreach (config('freelancerumesh.image_dimensions.email.main_image') as $dimension) {
                if (file_exists($this->folder_path.$dimension['width'].'_'.$dimension['height'].'_'.$row->image))
                    @unlink($this->folder_path.$dimension['width'].'_'.$dimension['height'].'_'.$row->image);
            }
        }

        $row->delete();

        $request->session()->flash($this->message_success, $this->panel.' successfully deleted.');
        return redirect()->route($this->base_route);

    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {

                foreach ($request->get('chkIds') as $row_id) {
                    $row_id = decrypt($row_id);
                    $row = emailMessage::find($row_id);
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
                            if ($row->image && file_exists($this->folder_path.$row->image)) {
                                @unlink($this->folder_path.$row->image);
                                foreach (config('freelancerumesh.image_dimensions.email.main_image') as $dimension) {
                                    if (file_exists($this->folder_path.$dimension['width'].'_'.$dimension['height'].'_'.$row->image))
                                        @unlink($this->folder_path.$dimension['width'].'_'.$dimension['height'].'_'.$row->image);
                                }
                            }

                            $row->delete();
                            break;
                    }

                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                    $request->session()->flash($this->message_success, 'Bulk Action Successful.');
                else
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')).' successfully.');

                return redirect()->route($this->base_route);

            } else {
                $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
                return redirect()->route($this->base_route);
            }
        } else return parent::invalidRequest();
    }
}