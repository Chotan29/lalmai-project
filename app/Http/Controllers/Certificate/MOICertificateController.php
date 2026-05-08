<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\Certificate;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Certificate\MOI\AddValidation;
use App\Http\Requests\Certificate\MOI\EditValidation;
use App\Models\CertificateTemplate;
use App\Models\CertificateHistory;
use App\Models\MediumOfInstructionCertificate;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use URL;
use ViewHelper;
class MOICertificateController extends CollegeBaseController
{
    protected $base_route = 'certificate.moi';
    protected $view_path = 'certificate.moi';
    protected $panel = 'Medium Of Instruction Certificate';
    protected $folder_path;
    protected $codePrefix = "MOIC";
    protected $codeStart = 00000;
    protected $filter_query = [];

    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $data = [];
        $data['student'] = Student::select('students.id','students.reg_no', 'students.reg_date',
            'students.faculty', 'students.semester', 'students.batch', 'students.academic_status', 'students.first_name',
            'students.middle_name', 'students.last_name',
            'moi.id as certificate_id', 'moi.date_of_issue','moi.ref_num', 'moi.moic_num', 'moi.ref_text','moi.status')
            ->where(function ($query) use ($request) {

                $this->commonStudentFilterCondition($query, $request);

                if ($request->has('issue-start-date') && $request->has('issue-end-date')) {
                    $query->whereBetween('moi.date_of_issue', [$request->get('issue-start-date'), $request->get('issue-end-date')]);
                    $this->filter_query['issue-start-date'] = $request->get('issue-start-date');
                    $this->filter_query['issue-end-date'] = $request->get('issue-end-date');
                } elseif ($request->has('issue-start-date')) {
                    $query->where('moi.date_of_issue', '>=', $request->get('issue-start-date'));
                    $this->filter_query['issue-start-date'] = $request->get('issue-start-date');
                } elseif ($request->has('issue-end-date')) {
                    $query->where('moi.date_of_issue', '<=', $request->get('issue-end-date'));
                    $this->filter_query['issue-end-date'] = $request->get('issue-end-date');
                }

                if ($request->has('year')) {
                    $query->where('moi.year', '=', $request->year);
                    $this->filter_query['moi.year'] = $request->year;
                }

                if ($request->has('moic_num')) {
                    $query->where('moi.moic_num', 'like', '%'. $request->moic_num.'%');
                    $this->filter_query['moi.moic_num'] = $request->moic_num;
                }

                if ($request->has('gpa')) {
                    $query->where('moi.gpa', 'like', '%'. $request->gpa.'%');
                    $this->filter_query['moi.gpa'] = $request->gpa;
                }

                if ($request->has('scale')) {
                    $query->where('moi.scale', 'like', '%'. $request->scale.'%');
                    $this->filter_query['moi.scale'] = $request->gpa;
                }


            })
            ->join('medium_of_instruction_certificates as moi', 'moi.students_id', '=', 'students.id')
            ->paginate(env('PAGINATION_LIMIT',$this->pagination_limit));


        $data['faculties'] = $this->activeFaculties();
        $data['batch'] = $this->activeBatch();
        $data['academic_status'] = $this->activeStudentAcademicStatus();


        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function add(Request $request, $id)
    {
        $id = decrypt($id);
        //check if certificate already exist return with message
        $certificateExist = MediumOfInstructionCertificate::where('students_id',$id)->first();
        if($certificateExist){
            $request->session()->flash($this->message_warning, $this->panel. ' already created for this Student. Please Find and Edit Certificate.');
            return redirect()->back();
        }

        $data['row'] = Student::find($id);
        $data['faculties'] = $this->activeFaculties();
        $data['semester'] = $this->activeSemester();
        $data['batch'] = $this->activeBatch();
        $ccInfo = MediumOfInstructionCertificate::latest()->first();
        if($ccInfo){
            $num = $ccInfo->moic_num;
            $numbers = intval(preg_replace('/[^0-9]/', '', $num))+1;
            $numbers = str_pad($numbers, 5, '0', STR_PAD_LEFT);
            $letters = preg_replace('/[^a-zA-Z]/', '', $num);
            $data['moic_num'] = $this->codePrefix.$numbers;
        }else{
            $numbers = intval($this->codeStart)+1;
            $numbers = str_pad($numbers, 5, '0', STR_PAD_LEFT);
            $data['moic_num'] = $this->codePrefix.$numbers;
        }

        $data['url'] = URL::current();
        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['date_of_issue' => Carbon::parse($request->issue_date)]);
        $request->request->add(['ref_text' => json_encode($request->except('_token'))]);

