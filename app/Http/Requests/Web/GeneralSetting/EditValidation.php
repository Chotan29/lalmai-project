<?php

namespace App\Http\Requests\Web\GeneralSetting;

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
            'site_title'              => 'required | max:100',
            'site_slogan'            => 'required | max:100',
            'site_desc'               => 'required',
            'favicon_image'           => 'mimes:jpeg,jpg,bmp,png,gif | dimensions:min_width=16,min_height=16',
            'site_logo_image'         => 'mimes:jpeg,jpg,bmp,png,gif | dimensions:min_width=400,min_height=80',
            'page_banner_image'       => 'mimes:jpeg,jpg,bmp,png,gif | dimensions:min_width=770,min_height=513',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
