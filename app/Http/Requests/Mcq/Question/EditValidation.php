<?php

 /*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Requests\Mcq\Question;

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
            'subjects_id'               =>     'required | exists:subjects,id',
            'mcq_question_groups_id'    =>     'required | exists:mcq_question_groups,id',
            'mcq_question_levels_id'    =>     'required | exists:mcq_question_levels,id',
            'question'                  =>     'required | unique:mcq_questions,question,'.decrypt($this->request->get('id')),
            'image'                     =>     'mimes:jpg,jpeg,bmp,png,gif',
            'mark'                      =>     'required',
            'type'                      =>     'required',


            'option_title.*'            =>     'required',
            'option_image_*'            =>      'mimes:jpeg,jpg,bmp,png,gif',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
