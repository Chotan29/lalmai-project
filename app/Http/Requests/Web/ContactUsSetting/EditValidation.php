<?php

namespace App\Http\Requests\Web\ContactUsSetting;

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
            'address'           => 'required | max:100',
            'phone'             => 'required | max:100',
            'email'             => 'required | max:100',
            'webURL'            => 'max:100',
            'latitude'          => 'max:100',
            'longitude'         => 'max:100',
            'facebook_link'     => 'max:100',
            'twitter_link'      => 'max:100',
            'googlePlus_link'   => 'max:100',
            'linkedIn_link'     => 'max:100',
            'instagram_link'    => 'max:100',
            'whatsApp_link'     => 'max:100',
            'skype_link'        => 'max:100',
            'pinterest_link'    => 'max:100',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
