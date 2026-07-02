<?php

 /*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Requests\Web\InfoNotice;

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
             'title'             => 'required | max:100 | unique:info_notices,title,'.decrypt($this->request->get('id')),
             'message'           => 'required',
             'publish_date'      => 'required',
             'end_date'          => 'required',
             'role'              => 'required'
         ];
    }

    public function messages()
    {
        return [
            'title.unique' => 'Create Unique Title.',
            'role.required' => 'Message Display Groups required.',
        ];
    }
}
