<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Controllers\Web\Admin\Setting;
use App\Http\Controllers\Web\Admin\AdminBaseController;
use App\Models\Web\WebContactUsSetting;


use App\Http\Requests\Web\ContactUsSetting\AddValidation;
use App\Http\Requests\Web\ContactUsSetting\EditValidation;
use Illuminate\Http\Request;
use View, AppHelper, Image, URL;

class ContactUsController extends AdminBaseController
{
    protected $base_route = 'web.admin.settings.contact-us';
    protected $view_path = 'web.admin.settings.contact-us';
    protected $panel = 'Contact US Page Setting';
    protected $folder_path;
    protected $folder_name = 'contact_us_settings';
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        $data['row'] = WebContactUsSetting::first();
        $data['url'] = '';

        if($data['row']){
            return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
        }else{
            return view(parent::WebsiteViewHelper($this->view_path.'.add'), compact('data'));
        }

    }

    public function add()
    {
        $data = [];
        $data['row'] = WebContactUsSetting::first();
        if($data['row']){
            return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
        };
        return view(parent::WebsiteViewHelper($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $data['row'] = WebContactUsSetting::first();
        if($data['row']){
            return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
        };

         $request->request->add(['created_by' => auth()->user()->id]);

        WebContactUsSetting::create($request->all());

        $request->session()->flash($this->message_success, $this->panel. ' successfully added.');

        return redirect()->route($this->view_path);
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = WebContactUsSetting::find($id))
        return parent::invalidRequest();

        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        if (!$row = WebContactUsSetting::find($id)) return parent::invalidRequest();
        

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' successfully updated.');
        return redirect()->route($this->base_route);
    }

}

