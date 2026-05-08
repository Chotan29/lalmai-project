<?php

namespace App\Http\Requests\Web\HomeSetting;

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
            'notice_title'                  => 'max:15',
            'blog_title'                    => 'max:15',
            'event_title'                    => 'max:15',
            'introduction_title'            => 'required | max:100',
            'introduction_description'      => 'required',
            'introduction_button_text'      => 'required | max:15',
            'introduction_link'             => 'required | max:100',
            'welcome_title'                => 'max:100',
            'welcome_button_text'          => 'max:15',
            'services_title'                => 'max:100',
            'counter_title'                 => 'max:100',
            'staff_title'                   => 'max:100',
            'testimonial_title'             => 'max:100',
            'client_title'                  => 'max:100',
            'email_call_to_action_title'    => 'max:100',
            'email_call_to_action_button_text'  => 'max:15',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