        $row = MediumOfInstructionCertificate::create($request->all());

        if($row){
            $CreateHistory = CertificateHistory::create([
                'students_id' => $row->students_id,
                'certificate' => 'MOI',
                'certificate_id' => $row->id,
                'history_type' => 'Created',
                'ref_text' => json_encode($request->except('ref_text','_token')),
                'created_by' => auth()->user()->id,
            ]);
        }

        $request->session()->flash($this->message_success, $this->panel. ' Created Successfully.');
        return redirect()->route($this->base_route);

    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        $data['row'] = Student::select('students.id','students.reg_no', 'students.reg_date',
            'students.faculty', 'students.semester', 'students.batch', 'students.academic_status', 'students.first_name',
            'students.middle_name', 'students.last_name',
            'moi.id as certificate_id', 'moi.date_of_issue','moi.ref_num', 'moi.moic_num', 'moi.ref_text','moi.status')
            ->join('medium_of_instruction_certificates as moi', 'moi.students_id', '=', 'students.id')
            ->find($id);

          if (!$data['row'])
            return parent::invalidRequest();

        $data['faculties'] = $this->activeFaculties();
        $data['semester'] = $this->activeSemester();
        $data['batch'] = $this->activeBatch();

        $data['url'] = URL::current();
        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.edit'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$student = Student::find($request->students_id)) return parent::invalidRequest();

        $updateData = [
            "moic_num" => $request->moic_num,
            "date_of_issue" => Carbon::parse($request->date_of_issue),
            "ref_num" => $request->ref_num,
            "ref_text" => json_encode($request->except('_token')),
            "last_updated_by" => auth()->user()->id,
        ];


