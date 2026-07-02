<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace app\Http\Controllers\Web\Website;

use App\Http\Controllers\Web\Website\WebsiteBaseController;
use App\Models\Web\WebBlog;
use App\Models\Web\WebClientAward;
use App\Models\Web\WebCounter;
use App\Models\Web\WebEvent;
use App\Models\Web\WebNotice;
use App\Models\Web\WebService;
use App\Models\Web\WebSlider;
use App\Models\Web\WebTestimonial;
use Carbon\Carbon;

class HomeController extends WebsiteBaseController
{

    protected $view_path = 'web.website.home';
    public $limit = 10;

    public function index()
    {
        $data = [];
        $data['home_setting'] = $this->getHomeSetting();

        if (!isset($data['home_setting'])) {
            // Keep webportal publicly accessible even when home settings are not configured.
            \Log::warning('[WEBPORTAL_TRACE] Home setting missing; rendering fallback public page.');
        }



        if(isset($data['home_setting'])) {
            if ($data['home_setting']->slider_status == 1) {
                $data['slider'] = WebSlider::select('id', 'title', 'description', 'image_name', 'link', 'button_text', 'open_in')
                    ->Active()
                    ->orderBy('rank', 'asc')
                    ->Active()
                    ->get();
            }

            if ($data['home_setting']->notice_status == 1) {
                $data['notice'] = WebNotice::select('title', 'slug', 'description', 'publish_date', 'image')
                    ->Active()
                    ->where('publish_date', '<=', Carbon::now())
                    ->orderBy('publish_date', 'desc')
                    ->limit($this->limit)
                    ->get();
            }

            if ($data['home_setting']->blog_status == 1) {
                $data['blog'] = WebBlog::select('web_blogs.id', 'web_blogs.created_by', 'web_blogs.publish_date', 'web_blogs.title', 'web_blogs.slug', 'web_blogs.short_desc', 'web_blogs.updated_at', 'web_blogs.image', 'web_blogs.status', 'web_blog_categories.id as category_id', 'web_blog_categories.title as category_title')
                    ->join('web_blog_categories', 'web_blogs.category_id', '=', 'web_blog_categories.id')
                    ->where('web_blogs.status', 1)
                    ->where('web_blogs.publish_date', '<=', Carbon::now())
                    ->orderBy('web_blogs.publish_date', 'desc')
                    ->limit($this->limit)
                    ->get();

            }

            if ($data['home_setting']->event_status == 1) {
                $data['event'] = WebEvent::select('id', 'title', 'slug', 'description', 'event_date', 'event_time', 'event_place')
                    ->Active()
                    ->orderBy('event_date', 'desc')
                    ->limit($this->limit)
                    ->get();

            }

            if ($data['home_setting']->services_status == 1) {
                $data['services'] = WebService::select('id', 'title','sub_title', 'description', 'image','video', 'link', 'button_text', 'open_in', 'rank')
                    ->Active()
                    ->orderBy('rank', 'asc')
                    ->Active()
                    ->get();
            }

            if ($data['home_setting']->counter_status == 1) {
                $data['counter'] = WebCounter::select('icon', 'title', 'counter')
                    ->where('type', '=', 'home')
                    ->Active()
                    ->limit(4)
                    ->orderBy('rank', 'asc')
                    ->get();
            }

            if ($data['home_setting']->testimonial_status == 1) {
                $data['testimonial'] = WebTestimonial::select('id', 'name', 'comment','office','position', 'link', 'profile_image')
                    ->Active()
                    ->limit($this->limit)
                    ->inRandomOrder()
                    ->get();
            }

          /*  if ($data['home_setting']->staff_status == 1) {
                $data['staffs'] = Staff::select('id', 'name', 'position', 'profile_image')
                    ->Active()
                    ->limit(6)
                    ->orderBy('rank')
                    ->get();
            }*/

            if ($data['home_setting']->client_status == 1) {
                $data['client'] = WebClientAward::select('id', 'title',  'image_name', 'link')
                    ->Active()
                    ->orderBy('rank', 'asc')
                    ->Active()
                    ->get();
            }
        }

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }
}