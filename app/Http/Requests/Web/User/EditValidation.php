<?php

namespace App\Http\Requests\Web\User;

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
            'name'              => 'required',
            'email'             => 'required | unique:users,email,'.decrypt($this->request->get('id')),
            'contact_number'    => 'required',
            'address'           => 'required',
            'main_image'     => 'mimes:jpeg,jpg,bmp,png,gif',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