        $row = $student->MOICertificate()->update($updateData);
        if($row){
            //manage history
            $CreateHistory = CertificateHistory::create([
                'students_id' => $student->id,
                'certificate' => 'MOI',
                'certificate_id' => $student->MOICertificate()->first()->id,
                'history_type' => 'Updated',
                'ref_text' => json_encode($request->except('ref_text','_token')),
                'created_by' => auth()->user()->id,
            ]);
        }

        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return redirect()->route($this->base_route);
    }

    public function view(Request $request, $id)
    {
        $id = decrypt($id);
        $data['student'] = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
            'students.faculty','students.semester','students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
            'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group',  'students.religion', 'students.caste','students.nationality',
            'students.mother_tongue','students.student_image', 'pd.father_first_name', 'pd.father_middle_name', 'pd.father_last_name',
            'moi.id as certificate_id', 'moi.date_of_issue','moi.ref_num', 'moi.moic_num', 'moi.ref_text','moi.status')
            ->where('students.id',$id)
            ->join('medium_of_instruction_certificates as moi', 'moi.students_id', '=', 'students.id')
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->get();

        if (!$data['student'])
            return parent::invalidRequest();

        return view(parent::loadDataToView($this->view_path.'.view'), compact('data'));

    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = Student::find($id)) return parent::invalidRequest();
        $certificate = $row->MOICertificate()->first();
        //delete history
        CertificateHistory::where('certificate_id','=',$certificate->id)->delete();
        //delete certificate
        $certificate->delete();

        $request->session()->flash($this->message_success, $this->panel.' Deleted Successfully.');
        return redirect()->route($this->base_route);
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['print','active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {
                foreach ($request->get('chkIds') as $row_id) {
                    $row_id = decrypt($row_id);
                    $row = MediumOfInstructionCertificate::find($row_id);

                    switch ($request->get('bulk_action')) {
                        case 'print':
                            $data['certificate_template'] = $certificateTemplate = CertificateTemplate::where('certificate','=','MEDIUM OF INSTRUCTION')
                                ->first();
                            $data['student'] = $this->bulkPrint($certificateTemplate, $request->get('stuIds'));
                            return view(parent::loadDataToView('print.certificate.moi'), compact('data'));
                            break;
                        case 'active':
                        case 'in-active':
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            //delete history
                            CertificateHistory::where('certificate_id','=',$row->id)->delete();
                            //delete certificate
                            $row->delete();
                            break;
                    }
                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')) . ' Successfully.');
                else
                    $request->session()->flash($this->message_success, ' Deleted successfully.');

                return redirect()->route($this->base_route);

            } else {
                $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
                return redirect()->route($this->base_route);
            }

        } else return parent::invalidRequest();

    }

    public function print($id)
    {
        $id = decrypt($id);

        $data['certificate_template'] = $certificateTemplate = CertificateTemplate::where('certificate','=','MEDIUM OF INSTRUCTION')
            ->first();

        if (!$data['certificate_template'])
            return parent::invalidRequest();

        $data['student'] = $this->bulkPrint($certificateTemplate, [$id]);

        if (!$data['student'])
            return parent::invalidRequest();

        return view(parent::loadDataToView('print.certificate.moi'), compact('data'));
    }

    public function bulkPrint($certificateTemplate, $studIds)
    {
        $students = Student::select('id')->whereIn('id',$studIds)->get();

        $filteredStudent = $students->filter(function ($student, $key) use($certificateTemplate) {
            $data = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
                'students.faculty','students.semester','students.batch', 'students.academic_status', 'students.first_name', 'students.middle_name',
                'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group',  'students.religion',
                'students.caste','students.nationality', 'students.mother_tongue', 'students.email', 'students.extra_info',
                'students.status',
                'ai.address', 'ai.state', 'ai.country', 'ai.temp_address', 'ai.temp_state', 'ai.temp_country', 'ai.home_phone',
                'ai.mobile_1', 'ai.mobile_2',
                'pd.grandfather_first_name',
                'pd.grandfather_middle_name', 'pd.grandfather_last_name', 'pd.father_first_name', 'pd.father_middle_name',
                'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_office', 'pd.father_office_number',
                'pd.father_residence_number', 'pd.father_mobile_1', 'pd.father_mobile_2', 'pd.father_email', 'pd.mother_first_name',
                'pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation', 'pd.mother_office',
                'pd.mother_office_number', 'pd.mother_residence_number', 'pd.mother_mobile_1', 'pd.mother_mobile_2', 'pd.mother_email',
                'gd.id as guardian_id', 'gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
                'gd.guardian_eligibility', 'gd.guardian_occupation', 'gd.guardian_office', 'gd.guardian_office_number', 'gd.guardian_residence_number',
                'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email', 'gd.guardian_relation', 'gd.guardian_address',
                'students.student_image','students.student_signature', 'pd.father_image', 'pd.mother_image', 'gd.guardian_image',
                'moi.date_of_issue', 'moi.moic_num', 'moi.ref_num','moi.ref_text')
                ->where('students.id','=',$student->id)
                ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
                ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                ->join('student_guardians as sg', 'sg.students_id','=','students.id')
                ->join('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
                ->join('medium_of_instruction_certificates as moi', 'moi.students_id', '=', 'students.id')
                ->first();

            $student->date_of_issue = $data->date_of_issue;
            $student->ref_num = $data->ref_num;
            $student->moic_num = $data->moic_num;
            $student->faculty = $data->faculty;
            $student->reg_no = $data->reg_no;
            $student->university_reg = $data->university_reg;
            $student->certificate = $certificateTemplate->certificate;

            if($certificateTemplate->student_photo == 1){
                $student->student_image = $data->student_image?$data->student_image:"";
            }

            $certificateTemplate = $this->studentCertificateTextReplace($data,$certificateTemplate);
            $student->certificate_template = $certificateTemplate;

            return $student;
        });

        return $data['students'] = $filteredStudent;

    }

}

