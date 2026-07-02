<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferCertificate extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'students_id', 'date_of_issue', 'date_of_leaving', 'tc_num',
        'join_time_class','leaving_time_class', 'qualified_to_promote','paid_fee_status','character',
        'fee_concession_detail', 'exam_fail_status', 'subject_studies', 'last_taken_exam_with_result', 'cadet_detail',
        'reason_to_leave', 'total_attendance', 'school_college_open_total','any_other_remark','birth_place', 'promoted_class',
        'application_date', 'dob_certificate', 'school_category', 'extra_activity_detail',
        'ref_text','status'];
}
