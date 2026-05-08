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
use App\Charts\BrowserCompare;
use App\Charts\DatePageViewsCompare;
use App\Charts\UserTypeCompare;
use App\Mail\MailSubscribers;
use App\Models\Client;
use App\Models\InfoNotice;
use App\Models\Service;
use App\Models\Web\ContactMesage;
use App\Models\Web\WebBlog;
use App\Models\Web\WebClientAward;
use App\Models\Web\WebCounter;
use App\Models\Web\WebEvent;
use App\Models\Web\WebFaq;
use App\Models\Web\WebGallery;
use App\Models\Web\WebNotice;
use App\Models\Web\WebPage;
use App\Models\Web\WebService;
use App\Models\Web\WebSlider;
use App\Models\Web\WebSubscriber;
use App\Models\Web\WebTestimonial;
use Carbon\Carbon;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View, AppHelper;
use App\Models\Slider;
use App\Models\Page;
use App\Models\Blog;
use App\Models\Notice;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\Download;
use App\Models\Counter;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\Staff;
use App\User;
use App\Models\ContactList;
use App\Models\Subscriber;

use Illuminate\Support\Facades\Mail;

use Analytics;
use Spatie\Analytics\Period;

class DashboardController extends AdminBaseController
{
    protected $base_route = 'web.admin.dashboard';
    protected $view_path = 'web.admin.dashboard';
    protected $panel = 'Dashboard';


    public function __construct()
    {
                    

    }

    public function index(Request $request)
    {
        $data = [];
        $userRoleId = auth()->user()->roles()->first()->id;

        $data['slider']         =   WebSlider::count();
        $data['pages']          =   WebPage::count();
        $data['blog']           =   WebBlog::count();
        $data['notice']         =   WebNotice::count();
        $data['event']          =   WebEvent::count();
        $data['service']        =   WebService::count();
        $data['gallery']        =   WebGallery::count();
        $data['download']       =   Download::count();
        $data['counter']        =   WebCounter::count();
        $data['faq']            =   WebFaq::count();
        $data['testimonial']    =   WebTestimonial::count();
        $data['staff']          =   Staff::count();
        $data['clint_award']    =   WebClientAward::count();

        $data['user']           =   User::count();
        $data['subscribers']    =   WebSubscriber::count();

        $data['contact_message'] = ContactMesage::orderBy('created_at', 'desc')
                                    ->where('view_status',0)
                                    ->paginate(4);

        /*if($setting = $this->getGeneralSetting()){
            if(isset($setting->analytics_view_id) && isset($setting->analytics_json_file) && file_exists(app_path('analytics'.DIRECTORY_SEPARATOR.$setting->analytics_json_file))){
                //analytics data
                //check internet connection
                $connected = @fsockopen("www.google.com", 80); //website, port  (try 80 or 443)
                if ($connected){
                    $defaultDays = 30;
                    $maxresult = 10;
                    $period = Period::days($defaultDays);

                    //Date Wise Visitors and previews Count
                    $data['date_views'] = Analytics::fetchTotalVisitorsAndPageViews($period);
                    //Visitor chart
                    $dateVisitors = $data['date_views']->pluck('visitors');
                    $datePageViews = $data['date_views']->pluck('pageViews');
                    $visitDate = $data['date_views']->pluck('date');

                    foreach ($visitDate as $vdate){
                        $visitDateSeparate[] = Carbon::parse($vdate)->format('d-M-Y');
                    }

                    //Date Visitor Page chart
                    $data['datePageVisitorChart'] = new DatePageViewsCompare($visitDateSeparate);
                    $data['datePageVisitorChart']->dataset('Visitors', 'bar', $dateVisitors)
                        ->options(['borderColor' => '#46b8da', 'backgroundColor'=>'#46b8da' ]);

                    $data['datePageVisitorChart']->dataset('Page Views', 'bar', $datePageViews)
                        ->options(['borderColor' => '#FF6384', 'backgroundColor'=>'#FF6384']);



                }else{
                    $request->session()->flash($this->message_warning, 'Internet Not Working Properly, Problem on Getting Google Analytic Data. Charts Not Showing Yet.');
                }

            }else{
                $request->session()->flash($this->message_warning, ' Analytics View Id & Json File Not Setup to View Analytic Charts.');
            }

        }else{
            return redirect()->route('web.admin.settings.general');
        }*/

        return view(parent::WebsiteViewHelper($this->view_path . '.index'), compact('data'));

    }

