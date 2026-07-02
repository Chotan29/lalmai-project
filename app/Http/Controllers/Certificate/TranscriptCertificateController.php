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
use App\Http\Requests\Certificate\Transcript\AddValidation;
use App\Http\Requests\Certificate\Transcript\EditValidation;
use App\Models\CertificateTemplate;
use App\Models\CharacterCertificate;
use App\Models\CertificateHistory;
use App\Models\ExamMarkLedger;
use App\Models\Faculty;
use App\Models\GradingScale;
use App\Models\ProvisionalCertificate;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TranscriptCertificate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use URL;
use ViewHelper;
class TranscriptCertificateController extends CollegeBaseController
{
    protected $base_route = 'certificate.transcript';
    protected $view_path = 'certificate.transcript';
    protected $panel = 'Transcript Certificate';
    protected $folder_path;
    protected $codePrefix = "TRC";
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
            'tc.id as certificate_id', 'tc.date_of_issue','tc.trc_num', 'tc.year','tc.duration','tc.credit_required',
            'tc.gpa','tc.verification_code', 'tc.mark_sheet_sn', 'tc.provisional_certificate_num',
            'tc.ref_text')
            ->where(function ($query) use ($request) {

                $this->commonStudentFilterCondition($query, $request);

                if ($request->has('issue-start-date') && $request->has('issue-end-date')) {
                    $query->whereBetween('tc.date_of_issue', [$request->get('issue-start-date'), $request->get('issue-end-date')]);
                    $this->filter_query['issue-start-date'] = $request->get('issue-start-date');
                    $this->filter_query['issue-end-date'] = $request->get('issue-end-date');
                } elseif ($request->has('issue-start-date')) {
                    $query->where('tc.date_of_issue', '>=', $request->get('issue-start-date'));
                    $this->filter_query['issue-start-date'] = $request->get('issue-start-date');
                } elseif ($request->has('issue-end-date')) {
                    $query->where('tc.date_of_issue', '<=', $request->get('issue-end-date'));
                    $this->filter_query['issue-end-date'] = $request->get('issue-end-date');
                }

                if ($request->has('year')) {
                    $query->where('tc.year', '=', $request->year);
                    $this->filter_query['tc.year'] = $request->year;
                }

                if ($request->has('trc_num')) {
                    $query->where('tc.trc_num', 'like', '%'. $request->trc_num.'%');
                    $this->filter_query['tc.trc_num'] = $request->trc_num;
                }

                if ($request->has('gpa')) {
                    $query->where('tc.gpa', 'like', '%'. $request->gpa.'%');
                    $this->filter_query['tc.gpa'] = $request->gpa;
                }

                if ($request->has('scale')) {
                    $query->where('tc.scale', 'like', '%'. $request->scale.'%');
                    $this->filter_query['tc.scale'] = $request->gpa;
                }


            })
            ->join('transcript_certificates as tc', 'tc.students_id', '=', 'students.id')
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
        $certificateExist = TranscriptCertificate::where('students_id',$id)->first();
        if($certificateExist){
            $request->session()->flash($this->message_warning, $this->panel. ' already created for this Student. Please Find and Edit Certificate.');
            return redirect()->back();
        }

        $data['TranscriptData'] = $TranscriptData = $this->getTranscriptData($id);

        $data['row'] = Student::find($id);
        $data['faculties'] = $this->activeFaculties();
        $data['semester'] = $this->activeSemester();
        $data['batch'] = $this->activeBatch();
        $data['duration'] = '4';
        $ccInfo = TranscriptCertificate::latest()->first();
        if($ccInfo){
            $num = $ccInfo->trc_num;
            $numbers = intval(preg_replace('/[^0-9]/', '', $num))+1;
            $numbers = str_pad($numbers, 5, '0', STR_PAD_LEFT);
            $letters = preg_replace('/[^a-zA-Z]/', '', $num);
            $data['trc_num'] = $this->codePrefix.$numbers;
        }else{
            $numbers = intval($this->codeStart)+1;
            $numbers = str_pad($numbers, 5, '0', STR_PAD_LEFT);
            $data['trc_num'] = $this->codePrefix.$numbers;
        }

        $data['url'] = URL::current();
        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $id = $request->students_id;
        $data['TranscriptData'] = $TranscriptData = $this->getTranscriptData($id);
        $request->request->add(['verification_code' => isset($data['TranscriptData']->reg_no)?$data['TranscriptData']->reg_no:""]);
        $request->request->add(['duration' => isset($data['TranscriptData']->duration)?$data['TranscriptData']->duration:""]);
        $request->request->add(['gpa' => isset($data['TranscriptData']->transcriptGPA)?$data['TranscriptData']->transcriptGPA:""]);
        $request->request->add(['credit_required' => isset($data['TranscriptData']->credit_required)?$data['TranscriptData']->credit_required:""]);

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['date_of_issue' => Carbon::parse($request->issue_date)]);
        $request->request->add(['ref_text' => json_encode($request->except('_token'))]);

        $row = TranscriptCertificate::create($request->all());

        if($row){
            $CreateHistory = CertificateHistory::create([
                'students_id' => $row->students_id,
                'certificate' => 'transcript',
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
            'tc.id as certificate_id', 'tc.date_of_issue','tc.trc_num', 'tc.year','tc.duration','tc.credit_required',
            'tc.gpa','tc.verification_code', 'tc.mark_sheet_sn', 'tc.provisional_certificate_num',
            'tc.ref_text')
            ->join('transcript_certificates as tc', 'tc.students_id', '=', 'students.id')
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

        $id = $request->students_id;
        $data['TranscriptData'] = $TranscriptData = $this->getTranscriptData($id);

        $updateData = [
            "trc_num" => $request->trc_num,
            "date_of_issue" => Carbon::parse($request->date_of_issue),
            "year" => $request->year,
            "duration" => isset($data['TranscriptData']->duration)?$data['TranscriptData']->duration:"",
            "credit_required" => isset($data['TranscriptData']->credit_required)?$data['TranscriptData']->credit_required:"",
            "gpa" => isset($data['TranscriptData']->transcriptGPA)?$data['TranscriptData']->transcriptGPA:"",
            "verification_code" => isset($data['TranscriptData']->reg_no)?$data['TranscriptData']->reg_no:"",
            "mark_sheet_sn" => $request->mark_sheet_sn,
            "provisional_certificate_num" => $request->provisional_certificate_num,
            "ref_text" => json_encode($request->except('_token')),
            "last_updated_by" => auth()->user()->id,
        ];

        $row = $student->transcriptCertificate()->update($updateData);
        if($row){
            //manage history
            $CreateHistory = CertificateHistory::create([
                'students_id' => $student->id,
                'certificate' => 'transcript',
                'certificate_id' => $student->transcriptCertificate()->first()->id,
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
            'tc.id as certificate_id', 'tc.date_of_issue','tc.trc_num', 'tc.year','tc.duration','tc.credit_required',
            'tc.gpa','tc.verification_code', 'tc.mark_sheet_sn', 'tc.provisional_certificate_num',
            'tc.ref_text')
            ->where('students.id',$id)
            ->join('transcript_certificates as tc', 'tc.students_id', '=', 'students.id')
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
        $certificate = $row->transcriptCertificate()->first();
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
                    $row = TranscriptCertificate::find($row_id);

                    switch ($request->get('bulk_action')) {
                        case 'print':
                            $data['certificate_template'] = $certificateTemplate = CertificateTemplate::where('certificate','=','TRANSCRIPT')
                                ->first();
                            $data['student'] = $this->bulkTranscriptPrint($certificateTemplate, $request->get('stuIds'));
                            return view(parent::loadDataToView('print.certificate.transcript'), compact('data'));
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

    public function print($id,$template)
    {
        $id = decrypt($id);

        $data['certificate_template'] = $certificateTemplate = CertificateTemplate::where('certificate','=',$template)
            ->first();

        if (!$data['certificate_template'])
            return parent::invalidRequest();

        $data['student'] = $this->bulkTranscriptPrint($certificateTemplate, [$id]);

        if (!$data['student'])
            return parent::invalidRequest();

        if($template =="TRANSCRIPT"){
            return view(parent::loadDataToView('print.certificate.transcript'), compact('data'));
        }elseif($template == "SEMESTER WISE FINAL RESULT"){
            return view(parent::loadDataToView('print.certificate.semester-wise-final-result'), compact('data'));
        }else{

        }
        //resources/views/print/exam/transcript.blade.php
        //return view(parent::loadDataToView($this->view_path.'.transcript'), compact('data'));
    }



}

