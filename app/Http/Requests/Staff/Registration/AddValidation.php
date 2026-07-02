<?php

namespace App\Http\Requests\Staff\Registration;

use App\Rules\AttendanceProfilePhotoRule;
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
            'reg_no'                => ['required', 'regex:/^[0-9]+$/', 'unique:staff,reg_no'],
            'join_date'              => 'required',
            'designation'           => 'required',
            'first_name'            => 'required',
            'last_name'             => 'required',
            'date_of_birth'         => 'required',
            'gender'                => 'required',
            'qualification'         => 'required',
            'mobile_1'              => 'required',
            'main_image'           => ['required', 'mimes:jpeg,jpg,png', 'max:5120', new AttendanceProfilePhotoRule()],
        ];

    }

    public function messages()
    {
        return [
            'reg_no.required'                => 'Staff Id is required.',
            'reg_no.regex'                   => 'Staff Id must be numeric (digits only).',
            'reg_no.unique'                  => 'This Staff Id is already used. Enter a unique Staff Id.',
            'join_date.required'             => 'Join Date is required.',
            'designation.required'           => 'Designation is required.',
            'first_name.required'            => 'First Name is required.',
            'last_name.required'             => 'Last Name is required.',
            'date_of_birth.required'         => 'Date of Birth is required.',
            'gender.required'                => 'Gender is required.',
            'mobile_1.required'              => 'Mobile number is required.',
            'qualification.required'         => 'Qualification is required.',
            'main_image.required'            => 'Staff photo is required. Upload a passport size photo with white background.',
            'main_image.mimes'               => 'Photo must be JPG or PNG format.',
            'main_image.max'                 => 'Photo file size cannot exceed 5MB.',
        ];
    }
}
