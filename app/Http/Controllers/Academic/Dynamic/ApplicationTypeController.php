<?php

namespace App\Http\Controllers\Academic\Dynamic;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Academic\Dynamic\Degree\AddValidation;
use App\Http\Requests\Academic\Dynamic\Degree\EditValidation;
use App\Models\ApplicationType;
use Illuminate\Http\Request;

class ApplicationTypeController extends CollegeBaseController
{
    protected $base_route = 'dynamic.application-type';
    protected $view_path = 'academic.dynamic-hub.application-type';
    protected $panel;
    protected $filter_query = [];

    public function __construct()
    {
        $this->panel = __('academic.child.dynamic_gallery.child.application_type');
    }

    public function index(Request $request)
    {
        $data = [];
        $data['state'] = ApplicationType::get();
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(AddValidation $request)
    {
       $request->request->add(['created_by' => auth()->user()->id]);

       ApplicationType::create($request->all());

       $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');
       return redirect()->route($this->base_route);
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = ApplicationType::find($id))
            return parent::invalidRequest();

        $data['state'] = ApplicationType::orderBy('title')->get();

        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
       if (!$row = ApplicationType::find($id)) return parent::invalidRequest();

        $request->request->add(['last_updated_by' => auth()->user()->id]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = ApplicationType::find($id)) return parent::invalidRequest();

        $row->delete();

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {
                foreach ($request->get('chkIds') as $row_id) {
                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            $row = ApplicationType::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = ApplicationType::find($row_id);
                            $row->delete();
                            break;
                    }
                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')) . ' Successfully.');
                else
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')).' successfully.');

                return redirect()->route($this->base_route);

            } else {
                $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
                return redirect()->route($this->base_route);
            }

        } else return parent::invalidRequest();

    }

    public function active(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = ApplicationType::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->semester.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = ApplicationType::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->semester.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function ApproveActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = ApplicationType::find($id)) return parent::invalidRequest();


        $request->request->add(['need_approve' => 1]);
        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->semester.' '.$this->panel.'  Make Need Activation YES Successfully.');
        return redirect()->route($this->base_route);
    }

    public function ApproveinActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = ApplicationType::find($id)) return parent::invalidRequest();

        $request->request->add(['need_approve' => 0]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->semester.' '.$this->panel.' Make Need Activation No Successfully.');
        return redirect()->route($this->base_route);
    }
}