<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani 1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Academic\DepartmentHead\AddValidation;
use App\Http\Requests\Academic\DepartmentHead\EditValidation;
use App\Models\AcademicInfoLevel;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\DepartmentHead;
use App\Models\GradingType;
use App\Models\Semester;
use Illuminate\Http\Request;
class DepartmentHeadController extends CollegeBaseController
{
    protected $base_route = 'department-head';
    protected $view_path = 'academic.academic-hub.department-head';
    protected $panel;
    protected $filter_query = [];

    public function __construct()
    {
        $this->panel = __('academic.child.academic_level.child.department_head');
    }

    public function index()
    {
       $data = [];

       $data['department-head'] = DepartmentHead::orderBy('sorting')->get();

       $data['department'] = Department::select('id', 'department')
            ->Active()
            ->OrderBy('department','asc')
            ->get();

       return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $request->request->add(['created_by' => auth()->user()->id]);

        $departmentHead = DepartmentHead::create($request->all());

        //manage department
        $departments = [];
        if($request->get('department')){
            foreach ($request->get('department') as $department){
                $departments[$department] = [
                    'department_head_id' => $departmentHead->id,
                    'department_id' => $department
                ];
            }
        }

        $departmentHead->department()->sync($departments);

        $request->session()->flash($this->message_success, $this->panel. 'Created Successfully.');
        return redirect()->route($this->base_route);
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = DepartmentHead::find($id))
            return parent::invalidRequest();

        $data['department-head'] = DepartmentHead::orderBy('department_head')->get();

        $data['department'] = Department::select('id', 'department')
            ->Active()
            ->get();
        //dd($data['department']);

        $data['active_department'] = $data['row']->department()->orderBy('departments.department')->pluck('departments.department', 'departments.id')->toArray();

        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$departmentHead = DepartmentHead::find($id)) return parent::invalidRequest();

        $request->request->add(['last_updated_by' => auth()->user()->id]);

        $departmentHead->update($request->all());

        //manage department
        if($departmentHead){
            $departments = [];
            if($request->get('department')){
                foreach ($request->get('department') as $department){
                    $departments[$department] = [
                        'department_head_id' => $departmentHead->id,
                        'department_id' => $department
                    ];
                }
            }

            $departmentHead->department()->sync($departments);
        }

        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = DepartmentHead::find($id)) return parent::invalidRequest();

        $row->delete();
        $row->department()->detach();

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
                            $row = DepartmentHead::find(decrypt($row_id));
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = DepartmentHead::find(decrypt($row_id));
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
        if (!$row = DepartmentHead::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->department.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = DepartmentHead::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->department.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }
}
