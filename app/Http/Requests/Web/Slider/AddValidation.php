<?php

namespace App\Http\Requests\Web\Slider;

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
            'title' => 'required | max:100 | unique:web_sliders,title',
            'main_image' => 'required | mimes:jpeg,jpg,bmp,png,gif | dimensions:min_width=1200,min_height=600',
            'rank' => 'required',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}