<?php

 /*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani 1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Requests\Academic\Faculty;

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
            'faculty'       => 'required | max:100 | unique:faculties,faculty',
            'faculty_code'      => 'required | unique:faculties,faculty_code',
            /* Both optional. Bounded because they date an identity card: a stray 40 in the years
               box would print a card valid until 2065. */
            'id_card_valid_years'  => 'nullable | integer | min:1 | max:10',
            'id_card_expiry_month' => 'nullable | integer | min:1 | max:12',
        ];
    }

    public function messages()
    {
        return [
            'faculty.required' => 'Please, Add Faculty.',
            'faculty.unique' => 'The Faculty/Program/Class already exist. Please, edit or create new.',
            'faculty_code.unique' => 'The Faculty code already exist. Please Enter Unique Faculty Code.',
        ];
    }
}
