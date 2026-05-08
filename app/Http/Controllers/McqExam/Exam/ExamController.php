<?php

namespace App\Http\Controllers\McqExam\Exam;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Mcq\Exam\AddValidation;
use App\Http\Requests\Mcq\Exam\EditValidation;
use App\Models\Category;
use App\Models\McqExam;
use App\Models\McqQuestion;
use App\Models\McqQuestionGroup;
use App\Models\McqQuestionLevel;
use App\Models\McqQuestionOption;
use App\Models\Semester;
use App\Models\Subject;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Image, URL;
use ViewHelper;

class ExamController extends CollegeBaseController
{
    protected $base_route = 'mcq.exam';
    protected $view_path = 'mcq.exam.online-exam';
    protected $panel = 'Online Exam';
    protected $filter_query = [];


    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $data = [];
        if($request->all()){
            $data['exams'] = McqExam::where(function ($query) use ($request) {

                if ($request->get('faculty') > 0) {
                    $query->where('faculty', $request->get('faculty'));
                    $this->filter_query['faculty'] = $request->get('faculty');
                }

                if ($request->get('semesters_id') > 0) {
                    $query->where('semester', $request->get('semesters_id'));
                    $this->filter_query['semester'] = $request->get('semesters_id');
                }

                if ($request->get('subjects_id') > 0) {
                    $query->where('subjects_id', $request->get('subjects_id'));
                    $this->filter_query['subjects_id'] = $request->get('subjects_id');
                }


                if ($request->get('title') !=='') {
                    $query->where('title','like','%'. $request->get('title').'%');
                    $this->filter_query['title'] = $request->get('title');
                }

                if ($request->get('mcq_instructions_id') > 0) {
                    $query->where('mcq_instructions_id', $request->get('mcq_instructions_id'));
                    $this->filter_query['mcq_instructions_id'] = $request->get('mcq_instructions_id');
                }

                if ($request->get('exam_status') !=='') {
                    $query->where('exam_status','like','%'. $request->get('exam_status').'%');
                    $this->filter_query['exam_status'] = $request->get('exam_status');
                }

                if ($request->get('mark_type') !== null) {
                    $query->where('mark_type','=',$request->get('mark_type'));
                    $this->filter_query['mark_type'] = $request->get('mark_type');
                }

                if ($request->get('pass_mark') !== null) {
                    $query->where('pass_mark','=',$request->get('pass_mark'));
                    $this->filter_query['pass_mark'] = $request->get('pass_mark');
                }

                if ($request->get('question_type') !== null) {
                    $query->where('question_type','=',$request->get('question_type'));
                    $this->filter_query['question_type'] = $request->get('question_type');
                }

                if ($request->get('no_of_question') !== null) {
                    $query->where('no_of_question','=',$request->get('no_of_question'));
                    $this->filter_query['no_of_question'] = $request->get('no_of_question');
                }

                if ($request->get('result_status') !== null) {
                    $query->where('result_status','=',$request->get('result_status'));
                    $this->filter_query['result_status'] = $request->get('result_status');
                }

                if ($request->get('exam_type') !== null) {
                    $query->where('exam_type','=',$request->get('exam_type'));
                    $this->filter_query['exam_type'] = $request->get('exam_type');
                }


                //&start_date=2020-09-09&end_date=2020-09-10&status=all
                //?exam_type=date_duration&start_date=2020-09-09&end_date=2020-09-10&status=all


                if ($request->get('exam_type') == 'date_duration' && $request->get('start_date') !== null ) {
                    $query->where('start_date', '=', $request->get('start_date'));
                    $this->filter_query['start_date'] = $request->get('start_date');
                }

                if ($request->get('exam_type') == 'date_duration' && $request->get('end_date') !== null ) {
                    $query->where('end_date', '=', $request->get('end_date'));
                    $this->filter_query['end_date'] = $request->get('end_date');
                }

                if ($request->get('exam_type') == 'date_time_duration' && $request->get('start_date_time') !== null ) {
                    $query->where('start_date_time', '=', $request->get('start_date_time'));
                    $this->filter_query['start_date_time'] = $request->get('start_date_time');
                }

                if ($request->get('exam_type') == 'date_time_duration' && $request->get('end_date_time') !== null ) {
                    $query->where('end_date_time', '=', $request->get('end_date_time'));
                    $this->filter_query['end_date_time'] = $request->get('end_date_time');
                }

                if ($request->get('status') !== 'all' && $request->get('status') !== null) {
                    $query->where('status', $request->status == 'active' ? 1 : 0);
                    $this->filter_query['status'] = $request->get('status');
                }

            })
                ->get();
        }else{
            $data['exams'] = McqExam::limit($this->pagination_limit)
                ->get();
        }

