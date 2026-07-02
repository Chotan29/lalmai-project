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
use App\Http\Requests\Academic\Faculty\AddValidation;
use App\Http\Requests\Academic\Faculty\EditValidation;
use App\Models\AcademicInfoLevel;
use App\Models\Faculty;
use App\Models\GradingType;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class FacultyController extends CollegeBaseController
{
    protected $base_route = 'faculty';
    protected $view_path = 'academic.academic-hub.faculty';
    protected $panel;
    protected $filter_query = [];

    public function __construct()
    {
        $this->panel = __('academic.child.academic_level.child.faculty');
    }

    public function index()
    {
       $data = [];

       $data['faculty'] = Faculty::orderBy('sorting')->get();

       $data['semester'] = Semester::select('id', 'semester','slug')
            ->Active()
            ->OrderBy('semester','asc')
            ->get();

        $data['gradingScales'] = [];
        $data['gradingScales'][] = '';
        foreach (GradingType::select('id','title')->Active()->get() as $grading) {
            $data['gradingScales'][$grading->id] = $grading->title;
        }

       return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $request->request->add(['created_by' => auth()->user()->id]);

        $faculty = Faculty::create($request->all());

        //manage semester
        $semesters = [];
        if($request->get('semester')){
            foreach ($request->get('semester') as $semester){
                $semesters[$semester] = [
                    'faculty_id' => $faculty->id,
                    'semester_id' => $semester
                ];
            }
        }

        $faculty->semesters()->sync($semesters);

        $request->session()->flash($this->message_success, $this->panel. 'Created Successfully.');
        return redirect()->route($this->base_route);
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = Faculty::find($id))
            return parent::invalidRequest();

        $data['faculty'] = Faculty::orderBy('faculty')->get();

        $data['semester'] = Semester::select('id', 'semester','slug')
            ->Active()
            ->get();

        $data['active_semester'] = $data['row']->semesters()->pluck('semesters.semester', 'semesters.id')->toArray();

        $data['gradingScales'] = [];
        $data['gradingScales'][] = '';
        foreach (GradingType::select('id','title')->Active()->get() as $grading) {
            $data['gradingScales'][$grading->id] = $grading->title;
        }

        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Faculty::find($id)) return parent::invalidRequest();

        $request->request->add(['last_updated_by' => auth()->user()->id]);

        $faculty = $row->update($request->all());

        //manage semester
        if($faculty){
            $semesters = [];
            if($request->get('semester')){
                foreach ($request->get('semester') as $semester){
                    $semesters[$semester] = [
                        'faculty_id' => $row->id,
                        'semester_id' => $semester
                    ];
                }
            }

            $row->semesters()->sync($semesters);
        }

        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Faculty::find($id)) return parent::invalidRequest();

        DB::table('faculty_head_programs')->where('faculty_id', $row->id)->delete();
        $row->semesters()->detach();
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
                            $row = Faculty::find(decrypt($row_id));
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = Faculty::find(decrypt($row_id));
                            if ($row) {
                                DB::table('faculty_head_programs')->where('faculty_id', $row->id)->delete();
                                $row->semesters()->detach();
                                $row->delete();
                            }
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
        if (!$row = Faculty::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->faculty.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Faculty::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->faculty.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }
}
