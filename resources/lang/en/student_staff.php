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
        'name'      =>  __('module.student_staff'),
        'child'     =>    [
                            'student'   => [
                                'name'      =>  'Student',
                                'child'     =>    [
                                                    'transfer'    =>      'Transfer',
                                                ],
                            ],
                            'guardian' => [
                                'name'      =>  'Guardian',
                                'child'     =>    [
                                ],
                            ],
                            'staff' =>      [
                                'name'      =>  'Staff',
                                'child'     =>    [
                                                        'designation'           =>      'Designation',
                                                    ],
                            ],

                        ],
        'common'    =>    [
            'detail'                        =>      'Detail',
            'registration'                  =>      'Registration',
            'bulk_import'                   =>      'Bulk Import',
            'document_upload'               =>      'Document',
            'notes'                         =>      'Notes',
            'complete_records'              =>      'Complete Record',
        ],
    ];