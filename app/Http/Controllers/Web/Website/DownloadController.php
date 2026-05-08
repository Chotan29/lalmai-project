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


use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Web\WebBlog;
use App\Models\Web\WebDownload;
use Illuminate\Http\Request;

class DownloadController extends WebsiteBaseController
{

    protected $view_path = 'web.website.download';
    public function __construct()
    {

    }
    
    public function index(Request $request)
    {
        $this->middleware('page-status');
        $data = [];
        $data['rows'] = WebDownload::where('status', 1)
                    ->where(function ($query) use ($request) {

                        if ($request->has('s')) {
                            $query->where('title', 'like', '%'.$request->s.'%');
                            $this->filter_query['title'] = $request->s;
                        }
                    })
                    ->latest()
                    ->paginate($this->pagination_limit);

        $data['recent'] = WebBlog::select('title', 'slug', 'short_desc', 'publish_date', 'image')
            ->Active()
            ->limit(10)
            ->get();

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));

    }

}