<?php

namespace App\Http\Requests\Student\PublicRegistration;

use Illuminate\Foundation\Http\FormRequest;

class AddValidation extends FormRequest
{
    const PASSPORT_MIN_WIDTH = 300;
    const PASSPORT_MIN_HEIGHT = 400;
    const PASSPORT_RATIO = 35 / 45;
    const PASSPORT_RATIO_TOLERANCE = 0.08;

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
            'reg_no'                        => 'max:25 | unique:students,reg_no',
//            'reg_date'                      => 'required',
            'faculty'                       => 'required',
            'first_name'                    => 'required | max:25',
            'last_name'                     => 'required | max:25',
            'date_of_birth'                 => 'required',
            'gender'                        => 'required',
            'blood_group'                   => 'required | max:5',
            'religion'                      => 'required | max:25',
            'email'                         => 'required | max:100 | unique:students,email',
            'mobile_1'                      => 'max:25',
            'father_first_name'             => 'max:25',
            'father_middle_name'            => 'max:25',
            'father_last_name'              => 'max:25',
            'mother_first_name'             => 'max:25',
            'mother_middle_name'            => 'max:25',
            'mother_last_name'              => 'max:25',
            'guardian_first_name'             => 'max:25',
            'guardian_middle_name'            => 'max:25',
            'guardian_last_name'              => 'max:25',
            'guardian_mobile_1'               => 'max:25',
            'guardian_email'                  => 'max:100',
            // Image validation only client-side, server-side only required
            'student_main_image'            => 'required',
        ];

    }


    public function messages()
    {
        return [
            'reg_no.unique'                          => 'Enter Unique Reg.No.',
            'student_main_image.required'            => 'Image Required, Please Upload Image',
            'blood_group.required'                  => 'Please select blood group.',
            'religion.required'                     => 'Please select religion.',
        ];
    }
}
