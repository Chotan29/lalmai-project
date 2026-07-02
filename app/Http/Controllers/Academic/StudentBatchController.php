<?php
/*
 * Mr. Umesh Kumar Yadav
 */

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Academic\StudentBatch\AddValidation;
use App\Http\Requests\Academic\StudentBatch\EditValidation;
use App\Models\StudentBatch;
use Illuminate\Http\Request;

class StudentBatchController extends CollegeBaseController
{
    protected $base_route = 'student-batch';
    protected $view_path = 'academic.academic-hub.student-batch';
    protected $panel;
    protected $filter_query = [];

    public function __construct()
    {
        $this->panel = __('academic.child.academic_level.child.batch');
    }

    public function index(Request $request)
    {
        $data = [];
        $data['student-batch'] = StudentBatch::select('id', 'title','active_status', 'status')->get();
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(AddValidation $request)
    {
       $request->request->add(['created_by' => auth()->user()->id]);

       StudentBatch::create($request->all());

       $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');
       return redirect()->route($this->base_route);
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = StudentBatch::find($id))
            return parent::invalidRequest();

        $data['student-batch'] = StudentBatch::select('id', 'title', 'status')->orderBy('title')->get();

        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
       if (!$row = StudentBatch::find($id)) return parent::invalidRequest();

        $request->request->add(['last_updated_by' => auth()->user()->id]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = StudentBatch::find($id)) return parent::invalidRequest();

        $row->delete();

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {
                foreach ($request->get('chkIds') as $row_id) {
                    $row_id = decrypt($row_id);
                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            $row = StudentBatch::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = StudentBatch::find($row_id);
                            $row->delete();
                            break;
                    }
                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                    $request->session()->flash($this->message_success, $request->get('bulk_action'). ' Action Successfully.');
                else
                    $request->session()->flash($this->message_success, 'Deleted successfully.');

                return redirect()->route($this->base_route);

            } else {
                $request->session()->flash($this->message_warning, 'Please, Check at least one row.');
                return redirect()->route($this->base_route);
            }

        } else return parent::invalidRequest();

    }

    public function active(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = StudentBatch::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->semester.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = StudentBatch::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->semester.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function activeStatus(request $request, $id)
    {
        $id = decrypt($id);
        StudentBatch::where('id','=',$id)->update(['active_status' => 1]);
        StudentBatch::where('id','!=',$id)->update(['active_status' => 0]);
        $request->session()->flash($this->message_success, $this->panel.' Active Successfully.');

        return redirect()->route($this->base_route);
    }
}