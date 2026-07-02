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
use App\Models\Web\WebGeneralSetting;
use App\Traits\EnvironmentScope;

use App\Http\Requests\Web\GeneralSetting\AddValidation;
use App\Http\Requests\Web\GeneralSetting\EditValidation;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use View, AppHelper, Image, URL;

class GeneralSettingController extends AdminBaseController
{
    protected $base_route = 'web.admin.settings.general';
    protected $view_path = 'web.admin.settings.general';
    protected $panel = 'General Setting';
    protected $folder_path;
    protected $folder_name = 'general_settings';
    protected $filter_query = [];

    use EnvironmentScope;

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        $data['row'] = WebGeneralSetting::first();
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
        $data['row'] = WebGeneralSetting::first();
        if($data['row']){
            return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
        };
        return view(parent::WebsiteViewHelper($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $data['row'] = WebGeneralSetting::first();
        if($data['row']){
            return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
        };

        if ($request->hasFile('favicon_image')){
            $favicon_image_name = parent::uploadImages($request, 'favicon_image');
            $request->request->add(['favicon' => $favicon_image_name]);
        }

        if ($request->hasFile('site_logo_image')){
            $site_logo_image_name = parent::uploadImages($request, 'site_logo_image');
            $request->request->add(['site_logo' => $site_logo_image_name]);
        }


        if ($request->hasFile('page_banner_image')){
            $page_banner_image_name = parent::uploadImages($request, 'page_banner_image');
            $request->request->add(['page_banner' => $page_banner_image_name]);
        }

        $request->request->add(['created_by' => auth()->user()->id]);

        if ($request->hasFile('json_file')){
            $jsonFile = $request->file('json_file');
            $json_file_name = 'service-account-credentials.json';
            $jsonFile->move(app_path().DIRECTORY_SEPARATOR.'analytics'.DIRECTORY_SEPARATOR, $json_file_name);

            $request->request->add(['analytics_json_file' => $json_file_name]);
        }



        WebGeneralSetting::create($request->all());

        /*set values on .env*/
        $this->setEnv('ANALYTICS_VIEW_ID', $request->analytics_view_id);
        $this->setEnv('RECAPTCHA_SITE_KEY', $request->recaptcha_site_key);
        $this->setEnv('RECAPTCHA_SECRET_KEY', $request->recaptcha_secret_key);


        $request->session()->flash($this->message_success, $this->panel. ' successfully added.');

        return redirect()->route($this->view_path);
    }


    public function edit(Request $request, $id)
    {
        $data = [];
        if (!$data['row'] = WebGeneralSetting::find($id))
        return parent::invalidRequest();

        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        if (!$row = WebGeneralSetting::find($id)) return parent::invalidRequest();

        if ($request->hasFile('favicon_image')){
            $favicon_image_name = parent::uploadImages($request, 'favicon_image');
            // remove old image from folder
            if (file_exists($this->folder_path.$row->favicon))
                @unlink($this->folder_path.$row->favicon);

            $request->request->add(['favicon' => isset($favicon_image_name)?$favicon_image_name:$row->favicon]);

        }

        if ($request->hasFile('site_logo_image')){
            $site_logo_image_name = parent::uploadImages($request, 'site_logo_image');
            // remove old image from folder
            if (file_exists($this->folder_path.$row->site_logo))
                @unlink($this->folder_path.$row->site_logo);

            $request->request->add(['site_logo' => isset($site_logo_image_name)?$site_logo_image_name:$row->site_logo]);
        }


        if ($request->hasFile('page_banner_image')){
            $page_banner_image_name = parent::uploadImages($request, 'page_banner_image');
            // remove old image from folder
            if (file_exists($this->folder_path.$row->page_banner))
                @unlink($this->folder_path.$row->page_banner);

            $request->request->add(['page_banner' => isset($page_banner_image_name)?$page_banner_image_name:$row->page_banner]);

        }



        $request->request->add(['last_updated_by' => auth()->user()->id]);

        if ($request->hasFile('json_file')){
            $jsonFile = $request->file('json_file');
            $json_file_name = 'service-account-credentials.json';
            $jsonFile->move(app_path().DIRECTORY_SEPARATOR.'analytics'.DIRECTORY_SEPARATOR, $json_file_name);

            if (file_exists($this->folder_path.$row->analytics_json_file))
                @unlink($this->folder_path.$row->analytics_json_file);
        }

        $request->request->add(['analytics_json_file' => isset($json_file_name)?$json_file_name:$row->analytics_json_file]);


        $row->update($request->all());

        /*set values on .env*/
        $this->setEnv('ANALYTICS_VIEW_ID', $request->analytics_view_id);
        $this->setEnv('RECAPTCHA_SITE_KEY', $request->recaptcha_site_key);
        $this->setEnv('RECAPTCHA_SECRET_KEY', $request->recaptcha_secret_key);

        $request->session()->flash($this->message_success, $this->panel.' successfully updated.');
        return redirect()->route($this->base_route);
    }

}