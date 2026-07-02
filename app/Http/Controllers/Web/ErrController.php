<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\Website\WebsiteBaseController;
use App\Models\Blog;
use App\Models\Event;
use App\Models\Notice;
use App\Models\Web\WebBlog;
use App\Models\Web\WebEvent;
use App\Models\Web\WebNotice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use View, URL;

class ErrController extends WebsiteBaseController
{

    protected $limit = 5;

    public function __construct()
    {
    }

    //404
    public function pageNotFound(Request $request)
    {
        $data['url'] = URL::current();
        //Notice
        $data['notice'] = WebNotice::select('title', 'slug', 'description', 'publish_date', 'image')
            ->Active()
            ->where('publish_date', '<=', Carbon::now())
            ->where(function ($query) use ($request) {
                if ($request->has('s')) {
                    $query->where('title', 'like', '%'.$request->s.'%');
                    $this->filter_query['title'] = $request->s;
                }
            })
            ->orderBy('publish_date', 'desc')
            ->limit($this->limit)
            ->get();

        //Blog
        $data['blog'] = WebBlog::select('web_blogs.id', 'web_blogs.created_by', 'web_blogs.publish_date', 'web_blogs.title', 'web_blogs.slug', 'web_blogs.short_desc', 'web_blogs.updated_at', 'web_blogs.image', 'web_blogs.status', 'categories.id as category_id', 'categories.title as category_title')
            ->join('categories', 'web_blogs.category_id', '=', 'categories.id')
            ->where('web_blogs.status', 1)
            ->where('web_blogs.publish_date', '<=', Carbon::now())
            ->where(function ($query) use ($request) {
                if ($request->has('s')) {
                    $query->where('web_blogs.title', 'like', '%'.$request->s.'%');
                    $this->filter_query['web_blogs.title'] = $request->s;
                }
            })
            ->orderBy('web_blogs.publish_date', 'desc')
            ->limit($this->limit)
            ->get();



        //Events
        $data['event'] = WebEvent::select('id', 'title', 'slug', 'description', 'event_date', 'event_time', 'event_place')
            ->Active()
            ->where(function ($query) use ($request) {
                if ($request->has('s')) {
                    $query->where('title', 'like', '%'.$request->s.'%');
                    $this->filter_query['title'] = $request->s;
                }
            })
            ->orderBy('event_date', 'desc')
            ->limit($this->limit)
            ->get();

        return view(parent::WebsiteViewHelper('web.errors.404'), compact('data'));
    }

}