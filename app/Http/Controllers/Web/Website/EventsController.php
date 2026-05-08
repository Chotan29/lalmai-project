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


use App\Models\Blog;
use App\Models\Category;
use App\Models\Event;
use App\Models\Web\WebBlog;
use App\Models\Web\WebBlogCategory;
use App\Models\Web\WebEvent;
use Illuminate\Http\Request;

class EventsController extends WebsiteBaseController
{
    protected $folder_name = 'events';
    protected $view_path = 'web.website.events';
    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $this->middleware('page-status');

        $data['rows'] = WebEvent::Active()
                        ->where(function ($query) use ($request) {
                            if ($request->has('s')) {
                                $query->where('title', 'like', '%'.$request->s.'%');
                                $this->filter_query['title'] = $request->s;
                            }
                        })
                        ->orderBy('event_date', 'desc')
                        ->paginate($this->pagination_limit);

        $data['recent'] = WebBlog::select('title', 'slug', 'short_desc', 'publish_date', 'image')
            ->Active()
            ->limit(10)
            ->get();

        $data['categories'] = WebBlogCategory::select('id', 'title', 'slug', 'description', 'image', 'status')
            ->Active()
            ->get();

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

    public function eventDetail($slug)
    {
        $data = [];
        if(!$data['row'] = WebEvent::where('slug', $slug)->first())
            return redirect()->route('web.home');

        $data['recent'] = WebEvent::select('title', 'slug', 'description', 'event_date', 'image', 'status')
            ->Active()
            ->whereNotIn('id',[$data['row']->id])
            ->limit(5)
            ->get();

        WebEvent::where('id','=',$data['row']->id)->increment('view_count', 1);

        return view(parent::WebsiteViewHelper($this->view_path.'.events_detail'), compact('data'));
    }
}