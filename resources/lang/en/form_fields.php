<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pagination Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the paginator library to build
    | the simple pagination links. You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */
    'student'   =>
                [
                    'tabs' =>   [
                        'general_info'      =>  'General Info',
                        'academic_info'     =>  'Academic Info',
                        'profile_image'     =>  'Profile Image',
                        'annexure'          =>  'Attach Document',
                        'extra_info'        =>  'Extra Info',
                    ],
                    'section_label' =>[
                        'general_info'      =>  'General Info',
                        'enroll_info'      =>  'Course Info',
                        'student_info'      =>  'Student Info',
                        'contact_info'      =>    'Contact Info',
                        'address'           =>    'Permanent Address',
                        'temp_address'      =>    'Temporary Address',
                        'parent_info'       =>  'Parental Info',
                        'grand_father'      =>  'Grand Father\'s Detail',
                        'father'            =>  'Father\'s Detail',
                        'mother'            =>  'Mothers\'s Detail',
                        'guardian'            =>  'Guardian\'s Detail',
                    ],
                    'fields' =>
                        [
                                'reg_no'                        =>  'Roll No.',
                            'reg_date'                      =>  'Reg. Date',
                            'university_reg'                =>  'Unique Student Id',//UNIVERSITY REG.
                            'special_category'              =>  'Special Category',
                            'weightage_claim'               =>  'Weightage Claim',
                            'faculty'                       =>  __('academic.child.academic_level.child.faculty'),
                            'semester'                      =>  __('academic.child.academic_level.child.semester'),
                            'academic_status'               =>  'Academic Status',
                            'batch'                         =>  __('academic.child.academic_level.child.batch'),
                            'name_of_student'               =>  'Name of Student',
                            'first_name'                    =>  'First Name',
                            'middle_name'                   =>  'Middle Name',
                            'last_name'                     =>  'Last Name',
                            'date_of_birth'                 =>  'Date of Birth',
                            'gender'                        =>  'Gender',
                            'blood_group'                   =>  'Blood Group',
                            'religion'                      =>  'Religion',//Religion
                            'religion_default'              =>  '',//Default PlaceHolder
                            'caste'                         =>  'Caste',
                            'nationality'                   =>  'Nationality',
                            'national_id_1'                 =>  'ID. 1',//ID. 1
                            'national_id_2'                 =>  'ID. 2',//ID. 2
                            'national_id_3'                 =>  'ID. 3',//ID. 3
                            'national_id_4'                 =>  'ID. 4',//ID. 4
                            'mother_tongue'                 =>  'Mother Tongue',
                            'email'                         =>  'E-mail',
                            'extra_info'                    =>  'Extra Info',
                            'student_image'                 =>  'Student Profile Picture',
                            'student_signature'             =>  'Student Signature',
                            'reg_fee'                       =>  'Registration Fee',
                            'sbi_collect_no'                =>  'Bank Collect No',
                            'bank_ref_no'                   =>  'Bank Ref. No.',
                            'admission_payment_ref_no'      =>  'Admission Payment Ref No.',
                            'payment_date'                  =>  'Payment Date',
                            'university_enrollment_no'      =>  'University Enrollment No.',
                            'admission_date'                =>  'Admission Date',
                            'admission_no'                  =>  'Admission No.',
                            'admission_course_fee'          =>  'Admission Course Fee',

                            'home_phone'                    =>  'Home Phone',
                            'mobile_1'                      =>  'Mobile 1',
                            'mobile_2'                      =>  'Mobile 2',
                            'address'                       =>  'Address',
                                'state'                         =>  'Division',
                            'country'                       =>  'Country',
                            'postal_code'                   =>  'Postal Code',

                            'grandfather_name'              =>  'Grand Father Name',
                            'grandfather_first_name'        =>  'Grand Father First Name',
                            'grandfather_middle_name'       =>  'Grand Father Middle Name',
                            'grandfather_last_name'         =>  'Grand Father Last Name',
                                'father_name'                   =>  'Father Name',
                            'father_first_name'             =>  'Father First Name',
                            'father_middle_name'            =>  'Father Middle Name',
                            'father_last_name'              =>  'Father Last Name',
                                'mother_name'                   =>  'Mother',
                            'mother_first_name'             =>  'Mother First Name',
                            'mother_middle_name'            =>  'Mother Middle Name',
                            'mother_last_name'              =>  'Mother Last Name',
                            'eligibility'                   =>  'Eligibility',
                            'occupation'                    =>  'Occupation',
                            'office'                        =>  'Office',
                            'office_number'                 =>  'Office Number',
                            'residence_number'              =>  'Residence Number',
                            'father_image'                  =>  'Father Image',
                            'mother_image'                  =>  'Mother Image',

                            'guardian_name'                 =>  'Name of Guardian',
                            'guardian_first_name'           =>  'Guardian First Name',
                            'guardian_middle_name'          =>  'Guardian Middle Name',
                            'guardian_last_name'            =>  'Guardian Last Name',
                            'relation'                      =>  'Relation',
                            'guardian_image'                =>  'Guardian Image',



                        ]
                ],
        'staff'   =>
                [
                    'tabs' =>[
                        'general_info'      =>  'General Info',
                        'profile_image'     =>  'Profile Image',
                        'extra_info'        =>  'Extra Info',
                    ],
                    'fields' =>
                        [
                            'designation'                   =>  'Designation',


                        ]
                ],



    ];

