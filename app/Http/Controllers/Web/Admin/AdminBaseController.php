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

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Web\WebBaseController;
use App\Models\Web\WebAboutUsSetting;
use App\Models\Web\WebContactUsSetting;
use App\Models\Web\WebHomeSetting;
use App\Traits\CommonScope;
use View;
use AppHelper, Image;
use Illuminate\Support\Str;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;

class AdminBaseController extends WebBaseController
{
    public $pagination_limit = 10;
    public $registration_pagination_limit = 100;


    use CommonScope;

    protected function WebsiteViewHelper($view_path)
    {
        View::composer($view_path, function ($view) {
            $view->with('base_route', $this->base_route);
            $view->with('view_path', $this->view_path);
            $view->with('panel', $this->panel);
            $view->with('folder_name', property_exists($this, 'folder_name')?$this->folder_name:'');
            $view->with('admin_info', $this->getGeneralSetting());
            $view->with('profileImageSrc', $this->profileImageSrc());
            //$view->with('contact_info', $this->contactInfo());
        });

        return $view_path;
    }

    protected function getGeneralSetting()
    {
        $data['general_setting'] = GeneralSetting::first();
        return $data['general_setting'];
    }

    protected function invalidRequest($message = 'Invalid request!!')
    {
        request()->session()->flash($this->message_warning, $message);
        return redirect()->route($this->base_route);
    }

    protected function uploadImages(Request $request, $image_name)
    {
        $image = $request->file($image_name);
        $filename = basename($request->file($image_name)->getClientOriginalName(), '.'.$request->file($image_name)->getClientOriginalExtension());
        $image_name = Str::slug($filename).'.'.$image->getClientOriginalExtension();
        $image->move($this->folder_path, $image_name);

        return $image_name;
    }

    protected function uploadImagesWithDimensions($image)
    {
        $image_name = rand(4585, 9857) . '_' . $image->getClientOriginalName();
        $image->move($this->folder_path, $image_name);

        /*if (config('freelancerumesh.image_dimensions.' . $this->folder_name . '.gallery_image')){
            foreach (config('freelancerumesh.image_dimensions.' . $this->folder_name . '.gallery_image') as $dimension) {

                // open and resize an image file
                $img = Image::make($this->folder_path . $image_name)->resize($dimension['width'], $dimension['height']);

                // save the same file as jpeg with default quality
                $img->save($this->folder_path . $dimension['width'] . '_' . $dimension['height'] . '_' . $image_name);

            }
        }*/

        return $image_name;
    }

    /*protected function uploadImagesWithDimensions($image)
    {
        $image_name = rand(4585, 9857) . '_' . $image->getClientOriginalName();
        $image->move($this->folder_path, $image_name);

        if (config('freelancerumesh.image_dimensions.' . $this->folder_name . '.gallery_image')){
            foreach (config('freelancerumesh.image_dimensions.' . $this->folder_name . '.gallery_image') as $dimension) {

                // open and resize an image file
                $img = Image::make($this->folder_path . $image_name)->resize($dimension['width'], $dimension['height']);

                // save the same file as jpeg with default quality
                $img->save($this->folder_path . $dimension['width'] . '_' . $dimension['height'] . '_' . $image_name);

            }
        }

        return $image_name;
    }*/

    protected function uploadFile(Request $request, $download_file)
    {

        $download_file = $request->file($download_file);
        $download_file_name = rand(4585, 9857).'_'.$download_file->getClientOriginalName();

        $download_file->move($this->folder_path, $download_file_name);

        return $download_file_name;
    }

    public function checkRequireSettings()
    {
        //check other required setting too
        $generalSetting = GeneralSetting::first();
        if($generalSetting){
            $homePageSetting = WebHomeSetting::first();
            $aboutUsPageSetting = WebAboutUsSetting::first();
            $contactUsPageSetting = WebContactUsSetting::first();
            if(!isset($homePageSetting))
                return redirect()->route('web.admin.settings.home');

            if(!isset($aboutUsPageSetting))
                return redirect()->route('web.admin.settings.about-us');

            if(!isset($contactUsPageSetting))
                return redirect()->route('web.admin.settings.contact-us');

        }
    }
}