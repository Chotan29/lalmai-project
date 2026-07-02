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
use App\Models\Category;
use App\Models\Web\WebBlog;
use App\Models\Web\WebBlogCategory;
use Carbon\Carbon;

class CategoryController extends WebsiteBaseController
{

    protected $view_path = 'web.website.category';
    public function __construct()
    {

    }

    public function index()
    {
        $this->middleware('page-status');

        $data = [];
        $data['rows'] = WebBlogCategory::select('id', 'title', 'slug', 'description', 'image', 'status')
            ->Active()
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

    public function CategoryPost($slug)
    {
        $data = [];
        if(!$data['row'] = WebBlogCategory::where('slug', $slug)->first())
            return redirect()->route('web.home');

        $data['category_post'] = WebBlog::select(
            'web_blogs.title', 'web_blogs.slug', 'web_blogs.short_desc', 'web_blogs.image', 'web_blogs.publish_date',
            'web_blogs.view_count', 'web_blog_categories.title as category_title','web_blog_categories.slug as category_slug')
            ->join('web_blog_categories', 'web_blog_categories.id', '=', 'web_blogs.category_id')
            ->where('web_blogs.category_id',$data['row']->id)
            ->where('web_blogs.status', 1)
            ->where('web_blog_categories.status', 1)
            ->where('web_blogs.publish_date', '<=', Carbon::now())
            ->orderBy('web_blogs.publish_date', 'desc')
            ->paginate($this->pagination_limit);

        $data['categories'] = WebBlogCategory::select('id', 'title', 'slug', 'description', 'image', 'status')
            ->Active()
            ->get();

        $data['recent'] = WebBlog::select('title', 'slug', 'short_desc', 'publish_date', 'image')
            ->Active()
            ->whereNotIn('id',[$data['row']->id])
            ->limit(10)
            ->get();


        return view(parent::WebsiteViewHelper($this->view_path.'.category_detail'), compact('data'));

    }
}