       // dd($data['exams']->toArray());

        $data['group'] = $this->getQuestionGroups();
        $data['level'] = $this->getQuestionLevel();


        $data['faculties'] = $this->activeFaculties();
        $data['subjects'] = $this->allSubjectsList();
        $data['instruction'] = $this->getExamInstructions();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;


        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function add()
    {
        $data = [];
       // $data['blank_ins'] = new McqQuestion();

       /* $data['group'] = $this->getQuestionGroups();
        $data['level'] = $this->getQuestionLevel();
        $data['subjects'] = $this->allSubjectsList();*/
        $data['instruction'] = $this->getExamInstructions();
        $data['faculties'] = $this->activeFaculties();

        return view(parent::loadDataToView($this->view_path.'.registration.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {

        if ($request->hasFile('main_image')){
            $image = $request->file('main_image');

            $image_name = strtotime(Carbon::now()).'_'.$this->randomNum(2,3).'.'.$image->getClientOriginalExtension();
            $image->move($this->folder_path , $image_name);
        }else{
            $image_name = "";
        }

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['semester' => $request->semesters_id]);


        $row = McqExam::create($request->all());

        /*if ($row && $request->has('option_title')) {
            foreach ($request->get('option_title') as $key => $field) {
                $num = $key+1;
                if ($request->hasFile('option_image_'.$num)){
                    $option_image = $request->file('option_image_'.$num);
                    $option_image_name = strtotime(Carbon::now()).'_'.$this->randomNum(2,3).'.'.$option_image->getClientOriginalExtension();
                    $option_image->move($this->folder_path , $option_image_name);
                }else{
                    $option_image_name = "";
                }

                McqQuestionOption::create([
                    'mcq_exams_id'=>$row->id,
                    'option'=>$field,
                    'image'=>$option_image_name,
                    'answer_status'=>$request->get('answer_status')[$key],
                    'status' => $request->get('option_status')[$key],
                    'created_by' => auth()->user()->id,
                    'last_updated_by' => auth()->user()->id,
                ]);
            }
        }*/

        $request->session()->flash($this->message_success, $this->panel. ' successfully created.');
        if($request->add_another) {
            return back();
        }else{
            return redirect()->route($this->base_route.'.index');
        }
    }

    public function view($id)
    {
        $id = decrypt($id);
        $data = [];
        $data['row'] = McqExam::find($id);

        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.view'), compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = McqExam::find($id))
            return parent::invalidRequest();

        $data['options'] = $data['row']->options;

        $data['group'] = $this->getQuestionGroups();
        $data['level'] = $this->getQuestionLevel();
        $data['subjects'] = $this->allSubjectsList();

        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.registration.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$row = McqExam::find($id))
            return parent::invalidRequest();

        if ($request->hasFile('main_image')){
            if (file_exists($this->folder_path.$row->image))
                @unlink($this->folder_path.$row->image);

            $image = $request->file('main_image');
            $image_name = strtotime(Carbon::now()).'_'.$this->randomNum(2,3).'.'.$image->getClientOriginalExtension();
            $image->move($this->folder_path , $image_name);
        }else{
            $image_name =$row->image;
        }

        $request->request->add(['last_updated_by' => auth()->user()->id]);
        $request->request->add(['image' => $image_name]);

        $row->update($request->all());

        if ($row && $request->has('option_title')) {
            $newOptionsId = [];
            foreach ($request->get('option_title') as $key => $field) {
                $num = $key+1;
                //find the option value if exist then update
                if(isset($request->get('option_id')[$key])){
                    $existOptionId = $request->get('option_id')[$key];
                    $optionExist = McqQuestionOption::find($existOptionId);
                    //if not exit then create
                    if ($request->hasFile('option_image_'.$num)){
                        //remove options image
                        if (file_exists($this->folder_path.$optionExist->image))
                            @unlink($this->folder_path.$optionExist->image);

                        $option_image = $request->file('option_image_'.$num);
                        $option_image_name = strtotime(Carbon::now()).'_'.$this->randomNum(2,3).'.'.$option_image->getClientOriginalExtension();
                        $option_image->move($this->folder_path , $option_image_name);
                    }else{
                        $option_image_name = $optionExist->image;;
                    }

                    $updateData =  [
                        'option'            =>  $field,
                        'image'             =>  $option_image_name,
                        'answer_status'     =>  $request->get('answer_status')[$key],
                        'status'            => $request->get('option_status')[$key],
                        'last_updated_by'   => auth()->user()->id,
                    ];

                    $optionExist->update($updateData);

                }else{
                    //if not exit then create
                    if ($request->hasFile('option_image_'.$num)){
                        $option_image = $request->file('option_image_'.$num);
                        $option_image_name = strtotime(Carbon::now()).'_'.$this->randomNum(2,3).'.'.$option_image->getClientOriginalExtension();
                        $option_image->move($this->folder_path , $option_image_name);
                    }else{
                        $option_image_name = "";
                    }

                    $newOption = McqQuestionOption::create([
                        'mcq_exams_id'=>$row->id,
                        'option'=>$field,
                        'image'=>$option_image_name,
                        'answer_status'=>$request->get('answer_status')[$key],
                        'status' => $request->get('option_status')[$key],
                        'created_by' => auth()->user()->id,
                        'last_updated_by' => auth()->user()->id,
                    ]);
                    $newOptionsId[] = $newOption->id;
                }

            }
            //dd(array_flatten($newOptionsId));

            if($request->get('option_id')){
                //remove option with images
                $allOptionIds = array_flatten(array_prepend($request->option_id,$newOptionsId));
                //dd(array_flatten($allOptionIds));

                $removeOptions = $row->options()->whereNotIn('id',$allOptionIds)->get()->pluck('id');

                foreach ($removeOptions as $removeOption){
                    $removableData = McqQuestionOption::find($removeOption);
                    //remove options image
                    if (file_exists($this->folder_path.$removableData->image))
                        @unlink($this->folder_path.$removableData->image);

                    $removableData->delete();
                }

            }
        }

        $request->session()->flash($this->message_success, $this->panel. ' Updated Successfully.');
        return back();

    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = McqExam::find($id)) return parent::invalidRequest();

