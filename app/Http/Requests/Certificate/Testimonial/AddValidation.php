<?php

namespace App\Http\Requests\Certificate\Testimonial;

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
        return [
            'students_id'           => 'required | unique:testimonial_certificates,students_id',
            'date_of_issue'         => 'required',
            'ref_num'                => 'required | unique:testimonial_certificates,ref_num',
            'tmc_num'                => 'required | unique:testimonial_certificates,tmc_num',
            'year'                  => 'required',
            //'gpa'                   => 'required',
            //'scale'                 => 'required',
            //'average_grade'         => 'required',
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

