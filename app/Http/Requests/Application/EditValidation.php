<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class EditValidation extends FormRequest
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
            'application_type_id'   => 'required',
            'date'                  => 'required',
            'subject'                 => 'required | max:100',
            'message'               => 'required',
            'attach_file'           => 'max:10000|mimes:pdf,doc,docx,ppt,xls,xlsx,jpeg,jpg,bmp,png',
        ];

    }

    public function messages()
    {
        return [
            'application_type_id.required'         => 'Application Type is Required',
        ];

    }
}
