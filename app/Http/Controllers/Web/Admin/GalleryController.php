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
use App\Models\Web\WebGallery;
use App\Models\Web\WebGalleryImage;


use App\Http\Requests\Web\Gallery\AddValidation;
use App\Http\Requests\Web\Gallery\EditValidation;
use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use View, AppHelper, Image, URL,ViewHelper;

class GalleryController extends AdminBaseController
{
    protected $base_route = 'web.admin.gallery';
    protected $view_path = 'web.admin.gallery';
    protected $panel = 'Gallery';
    protected $folder_path;
    protected $folder_name = 'gallery';
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        $data['rows'] = WebGallery::where(function ($query) use ($request) {

                            if ($request->has('title')) {
                                $query->where('title', 'like', '%'.$request->title.'%');
                                $this->filter_query['title'] = $request->title;
                            }

                            if ($request->has('description')) {
                                $query->where('description', 'like', '%'.$request->description.'%');
                                $this->filter_query['description'] = $request->description;
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
                        ->orderBy('rank', 'asc')
                        ->paginate($this->pagination_limit);

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

    public function add()
    {
        $data = [];
        $data['blank_ins'] = new WebGallery();
        if ($latest = WebGallery::orderBy('id', 'DESC')->first())
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
            return parent::invalidRequest();

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['slug' => $request->get('title')]);
        $request->request->add(['image_name' => $image_name]);

        $row = WebGallery::create($request->all());

        if ($request->hasFile('gallery_image')) {
            foreach ($request->file('gallery_image') as $key => $image) {

                $image_name = parent::uploadImagesWithDimensions($image);
                WebGalleryImage::create([
                    'gallery_id' => $row->id,
                    'image' => $image_name,
                    'caption' => $request->get('gallery_caption')[$key],
                    'status' => $request->get('gallery_status')[$key],
                    'created_by' => auth()->user()->id,
                    'rank' => $key + 1
                ]);
            }
        }

        $request->session()->flash($this->message_success, $this->panel. ' successfully added.');
        return redirect()->route($this->base_route);
    }

    public function view($id)
    {
        $id = decrypt($id);
        $data = [];
        $data['row'] = WebGallery::find($id);
        $data['base_route'] = $this->base_route;

        return view(parent::WebsiteViewHelper($this->view_path.'.view'), compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = WebGallery::find($id))
            return parent::invalidRequest();

        $data['images'] = $data['row']->images;

        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if ($row = WebGallery::find($id)){

            if ($request->hasFile('main_image')) {

                $image_name = parent::uploadImages($request, 'main_image');

                // remove old image from folder
                if (file_exists($this->folder_path . $row->image_name))
                    @unlink($this->folder_path . $row->image_name);

            }

            $request->request->add(['last_updated_by' => auth()->user()->id]);
            $request->request->add(['image_name' => isset($image_name) ? $image_name : $row->image_name]);

            $row->update($request->all());

            if ($request->has('gallery_caption')) {

                $current_option_value_ids = [];

                // 1. with only old data
                // 2. with all old data and new data
                // 3. with some old data removed and no new
                // 3. with some old data and new data
                // 4. removed all old data and new data

                foreach ($request->get('gallery_caption') as $key => $value) {

                    if ($request->has('gallery_id') && array_key_exists($key, $request->get('gallery_id'))) {

                        $current_gallery_value_ids[] = $gallery_value_id = $request->get('gallery_id')[$key];
                        $gallery_value = WebGalleryImage::find($gallery_value_id);

                        $image_name = $gallery_value->image;
                        if ($request->hasFile('gallery_image') && array_key_exists($key, $request->file('gallery_image'))) {

                            $image_name = parent::uploadImagesWithDimensions($request->file('gallery_image')[$key]);
                            if (file_exists($this->folder_path.$gallery_value->image)) {

                                @unlink($this->folder_path.$gallery_value->image);
                            }
                        }

                        $gallery_value->update([
                            'gallery_id' => $row->id,
                            'image' => $image_name,
                            'caption' => $request->get('gallery_caption')[$key],
                            'alt_text' => $value,
                            'status' => $request->get('gallery_status')[$key],
                            'rank' => $key + 1
                        ]);

                    } else {

                        if ($request->hasFile('gallery_image') && array_key_exists($key, $request->file('gallery_image'))) {

                            $image_name = parent::uploadImagesWithDimensions($request->file('gallery_image')[$key]);

                            $gallery_value_row = WebGalleryImage::create([
                                'gallery_id' => $row->id,
                                'image' => $image_name,
                                'caption' => $request->get('gallery_caption')[$key],
                                'alt_text' => $value,
                                'status' => $request->get('gallery_status')[$key],
                                'created_by' => auth()->user()->id,
                                'rank' => $key + 1
                            ]);

                            $current_gallery_value_ids[] = $gallery_value_row->id;
                        }
                    }
                }

                WebGalleryImage::where('gallery_id', $row->id)->whereNotIn('id', $current_gallery_value_ids)->delete();


            } else {

                // if there is not existing image values then should remove for
                // Gallery image table
                if ($row->images->count() > 0) {
                    foreach ($row->images as $image) {
                        if (file_exists($this->folder_path.$image->image)) {
                            @unlink($this->folder_path.$image->image);
                        }

                        $image->delete();
                    }

                }

            }
            $request->session()->flash($this->message_success, $this->panel.' successfully updated.');

        } else {
            $request->session()->flash('message_error', 'Invalid Request!!');
        }

        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);

        if (!$row = WebGallery::find($id)) return parent::invalidRequest();

      // remove old image from folder
       if (file_exists($this->folder_path . $row->image_name))
            @unlink($this->folder_path . $row->image_name);

        $row->delete();

        if ($row->images->count() > 0) {
            foreach ($row->images as $image) {
                if (file_exists($this->folder_path.$image->image)) {
                    @unlink($this->folder_path.$image->image);
                }
                $image->delete();
            }
        }

        $request->session()->flash($this->message_success, $this->panel.' successfully deleted.');
        return redirect()->route($this->base_route);

    }

    public function active(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebGallery::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebGallery::find($id)) return parent::invalidRequest();

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
                    $row = WebGallery::find($row_id);
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
                           if (file_exists($this->folder_path . $row->image_name))
                                @unlink($this->folder_path . $row->image_name);

                            $row->delete();

                            if ($row->images->count() > 0) {
                                foreach ($row->images as $image) {
                                    if (file_exists($this->folder_path.$image->image)) {
                                        @unlink($this->folder_path.$image->image);
                                    }
                                    $image->delete();
                                }
                            }
                            
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

    public function imageHtml()
    {
        $response = [];
        $response['error'] = false;
        $response['html'] = view($this->view_path.'.includes.image_tr')->render();
        return response()->json(json_encode($response));
    }

    public function removeImage(Request $request)
    {
        $response = [];
        $response['error'] = true;

        if ($request->has('gallery_id')) {

            if ($product = WebGallery::find($request->get('gallery_id'))) {

                if (file_exists($this->folder_path.$product->main_image)) {

                    @unlink($this->folder_path.$product->main_image);
                }

                $product->main_image = null;
                $product->save();

                $response['message'] = 'Image removed successfully.';
                $response['error'] = false;


            } else
                $response['message'] = 'Invalid request!!!';

        } else
            $response['message'] = 'Invalid request!!!';

        return response()->json(json_encode($response));
    }


}