        //remove question image
        if (file_exists($this->folder_path.$row->image))
            @unlink($this->folder_path.$row->image);

        if($row->options()->count() > 0){
            foreach ($row->options as $option){
                //remove options image
                if (file_exists($this->folder_path.$option->image))
                    @unlink($this->folder_path.$option->image);
            }

            $row->options()->delete();
        }

        $row->delete();
        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return back();
    }

    public function active(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = McqExam::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->code.' '.$this->panel.' Active Successfully.');
        return back();
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = McqExam::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);
        $row->update($request->all());

        //in active question login detail
        $login_detail = User::where([['role_id',5],['hook_id',$row->id]])->first();
        if($login_detail) {
            $request->request->add(['status' => 'in-active']);
            $login_detail->update($request->all());
        }

        $request->session()->flash($this->message_success, $row->code.' '.$this->panel.' In-Active Successfully.');
        return back();
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {
                foreach ($request->get('chkIds') as $row_id) {
                    $row_id = decrypt($row_id);
                    $row = McqExam::find($row_id);
                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row->price()->delete();
                            $row->stock()->delete();
                            $row->delete();
                            break;
                    }
                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                    $request->session()->flash($this->message_success, ucfirst($request->get('bulk_action')). ' Action Successfully.');
                else
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')).' successfully.');

                return redirect()->route($this->base_route);

            } else {
                $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
                return redirect()->route($this->base_route);
            }

        } else return parent::invalidRequest();

    }









    public function findSubject(Request $request)
    {
        $semester = Semester::where('id',$request->get('semester_id'))->first();

        if(auth()->user()->hasRole('staff')){
            $id = auth()->user()->hook_id;

            /*Find Teacher/Staff Accessible Subject*/
            $collectSubject = $semester->subjects()->select('subjects.id as subject_id','subjects.title as subject_title')
                ->where('subjects.staff_id',$id)
                ->get();
            $subjects = array_pluck($collectSubject,'subject_title','subject_id');
        }else{
            /*Find Subject Title with associated Ids*/
            $collectSubject = $semester->subjects()->select('subjects.id as subject_id','subjects.title as subject_title')->get();
            $subjects = array_pluck($collectSubject,'subject_title','subject_id');
        }


        if ($subjects) {
            $response['subjects'] = $subjects;
            $response['success'] = 'Subjects Found, Select Subject and Manage Question.';
        }else {
            $response['error'] = 'No Any Subject Found. Please Contact Your Administrator.';
        }

        return response()->json(json_encode($response));
    }


    /*bulk import*/
    public function importQuestion()
    {
        return view(parent::loadDataToView($this->view_path.'.registration.import'));
    }

    public function handleImportQuestion(Request $request)
    {
        //file present or not validation
        $validator = Validator::make($request->all(), [
            'file' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator);
        }

        $file = $request->file('file');
        $csvData = file_get_contents($file);
        $rows = array_map("str_getcsv", explode("\n", $csvData));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            if (count($header) != count($row)) {
                continue;
            }

            $row = array_combine($header, $row);

            //find ids
            $row['subjects_id'] = Subject::where('code',$row['subject_code'])->first()->id;
            $row['mcq_question_groups_id'] = McqQuestionGroup::where('title',$row['groups'])->first()->id;
            $row['mcq_question_levels_id'] = McqQuestionLevel::where('title',$row['level'])->first()->id;

            // validation
            $validator = Validator::make($row, [
                'subjects_id'               =>     'required | exists:subjects,id',
                'mcq_question_groups_id'    =>     'required | exists:mcq_question_groups,id',
                'mcq_question_levels_id'    =>     'required | exists:mcq_question_levels,id',
                'question'                  =>     'required | unique:mcq_exams,question',
                'mark'                      =>     'required',
                'type'                      =>     'required',
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator);
            }

            //Question import
            $question = McqExam::create([
                'subjects_id'               => $row['subjects_id'],
                'mcq_question_groups_id'    => $row['mcq_question_groups_id'],
                'mcq_question_levels_id'    => $row['mcq_question_levels_id'],
                "question"                  => $row['question'],
                "mark"                      => $row['mark'],
                "type"                      => $row['type'],
                'created_by'                => auth()->user()->id
            ]);

            if($question) {
                //'mcq_exams_id', 'option', 'image', 'answer_status', 'status'
                $i=1;
                do {
                    if($row['option_'.$i] !== ''){
                        $option = McqQuestionOption::create([
                            'created_by'        => auth()->user()->id,
                            'mcq_exams_id'  => $question->id,
                            'option'            => $row['option_'.$i],
                            'answer_status'     => $row['option_'.$i.'_answer'],
                        ]);
                    }

                    $i++;
                }
                while ($i!=5);
            }
        }

        $request->session()->flash($this->message_success,'Questions imported Successfully');
        return back();
    }


    /*option Field*/
    public function optionHtml(request $request)
    {
        $response = [];
        $response['error'] = false;
        $response['html'] = view($this->view_path.'.registration.includes.option_tr',['type'=> $request->type,'number'=>$request->number])->render();
        return response()->json(json_encode($response));
    }

    public function optionDelete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = McqQuestionOption::find($id)) return parent::invalidRequest();

        //delete option image if exist
        if (file_exists($this->folder_path.$row->image))
            @unlink($this->folder_path.$row->image);

        $row->delete();

        $request->session()->flash($this->message_success, 'Option successfully deleted.');
        return back();

    }

    public function optionActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = McqQuestionOption::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);
        $row->update($request->all());

        $request->session()->flash($this->message_success, 'Option Active Successfully.');
        return back();
    }

    public function optionInActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = McqQuestionOption::find($id)) return parent::invalidRequest();

        if($row->answer_status ==1){

            $request->session()->flash($this->message_warning, 'You can not deactivate answer options.');
            return back();
        }else{
            $request->request->add(['status' => 'in-active']);
            $row->update($request->all());

            $request->session()->flash($this->message_success, 'Option In-Active Successfully.');
            return back();
        }


    }

}
