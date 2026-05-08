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
use App\Http\Requests\Web\Staff\AddValidation;
use App\Http\Requests\Web\Staff\EditValidation;
use App\Models\Web\WebStaff;
use Illuminate\Http\Request;
use View, AppHelper, Image, URL;

class StaffController extends AdminBaseController
{
    protected $base_route = 'web.admin.staff';
    protected $view_path = 'web.admin.staff';
    protected $panel = 'Staff';
    protected $folder_path;
    protected $folder_name = 'staff';
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        $data['rows'] = WebStaff::where(function ($query) use ($request) {

                        if ($request->has('name')) {
                            $query->where('name', 'like', '%'.$request->name.'%');
                            $this->filter_query['name'] = $request->name;
                        }

                        if ($request->has('position')) {
                            $query->where('position', 'like', '%'.$request->position.'%');
                            $this->filter_query['position'] = $request->position;
                        }

                        if ($request->has('address')) {
                            $query->where('address', 'like', '%'.$request->address.'%');
                            $this->filter_query['address'] = $request->address;
                        }

                        if ($request->has('tel')) {
                            $query->where('tel', 'like', '%'.$request->tel.'%');
                            $this->filter_query['tel'] = $request->tel;
                        }

                        if ($request->has('cel_1')) {
                            $query->where('cel_1', 'like', '%'.$request->cel_1.'%');
                            $this->filter_query['cel_1'] = $request->cel_1;
                        }

                        if ($request->has('cel_2')) {
                            $query->where('cel_2', 'like', '%'.$request->cel_2.'%');
                            $this->filter_query['cel_2'] = $request->cel_2;
                        }

                        if ($request->has('email')) {
                            $query->where('email', 'like', '%'.$request->email.'%');
                            $this->filter_query['email'] = $request->email;
                        }

                        if ($request->has('description')) {
                            $query->where('description', 'like', '%'.$request->description.'%');
                            $this->filter_query['description'] = $request->description;
                        }

                        if ($request->has('twitter_url')) {
                            $query->where('twitter_url', 'like', '%'.$request->twitter_url.'%');
                            $this->filter_query['twitter_url'] = $request->twitter_url;
                        }

                        if ($request->has('linkedIn_url')) {
                            $query->where('linkedIn_url', 'like', '%'.$request->linkedIn_url.'%');
                            $this->filter_query['linkedIn_url'] = $request->linkedIn_url;
                        }

                        if ($request->has('facebook_url')) {
                            $query->where('facebook_url', 'like', '%'.$request->facebook_url.'%');
                            $this->filter_query['facebook_url'] = $request->facebook_url;
                        }

                        if ($request->has('instagram_url')) {
                            $query->where('instagram_url', 'like', '%'.$request->instagram_url.'%');
                            $this->filter_query['instagram_url'] = $request->instagram_url;
                        }

                        if ($request->has('whatsapp_url')) {
                            $query->where('whatsapp_url', 'like', '%'.$request->whatsapp_url.'%');
                            $this->filter_query['whatsapp_url'] = $request->whatsapp_url;
                        }

                        if ($request->has('skype_url')) {
                            $query->where('skype_url', 'like', '%'.$request->skype_url.'%');
                            $this->filter_query['skype_url'] = $request->skype_url;
                        }

                        if ($request->has('pinterest_url')) {
                            $query->where('pinterest_url', 'like', '%'.$request->pinterest_url.'%');
                            $this->filter_query['pinterest_url'] = $request->pinterest_url;
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
                    ->orderBy('rank','asc')
                    ->paginate($this->pagination_limit);

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

    public function add()
    {
        $data = [];
        $data['blank_ins'] = new WebStaff();
        if ($latest = WebStaff::orderBy('id', 'DESC')->first())
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
        $request->request->add(['slug' => $request->get('name')]);
        $request->request->add(['profile_image' => $image_name]);

        WebStaff::create($request->all());

        $request->session()->flash($this->message_success, $this->panel. ' successfully added.');
        return redirect()->route($this->base_route);
    }

    public function view($id)
    {
        $id = decrypt($id);
        $data = [];
        $data['row'] = WebStaff::find($id);
        $data['base_route'] = $this->base_route;

        return view(parent::WebsiteViewHelper($this->view_path.'.view'), compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = WebStaff::find($id))
            return parent::invalidRequest();

        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebStaff::find($id)) return parent::invalidRequest();

        if ($request->hasFile('main_image')) {

            $image_name = parent::uploadImages($request, 'main_image');

            // remove old image from folder
            if (file_exists($this->folder_path.$row->profile_image))
                @unlink($this->folder_path.$row->profile_image);
        }

        $request->request->add(['last_updated_by' => auth()->user()->id]);
        $request->request->add(['slug' => $request->get('name')]);
        $request->request->add(['profile_image' => isset($image_name)?$image_name:$row->profile_image]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' successfully updated.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebStaff::find($id)) return parent::invalidRequest();

        // remove old image from folder
        if ($row->profile_image && file_exists($this->folder_path.$row->profile_image)) {
            @unlink($this->folder_path.$row->profile_image);
        }

        $row->delete();

        $request->session()->flash($this->message_success, $this->panel.' successfully deleted.');
        return redirect()->route($this->base_route);

    }

    public function active(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebStaff::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebStaff::find($id)) return parent::invalidRequest();

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
                    $row = WebStaff::find($row_id);
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
                            if ($row->profile_image && file_exists($this->folder_path.$row->profile_image)) {
                                @unlink($this->folder_path.$row->profile_image);
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