    /**
     * @param Request $request
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function analytics(Request $request)
    {
        $data = [];
        //check internet connection
        $connected = @fsockopen("www.google.com", 80); //website, port  (try 80 or 443)
        if (!$connected){
            return redirect()->route('web.admin.dashboard');
        }

        if($setting = $this->getGeneralSetting()){
            if(isset($setting->analytics_view_id) && isset($setting->analytics_json_file) && file_exists(app_path('analytics'.DIRECTORY_SEPARATOR.$setting->analytics_json_file))){
                //data gathering
                /*analytics*/
                //https://developers.google.com/analytics/devguides/reporting/core/v3/common-queries
                //https://github.com/spatie/laravel-analytics
                //https://erik.cat/projects/charts
                $defaultDays = 30;
                $maxresult = isset($request->max_result)?$request->max_result:10;
                if($request->all()){
                    if($request->period){
                        if($request->period == 'year'){
                            if($request->start_date && $request->end_date){
                                $filterYears = Carbon::parse($request->end_date)->diffInYears(Carbon::parse($request->start_date));
                            }
                            $years = isset($filterYears)?$filterYears:$defaultDays;

                            $period = Period::years($years);

                        }elseif($request->period == 'month'){

                            if($request->start_date && $request->end_date){
                                $filterMonths = Carbon::parse($request->end_date)->diffInMonths(Carbon::parse($request->start_date));
                            }
                            $months = isset($filterMonths)?$filterMonths:$defaultDays;

                            $period = Period::months($months);
                        }else{

                            if($request->start_date && $request->end_date){
                                $filterDays = Carbon::parse($request->end_date)->diffIndays(Carbon::parse($request->start_date));
                            }
                            $days = isset($filterDays)?$filterDays:$defaultDays;

                            $period = Period::days($days);
                        }
                    }
                }else{
                    $period = Period::days($defaultDays);
                }

                /*
                 * Visitors and page views
                 * Total visitors and page views
                 * */
                //User Types
                $userTypeCompare = Analytics::fetchUserTypes($period);
                $data['userTypeCompare'] = new UserTypeCompare($userTypeCompare->pluck('type'));
                $data['userTypeCompare']->dataset('UserType','doughnut', $userTypeCompare->pluck('sessions'))
                    ->options(['borderColor' => '#46b8da', 'backgroundColor'=>['#46b8da','#FF6384'] ]);

                //Top browsers
                $color =[];
                $browserCompare = Analytics::fetchTopBrowsers($period, $maxresult);
                foreach ($browserCompare as $browser){
                    $color[] = sprintf('#%06X', mt_rand(25, 0x46b8da));
                }

                $data['browserCompare'] = new BrowserCompare($browserCompare->pluck('browser'));
                $data['browserCompare']->dataset('Browser','doughnut', $browserCompare->pluck('sessions'))
                    ->options(['borderColor' => '#46b8da', 'backgroundColor'=>$color]);

                //Date Wise Visitors and previews Count
                $data['visitors_page'] = Analytics::fetchVisitorsAndPageViews($period,$maxresult);

                //Date Wise Visitors and previews Count
                $data['date_views'] = Analytics::fetchTotalVisitorsAndPageViews($period);

                /*Visitor chart*/
                $dateVisitors = $data['date_views']->pluck('visitors');
                $datePageViews = $data['date_views']->pluck('pageViews');
                $visitDate = $data['date_views']->pluck('date');

                foreach ($visitDate as $vdate){
                    $visitDateSeparate[] = Carbon::parse($vdate)->format('d-M-Y');
                }

                /*Date Visitor Page chart*/
                $data['datePageVisitorChart'] = new DatePageViewsCompare($visitDateSeparate);
                $data['datePageVisitorChart']->dataset('Visitors', 'bar', $dateVisitors)
                    ->options(['borderColor' => '#46b8da', 'backgroundColor'=>'#46b8da' ]);

                $data['datePageVisitorChart']->dataset('Page Views', 'bar', $datePageViews)
                    ->options(['borderColor' => '#FF6384', 'backgroundColor'=>'#FF6384']);

                //Most visited pages
                $data['page_views'] = Analytics::fetchMostVisitedPages($period, $maxresult);

                //Top referrers
                $data['top_referrals'] = Analytics::fetchTopReferrers($period, $maxresult);

            }else{
                $request->session()->flash($this->message_warning, ' Analytics View Id & Json File Not Setup to View Analytic Charts.');
            }

        }else{
            return redirect()->route('web.admin.settings.general');
        }


        return view(parent::WebsiteViewHelper($this->view_path . '.analytics.index'), compact('data'));
    }
}