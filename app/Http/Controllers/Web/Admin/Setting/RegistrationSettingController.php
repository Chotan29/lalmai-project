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
use App\Models\Web\WebRegistrationSetting;
use Carbon\Carbon;


use App\Http\Requests\Web\RegistrationSetting\AddValidation;
use App\Http\Requests\Web\RegistrationSetting\EditValidation;
use Illuminate\Http\Request;
use View, AppHelper, Image, URL;

class RegistrationSettingController extends AdminBaseController
{
    protected $base_route = 'web.admin.settings.registration';
    protected $view_path = 'web.admin.settings.registration';
    protected $panel = 'Registration Setting';
    protected $folder_path;
    protected $folder_name = 'registration_settings';
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        $data['row'] = WebRegistrationSetting::first();
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
        $data['row'] = WebRegistrationSetting::first();
        if($data['row']){
            return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
        };
        return view(parent::WebsiteViewHelper($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $data['row'] = WebRegistrationSetting::first();
        if($data['row']){
            return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
        };


        if ($request->hasFile('logo_image')){
            $logo_image_name = parent::uploadImages($request, 'logo_image');
        }else{
            $logo_image_name = "";
        }

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['logo' => isset($logo_image_name)?$logo_image_name:'']);
        $request->request->add(['start_date' => Carbon::parse($request->start_date)->format('Y-m-d')]);
        $request->request->add(['end_date' => Carbon::parse($request->end_date)->format('Y-m-d')]);

        //dd($request->all());
        WebRegistrationSetting::create($request->all());

        $request->session()->flash($this->message_success, $this->panel. ' successfully added.');
        return redirect()->route($this->view_path);
    }


    public function edit(Request $request, $id)
    {
        $data = [];
        if (!$data['row'] = WebRegistrationSetting::find($id))
        return parent::invalidRequest();

        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        if (!$row = WebRegistrationSetting::find($id)) return parent::invalidRequest();

        if ($request->hasFile('logo_image')){
            $logo_image_name = parent::uploadImages($request, 'logo_image');
            // remove old image from folder
            if (file_exists($this->folder_path.$row->logo))
                @unlink($this->folder_path.$row->logo);
        }


        $request->request->add(['logo' => isset($logo_image_name)?$logo_image_name:$row->logo]);
        $request->request->add(['start_date' => Carbon::parse($request->start_date)->format('Y-m-d')]);
        $request->request->add(['end_date' => Carbon::parse($request->end_date)->format('Y-m-d')]);
        $request->request->add(['last_updated_by' => auth()->user()->id]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' successfully updated.');
        return redirect()->route($this->base_route);
    }
}