<?php

namespace App\Http\Requests\Web\Service;

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
            'title' => 'required | max:100 | unique:web_services,title',
            'sub_title' => 'required',
            'main_image' => 'mimes:jpeg,jpg,bmp,png,gif | dimensions:min_width=585,min_height=385',
            'rank' => 'required',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}