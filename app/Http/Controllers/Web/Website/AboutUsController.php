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


use App\Models\Web\WebAboutUsSetting;
use App\Models\Web\WebCounter;
use App\Models\Web\WebPage;
use App\Models\WebAboutUs;
use App\Models\Counter;
use App\Models\Page;
use App\Models\Staff;
use Illuminate\Http\Request;

class AboutUsController extends WebsiteBaseController
{

    protected $view_path = 'web.website.about_us';
    public function __construct()
    {
        $this->middleware('page-status');
    }

    public function index(Request $request)
    {
        $data = [];

        $data['pageDetail'] = $request->instance()->query('pageDetail');

        $data['aboutUs_setting'] = WebAboutUsSetting::first();

        if(!$data['aboutUs_setting'])
            return redirect()->route('web.home');

        if($data['aboutUs_setting']->counter_status == 1) {
            $pageId = WebPage::where('link','about-us')->first();

            $data['child_page'] = WebPage::select('id','title', 'slug', 'page_type', 'link')
                ->where('parent_id','=', $pageId->id)
                ->Active()
                ->orderBy('title')
                ->get();

            $data['counter'] = WebCounter::select('icon', 'title', 'counter')
                ->where('type', '=', 'about_us')
                ->Active()
                ->limit(4)
                ->orderBy('rank', 'asc')
                ->get();


        }

        /*if($data['aboutUs_setting']->staffs_status == 1) {
            $data['staffs'] = Staff:: where('status', 1)
                                        ->orderBy('rank', 'asc')
                                        ->get();

        }*/

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }
}