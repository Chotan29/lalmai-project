<?php

namespace App\Http\Requests\Web\Testimonial;

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
            'name' => 'required | unique:web_testimonials,name,'.decrypt($this->request->get('id')),
            'main_image' => 'mimes:jpeg,jpg,bmp,png,gif | dimensions:min_width=81,min_height=81',
            'comment' => 'required',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
