<?php

namespace App\Http\Requests\Certificate\NirgamUtara;

use Illuminate\Foundation\Http\FormRequest;

class AddValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /*'created_by', 'last_updated_by', 'students_id', 'nu_num', 'date_of_issue', 'date_of_leaving',
        'leaving_time_class', 'previous_school_name','join_time_class', 'reason_to_leave', 'mention_body_mark',
         'any_other_remark', 'ref_text', 'status*/
        return [
            'students_id'           => 'required | unique:nirgam_utara_certificates,students_id',
            'date_of_issue'         => 'required',
            'date_of_leaving'       => 'required',
            'nu_num'                => 'required | unique:nirgam_utara_certificates,nu_num',
            'join_time_class'       => 'required',
            'leaving_time_class'    => 'required',
        ];

    }

    public function messages()
    {
        return [
            'students_id.required'     => 'Student Information Required',
            'students_id.unique'       => 'Certificate already created for this Student. Please Find and Edit Certificate',
        ];
    }
}

