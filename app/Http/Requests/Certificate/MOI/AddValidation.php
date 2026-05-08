<?php

namespace App\Http\Requests\Certificate\MOI;

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
            'students_id'           => 'required | unique:medium_of_instruction_certificates,students_id',
            'date_of_issue'         => 'required',
            'ref_num'                => 'required | unique:medium_of_instruction_certificates,ref_num',
            'moic_num'                => 'required | unique:medium_of_instruction_certificates,moic_num',
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

