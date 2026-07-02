<?php

 /*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Requests\Web\Blog;

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
            'title' => 'required | unique:web_blogs,title',
            'main_image' => 'mimes:jpeg,jpg,bmp,png,gif | dimensions:min_width=870,min_height=439',
            'category_id' => 'required',
            'detail_desc' => 'required',
            'publish_date' => 'required',
            'seo_title' => 'required',
            'seo_keywords' => 'required',
            'seo_description' => 'required',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
