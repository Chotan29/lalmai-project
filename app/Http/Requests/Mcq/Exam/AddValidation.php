<?php

 /*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Requests\Mcq\Exam;

use Illuminate\Foundation\Http\FormRequest;
use Zend\Http\Request;

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
    public function rules(Request $request)
    {
            $rules['faculty']                       =     'required | exists:faculties,id';
            $rules['semesters_id']              =     'required | exists:semesters,id';
            $rules['subjects_id']               =     'required | exists:subjects,id';
            $rules['title']                     =     'required | unique:mcq_exams,title';

            $rules['mcq_instructions_id']       =     'required';
            $rules['exam_status']               =     'required';
            $rules['mark_type']                 =     'required';
            $rules['question_type']             =     'required';
            $rules['no_of_question']            =     'required';
            $rules['result_status']             =     'required';
            $rules['exam_type']                 =     'required';

        if($rules['exam_type'] && $rules['exam_type']=="date_duration")
        {
            $rules['duration']            =     'required';
            $rules['start_date']            =     'required';
            $rules['end_date']            =     'required';
        }elseif($rules['exam_type'] && $rules['exam_type']=="date_time_duration"){


            $rules['duration']            =     'required';
            $rules['start_date_time']            =     'required';
            $rules['end_date_time']            =     'required';
        }else{
            $rules['duration']            =     'required';
        }
        //'duration'=>'Duration','date_duration'=>'Date & Duration','date_time_duration'=>'Date,Time & Duration'


        /*return [
            'faculty'                   =>     'required | exists:faculties,id',
            'semesters_id'              =>     'required | exists:semesters,id',
            'subjects_id'               =>     'required | exists:subjects,id',
            'title'                     =>     'required | unique:title,mcq_exams',

            'mcq_instructions_id'                      =>     'required',
            'exam_status'                      =>     'required',
            'mark_type'                      =>     'required',
            'question_type'                      =>     'required',
            'no_of_question'                      =>     'required',
            'result_status'                      =>     'required',
            'exam_type'                      =>     'required',



        ];*/
        return $rules;
    }

    public function messages()
    {
        return [

        ];
    }
}
