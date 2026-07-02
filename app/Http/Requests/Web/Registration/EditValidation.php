<?php

 /*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Requests\Web\Registration;

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
            'reg_no'                        => 'max:50 | unique:web_registrations,reg_no,'.decrypt($this->request->get('id')),
            'reg_date'                      => 'required',
            'web_registration_programmes_id'    => 'required',
            'name'                          => 'required | max:100',
            'date_of_birth'                 => 'required',
            'sex'                           => 'required',
            'religion'                      =>'max:25',
            'caste'                         =>'max:25',
            'nationality'                   => 'required | max:25',

            'state'                         => 'required | max:100',
            'mother_tongue'                 => 'max:25',
            'blood_group'                   => 'max:5',

            'medicine_info'                 => 'max:100',
            'disease_info'                  => 'max:100',

            'guardian_name'                  => 'required | max:100',
            'guardian_relation'              => 'required | max:50',
            'guardian_occupation'            => 'max:50',
            'guardian_annual_income'         => '',

            'address'                       => 'required | max:100',
            'tel'                           => 'max:15',
            'cell_1'                        => 'required | max:15',
            'cell_2'                        => 'max:15',
            'email'                         => 'max:100 | unique:web_registrations,email,'.decrypt($this->request->get('id')),

            'mailing_address'               => 'required | max:100',
            'mailing_tel'                   => 'max:15',
            'mailing_cell_1'                => 'max:15',
            'mailing_cell_2'                => 'max:15',
            'mailing_email'                 => 'max:100',


            'student_main_image'            => 'mimes:jpeg,jpg,bmp,png',
            'student_signature_main_image'  => 'mimes:jpeg,jpg,bmp,png',
            'guardian_signature_main_image'  => 'mimes:jpeg,jpg,bmp,png',

            //academic
            'examination.*'                  => 'max:50',
            'institution.*'                  => 'max:100',
            'board_university.*'             => 'max:50',
            'year_of_pass.*'                 => 'max:4',
            'percentage_grade.*'             => 'max:25',

            //working experience
            'experience_info.*'                  => '',
        ];
    }

    public function messages()
    {
        return [
            'web_registration_programmes_id.required'  =>    'Programme Applied For Required.'
        ];
    }
}
