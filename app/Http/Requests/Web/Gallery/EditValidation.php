<?php

namespace App\Http\Requests\Web\Gallery;

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
            'title' => 'required | unique:web_galleries,title,'.decrypt($this->request->get('id')),
            'main_image' => 'mimes:jpeg,jpg,bmp,png,gif | dimensions:min_width=410,min_height=376',
            'gallery_image.*' => 'mimes:jpeg,jpg,bmp,png,gif | dimensions:min_width=300,min_height=211',
            'description' => 'required',
            'rank' => 'required',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
