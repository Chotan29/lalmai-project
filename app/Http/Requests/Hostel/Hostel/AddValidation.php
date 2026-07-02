<?php

 /*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Requests\Hostel\Hostel;

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
            'name'              => 'required | max:50 | unique:hostels,name',
            'address'           => 'required | max:50',
            'contact_detail'    => 'required',
            'warden'            => 'required | max:50',
            'type'              => 'required | max:50',
            'status'            => 'required | in:active,in-acctive',
            'rooms'             => 'required',
            'room_type'         => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'Hostels already exist',
        ];
    }
}
