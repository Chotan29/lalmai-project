<?php
return [
    'student'   =>
        [
            'registration' => [
                                'tabs' =>[
                                    'general_info'      =>  [
                                                                'status'            => 1,
                                                                'general_info'      => 1,
                                                                'subject_info'      => 0,
                                                                'contact_info'      => 1,
                                                                'address'           => 1,
                                                                'temp_address'      => 1,
                                                                'parent_info'       => 1,
                                                                'grand_father'      => 1,
                                                                'father'            => 1,
                                                                'mother'            => 1,
                                                                'guardian'          => 1,
                                                            ],
                                    'academic_info'     =>  1,
                                    'profile_image'     =>  1,
                                    'annexure'          =>  1,
                                    'extra_info'        =>  1,
                                ],
                            ],
            'online_registration' => [
                                'tabs' =>[
                                    'general_info'      =>  [
                                        'status'            => 1,
                                        'general_info'      => 1,
                                        'subject_info'      => 1,
                                        'contact_info'      => 0,
                                        'address'           => 1,
                                        'temp_address'      => 0,
                                        'parent_info'       => 1,
                                        'grand_father'      => 0,
                                        'father'            => 1,
                                        'mother'            => 1,
                                        'guardian'          => 0,
                                    ],
                                    'academic_info'     =>  1,
                                    'profile_image'     =>  1,
                                    'annexure'          =>  1,
                                    'extra_info'        =>  1,
                                ],
                            ],

        ]
];
