<?php
namespace App\Traits;

use App\Models\CertificateTemplate;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\DepartmentHead;
use App\Models\GuardianDetail;
use App\Models\Semester;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentBatch;
use App\Models\Year;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait CertificateScope{
    public function activeCertificateTemplates()
    {
        $template = CertificateTemplate::whereNotIn('id',[14,15])->Active()->orderBy('certificate')->pluck('certificate','id')->toArray();
        return array_prepend($template,'Select Certificate Template','');
    }
    public function printCustomCertificate($request)
    {

    }
    public function studentCertificateIssuedStatus($student,$certificateTemplate)
    {
        $issuedStatus = null;
        switch ($certificateTemplate) {
            case 'ATTENDANCE':
                $issuedStatus = $student->attendanceCertificate()->first();
                break;
            case 'BONAFIDE':
                $issuedStatus = $student->bonafideCertificate()->first();
                break;
            case 'CHARACTER':
                $issuedStatus = $student->characterCertificate()->first();
                break;
            case 'COURSE COMPLETION':
                $issuedStatus = $student->courseCompletionCertificate()->first();
                break;
            case 'FEE VOUCHER':
                break;
            case 'GENERAL TEMPLATE':
                $issuedStatus = true;
                break;
            case 'IDENTITY CARD':
                $issuedStatus = true;
                break;
            case 'MEDIUM OF INSTRUCTION':
                $issuedStatus = $student->MOICertificate()->first();
                break;
            case 'NIRGAMUTARA_1':
                $issuedStatus = true;
                break;
            case 'NIRGAMUTARA_2':
                $issuedStatus = true;
                break;
            case 'PROVISIONAL':
                $issuedStatus = $student->provisionalCertificate()->first();
                break;
            case 'REGISTRATION FORM':
                $issuedStatus = true;
                break;
            case 'SEMESTER WISE FINAL RESULT':
                $issuedStatus = $student->testimonialCertificate()->first();
                break;
            case 'TESTIMONIAL':
                $issuedStatus = $student->testimonialCertificate()->first();
                break;
            case 'TRANSCRIPT':
                $issuedStatus = $student->transcriptCertificate()->first();
                break;
            case 'TRANSFER_1':
                $issuedStatus = $student->transferCertificate()->first();
                break;
            case 'TRANSFER_2':
                 $issuedStatus = $student->transferCertificate()->first();
                break;
            case 'TRANSFER_3':
                 $issuedStatus = $student->transferCertificate()->first();
                break;
        }
        return $issuedStatus;
    }

    public function studentCertificateTextReplace($student,$certificateTemplate)
    {
        $text = '';
        //dd($student,$certificateTemplate);
        switch ($certificateTemplate->certificate) {
            case 'ATTENDANCE':
                $data = $student->attendanceCertificate()->first();
                $text = str_replace('{{year_of_study}}', $data->year_of_study, $certificateTemplate->template);
                $text = str_replace('{{percentage_of_attendance}}', $data->percentage_of_attendance, $text);
                break;
            case 'BONAFIDE':
                $data = $student->bonafideCertificate()->first();
                $text = str_replace('{{course}}', $data->course, $certificateTemplate->template);
                $text = str_replace('{{period}}', $data->period, $text);
                $text = str_replace('{{character}}', $data->character, $text);
                break;
            case 'CHARACTER':
                $data = $student->characterCertificate()->first();
                $text = str_replace('{{date_of_issue}}', Carbon::parse($data->date_of_leaving)->format('d-m-Y'), $certificateTemplate->template);
                $text = str_replace('{{cc_num}}', $data->cc_num, $text);
                $text = str_replace('{{year}}', $data->year, $text);
                $text = str_replace('{{character}}', $data->character, $text);
                break;
            case 'COURSE COMPLETION':
                $data = $student->courseCompletionCertificate()->first();
                $text = str_replace('{{course}}', $data->course, $certificateTemplate->template);
                $text = str_replace('{{period}}', $data->period, $text);
                $text = str_replace('{{character}}', $data->character, $text);
                break;
            case 'FEE VOUCHER':
                $text = $certificateTemplate->template;
                break;
            case 'GENERAL TEMPLATE':
                $text = $certificateTemplate->template;
                break;
            case 'IDENTITY CARD':
                $text = $certificateTemplate->template;
                break;
            case 'MEDIUM OF INSTRUCTION':
                $data = $student->MOICertificate()->first();
                $text = str_replace('{{date_of_issue}}', Carbon::parse($data->date_of_leaving)->format('d-m-Y'), $certificateTemplate->template);
                $text = str_replace('{{moic_num}}', $data->moic_num, $text);
                $text = str_replace('{{ref_num}}', $data->ref_num, $text);
                break;
            case 'NIRGAMUTARA_1':
                $data = $student->nirgamUtaraCertificate()->first();
                $text = str_replace('{{date_of_leaving}}', Carbon::parse($data->date_of_leaving)->format('d-m-Y'), $certificateTemplate->template);
                $text = str_replace('{{date_of_issue}}', Carbon::parse($data->date_of_issue)->format('d-m-Y'), $text);
                $text = str_replace('{{nu_num}}', $data->nu_num, $text);
                $text = str_replace('{{leaving_time_class}}', $data->leaving_time_class, $text);
                $text = str_replace('{{join_time_class}}', $data->join_time_class, $text);
                $text = str_replace('{{previous_school_name}}', $data->previous_school_name, $text);
                $text = str_replace('{{reason_to_leave}}', $data->reason_to_leave, $text);
                $text = str_replace('{{mention_body_mark}}', $data->mention_body_mark, $text);
                $text = str_replace('{{any_other_remark}}', $data->any_other_remark, $text);
                break;
            case 'NIRGAMUTARA_2':
                $data = $student->nirgamUtaraCertificate()->first();
                $text = str_replace('{{date_of_leaving}}', Carbon::parse($data->date_of_leaving)->format('d-m-Y'), $certificateTemplate->template);
                $text = str_replace('{{date_of_issue}}', Carbon::parse($data->date_of_issue)->format('d-m-Y'), $text);
                $text = str_replace('{{nu_num}}', $data->nu_num, $text);
                $text = str_replace('{{leaving_time_class}}', $data->leaving_time_class, $text);
                $text = str_replace('{{join_time_class}}', $data->join_time_class, $text);
                $text = str_replace('{{previous_school_name}}', $data->previous_school_name, $text);
                $text = str_replace('{{reason_to_leave}}', $data->reason_to_leave, $text);
                $text = str_replace('{{mention_body_mark}}', $data->mention_body_mark, $text);
                $text = str_replace('{{any_other_remark}}', $data->any_other_remark, $text);
                break;
            case 'PROVISIONAL':
                $data = $student->provisionalCertificate()->first();
                $text = str_replace('{{date_of_issue}}', Carbon::parse($data->date_of_leaving)->format('d-m-Y'), $certificateTemplate->template);
                $text = str_replace('{{result_publish_date}}', Carbon::parse($data->result_publish_date)->format('d-m-Y'), $certificateTemplate->template);
                $text = str_replace('{{pc_num}}', $data->pc_num, $text);
                $text = str_replace('{{year}}', $data->year, $text);
                $text = str_replace('{{gpa}}', $data->gpa, $text);
                $text = str_replace('{{scale}}', $data->scale, $text);
                break;
            case 'REGISTRATION FORM':
                $data = true;
                $text = $certificateTemplate->template;
                break;
            case 'SEMESTER WISE FINAL RESULT':
                $data = $student->transcriptCertificate()->first();
                $text = $certificateTemplate->template;
                break;
            case 'TESTIMONIAL':
                $data = $student->testimonialCertificate()->first();
                $text = str_replace('{{date_of_issue}}', Carbon::parse($data->date_of_leaving)->format('d-m-Y'), $certificateTemplate->template);
                $text = str_replace('{{tmc_num}}', $data->tmc_num, $text);
                $text = str_replace('{{ref_num}}', $data->ref_num, $text);
                $text = str_replace('{{year}}', $data->year, $text);
                $text = str_replace('{{gpa}}', $data->gpa, $text);
                $text = str_replace('{{scale}}', $data->scale, $text);
                $text = str_replace('{{program_duration}}', $data->scale, $text);
                $text = str_replace('{{average_grade}}', $data->average_grade, $text);
                break;
            case 'TRANSCRIPT':
                $data = $student->transcriptCertificate()->first();
                $text = str_replace('{{date_of_issue}}', Carbon::parse($data->date_of_leaving)->format('d-m-Y'), $certificateTemplate->template);
                $text = str_replace('{{trc_num}}', $data->trc_num, $text);
                $text = str_replace('{{year}}', $data->year, $text);
                $text = str_replace('{{duration}}', $data->duration, $text);
                $text = str_replace('{{credit_required}}', $data->credit_required, $text);
                //semester wise
                $text = str_replace('{{gpa}}', $data->gpa, $text);
                $text = str_replace('{{verification_code}}', $data->verification_code, $text);
                $text = str_replace('{{mark_sheet_sn}}', $data->mark_sheet_sn, $text);
                $text = str_replace('{{provisional_certificate_num}}', $data->provisional_certificate_num, $text);

//                if($certificateTemplate->student_photo == 1){
//                    $student->student_image = $data->student_image?$data->student_image:"";
//                    $imageUrl=url('images/studentProfile').'/'.$student->student_image;
//                    $image = "<img class=\"img-thumbnail\" alt=\"\" src=\"$imageUrl\" width=\"150px\" />";
//
//                    $text = str_replace('{{student_image}}', $image, $text);
//                }else{
//                    $text = str_replace('{{student_image}}', '', $text);
//                }

                break;
            case 'TRANSFER_1':
                $data = $student->transferCertificate()->first();
                $text = str_replace('{{application_date}}',Carbon::parse($data->application_date)->format('d-m-Y'), $certificateTemplate->template);
                $text = str_replace('{{date_of_leaving}}', Carbon::parse($data->date_of_leaving)->format('d-m-Y'), $text);
                $text = str_replace('{{date_of_issue}}', Carbon::parse($data->date_of_issue)->format('d-m-Y'), $text);
                $text = str_replace('{{school_category}}', $data->school_category, $text);
                $text = str_replace('{{dob_certificate}}', $data->dob_certificate, $text);
                $text = str_replace('{{character}}', $data->character, $text);
                $text = str_replace('{{tc_num}}', $data->tc_num, $text);
                $text = str_replace('{{join_time_class}}', $data->join_time_class, $text);
                $text = str_replace('{{leaving_time_class}}', $data->leaving_time_class, $text);
                $text = str_replace('{{qualified_to_promote}}', $data->qualified_to_promote, $text);
                $text = str_replace('{{paid_fee_status}}', $data->paid_fee_status, $text);
                //request from India
                $text = str_replace('{{fee_concession_detail}}', $data->fee_concession_detail, $text);
                $text = str_replace('{{exam_fail_status}}', $data->exam_fail_status, $text);
                $text = str_replace('{{subject_studies}}', $data->subject_studies, $text);
                $text = str_replace('{{last_taken_exam_with_result}}', $data->last_taken_exam_with_result, $text);
                $text = str_replace('{{cadet_detail}}', $data->cadet_detail, $text);
                $text = str_replace('{{reason_to_leave}}', $data->reason_to_leave, $text);
                $text = str_replace('{{total_attendance}}', $data->total_attendance, $text);
                $text = str_replace('{{school_college_open_total}}', $data->school_college_open_total, $text);
                $text = str_replace('{{birth_place}}', $data->birth_place, $text);
                $text = str_replace('{{promoted_class}}', $data->promoted_class, $text);
                $text = str_replace('{{extra_activity_detail}}', $data->extra_activity_detail, $text);
                $text = str_replace('{{any_other_remark}}', $data->any_other_remark, $text);
                if($certificateTemplate->student_photo == 1){
                    $student->student_image = $data->student_image?$data->student_image:"";
                    $imageUrl=url('images/studentProfile').'/'.$student->student_image;
                    $image = "<img class=\"img-thumbnail\" alt=\"\" src=\"$imageUrl\" width=\"150px\" />";

                    $text = str_replace('{{student_image}}', $image, $text);
                }else{
                    $text = str_replace('{{student_image}}', '', $text);
                }

                break;
            case 'TRANSFER_2':
                $data = $student->transferCertificate()->first();
                $text = str_replace('{{application_date}}',Carbon::parse($data->application_date)->format('d-m-Y'), $certificateTemplate->template);
                $text = str_replace('{{date_of_leaving}}', Carbon::parse($data->date_of_leaving)->format('d-m-Y'), $text);
                $text = str_replace('{{date_of_issue}}', Carbon::parse($data->date_of_issue)->format('d-m-Y'), $text);
                $text = str_replace('{{school_category}}', $data->school_category, $text);
                $text = str_replace('{{dob_certificate}}', $data->dob_certificate, $text);
                $text = str_replace('{{character}}', $data->character, $text);
                $text = str_replace('{{tc_num}}', $data->tc_num, $text);
                $text = str_replace('{{join_time_class}}', $data->join_time_class, $text);
                $text = str_replace('{{leaving_time_class}}', $data->leaving_time_class, $text);
                $text = str_replace('{{qualified_to_promote}}', $data->qualified_to_promote, $text);
                $text = str_replace('{{paid_fee_status}}', $data->paid_fee_status, $text);
                //request from India
                $text = str_replace('{{fee_concession_detail}}', $data->fee_concession_detail, $text);
                $text = str_replace('{{exam_fail_status}}', $data->exam_fail_status, $text);
                $text = str_replace('{{subject_studies}}', $data->subject_studies, $text);
                $text = str_replace('{{last_taken_exam_with_result}}', $data->last_taken_exam_with_result, $text);
                $text = str_replace('{{cadet_detail}}', $data->cadet_detail, $text);
                $text = str_replace('{{reason_to_leave}}', $data->reason_to_leave, $text);
                $text = str_replace('{{total_attendance}}', $data->total_attendance, $text);
                $text = str_replace('{{school_college_open_total}}', $data->school_college_open_total, $text);
                $text = str_replace('{{birth_place}}', $data->birth_place, $text);
                $text = str_replace('{{promoted_class}}', $data->promoted_class, $text);
                $text = str_replace('{{extra_activity_detail}}', $data->extra_activity_detail, $text);
                $text = str_replace('{{any_other_remark}}', $data->any_other_remark, $text);
                if($certificateTemplate->student_photo == 1){
                    $student->student_image = $data->student_image?$data->student_image:"";
                    $imageUrl=url('images/studentProfile').'/'.$student->student_image;
                    $image = "<img class=\"img-thumbnail\" alt=\"\" src=\"$imageUrl\" width=\"150px\" />";

                    $text = str_replace('{{student_image}}', $image, $text);
                }else{
                    $text = str_replace('{{student_image}}', '', $text);
                }
                break;
            case 'TRANSFER_3':
                $data = $student->transferCertificate()->first();
                $text = str_replace('{{application_date}}',Carbon::parse($data->application_date)->format('d-m-Y'), $certificateTemplate->template);
                $text = str_replace('{{date_of_leaving}}', Carbon::parse($data->date_of_leaving)->format('d-m-Y'), $text);
                $text = str_replace('{{date_of_issue}}', Carbon::parse($data->date_of_issue)->format('d-m-Y'), $text);
                $text = str_replace('{{school_category}}', $data->school_category, $text);
                $text = str_replace('{{dob_certificate}}', $data->dob_certificate, $text);
                $text = str_replace('{{character}}', $data->character, $text);
                $text = str_replace('{{tc_num}}', $data->tc_num, $text);
                $text = str_replace('{{join_time_class}}', $data->join_time_class, $text);
                $text = str_replace('{{leaving_time_class}}', $data->leaving_time_class, $text);
                $text = str_replace('{{qualified_to_promote}}', $data->qualified_to_promote, $text);
                $text = str_replace('{{paid_fee_status}}', $data->paid_fee_status, $text);
                //request from India
                $text = str_replace('{{fee_concession_detail}}', $data->fee_concession_detail, $text);
                $text = str_replace('{{exam_fail_status}}', $data->exam_fail_status, $text);
                $text = str_replace('{{subject_studies}}', $data->subject_studies, $text);
                $text = str_replace('{{last_taken_exam_with_result}}', $data->last_taken_exam_with_result, $text);
                $text = str_replace('{{cadet_detail}}', $data->cadet_detail, $text);
                $text = str_replace('{{reason_to_leave}}', $data->reason_to_leave, $text);
                $text = str_replace('{{total_attendance}}', $data->total_attendance, $text);
                $text = str_replace('{{school_college_open_total}}', $data->school_college_open_total, $text);
                $text = str_replace('{{birth_place}}', $data->birth_place, $text);
                $text = str_replace('{{promoted_class}}', $data->promoted_class, $text);
                $text = str_replace('{{extra_activity_detail}}', $data->extra_activity_detail, $text);
                $text = str_replace('{{any_other_remark}}', $data->any_other_remark, $text);
                if($certificateTemplate->student_photo == 1){
                    $student->student_image = $data->student_image?$data->student_image:"";
                    $imageUrl=url('images/studentProfile').'/'.$student->student_image;
                    $image = "<img class=\"img-thumbnail\" alt=\"\" src=\"$imageUrl\" width=\"150px\" />";

                    $text = str_replace('{{student_image}}', $image, $text);
                }else{
                    $text = str_replace('{{student_image}}', '', $text);
                }
                break;
        }

        $certificateTemplate = $this->textReplace($student, $text);
        return $certificateTemplate;
    }

    public function CertificateViewPath($certificate)
    {
        $layoutPath = null;
        switch ($certificate) {
            case 'ATTENDANCE':
                $layoutPath = 'print.certificate.attendance';
                break;
            case 'BONAFIDE':
                $layoutPath = 'print.certificate.bonafide';
                break;
            case 'CHARACTER':
                $layoutPath = 'print.certificate.character';
                break;
            case 'COURSE COMPLETION':
                $layoutPath = 'print.certificate.course-completion';
                break;
            case 'FEE VOUCHER':
                $layoutPath = 'print.student-fee.generate-fee-voucher';
                break;
            case 'GENERAL TEMPLATE':
                $layoutPath = 'print.certificate.generate';
                break;
            case 'IDENTITY CARD':
                $layoutPath = 'print.certificate.identity-card';
                break;
            case 'MEDIUM OF INSTRUCTION':
                $layoutPath = 'print.certificate.moi';
                break;
            case 'NIRGAMUTARA_1':
                $layoutPath = 'print.certificate.nirgam_utara_1';
                break;
            case 'NIRGAMUTARA_2':
                $layoutPath = 'print.certificate.nirgam_utara_2';
                break;
            case 'PROVISIONAL':
                $layoutPath = 'print.certificate.provisional';
                break;
            case 'REGISTRATION FORM':
                $layoutPath = 'print.student.registration-card';
                break;
            case 'SEMESTER WISE FINAL RESULT':
                $layoutPath = 'print.certificate.semester-wise-final-result';
                break;
            case 'TESTIMONIAL':
                $layoutPath = 'print.certificate.testimonial';
                break;
            case 'TRANSCRIPT':
                $layoutPath = 'print.certificate.transcript';
                break;
            case 'TRANSFER_1':
                $layoutPath = 'print.certificate.transfer_1';
                break;
            case 'TRANSFER_2':
                $layoutPath = 'print.certificate.transfer_2';
                break;
            case 'TRANSFER_3':
                $layoutPath = 'print.certificate.transfer_3';
                break;
        }

        return $layoutPath;
    }


}