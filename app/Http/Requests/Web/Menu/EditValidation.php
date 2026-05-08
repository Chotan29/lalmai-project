<?php

namespace App\Http\Requests\Web\Menu;

use App\Models\Menu;
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
            'title' => 'required | unique:web_menus,title,'.decrypt($this->request->get('id')),
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Please, add title.',
        ];
    }

   /* public function customRule()
    {
        Validator::extend('valid_id', function($attribute, $value, $parameter, $validation){
            if(Menu::find($this->request->get('id')))
                return true;

            return false;
        });

    }*/
}
