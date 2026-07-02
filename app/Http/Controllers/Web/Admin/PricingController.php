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

use App\Http\Requests\Web\Pricing\AddValidation;
use App\Http\Requests\Web\Pricing\EditValidation;
use App\Models\Web\WebPricing;
use Illuminate\Http\Request;
use View, AppHelper, Image, URL;

class PricingController extends AdminBaseController
{
    protected $base_route = 'web.admin.pricing';
    protected $view_path = 'web.admin.pricing';
    protected $panel = 'Pricing';
    protected $folder_path;
    protected $folder_name = 'pricing';
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        $data['rows'] = WebPricing::where(function ($query) use ($request) {
                                if ($request->has('title')) {
                                    $query->where('title', 'like', '%'.$request->title.'%');
                                    $this->filter_query['title'] = $request->title;
                                }

                                if ($request->has('description')) {
                                    $query->where('description', 'like', '%'.$request->description.'%');
                                    $this->filter_query['description'] = $request->description;
                                }

                                if ($request->has('old_price')) {
                                    $query->where('old_price', '=', $request->old_price);
                                    $this->filter_query['old_price'] = $request->old_price;
                                }

                                if ($request->has('new_price')) {
                                    $query->where('new_price', '=', $request->new_price);
                                    $this->filter_query['new_price'] = $request->new_price;
                                }

                                if ($request->has('per_term')) {
                                    $query->where('per_term', 'like', '%'.$request->per_term.'%');
                                    $this->filter_query['per_term'] = $request->per_term;
                                }

                                if ($request->has('tag')) {
                                    $query->where('tag', 'like', '%'.$request->tag.'%');
                                    $this->filter_query['tag'] = $request->tag;
                                }

                                if ($request->has('link')) {
                                    $query->where('link', 'like', '%'.$request->link.'%');
                                    $this->filter_query['link'] = $request->link;
                                }

                                if ($request->has('open_in')) {
                                    $query->where('open_in', '=', $request->open_in);
                                    $this->filter_query['open_in'] = $request->open_in;
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

                                if ($request->has('rank')) {
                                    $query->where('rank', '=', $request->rank);
                                    $this->filter_query['rank'] = $request->rank;
                                }

                                if ($request->has('status')) {
                                    $query->where('status', $request->status == 'active'?1:0);
                                    $this->filter_query['status'] = $request->get('status');
                                }

                            })
                            ->paginate($this->pagination_limit);

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

    public function add()
    {
        $data = [];
        $data['blank_ins'] = new WebPricing();
        if ($latest = WebPricing::orderBy('id', 'DESC')->first())
            $rank = $latest->rank + 10;
        else
            $rank = 10;

        $data['blank_ins']->status = 'active';
        $data['blank_ins']->rank = $rank;
        return view(parent::WebsiteViewHelper($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        if ($request->hasFile('main_image'))
            $image_name = parent::uploadImages($request, 'main_image');
        else
            $image_name ="";

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['slug' => $request->get('title')]);
        $request->request->add(['image' => $image_name]);

        WebPricing::create($request->all());

        $request->session()->flash($this->message_success, $this->panel. ' successfully added.');
        return redirect()->route($this->base_route);
    }

    public function view($id)
    {
        $id = decrypt($id);
        $data = [];
        $data['row'] = WebPricing::find($id);
        $data['base_route'] = $this->base_route;

        return view(parent::WebsiteViewHelper($this->view_path.'.view'), compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = WebPricing::find($id))
            return parent::invalidRequest();

        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebPricing::find($id)) return parent::invalidRequest();

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
        if (!$row = WebPricing::find($id)) return parent::invalidRequest();

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
        if (!$row = WebPricing::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebPricing::find($id)) return parent::invalidRequest();

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
                    $row = WebPricing::find($row_id);
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
                    $request->session()->flash($this->message_success, 'Bulk Action Successful.');
                else
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')).' successfully.');

                return redirect()->route($this->base_route);

            } else {
                $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
                return redirect()->route($this->base_route);
            }

        } else return parent::invalidRequest();


    }

}