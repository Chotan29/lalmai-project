<?php

namespace App\Http\Requests\Web\ContactList;

use App\Rules\GoogleRecaptcha;
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
            'first_name' => 'required | max:50',
            'last_name' => 'required | max:50',
            'phone' => 'max:50',
            'email' => 'required  | max:100',
            'address' => 'max:100',
            'message' => 'required',
           'g-recaptcha-response' => ['required', new GoogleRecaptcha()]
        ];
    }

    public function messages()
    {
        return [
            'g-recaptcha-response.required' => 'Google Re-Captcha Required to verify you.'
        ];
    }
}
