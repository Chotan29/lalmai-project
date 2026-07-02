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
use App\Models\Web\WebPage;

use App\Http\Requests\Web\Page\AddValidation;
use App\Http\Requests\Web\Page\EditValidation;

use Illuminate\Http\Request;
use View, AppHelper, Image, URL;

class PageController extends AdminBaseController
{
    protected $base_route = 'web.admin.page';
    protected $view_path = 'web.admin.page';
    protected $panel = 'Page';
    protected $folder_path;
    protected $folder_name = 'page';
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        $data['rows'] = WebPage::where(function ($query) use ($request) {

                if ($request->has('title')) {
                    $query->where('title', 'like', '%'.$request->title.'%');
                    $this->filter_query['title'] = $request->title;
                }

                if ($request->has('page_type')) {
                    $query->where('page_type', '=', $request->page_type);
                    $this->filter_query['page_type'] = $request->page_type;
                }

                if ($request->has('link')) {
                    $query->where('link', '=', $request->link);
                    $this->filter_query['link'] = $request->link;
                }

                if ($request->has('detail_desc')) {
                    $query->where('detail_desc', 'like', '%'.$request->detail_desc.'%');
                    $this->filter_query['detail_desc'] = $request->detail_desc;
                }

                if ($request->has('create_start_date') && $request->has('create_end_date')) {
                    $query->whereBetween('created_at', [$request->get('create_start_date'), $request->get('create_end_date')]);
                    $this->filter_query['create_start_date'] = $request->get('create_start_date');
                    $this->filter_query['create_end_date'] = $request->get('create_end_date');
                }
                elseif ($request->has('create_start_date')) {
                    $query->where('created_at', '>=', $request->get('create_start_date'));
                    $this->filter_query['create_start_date'] = $request->get('create_start_date');
                }
                elseif($request->has('create_end_date')) {
                    $query->where('created_at', '<=', $request->get('create_end_date'));
                    $this->filter_query['create_end_date'] = $request->get('create_end_date');
                }

                if ($request->has('update_start_date') && $request->has('update_end_date')) {
                    $query->whereBetween('updated_at', [$request->get('update_start_date'), $request->get('update_end_date')]);
                    $this->filter_query['update_start_date'] = $request->get('update_start_date');
                    $this->filter_query['update_end_date'] = $request->get('update_end_date');
                }
                elseif ($request->has('update_start_date')) {
                    $query->where('updated_at', '>=', $request->get('update_start_date'));
                    $this->filter_query['update_start_date'] = $request->get('update_start_date');
                }
                elseif($request->has('update_end_date')) {
                    $query->where('updated_at', '<=', $request->get('update_end_date'));
                    $this->filter_query['update_end_date'] = $request->get('update_end_date');
                }

                if ($request->has('status')) {
                    $query->where('status', $request->status == 'active'?1:0);
                    $this->filter_query['status'] = $request->get('status');
                }

            })
            ->orderBy('title', 'asc')
            ->paginate($this->pagination_limit);


            
        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

    public function add()
    {
        $data = $this->getParentPageOptions();
        return view(parent::WebsiteViewHelper($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        if ($request->hasFile('main_image')){
            $image_name = parent::uploadImages($request, 'main_image');
        }else{
            $image_name = "";
        }

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['slug' => $request->get('title')]);
        $request->request->add(['image' => $image_name]);

        WebPage::create($request->all());

        $request->session()->flash($this->message_success, $this->panel. ' successfully added.');
        return redirect()->route($this->base_route);
    }

    public function view($id)
    {
        $id = decrypt($id);
        $data = [];
        $data['row'] = WebPage::find($id);
        $data['base_route'] = $this->base_route;

        return view(parent::WebsiteViewHelper($this->view_path.'.view'), compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = WebPage::find($id))
            return parent::invalidRequest();

        $data = array_merge($data, $this->getParentPageOptions());
        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebPage::find($id)) return parent::invalidRequest();

        if ($request->hasFile('main_image')) {

            $image_name = parent::uploadImages($request, 'main_image');

            // remove old image from folder
            if (file_exists($this->folder_path.$row->image))
                @unlink($this->folder_path.$row->image);
        }

        $request->request->add(['last_updated_by' => auth()->user()->id]);
        $request->request->add(['slug' => $request->get('title')]);
        $request->request->add(['image' => isset($image_name)?$image_name:$row->image]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' successfully updated.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebPage::find($id)) return parent::invalidRequest();

        if($row->page_type == "predefine-link")
            return back()->with($this->message_warning, 'You cannot delete predefine link pages.');

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
        if (!$row = WebPage::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebPage::find($id)) return parent::invalidRequest();

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
                    $row = WebPage::find($row_id);
                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }

                            break;
                        case 'delete':
                            if($row->page_type == "predefined-link")
                                return back()->with($this->message_warning, 'You cannot delete predefine link pages.');
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

    protected function getParentPageOptions()
    {
        $data = [];
        $data['parent_pages'] = WebPage::select('id', 'title')
            ->where('parent_id', 0)
            ->get();

        $data['select_categories'][] = 'Is Parent';
        foreach ($data['parent_pages'] as $parent_page) {
            $data['select_categories'][$parent_page->id] = $parent_page->title;
        }

        return $data;
    }


}