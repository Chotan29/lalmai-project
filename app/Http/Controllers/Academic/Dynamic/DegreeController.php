<?php

namespace App\Http\Controllers\Academic\Dynamic;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Academic\Dynamic\Degree\AddValidation;
use App\Http\Requests\Academic\Dynamic\Degree\EditValidation;
use App\Models\CustomerStatus;
use App\Models\Degree;
use Illuminate\Http\Request;

class DegreeController extends CollegeBaseController
{
    protected $base_route = 'dynamic.degree';
    protected $view_path = 'academic.dynamic-hub.degree';
    protected $panel;
    protected $filter_query = [];

    public function __construct()
    {
        $this->panel = __('academic.child.dynamic_gallery.child.degree');
    }

    public function index(Request $request)
    {
        $data = [];
        $data['customer-status'] = Degree::select('id', 'title', 'status')->get();
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(AddValidation $request)
    {
       $request->request->add(['created_by' => auth()->user()->id]);

       Degree::create($request->all());

       $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');
       return redirect()->route($this->base_route);
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = Degree::find($id))
            return parent::invalidRequest();

        $data['customer-status'] = Degree::select('id', 'title', 'status')->orderBy('title')->get();

        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
       if (!$row = Degree::find($id)) return parent::invalidRequest();

        $request->request->add(['last_updated_by' => auth()->user()->id]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Degree::find($id)) return parent::invalidRequest();

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
                            $row = Degree::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = Degree::find($row_id);
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
        if (!$row = Degree::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->semester.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Degree::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->semester.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }
}