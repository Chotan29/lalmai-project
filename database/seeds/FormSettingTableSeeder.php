<?php
use App\Models\Permission;
use Illuminate\Database\Seeder;

class FormSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $formBuilder = [
            /*StudentForm*/
            //General Info

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'reg_no',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('reg_no'),
                'description' => \Illuminate\Support\Str::title('reg_no')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'reg_date',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('reg_date'),
                'description' => \Illuminate\Support\Str::title('reg_date')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'university_reg',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('university_reg'),
                'description' => \Illuminate\Support\Str::title('university_reg')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'special_category',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('special_category'),
                'description' => \Illuminate\Support\Str::title('special_category')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'weightage_claim',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('weightage_claim'),
                'description' => \Illuminate\Support\Str::title('weightage_claim')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'faculty',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('faculty'),
                'description' => \Illuminate\Support\Str::title('faculty')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'semester',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('semester'),
                'description' => \Illuminate\Support\Str::title('semester')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'academic_status',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('academic_status'),
                'description' => \Illuminate\Support\Str::title('academic_status')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'batch',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('batch'),
                'description' => \Illuminate\Support\Str::title('batch')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'first_name',
                'type' => 'text',
                'display_name' => 'Name of Student',
                'description' => 'Name of Student'
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'middle_name',
                'type' => 'text',
                'display_name' => 'Name of Student',
                'description' => 'Name of Student'
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'last_name',
                'type' => 'text',
                'display_name' => 'Name of Student',
                'description' => 'Name of Student'
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'date_of_birth',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('date_of_birth'),
                'description' => \Illuminate\Support\Str::title('date_of_birth')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'gender',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('gender'),
                'description' => \Illuminate\Support\Str::title('gender')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'blood_group',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('blood_group'),
                'description' => \Illuminate\Support\Str::title('blood_group')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'religion',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('religion'),
                'description' => \Illuminate\Support\Str::title('religion')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'caste',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('caste'),
                'description' => \Illuminate\Support\Str::title('caste')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'nationality',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('nationality'),
                'description' => \Illuminate\Support\Str::title('nationality')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'national_id_1',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('national_id_1'),
                'description' => \Illuminate\Support\Str::title('national_id_1')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'national_id_2',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('national_id_2'),
                'description' => \Illuminate\Support\Str::title('national_id_2')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'national_id_3',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('national_id_3'),
                'description' => \Illuminate\Support\Str::title('national_id_3')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'national_id_4',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('national_id_4'),
                'description' => \Illuminate\Support\Str::title('national_id_4')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'mother_tongue',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('mother_tongue'),
                'description' => \Illuminate\Support\Str::title('mother_tongue')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'email',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('email'),
                'description' => \Illuminate\Support\Str::title('email')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'extra_info',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('extra_info'),
                'description' => \Illuminate\Support\Str::title('extra_info')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'student_image',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('student_image'),
                'description' => \Illuminate\Support\Str::title('student_image')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'student_signature',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('student_signature'),
                'description' => \Illuminate\Support\Str::title('student_signature')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'reg_fee',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('reg_fee'),
                'description' => \Illuminate\Support\Str::title('reg_fee')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'sbi_collect_no',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('sbi_collect_no'),
                'description' => \Illuminate\Support\Str::title('sbi_collect_no')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'bank_ref_no',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('bank_ref_no'),
                'description' => \Illuminate\Support\Str::title('bank_ref_no')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'admission_payment_ref_no',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('admission_payment_ref_no'),
                'description' => \Illuminate\Support\Str::title('admission_payment_ref_no')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'payment_date',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('payment_date'),
                'description' => \Illuminate\Support\Str::title('payment_date')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'university_enrollment_no',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('university_enrollment_no'),
                'description' => \Illuminate\Support\Str::title('university_enrollment_no')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'admission_date',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('admission_date'),
                'description' => \Illuminate\Support\Str::title('admission_date')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'admission_no',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('admission_no'),
                'description' => \Illuminate\Support\Str::title('admission_no')
            ],

            [
                'form' => 'Student',
                'group' => 'General Info',
                'name' => 'admission_course_fee',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('admission_course_fee'),
                'description' => \Illuminate\Support\Str::title('admission_course_fee')
            ],

            /*Parent Info*/

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'grandfather_first_name',
                'type' => 'text',
                'display_name' => 'NAME OF GRAND FATHER',
                'description' => 'NAME OF GRAND FATHER',
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'grandfather_middle_name',
                'type' => 'text',
                'display_name' => 'NAME OF GRAND FATHER',
                'description' => 'NAME OF GRAND FATHER',
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'grandfather_last_name',
                'type' => 'text',
                'display_name' => 'NAME OF GRAND FATHER',
                'description' => 'NAME OF GRAND FATHER',
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'father_first_name',
                'type' => 'text',
                'display_name' => 'Name of Father',
                'description' => 'Name of Father'
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'father_middle_name',
                'type' => 'text',
                'display_name' => 'Name of Father',
                'description' => 'Name of Father'
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'father_last_name',
                'type' => 'text',
                'display_name' => 'Name of Father',
                'description' => 'Name of Father'
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'father_eligibility',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('father_eligibility'),
                'description' => \Illuminate\Support\Str::title('father_eligibility')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'father_occupation',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('father_occupation'),
                'description' => \Illuminate\Support\Str::title('father_occupation')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'father_office',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('father_office'),
                'description' => \Illuminate\Support\Str::title('father_office')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'father_office_number',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('father_office_number'),
                'description' => \Illuminate\Support\Str::title('father_office_number')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'father_residence_number',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('father_residence_number'),
                'description' => \Illuminate\Support\Str::title('father_residence_number')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'father_mobile_1',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('father_mobile_1'),
                'description' => \Illuminate\Support\Str::title('father_mobile_1')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'father_mobile_2',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('father_mobile_2'),
                'description' => \Illuminate\Support\Str::title('father_mobile_2')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'father_email',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('father_email'),
                'description' => \Illuminate\Support\Str::title('father_email')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'mother_first_name',
                'type' => 'text',
                'display_name' => 'Name of Mother',
                'description' => 'Name of Mother'
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'mother_middle_name',
                'type' => 'text',
                'display_name' => 'Name of Mother',
                'description' => 'Name of Mother'
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'mother_last_name',
                'type' => 'text',
                'display_name' => 'Name of Mother',
                'description' => 'Name of Mother'
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'mother_eligibility',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('mother_eligibility'),
                'description' => \Illuminate\Support\Str::title('mother_eligibility')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'mother_occupation',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('mother_occupation'),
                'description' => \Illuminate\Support\Str::title('mother_occupation')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'mother_office',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('mother_office'),
                'description' => \Illuminate\Support\Str::title('mother_office')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'mother_office_number',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('mother_office_number'),
                'description' => \Illuminate\Support\Str::title('mother_office_number')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'mother_residence_number',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('mother_residence_number'),
                'description' => \Illuminate\Support\Str::title('mother_residence_number')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'mother_mobile_1',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('mother_mobile_1'),
                'description' => \Illuminate\Support\Str::title('mother_mobile_1')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'mother_mobile_2',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('mother_mobile_2'),
                'description' => \Illuminate\Support\Str::title('mother_mobile_2')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'mother_email',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('mother_email'),
                'description' => \Illuminate\Support\Str::title('mother_email')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'father_image',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('father_image'),
                'description' => \Illuminate\Support\Str::title('father_image')
            ],

            [
                'form' => 'Student',
                'group' => 'Parents Info',
                'name' => 'mother_image',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('mother_image'),
                'description' => \Illuminate\Support\Str::title('mother_image')
            ],
            /*Guardian Info*/

            [
                'form' => 'Student',
                'group' => 'Guardian Info',
                'name' => 'guardian_first_name',
                'type' => 'text',
                'display_name' => 'Name of Guardian',
                'description' => 'Name of Guardian'
            ],

            [
                'form' => 'Student',
                'group' => 'Guardian Info',
                'name' => 'guardian_middle_name',
                'type' => 'text',
                'display_name' => 'Name of Guardian',
                'description' => 'Name of Guardian'
            ],

            [
                'form' => 'Student',
                'group' => 'Guardian Info',
                'name' => 'guardian_last_name',
                'type' => 'text',
                'display_name' => 'Name of Guardian',
                'description' => 'Name of Guardian'
            ],

            [
                'form' => 'Student',
                'group' => 'Guardian Info',
                'name' => 'guardian_eligibility',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('guardian_eligibility'),
                'description' => \Illuminate\Support\Str::title('guardian_eligibility')
            ],

            [
                'form' => 'Student',
                'group' => 'Guardian Info',
                'name' => 'guardian_occupation',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('guardian_occupation'),
                'description' => \Illuminate\Support\Str::title('guardian_occupation')
            ],

            [
                'form' => 'Student',
                'group' => 'Guardian Info',
                'name' => 'guardian_office',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('guardian_office'),
                'description' => \Illuminate\Support\Str::title('guardian_office')
            ],

            [
                'form' => 'Student',
                'group' => 'Guardian Info',
                'name' => 'guardian_office_number',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('guardian_office_number'),
                'description' => \Illuminate\Support\Str::title('guardian_office_number')
            ],

            [
                'form' => 'Student',
                'group' => 'Guardian Info',
                'name' => 'guardian_residence_number',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('guardian_residence_number'),
                'description' => \Illuminate\Support\Str::title('guardian_residence_number')
            ],

            [
                'form' => 'Student',
                'group' => 'Guardian Info',
                'name' => 'guardian_mobile_1',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('guardian_mobile_1'),
                'description' => \Illuminate\Support\Str::title('guardian_mobile_1')
            ],

            [
                'form' => 'Student',
                'group' => 'Guardian Info',
                'name' => 'guardian_mobile_2',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('guardian_mobile_2'),
                'description' => \Illuminate\Support\Str::title('guardian_mobile_2')
            ],

            [
                'form' => 'Student',
                'group' => 'Guardian Info',
                'name' => 'guardian_email',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('guardian_email'),
                'description' => \Illuminate\Support\Str::title('guardian_email')
            ],

            [
                'form' => 'Student',
                'group' => 'Guardian Info',
                'name' => 'guardian_relation',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('guardian_relation'),
                'description' => \Illuminate\Support\Str::title('guardian_relation')
            ],

            [
                'form' => 'Student',
                'group' => 'Guardian Info',
                'name' => 'guardian_address',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('guardian_address'),
                'description' => \Illuminate\Support\Str::title('guardian_address')
            ],

            [
                'form' => 'Student',
                'group' => 'Address And Contact Info',
                'name' => 'address',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('address'),
                'description' => \Illuminate\Support\Str::title('address')
            ],

            [
                'form' => 'Student',
                'group' => 'Address And Contact Info',
                'name' => 'state',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('state'),
                'description' => \Illuminate\Support\Str::title('state')
            ],

            [
                'form' => 'Student',
                'group' => 'Address And Contact Info',
                'name' => 'country',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('country'),
                'description' => \Illuminate\Support\Str::title('country')
            ],

            [
                'form' => 'Student',
                'group' => 'Address And Contact Info',
                'name' => 'postal_code',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('postal_code'),
                'description' => \Illuminate\Support\Str::title('postal_code')
            ],

            [
                'form' => 'Student',
                'group' => 'Address And Contact Info',
                'name' => 'temp_address',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('temp_address'),
                'description' => \Illuminate\Support\Str::title('temp_address')
            ],

            [
                'form' => 'Student',
                'group' => 'Address And Contact Info',
                'name' => 'temp_state',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('temp_state'),
                'description' => \Illuminate\Support\Str::title('temp_state')
            ],

            [
                'form' => 'Student',
                'group' => 'Address And Contact Info',
                'name' => 'temp_country',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('temp_country'),
                'description' => \Illuminate\Support\Str::title('temp_country')
            ],

            [
                'form' => 'Student',
                'group' => 'Address And Contact Info',
                'name' => 'temp_postal_code',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('temp_postal_code'),
                'description' => \Illuminate\Support\Str::title('temp_postal_code')
            ],

            [
                'form' => 'Student',
                'group' => 'Address And Contact Info',
                'name' => 'home_phone',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('home_phone'),
                'description' => \Illuminate\Support\Str::title('home_phone')
            ],

            [
                'form' => 'Student',
                'group' => 'Address And Contact Info',
                'name' => 'mobile_1',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('mobile_1'),
                'description' => \Illuminate\Support\Str::title('mobile_1')
            ],

            [
                'form' => 'Student',
                'group' => 'Address And Contact Info',
                'name' => 'mobile_2',
                'type' => 'text',
                'display_name' => \Illuminate\Support\Str::title('mobile_2'),
                'description' => \Illuminate\Support\Str::title('mobile_2')
            ]

        ];


        foreach ($formBuilder as $key => $value) {
            \App\Models\FormSetting::create($value);
        }
    }
}