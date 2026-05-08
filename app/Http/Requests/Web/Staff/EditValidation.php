<?php

namespace App\Http\Requests\Web\Staff;

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
            'name' => 'required',
            'position' => 'required',
            'main_image' => 'mimes:jpeg,jpg,bmp,png,gif',
            'email' => 'required | unique:web_staff,email,'.decrypt($this->request->get('id')),
            'rank' => 'required',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
