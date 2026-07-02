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

use App\Models\Web\WebBlogCategory;
use App\Models\Web\WebNotice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NoticeController extends WebsiteBaseController
{
    protected $folder_name = 'notice';
    protected $view_path = 'web.website.notice';
    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $this->middleware('page-status');

        $data['rows'] = WebNotice::where('status', 1)
        ->where('publish_date', '<=', Carbon::now())
        ->where(function ($query) use ($request) {

            if ($request->has('s')) {
                $query->where('title', 'like', '%'.$request->s.'%');
                $this->filter_query['title'] = $request->s;
            }
        })
        ->orderBy('publish_date', 'desc')
        ->paginate($this->pagination_limit);

        $data['categories'] = WebBlogCategory::select('id', 'title', 'slug', 'description', 'image', 'status')
            ->Active()
            ->get();

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

    public function noticeDetail($slug)
    {
        $data = [];
        if(!$data['row'] = WebNotice::where('slug', $slug)->Active()->first())
            return redirect()->route('web.home');

        $data['recent'] =  WebNotice::select('title', 'slug')
            ->Active()
            ->whereNotIn('id',[$data['row']->id])
            ->latest()
            ->limit(10)
            ->get();

        WebNotice::where('id','=',$data['row']->id)->increment('view_count', 1);


        return view(parent::WebsiteViewHelper($this->view_path.'.notice_detail'), compact('data'));
    }
}