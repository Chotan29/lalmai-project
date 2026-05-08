<?php

 /*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani 1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Requests\Academic\Semester;

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
            'semester' => 'required | max:50',
            'slug'     => 'required | max:50 | unique:semesters,slug',
            'gradingType_id' => 'required | min:1',
        ];
    }

    public function messages()
    {
        return [
            'semester.required' => 'Please, Add Semester.',
            'gradingType_id.required' => 'Grading Type is Required.',
            'gradingType_id.min' => 'Grading Type is Required.',
        ];
    }
}
