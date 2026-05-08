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
use App\Models\Web\WebBlog;
use App\Models\Web\WebBlogCategory;
use Carbon\Carbon;

use App\Http\Requests\Web\Blog\AddValidation;
use App\Http\Requests\Web\Blog\EditValidation;
use Illuminate\Http\Request;
use View, AppHelper, Image, URL, WebsiteViewHelper;

class BlogController extends AdminBaseController
{
    protected $base_route = 'web.admin.blog';
    protected $view_path = 'web.admin.blog';
    protected $panel = 'Post';
    protected $folder_path;
    protected $folder_name = 'blog';
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        $data['rows'] = WebBlog::select('web_blogs.created_at','web_blogs.updated_at','web_blogs.id','web_blogs.title', 'web_blogs.slug', 'web_blogs.short_desc', 'web_blogs.detail_desc', 'web_blogs.publish_date',
                                    'web_blogs.seo_title', 'web_blogs.seo_keywords', 'web_blogs.seo_description', 'web_blogs.image', 'web_blogs.view_count',
                                    'web_blogs.status', 'cat.id as cat_id')
                            ->where(function ($query) use ($request) {

                                if ($request->has('category_id') && $request->get('category_id') !==null ) {
                                    $query->where('web_blogs.category_id', 'like', '%'.$request->category_id.'%');
                                    $this->filter_query['category_id'] = $request->category_id;
                                }

                                if ($request->has('title') && $request->get('title') !==null ) {
                                    $query->where('web_blogs.title', 'like', '%'.$request->title.'%');
                                    $this->filter_query['title'] = $request->title;
                                }

                                if ($request->has('description')) {
                                    $query->where('web_blogs.detail_desc', 'like', '%'.$request->description.'%');
                                    $this->filter_query['description'] = $request->description;
                                }

                                if ($request->has('publish_start_date') && $request->has('publish_end_date')) {
                                    $query->whereBetween('web_blogs.publish_date', [$request->get('publish_start_date'), $request->get('publish_end_date')]);
                                    $this->filter_query['publish_start_date'] = $request->get('publish_start_date');
                                    $this->filter_query['publish_end_date'] = $request->get('publish_end_date');
                                }
                                elseif ($request->has('publish_start_date')) {
                                    $query->where('web_blogs.publish_date', '>=', $request->get('publish_start_date'));
                                    $this->filter_query['publish_start_date'] = $request->get('publish_start_date');
                                }
                                elseif($request->has('publish_end_date')) {
                                    $query->where('web_blogs.publish_date', '<=', $request->get('publish_end_date'));
                                    $this->filter_query['publish_end_date'] = $request->get('publish_end_date');
                                }

                                if ($request->has('create_start_date') && $request->has('create_end_date')) {
                                    $query->whereBetween('web_blogs.created_at', [$request->get('create_start_date'), $request->get('create_end_date')]);
                                    $this->filter_query['create_start_date'] = $request->get('create_start_date');
                                    $this->filter_query['create_end_date'] = $request->get('create_end_date');
                                }
                                elseif ($request->has('create_start_date')) {
                                    $query->where('web_blogs.created_at', '>=', $request->get('create_start_date'));
                                    $this->filter_query['create_start_date'] = $request->get('create_start_date');
                                }
                                elseif($request->has('create_end_date')) {
                                    $query->where('web_blogs.created_at', '<=', $request->get('create_end_date'));
                                    $this->filter_query['create_end_date'] = $request->get('create_end_date');
                                }

                                if ($request->has('update_start_date') && $request->has('update_end_date')) {
                                    $query->whereBetween('web_blogs.updated_at', [$request->get('update_start_date'), $request->get('update_end_date')]);
                                    $this->filter_query['update_start_date'] = $request->get('update_start_date');
                                    $this->filter_query['update_end_date'] = $request->get('update_end_date');
                                }
                                elseif ($request->has('update_start_date')) {
                                    $query->where('web_blogs.updated_at', '>=', $request->get('update_start_date'));
                                    $this->filter_query['update_start_date'] = $request->get('update_start_date');
                                }
                                elseif($request->has('update_end_date')) {
                                    $query->where('web_blogs.updated_at', '<=', $request->get('update_end_date'));
                                    $this->filter_query['update_end_date'] = $request->get('update_end_date');
                                }

                                if ($request->has('seo_title')) {
                                    $query->where('web_blogs.seo_title', 'like', '%'.$request->seo_title.'%');
                                    $this->filter_query['seo_title'] = $request->seo_title;
                                }

                                if ($request->has('seo_keywords')) {
                                    $query->where('web_blogs.seo_keywords', 'like', '%'.$request->seo_keywords.'%');
                                    $this->filter_query['seo_keywords'] = $request->seo_keywords;
                                }

                                if ($request->has('seo_description')) {
                                    $query->where('web_blogs.seo_description', 'like', '%'.$request->seo_description.'%');
                                    $this->filter_query['seo_description'] = $request->seo_description;
                                }

                                if ($request->has('status')) {
                                    $query->where('web_blogs.status', $request->status == 'active'?1:0);
                                    $this->filter_query['status'] = $request->get('status');
                                }

                            })
                            ->orderBy('publish_date', 'desc')
                            ->join('web_blog_categories as cat', 'web_blogs.category_id', '=', 'cat.id')
                            ->paginate($this->pagination_limit);


