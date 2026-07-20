<?php

/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers;


use App\Models\CertificateTemplate;
use App\Models\Student;
use App\Traits\AcademicScope;
use App\Traits\CertificateScope;
use App\Traits\LibraryScope;
use App\Traits\SmsEmailScope;
use App\Traits\UserScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use URL;
class VerificationController extends CollegeBaseController
{
    protected $base_route = 'verification';
    protected $view_path = 'verification';
    protected $panel;
    protected $folder_path;
    protected $folder_name = 'studentProfile';
    protected $filter_query = [];

    use SmsEmailScope;
    use UserScope;
    use LibraryScope;
    use AcademicScope;
    use CertificateScope;

    public function __construct()
    {
        $this->panel = __('student_staff.child.student.name');
    }

    protected function publicCertificateTemplateQuery()
    {
        $query = CertificateTemplate::query()->Active()->orderBy('certificate');

        if (Schema::hasColumn('certificate_templates', 'public_verify')) {
            $query->where('public_verify', 1);
        }

        return $query;
    }

    public function certificate(Request $request)
    {
//dd($request->all());
        $data = [];
        $student =[];
        if($request->all()) {
            if($request->certificate == 20){
                $student = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
                    'students.faculty','students.semester', 'students.academic_status', 'students.first_name', 'students.middle_name',
                    'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group',  'students.religion', 'students.caste','students.nationality',
                    'students.mother_tongue', 'students.email', 'students.extra_info', 'students.status',
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
                    'ic.id as certificate_id','ic.date_of_issue', 'ic.internship_title', 'ic.period', 'ic.character', 'ic.ref_text')
                    ->where(function ($query) use ($request) {
                        if ($request->has('reg_no') && $request->get('reg_no') != null) {
                            $query->where('students.reg_no', $request->reg_no);
                            $this->filter_query['reg_no'] = $request->reg_no;
                        }

                        if ($request->has('first_name')) {
                            $query->where('students.first_name', 'like', '%' . $request->first_name . '%');
                            $this->filter_query['first_name'] = $request->first_name;
                        }

                        if ($request->has('date_of_birth')) {
                            $query->where('students.date_of_birth', $request->date_of_birth);
                            $this->filter_query['date_of_birth'] = $request->date_of_birth;
                        }
                    })
                    ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
                    ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                    ->join('student_guardians as sg', 'sg.students_id','=','students.id')
                    ->join('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
                    ->join('intern_completion_certificates as ic', 'ic.students_id', '=', 'students.id')
                    ->first();
            }else{
                $student = Student::select('students.id','students.reg_no', 'students.reg_date', 'students.university_reg',
                    'students.faculty','students.semester', 'students.academic_status', 'students.first_name', 'students.middle_name',
                    'students.last_name', 'students.date_of_birth', 'students.gender', 'students.blood_group',  'students.religion', 'students.caste','students.nationality',
                    'students.mother_tongue', 'students.email', 'students.extra_info', 'students.status',
                    // 'ai.address', 'ai.state', 'ai.country', 'ai.temp_address', 'ai.temp_state', 'ai.temp_country', 'ai.home_phone',
                    // 'ai.mobile_1', 'ai.mobile_2',
                    // 'pd.grandfather_first_name',
                    // 'pd.grandfather_middle_name', 'pd.grandfather_last_name', 'pd.father_first_name', 'pd.father_middle_name',
                    // 'pd.father_last_name', 'pd.father_eligibility', 'pd.father_occupation', 'pd.father_office', 'pd.father_office_number',
                    // 'pd.father_residence_number', 'pd.father_mobile_1', 'pd.father_mobile_2', 'pd.father_email', 'pd.mother_first_name',
                    // 'pd.mother_middle_name', 'pd.mother_last_name', 'pd.mother_eligibility', 'pd.mother_occupation', 'pd.mother_office',
                    // 'pd.mother_office_number', 'pd.mother_residence_number', 'pd.mother_mobile_1', 'pd.mother_mobile_2', 'pd.mother_email',
                    // 'gd.id as guardian_id', 'gd.guardian_first_name', 'gd.guardian_middle_name', 'gd.guardian_last_name',
                    // 'gd.guardian_eligibility', 'gd.guardian_occupation', 'gd.guardian_office', 'gd.guardian_office_number', 'gd.guardian_residence_number',
                    // 'gd.guardian_mobile_1', 'gd.guardian_mobile_2', 'gd.guardian_email', 'gd.guardian_relation', 'gd.guardian_address',
                    // 'students.student_image','students.student_signature', 'pd.father_image', 'pd.mother_image', 'gd.guardian_image',
                    // 'cc.id as certificate_id','cc.date_of_issue', 'cc.course', 'cc.period', 'cc.character', 'cc.ref_text'
                    )
                    ->where(function ($query) use ($request) {
                        if ($request->has('reg_no') && $request->get('reg_no') != null) {
                            $query->where('students.reg_no', $request->reg_no);
                            $this->filter_query['reg_no'] = $request->reg_no;
                        }

                        if ($request->has('first_name')) {
                            $query->where('students.first_name', 'like', '%' . $request->first_name . '%');
                            $this->filter_query['first_name'] = $request->first_name;
                        }

                        if ($request->has('date_of_birth')) {
                            $query->where('students.date_of_birth', $request->date_of_birth);
                            $this->filter_query['date_of_birth'] = $request->date_of_birth;
                        }
                    })
                    // ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
                    // ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                    // ->join('student_guardians as sg', 'sg.students_id','=','students.id')
                    // ->join('guardian_details as gd', 'gd.id', '=', 'sg.guardians_id')
                    // ->join('course_completion_certificates as cc', 'cc.students_id', '=', 'students.id')
                    ->first();
            }
//dd($student);

            if(isset($student)) {
                $certificateTemplate = $this->publicCertificateTemplateQuery()
                    ->where('id', $request->get('certificate'))
                    ->first();
               //dd($certificateTemplate);
                if(isset($certificateTemplate)){
                   $data['issue_detail'] =  $issueStatus = $this->studentCertificateIssuedStatus($student, $certificateTemplate->certificate);
                    if (isset($issueStatus)) {
                       $certificateContent = $this->studentCertificateTextReplace($student,$certificateTemplate);
                        $data['certificate_template'] = $certificateTemplate;
                       $data['certificateContent'] = $certificateContent;
                    } else {
                        $request->session()->flash($this->message_warning, 'Dear Verifier, We are unable to found Certificate with your provided information.');
                    }
                }else{
                    $request->session()->flash($this->message_warning, 'Dear Verifier, Verification is not provided for this certificate. For More Info Contact our Institution.');
                }
            }else {
                
                $request->session()->flash($this->message_warning, 'Dear Verifier, We are unable to found Certificate with your provided information.');
            }

        }

    $template = $this->publicCertificateTemplateQuery()->pluck('certificate','id')->toArray();
        $data['certificate-template'] =  array_prepend($template,'Select Certificate','');

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.certificate.index'), compact('data','student'));
    }

    /*Public: verify a student ID card scanned via QR (?t=encrypted student id)*/
    public function idCard(Request $request)
    {
        $student = null;
        $token = (string) $request->get('t', '');
        if ($token !== '') {
            try {
                $studentId = decrypt($token);
            } catch (\Exception $e) {
                $studentId = null;
            }
            if ($studentId) {
                $student = Student::select('students.id', 'students.reg_no', 'students.first_name',
                    'students.middle_name', 'students.last_name', 'students.date_of_birth',
                    'students.blood_group', 'students.student_image', 'students.status',
                    'f.faculty as faculty_name', 'b.title as batch_title', 'sem.semester as semester_name')
                    ->leftJoin('faculties as f', 'f.id', '=', 'students.faculty')
                    ->leftJoin('student_batches as b', 'b.id', '=', 'students.batch')
                    ->leftJoin('semesters as sem', 'sem.id', '=', 'students.semester')
                    ->where('students.id', $studentId)
                    ->first();
            }
        }
        return view('verification.id-card', compact('student'));
    }

}
