<?php
/*
 * Mr. Umesh Kumar Yadav
 */
namespace App\Http\Controllers\Setting;
use App\Http\Controllers\CollegeBaseController;
use App\Models\Faculty;
use App\Models\OnlineRegistrationProgram;
use App\Models\OnlineRegistrationSetting;
use App\Models\Web\WebRegistrationSetting;
use Carbon\Carbon;


use App\Http\Requests\Setting\OnlineRegistrationSetting\AddValidation;
use App\Http\Requests\Setting\OnlineRegistrationSetting\EditValidation;
use Illuminate\Http\Request;
use View, AppHelper, Image, URL;

class OnlineRegistrationSettingController extends CollegeBaseController
{
    protected $base_route = 'setting.online-registration';
    protected $view_path = 'setting.online-registration';
    protected $panel = 'Online Registration Setting';
    protected $folder_path;
    protected $folder_name = 'online-registration';
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $data = [];
        $data['row'] = OnlineRegistrationSetting::first();
        $data['exist_program'] = OnlineRegistrationProgram::select('online_registration_programs.id',
            'online_registration_programs.faculties_id','online_registration_programs.semesters_id',
            'online_registration_programs.start_date', 'online_registration_programs.end_date',
            'online_registration_programs.status','f.faculty','s.semester','s.slug')
            ->join('faculties as f','f.id','=','online_registration_programs.faculties_id')
            ->join('semesters as s','s.id','=','online_registration_programs.semesters_id')
            ->get();



        $data['url'] = '';