        $data['categories'] = $this->getCategoriesList();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

    public function add()
    {
        $data = [];
        $data['blank_ins'] = new WebBlog();
        $data['categories'] = $this->getCategoriesList();

        return view(parent::WebsiteViewHelper($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        if ($request->hasFile('main_image'))
            $image_name = parent::uploadImages($request, 'main_image');

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['slug' => $request->get('title')]);
        $request->request->add(['publish_date' => Carbon::parse($request->publish_date)->format('Y-m-d')]);
        $request->request->add(['image' => isset($image_name)?$image_name:'']);

        WebBlog::create($request->all());

        $request->session()->flash($this->message_success, $this->panel. ' successfully added.');
        return redirect()->route($this->base_route);
    }

    public function view($id)
    {
        $id = decrypt($id);
        $data = [];
        $data['row'] = WebBlog::find($id);
        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.view'), compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = WebBlog::find($id))
            return parent::invalidRequest();

        $data['categories'] = $this->getCategoriesList();

        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebBlog::find($id)) return parent::invalidRequest();

        if ($request->hasFile('main_image')) {

            $image_name = parent::uploadImages($request, 'main_image');

            // remove old image from folder
            if (file_exists($this->folder_path.$row->image))
                @unlink($this->folder_path.$row->image);


        }

        $request->request->add(['last_updated_by' => auth()->user()->id]);
        $request->request->add(['slug' => $request->get('title')]);
        $request->request->add(['publish_date' => Carbon::parse($request->publish_date)->format('Y-m-d')]);
        $request->request->add(['image' => isset($image_name)?$image_name:$row->image]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' successfully updated.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebBlog::find($id)) return parent::invalidRequest();

        // remove old image from folder
        if ($row->image && file_exists($this->folder_path.$row->image)) {
            @unlink($this->folder_path.$row->image);
        }

        $row->delete();

        $request->session()->flash($this->message_success, $this->panel.' successfully deleted.');
        return redirect()->route($this->base_route);

    }

    public function active(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebBlog::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebBlog::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {

                foreach ($request->get('chkIds') as $row_id) {
                    $row_id = decrypt($row_id);
                    $row = WebBlog::find($row_id);
                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }

                            break;
                        case 'delete':
                            // remove old image from folder
                            if ($row->image && file_exists($this->folder_path.$row->image)) {
                                @unlink($this->folder_path.$row->image);
                            }

                            $row->delete();
                            break;
                    }

                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                   $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')).' successful.');
                else
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')).' successfully.');

                return redirect()->route($this->base_route);

            } else {
                $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
                return redirect()->route($this->base_route);
            }

        } else return parent::invalidRequest();


    }

    protected function getCategoriesList()
    {
        $categories = WebBlogCategory::select('id', 'title', 'parent_id')
            ->Active()
            ->orderBy('title','asc')
            ->get();

        if(isset($categories) && $categories->count() > 0) {
            foreach ($categories as $item) {

                $data['categories'][$item->id] = WebsiteViewHelper::getCategoryTitle($item, $categories);

            }
        }else{
            $data['categories'] = [];
        }

        return $data['categories'];
    }

}