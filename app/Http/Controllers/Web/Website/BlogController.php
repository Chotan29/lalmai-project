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
use App\Models\Page;
use App\Models\Web\WebBlog;
use App\Models\Web\WebBlogCategory;
use App\Models\Web\WebPage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BlogController extends WebsiteBaseController
{
    protected $view_path = 'web.website.blog';

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $this->middleware('page-status');

        $data = [];
        if($request->all()){
            $data['rows'] = WebBlog::select(
                'web_blogs.title', 'web_blogs.slug', 'web_blogs.short_desc','web_blogs.short_desc','web_blogs.detail_desc', 'web_blogs.image', 'web_blogs.publish_date',
                'web_blogs.view_count', 'web_blog_categories.title as category_title','web_blog_categories.slug as category_slug')
                ->join('web_blog_categories', 'web_blogs.category_id', '=', 'web_blog_categories.id')
                ->where('web_blogs.status', 1)
                ->where(function ($query) use ($request) {

                    if ($request->has('cat')) {
                        $query->where('web_blog_categories.slug', $request->cat);
                        $this->filter_query['web_blog_categories.slug'] = $request->title;
                    }

                    if ($request->has('s')) {
                        $query->where('web_blogs.title', 'like', '%'.$request->s.'%');
                        $this->filter_query['web_blogs.title'] = $request->s;
                    }
                })
                ->where('web_blog_categories.status', 1)
                ->where('web_blogs.publish_date', '<=', Carbon::now())
                ->orderBy('web_blogs.publish_date', 'desc')
                ->paginate($this->pagination_limit);
        }else{
            $data['rows'] = WebBlog::select('web_blogs.title', 'web_blogs.slug', 'web_blogs.short_desc','web_blogs.short_desc','web_blogs.detail_desc', 'web_blogs.image', 'web_blogs.publish_date',
                'web_blogs.view_count', 'web_blog_categories.title as category_title','web_blog_categories.slug as category_slug')
                ->join('web_blog_categories', 'web_blogs.category_id', '=', 'web_blog_categories.id')
                ->where('web_blogs.status', 1)
                ->where('web_blog_categories.status', 1)
                ->where('web_blogs.publish_date', '<=', Carbon::now())
                ->orderBy('web_blogs.publish_date', 'desc')
                ->paginate($this->pagination_limit);
        }


        $data['categories'] = WebBlogCategory::select('id', 'title', 'slug', 'description', 'image', 'status')
            ->Active()
            ->get();

        $data['child_page'] = WebPage::select('title', 'slug', 'page_type', 'link')
            ->where('slug','<>', 'home')
            ->Active()
            ->limit(10)
            ->orderBy('title')
            ->get();



        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

    public function blogDetail($slug)
    {
        $data = [];
        $data['row'] = WebBlog::select(
            'web_blogs.id','web_blogs.created_by','web_blogs.title', 'web_blogs.slug', 'web_blogs.detail_desc', 'web_blogs.image', 'web_blogs.publish_date',
            'web_blogs.view_count','web_blogs.seo_title', 'web_blogs.seo_keywords', 'web_blogs.seo_description', 'web_blog_categories.title as category_title','web_blog_categories.slug as category_slug')
            ->join('web_blog_categories', 'web_blogs.category_id', '=', 'web_blog_categories.id')
            ->where('web_blogs.slug', $slug)
            ->where('web_blogs.status', 1)
            ->first();

        if(!$data['row'])
            return redirect()->route('web.home');

        WebBlog::where('id','=',$data['row']->id)->increment('view_count', 1);

        $data['recent'] = WebBlog::select('title', 'slug', 'short_desc', 'publish_date', 'image')
            ->Active()
            ->whereNotIn('id',[$data['row']->id])
            ->limit(10)
            ->get();

        $data['categories'] = WebBlogCategory::select('id', 'title', 'slug', 'description', 'image', 'status')
            ->Active()
            ->get();

        return view(parent::WebsiteViewHelper($this->view_path.'.blog_detail'), compact('data'));
    }

    protected function getBlogCategories()
    {
        return WebBlogCategory::select('slug', 'title')->get();
    }
}