        if($data['row']){
            return view(parent::loadDataToView($this->view_path.'.edit'), compact('data'));
        }else{
            $request->session()->flash($this->message_success, 'Before you setup online registration, Please active Online Registration Module from general/branding Setting. If already active then ignore this message.');
            return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
        }

    }

    public function add(Request $request)
    {
        $data = [];
        $data['row'] = OnlineRegistrationSetting::first();
        $data['faculties'] = $this->activeFaculties();

        if($data['row']){
            return view(parent::loadDataToView($this->view_path.'.edit'), compact('data'));
        };

        $request->session()->flash($this->message_success, 'Before you setup online registration, Please active online Module from General/Branding Setting.');
        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $data['row'] = OnlineRegistrationSetting::first();
        if($data['row']){
            return view(parent::loadDataToView($this->view_path.'.edit'), compact('data'));
        };

        if ($request->hasFile('logo_image')){
            $logo_image_name = parent::uploadImages($request, 'logo_image');
        }else{
            $logo_image_name = "";
        }

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['logo' => isset($logo_image_name)?$logo_image_name:'']);
        $request->request->add(['start_date' => Carbon::parse($request->start_date)->format('Y-m-d')]);
        $request->request->add(['end_date' => Carbon::parse($request->end_date)->format('Y-m-d')]);

        OnlineRegistrationSetting::create($request->all());

        if ($request->has('faculties_id')) {
            foreach ($request->get('faculties_id') as $key => $program) {

                OnlineRegistrationProgram::create([
                    'faculties_id' => $program,
                    'semesters_id' => isset($request->get('semester_select')[$key])?$request->get('semester_select')[$key]:'',
                    'start_date' => $request->get('program_start_date')[$key],
                    'end_date' => $request->get('program_end_date')[$key],
                    'status' => $request->get('program_status')[$key],
                    'created_by' => auth()->user()->id
                ]);
            }
        }

        $request->session()->flash($this->message_success, $this->panel. ' successfully added.');
        return redirect()->route($this->view_path);
    }

    public function update(EditValidation $request, $id)
    {
        if (!$row = OnlineRegistrationSetting::find($id)) return parent::invalidRequest();

        if ($request->hasFile('logo_image')){
            $logo_image_name = parent::uploadImages($request, 'logo_image');
            // remove old image from folder
            if (file_exists($this->folder_path.$row->logo))
                @unlink($this->folder_path.$row->logo);
        }


        $request->request->add(['logo' => isset($logo_image_name)?$logo_image_name:$row->logo]);
        $request->request->add(['start_date' => Carbon::parse($request->start_date)->format('Y-m-d')]);
        $request->request->add(['end_date' => Carbon::parse($request->end_date)->format('Y-m-d')]);
        $request->request->add(['last_updated_by' => auth()->user()->id]);

        $row->update($request->all());

        if ($request->has('faculties_id')) {
            foreach ($request->get('faculties_id') as $key => $program) {
                $existProgram = OnlineRegistrationProgram::where(['faculties_id'=>$program, 'semesters_id'=> $request->get('semester_select')[$key]])->first();
                //dd($request->get('semester_select')[$key]);
                if($existProgram){
                    $existProgram->update([
                        'faculties_id' => $program,
                        'semesters_id' => $request->get('semester_select')[$key],
                        'start_date' => $request->get('program_start_date')[$key],
                        'end_date' => $request->get('program_end_date')[$key],
                        'status' => $request->get('program_status')[$key],
                        'updated_by' => auth()->user()->id
                    ]);
                }else{
                    OnlineRegistrationProgram::create([
                        'faculties_id' => $program,
                        'semesters_id' => $request->get('semester_select')[$key],
                        'start_date' => $request->get('program_start_date')[$key],
                        'end_date' => $request->get('program_end_date')[$key],
                        'status' => $request->get('program_status')[$key],
                        'created_by' => auth()->user()->id
                    ]);
                }
            }

        }

        $request->session()->flash($this->message_success, $this->panel.' successfully updated.');
        return redirect()->route($this->base_route);
    }


    public function programHtml()
    {
        $response = [];
        $data = [];
        $response['error'] = false;

        $baseOfRegistration = OnlineRegistrationSetting::first();

        $existingProgram = OnlineRegistrationProgram::select('online_registration_programs.id',
            'online_registration_programs.faculties_id','online_registration_programs.semesters_id',
            'online_registration_programs.start_date', 'online_registration_programs.end_date',
            'online_registration_programs.status','f.faculty','s.semester')
            ->join('faculties as f','f.id','=','online_registration_programs.faculties_id')
            ->join('semesters as s','s.id','=','online_registration_programs.semesters_id')
            ->get();

        if($baseOfRegistration['base'] =="faculty" && $existingProgram->count() > 0){
            $facultyExceptId = $existingProgram->pluck('faculties_id');
            //registration base of Faculty/Program/Class or semester/section
            //if faculty
            $allFaculty = Faculty::whereNotIn('id',$facultyExceptId)->Active()->orderBy('faculty')->pluck('faculty','id')->toArray();
        }else{
            $allFaculty = Faculty::Active()->orderBy('faculty')->pluck('faculty','id')->toArray();
        }
        $faculties = array_prepend($allFaculty,'Select Faculty/Program/Class','');

        $response['html'] = view($this->view_path.'.includes.program_tr',['programs'=>$faculties])->render();
        return response()->json($response);
    }

    public function removeProgram(Request $request)
    {

        if (!$row = OnlineRegistrationProgram::find($request->id)){
            $response['message'] = 'Invalid Request.';
            $response['error'] = true;
        }else{
            $row->delete();
            $response['message'] = 'Program removed successfully.';
            $response['error'] = false;
        }

        return response()->json($response);
    }

    public function findSemester(Request $request)
    {
        $response = [];
        $response['error'] = true;

        $baseOfRegistration = OnlineRegistrationSetting::first();

        $existingProgram = OnlineRegistrationProgram::select('online_registration_programs.id',
            'online_registration_programs.faculties_id','online_registration_programs.semesters_id',
            'online_registration_programs.start_date', 'online_registration_programs.end_date',
            'online_registration_programs.status','f.faculty','s.semester')
            //->where('online_registration_programs.status',1)
            ->join('faculties as f','f.id','=','online_registration_programs.faculties_id')
            ->join('semesters as s','s.id','=','online_registration_programs.semesters_id')
            ->get();

        $semesterExceptId = $existingProgram->pluck('semesters_id');

        if($baseOfRegistration['base'] =="semester" && $existingProgram->count() > 0){
            if ($request->has('faculty_id')) {
                $faculty = Faculty::find($request->get('faculty_id'));
                if ($faculty) {
                    $response['semester'] = $faculty->semesters()->whereNotIn('semesters.id',$semesterExceptId)->select('semesters.id', 'semesters.semester', 'semesters.slug')->get();

                    $response['error'] = false;
                    $response['success'] = 'Semester/Sec. Available For This Faculty/Program/Class.';
                } else {
                    $response['error'] = true;
                    $response['message'] = 'No Any Semester Assign on This Faculty/Program/Class.';
                }
            } else {
                $response['error'] = true;
                $response['message'] = 'Invalid request!!';
            }
        } else {
            if ($request->has('faculty_id')) {
                $faculty = Faculty::find($request->get('faculty_id'));
                if ($faculty) {
                    $response['semester'] = $faculty->semesters()->select('semesters.id', 'semesters.semester', 'semesters.slug')->get();
                    $response['error'] = false;
                    $response['success'] = 'Semester/Sec. Available For This Faculty/Program/Class.';
                } else {
                    $response['error'] = true;
                    $response['message'] = 'No Any Semester Assign on This Faculty/Program/Class.';
                }
            } else {
                $response['error'] = true;
                $response['message'] = 'Invalid request!!';
            }
        }
        return response()->json($response);
    }

}