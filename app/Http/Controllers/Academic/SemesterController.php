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
use App\Http\Requests\Academic\Semester\AddValidation;
use App\Http\Requests\Academic\Semester\EditValidation;
use App\Models\AcademicInfoLevel;
use App\Models\GradingType;
use App\Models\Semester;
use App\Models\Staff;
use App\Models\Subject;
use Illuminate\Http\Request;

class SemesterController extends CollegeBaseController
{
    protected $base_route = 'semester';
    protected $view_path = 'academic.academic-hub.semester';
    protected $panel ;
    protected $filter_query = [];

    public function __construct()
    {
        $this->panel = __('academic.child.academic_level.child.semester');
    }

    public function index(Request $request)
    {
        $data = [];
        $data['semester'] = Semester::orderBy('semester')
            ->get();

        $data['gradingScales'] = [];
        $data['gradingScales'][] = '';
        foreach (GradingType::select('id','title')->Active()->get() as $grading) {
            $data['gradingScales'][$grading->id] = $grading->title;
        }

        $data['staffs'] = [];
        $data['staffs'][] = '';
        foreach (Staff::select('id','first_name','middle_name','last_name')->Active()->get() as $staff) {
            $data['staffs'][$staff->id] = $staff->first_name.' '.$staff->middle_name.' '.$staff->last_name ;
        }


        $data['academicInfoLevels'] = AcademicInfoLevel::select('id', 'title')
            ->Active()
            ->get();

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        //dd($request->all());
        $request->request->add(['created_by' => auth()->user()->id]);
       $sem = Semester::create($request->all());

        $subjects = [];
        if($request->get('sem_subject_id')){
            foreach ($request->get('sem_subject_id') as $subject){
                $subjects[$subject] = [
                    'created_by' => auth()->user()->id,
                    'semester_id' => $sem->id,
                    'subject_id' => $subject
                ];
            }
        }
        $sem->subjects()->sync($subjects);


        //manage academic info levels
        $academicInfoLevel = [];
        if($request->get('academic_level')){
            foreach ($request->get('academic_level') as $level){
                $academicInfoLevel[$level] = [
                    'semester_id' => $sem->id,
                    'academic_info_level_id' => $level
                ];
            }
        }

        $sem->programNeedAcademicLevel()->sync($academicInfoLevel);

       $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');
       return redirect()->route($this->base_route);
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = Semester::find($id))
            return parent::invalidRequest();

        $data['semester'] = Semester::orderBy('semester')
            ->get();

        $data['gradingScales'] = [];
        $data['gradingScales'][] = '';
        foreach (GradingType::select('id','title')->Active()->get() as $grading) {
            $data['gradingScales'][$grading->id] = $grading->title;
        }

        $data['staffs'] = [];
        $data['staffs'][] = '';
        foreach (Staff::select('id','first_name','middle_name','last_name')->Active()->get() as $staff) {
            $data['staffs'][$staff->id] = $staff->first_name.' '.$staff->middle_name.' '.$staff->last_name ;
        }

        $data['html'] = view($this->view_path.'.includes.subject_tr_rows', [
            'subjects' => $data['row']->subjects
        ])->render();

        $data['academicInfoLevels'] = AcademicInfoLevel::select('id', 'title')
            ->Active()
            ->get();

        $data['active_academic_level'] = $data['row']->programNeedAcademicLevel()->pluck('academic_info_levels.title', 'academic_info_levels.id')->toArray();

        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
       if (!$row = Semester::find($id)) return parent::invalidRequest();

        $request->request->add(['last_updated_by' => auth()->user()->id]);
       $row->update($request->all());

        $subjects = [];
        if($request->get('sem_subject_id')){
            foreach ($request->get('sem_subject_id') as $subject){
                $subjects[$subject] = [
                    'created_by' => auth()->user()->id,
                    'last_updated_by' => auth()->user()->id,
                    'semester_id' => $row->id,
                    'subject_id' => $subject
                ];
            }
        }
        $row->subjects()->sync($subjects);


        //manage academic info levels
        $academicInfoLevel = [];
        if($request->get('academic_level')){
            foreach ($request->get('academic_level') as $level){
                $academicInfoLevel[$level] = [
                    'semester_id' => $row->id,
                    'academic_info_level_id' => $level
                ];
            }
        }

        $row->programNeedAcademicLevel()->sync($academicInfoLevel);

        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Semester::find($id)) return parent::invalidRequest();
        $row->subjects()->sync([]);
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
                            $row = Semester::find(decrypt($row_id));
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = Semester::find(decrypt($row_id));
                            $row->subjects()->sync([]);
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
        if (!$row = Semester::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->semester.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Semester::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->semester.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function subjectHtmlRow(Request $request)
    {
        $response = [];
        $response['error'] = true;
        if ($request->has('id')) {
            if ($subject = Subject::find($request->get('id'))) {
                $response['error'] = false;
                $response['html'] = view($this->view_path.'.includes.subject_tr', [
                    'subject' => $subject,
                ])->render();
                $response['message'] = 'Operation successful.';

            } else{
                $response['message'] = $request->get('subject_id').'Invalid request!!';
            }
        } else{
            $response['message'] = $request->get('subject_id').'Invalid request!!';
        }

        return response()->json(json_encode($response));
    }
}