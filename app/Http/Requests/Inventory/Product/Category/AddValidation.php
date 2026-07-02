<?php

 /*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Requests\Inventory\Product\Category;

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
            'title'             => 'required | max:50 | unique:categories,title',
            'name.*'              => 'required',
        ];
    }

    /*public function messages()
    {
        return [
            'title.required' => 'Please, Add Title.',
            'name' => 'Please, Add Category Name',
            'percentage_from' => 'Please, Add Percentage From.',
            'percentage_to' => 'Please, Add Percentage To.',
        ];
    }*/
}
