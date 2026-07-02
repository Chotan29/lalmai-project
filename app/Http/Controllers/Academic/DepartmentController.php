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
use App\Http\Requests\Academic\Department\AddValidation;
use App\Http\Requests\Academic\Department\EditValidation;
use App\Models\Department;
use App\Models\DepartmentHead;
use App\Models\DepartmentProgram;
use App\Models\Faculty;
use Illuminate\Http\Request;
class DepartmentController extends CollegeBaseController
{
    protected $base_route = 'department';
    protected $view_path = 'academic.academic-hub.department';
    protected $panel;
    protected $filter_query = [];

    public function __construct()
    {
        $this->panel = __('academic.child.academic_level.child.department');
    }

    public function index()
    {
       $data = [];

       $data['department'] = Department::orderBy('sorting')->get();
       $data['faculty'] = Faculty::select('id', 'faculty')
            ->Active()
            ->OrderBy('faculty','asc')
            ->get();

       return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $request->request->add(['created_by' => auth()->user()->id]);

       // dd($request->all());
        $department = Department::create($request->all());

        //manage faculty
        $faculties = [];
        if($request->get('faculty')){
            foreach ($request->get('faculty') as $faculty){
                $faculties[$faculty] = [
                    'department_id' => $department->id,
                    'faculty_id' => $faculty
                ];
            }
        }

        $department->faculties()->sync($faculties);

        $request->session()->flash($this->message_success, $this->panel. 'Created Successfully.');
        return redirect()->route($this->base_route);
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = Department::find($id))
            return parent::invalidRequest();

        $data['department'] = Department::orderBy('department')->get();

        $data['faculty'] = Faculty::select('id', 'faculty')
            ->Active()
            ->get();

        $data['active_faculty'] = $data['row']->faculties()->orderBy('faculties.faculty')->pluck('faculties.faculty', 'faculties.id')->toArray();

        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$department = Department::find($id)) return parent::invalidRequest();

        $request->request->add(['last_updated_by' => auth()->user()->id]);
        $department->update($request->all());

        //manage faculty
        if($department){
            $faculties = [];
            if($request->get('faculty')){
                foreach ($request->get('faculty') as $faculty){
                    $faculties[$faculty] = [
                        'department_id' => $department->id,
                        'faculty_id' => $faculty
                    ];
                }
            }

            $department->faculties()->sync($faculties);
        }


        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Department::find($id)) return parent::invalidRequest();

        $row->delete();
        $row->faculties()->detach();

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
                            $row = Department::find(decrypt($row_id));
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = Department::find(decrypt($row_id));
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
        if (!$row = Department::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->faculty.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Department::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->faculty.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }
}
