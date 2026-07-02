<?php

namespace App\Http\Requests\Web\AboutUsSetting;

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
            'title' => 'required | max:100',
            'slogan' => 'required | max:100',
            'description' => 'required',
            'main_image' => 'mimes:jpeg,jpg,bmp,png,gif | dimensions:min_width=570,min_height=418',
            'counter_title' => 'max:100',
            'counter_slogan' => 'max:100',
            'staffs_title' => 'max:100',
            'staffs_slogan' => 'max:100',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
