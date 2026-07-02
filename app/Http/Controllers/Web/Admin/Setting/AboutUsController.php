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
use App\Models\Web\WebAboutUsSetting;

use App\Http\Requests\Web\AboutUsSetting\AddValidation;
use App\Http\Requests\Web\AboutUsSetting\EditValidation;
use Illuminate\Http\Request;
use View, AppHelper, Image, URL;

class AboutUsController extends AdminBaseController
{
    protected $base_route = 'web.admin.settings.about-us';
    protected $view_path = 'web.admin.settings.about-us';
    protected $panel = 'About US Page Setting';
    protected $folder_path;
    protected $folder_name = 'about_us_settings';
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        $data['row'] = WebAboutUsSetting::first();
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
        $data['row'] = WebAboutUsSetting::first();
        if($data['row']){
            return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
        };
        return view(parent::WebsiteViewHelper($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $data['row'] = WebAboutUsSetting::first();
        if($data['row']){
            return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
        };

        if ($request->hasFile('main_image')){
            $image_name = parent::uploadImages($request, 'main_image');
        }else{
            $image_name = "";
        }


        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['image' => $image_name]);

        WebAboutUsSetting::create($request->all());

        $request->session()->flash($this->message_success, $this->panel. ' successfully added.');


        return redirect()->route($this->view_path);
    }


    public function edit(Request $request, $id)
    {
        $data = [];
        if (!$data['row'] = WebAboutUsSetting::find($id))
        return parent::invalidRequest();

        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        if (!$row = WebAboutUsSetting::find($id)) return parent::invalidRequest();
        
        if ($request->hasFile('main_image')){
            $image_name = parent::uploadImages($request, 'main_image');
            // remove old image from folder
            if (file_exists($this->folder_path.$row->image))
                @unlink($this->folder_path.$row->image);

        }


        $request->request->add(['last_updated_by' => auth()->user()->id]);
        $request->request->add(['image' => isset($image_name)?$image_name:$row->image]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' successfully updated.');
        return redirect()->route($this->base_route);
    }

}