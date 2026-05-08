<?php

namespace App\Http\Requests\Student\PublicRegistration;

use Illuminate\Foundation\Http\FormRequest;

class AddValidation extends FormRequest
{
    const PASSPORT_MIN_WIDTH = 300;
    const PASSPORT_MIN_HEIGHT = 400;
    const PASSPORT_RATIO = 35 / 45;
    const PASSPORT_RATIO_TOLERANCE = 0.08;

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
            'reg_no'                        => 'max:25 | unique:students,reg_no',
//            'reg_date'                      => 'required',
            'faculty'                       => 'required',
            'first_name'                    => 'required | max:25',
            'last_name'                     => 'required | max:25',
            'date_of_birth'                 => 'required',
            'gender'                        => 'required',
            'email'                         => 'required | max:100 | unique:students,email',
            'mobile_1'                      => 'max:25',
            'father_first_name'             => 'max:25',
            'father_middle_name'            => 'max:25',
            'father_last_name'              => 'max:25',
            'mother_first_name'             => 'max:25',
            'mother_middle_name'            => 'max:25',
            'mother_last_name'              => 'max:25',
            'guardian_first_name'             => 'max:25',
            'guardian_middle_name'            => 'max:25',
            'guardian_last_name'              => 'max:25',
            'guardian_mobile_1'               => 'max:25',
            'guardian_email'                  => 'max:100',
            'student_main_image'            => 'required|image|mimes:jpeg,jpg,bmp,png|max:5120|dimensions:min_width=300,min_height=400',
        ];

    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->hasFile('student_main_image')) {
                return;
            }

            $image = $this->file('student_main_image');

            if (!$image || !$image->isValid()) {
                return;
            }

            $imageInfo = @getimagesize($image->getRealPath());
            if (!$imageInfo || count($imageInfo) < 2) {
                $validator->errors()->add('student_main_image', 'Uploaded photo could not be read. Please upload a clear JPG, JPEG, BMP, or PNG image.');
                return;
            }

            $width = (int) $imageInfo[0];
            $height = (int) $imageInfo[1];

            if ($width < self::PASSPORT_MIN_WIDTH || $height < self::PASSPORT_MIN_HEIGHT) {
                $validator->errors()->add('student_main_image', 'Photo resolution is too low. Minimum size is 300x400 pixels.');
            }

            if ($height > 0) {
                $ratio = $width / $height;
                if (abs($ratio - self::PASSPORT_RATIO) > self::PASSPORT_RATIO_TOLERANCE) {
                    $validator->errors()->add('student_main_image', 'Photo must be passport style with a portrait ratio close to 35x45.');
                }
            }
        });
    }

    public function messages()
    {
        return [
            'reg_no.unique'                          => 'Enter Unique Reg.No.',
            'student_main_image.required'            => 'Image Required, Please Upload Image',
            'student_main_image.image'               => 'Student photo must be a valid image file.',
            'student_main_image.mimes'               => 'Student photo must be a JPG, JPEG, BMP, or PNG file.',
            'student_main_image.max'                 => 'Student photo size must not be greater than 5 MB.',
            'student_main_image.dimensions'          => 'Student photo resolution must be at least 300x400 pixels.',

        ];
    }
}
