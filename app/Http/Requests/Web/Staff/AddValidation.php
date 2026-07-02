<?php

namespace App\Http\Requests\Web\Staff;

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
            'name'          => 'required',
            'main_image'    => 'mimes:jpeg,jpg,bmp,png,gif',
            'position'      => 'required',
            'email'         => 'required | unique:web_staff,email',
            'rank'          => 'required',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}