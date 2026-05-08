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
    'name'      =>  __('module.academic'),
    'child'     =>    [
                        'academic_level' => [
                            'name'      =>  'Academic Level',
                            'child'     =>    [
                                'department_head'  =>  'Department Head',
                                'department'        =>  'Department',
                                'faculty'            =>  'Program/Class',
                                'semester'          =>  'Semester/Section',
                                'batch'             =>  'Batch',
                                'routine'             =>  'Class Routine',
                            ],
                        ],
                        'grading_subject' => [
                            'name'      =>  'Grading & Subject',
                            'child'     =>    [
                                'subject'    =>  'Course / Subject',
                                'grading'    =>  'Grading System',
                            ],
                        ],
                        'year_month_day' => [
                            'name'      =>  'Year & Month',
                            'child'     =>    [
                                'year'    =>  'Year',
                                'month'    =>  'Month',
                                'day'    =>  'Day',
                            ],
                        ],
                        'status_setting' => [
                            'name'      =>  'Status Setting',
                            'child'     =>    [
                                'student_status'            =>  'Student Status',
                                'attendance_status'         =>  'Attendance Status',
                                'book_status'               =>  'Books Status',
                                'hostel_bed_status'         =>  'Hostel Bed Status',
                                'customer_status'           =>  'Customer Status',
                            ],
                        ],
                        'dynamic_gallery' => [
                            'name'      =>  'Dynamic Gallery',
                            'child'     =>    [
                                'placement'                 =>  'Placement',
                                'scholarship'               =>  'Scholarship',
                                'annexure'                  =>  'Attach Document',
                                'academic_info_level'       =>  'Academic Info',
                                'degree'                    =>  'Degree',
                                'state'                     =>  'State',
                                'application_type'          =>  'Application Type',
                            ],

                        ],
                    ],
    ];