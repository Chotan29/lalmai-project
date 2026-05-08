<?php

use App\Models\ExamSchedule;
use App\Models\StudentAnnexure;
use App\Models\StudentDegree;
use App\Models\StudentExtraInfo;
use App\Models\StudentScholarship;
use App\Models\StudentPlacement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Facades;

class UpdateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //run specific Update Table Seeder Only
        //php artisan db:seed --class=UpdateTableSeeder --force
         $students = \App\Models\Student::/*where('id',429)->*/get();
       // dd($students->toArray());
        if($students && $students->count() > 0){
            foreach ($students as $student){
               /*
                 //address Info
                $addressInfo = Addressinfo::where('students_id', $student->id)->first();
                if(!isset($addressInfo)){
                    Addressinfo::create([
                        'students_id' => $student->id,
                        'created_by' => 1,
                    ]);
                }

                //parents
                $parentsDetail = ParentDetail::where('students_id', $student->id)->first();
                if(!isset($parentsDetail)){
                    ParentDetail::create([
                        'students_id' => $student->id,
                        'created_by' => 1,
                    ]);
                }

                //guardian
                $guardianDetail = StudentGuardian::where('students_id', $student->id)->first();
                if(!isset($guardianDetail)){
                    ParentDetail::create([
                        'students_id' => $student->id,
                        'created_by' => 1,
                    ]);
                   $guardian = GuardianDetail::create([
                        'guardian_first_name' => 'no',
                        'created_by' => 1,
                    ]);
                    //student Guardian
                    StudentGuardian::create([
                        'students_id' => $student->id,
                        'guardians_id' => $guardian->id,//others
                    ]);
                }


                // Annexure List
                $studentAnnexure = StudentAnnexure::where('students_id', $student->id)->first();
                if(!isset($studentAnnexure)){
                    StudentAnnexure::create([
                        'students_id' => $student->id,
                        'annexures_id' => 14,//others
                        'created_by' => 1,
                    ]);
                }


                //extra info
                //'students_id', 'total_fee_per_year', 'bank_name', 'bank_code', 'bank_account_number', 'admission_portal_login_id', 'admission_portal_login_password', 'status'
                $studentExtraInfo = StudentExtraInfo::where('students_id', $student->id)->first();
                if(!isset($studentExtraInfo)){
                    StudentExtraInfo::create([
                        'students_id' => $student->id,
                        'created_by' => 1,
                    ]);
                }

                //scholarship
                //'students_id', 'scholarships_id', 'scholarship_application_id', 'scholarship_user_name', 'scholarship_password', 'status'
                $studentScholarship = StudentScholarship::where('students_id', $student->id)->first();
                if(!isset($studentScholarship)){
                    StudentScholarship::create([
                        'students_id' => $student->id,
                        'scholarships_id' => 1,
                        'created_by' => 1,
                    ]);
                }


                //placement
                //'students_id', 'placements_id', 'placement_date', 'placement_salary', 'placement_location', 'placement_designation', 'status'
                $studentPlacement = StudentPlacement::where('students_id', $student->id)->first();
                if(!isset($studentPlacement)){
                    StudentPlacement::create([
                        'students_id' => $student->id,
                        'placements_id' => 1,
                        'created_by' => 1,
                    ]);
                }

                * */


                //degrees
                $studentDegree = StudentDegree::where('students_id', $student->id)->first();
                if(!isset($studentDegree)){
                    $allDegrees = \App\Models\Degree::where('status',1)->get();
                    if($allDegrees && $allDegrees->count()>0){
                        foreach ($allDegrees as $degree){
                            StudentDegree::create([
                                'students_id'               =>  $student->id,
                                'degrees_id'                 =>  $degree->id,
                                'created_by'                =>  1,
                            ]);
                        }
                    }
                }

            };
        }

//        Schema::disableForeignKeyConstraints();
//        \App\Models\CertificateTemplate::truncate();
//        $this->call(CertificateTemplateSeeder::class);
//        Schema::enableForeignKeyConstraints();

        Schema::table('academic_infos', function (Blueprint $table) {
//            $table->string('grade_point')->nullable();
//            $table->string('grade_letter')->nullable();
//            $table->double('mark_obtained', 10,2)->nullable();
//            $table->string('roll_no', '15')->nullable();
//            $table->double('maximum_mark', 10,2)->nullable();
//            $table->string('division_grade', '10')->nullable();
        });

        Schema::table('faculties', function (Blueprint $table) {
           // $table->string('credit_required')->nullable();
            //$table->string('scale')->nullable();
//            $table->smallInteger('sorting')->default(0);
//            $table->unsignedInteger('gradingType_id')->nullable();
//            $table->string('duration')->nullable();
//            $table->string('registration_validate')->nullable();

        });

        Schema::table('transcript_certificates', function (Blueprint $table) {
            //$table->string('gpa');
            //$table->string('verification_code');
//            $table->string('mark_sheet_sn');
//            $table->string('provisional_certificate_num');
        });




//        \App\Models\SmsSetting::truncate();
//        $this->call(SmsTableSeeder::class);


        Schema::table('students', function (Blueprint $table) {
//
//                    $table->integer('reg_fee')->nullable();
//                    $table->string('sbi_collect_no', '50')->nullable();
//                    $table->string('bank_ref_no', '50')->nullable();
//                    $table->string('admission_payment_ref_no', '50')->nullable();
//                    $table->dateTime('payment_date')->nullable();
//                    $table->string('university_enrollment_no', '100')->nullable();

                    $table->string('home_phone', '15')->nullable();
                    $table->string('mobile_1', '15')->nullable();
                    $table->string('mobile_2', '15')->nullable();

//                    $table->dateTime('admission_date')->nullable();
//                    $table->string('admission_no', '25')->nullable();
//                    $table->integer('admission_course_fee')->nullable();

//                    $table->string('special_category', '50')->nullable();
//                    $table->string('weightage_claim', '25')->nullable();
        });

        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->boolean('print_institute_head')->default(1)->after('student_photo');
        });
        Schema::table('provisional_certificates', function (Blueprint $table) {
            $table->date('result_publish_date');
        });
        Schema::table('students', function (Blueprint $table) {
//                    $table->string('national_id_1', '25')->nullable();
//                    $table->string('national_id_2', '25')->nullable();
//                    $table->string('national_id_3', '25')->nullable();
//                    $table->string('national_id_4', '25')->nullable();
//
//                    $table->integer('reg_fee')->nullable();
//                    $table->string('sbi_collect_no', '50')->nullable();
//                    $table->string('bank_ref_no', '50')->nullable();
//
//                    $table->string('admission_payment_ref_no', '50')->nullable();
//                    $table->dateTime('payment_date')->nullable();
//
//                    $table->string('university_enrollment_no', '100')->nullable();
//
//                    $table->dateTime('admission_date')->nullable();
//                    $table->string('admission_no', '25')->nullable();
//                    $table->integer('admission_course_fee')->nullable();
//
//                    $table->string('special_category', '50')->nullable();
//                    $table->string('weightage_claim', '25')->nullable();
        });
        //run specific Update Table Seeder Only
        //php artisan db:seed --class=UpdateTableSeeder --force
        //php artisan db:seed --class=UpdateTableSeeder --force
        Schema::table('academic_infos', function (Blueprint $table) {
            $table->string('grade_point')->nullable();
            $table->string('grade_letter')->nullable();
            $table->double('mark_obtained', 10,2)->nullable();
            $table->string('roll_no', '15')->nullable();
            $table->double('maximum_mark', 10,2)->nullable();
            //$table->string('division_grade', '10')->nullable();
        });

        //$this->call(FormSettingTableSeeder::class);


        Schema::table('general_settings', function (Blueprint $table) {
                   $table->boolean('nav_layout')->default(0);
                });


        Schema::table('alert_settings', function (Blueprint $table) {
            //$table->text('email_template')->nullable();
        });
//                \App\Models\AlertSetting::truncate();
//                $this->call(AlertSettingSeeder::class);



    //1. Migrate the new

        //php artisan migrate
            \Illuminate\Support\Facades\Artisan::call('migrate');
            //\Illuminate\Support\Facades\Artisan::call('migrate:fresh --seed');

        //2. Seeders Call
            //$this->call(AnnexureSeeder::class);
            //$this->call(DegreesSeeder::class);
            //$this->call(AcademicInfoLevelSeeder::class);
            //$this->call(ApplicationTypeSeeder::class);
            //$this->call(StateSeeder::class);
            //$this->call(ScholarshipSeeder::class);
            //$this->call(PlacementSeeder::class);


        //3. modify tables with data
                //General Setting Table

                Schema::table('general_settings', function (Blueprint $table) {
                   // $table->boolean('application')->default(1);
                });

                //faculties
                Schema::table('faculties', function (Blueprint $table) {
                    //$table->smallInteger('sorting')->default(0);
                    //$table->unsignedInteger('gradingType_id')->nullable();
                    //$table->string('duration')->nullable();
                   // $table->string('registration_validate')->nullable();

                });

                //semester
                Schema::table('semesters', function (Blueprint $table) {
                   //$table->decimal('semester_fee',13,2)->default(0);
                   //$table->integer('major_subject_count')->nullable();
                });

                //student batch
                Schema::table('student_batches', function (Blueprint $table) {
                    //$table->boolean('active_status')->default(0);
                });

                //subject--
                Schema::table('subjects', function (Blueprint $table) {
//                    $table->decimal('course_fee',8,2)->nullable();
//                    $table->decimal('full_mark_theory',8,2)->charset(null)->change();
//                    $table->decimal('pass_mark_theory',8,2)->charset(null)->change();
//                    $table->decimal('full_mark_practical',8,2)->charset(null)->change();
//                    $table->decimal('pass_mark_practical',8,2)->charset(null)->change();
//                    $table->decimal('credit_hour',8,2)->charset(null)->change();
                });

                //student
                Schema::table('students', function (Blueprint $table) {
//                    $table->string('national_id_1', '25')->nullable();
//                    $table->string('national_id_2', '25')->nullable();
//                    $table->string('national_id_3', '25')->nullable();
//                    $table->string('national_id_4', '25')->nullable();
//
//                    $table->integer('reg_fee')->nullable();
//                    $table->string('sbi_collect_no', '50')->nullable();
//                    $table->string('bank_ref_no', '50')->nullable();
//
//                    $table->string('admission_payment_ref_no', '50')->nullable();
//                    $table->dateTime('payment_date')->nullable();
//
//                    $table->string('university_enrollment_no', '100')->nullable();
//
//                    $table->dateTime('admission_date')->nullable();
//                    $table->string('admission_no', '25')->nullable();
//                    $table->integer('admission_course_fee')->nullable();
//
//                    $table->string('special_category', '50')->nullable();
//                    $table->string('weightage_claim', '25')->nullable();
                });


                //get students record
/*
               $students = \App\Models\Student::get();
                if($students && $students->count() > 0){
                    foreach ($students as $student){
                        // Annexure List
                        StudentAnnexure::create([
                            'students_id' => $student->id,
                            'annexures_id' => 14,//others
                            'created_by' => 1,
                        ]);

                        //extra info
                        //'students_id', 'total_fee_per_year', 'bank_name', 'bank_code', 'bank_account_number', 'admission_portal_login_id', 'admission_portal_login_password', 'status'
                        StudentExtraInfo::create([
                            'students_id' => $student->id,
                            'created_by' => 1,
                        ]);

                        //scholarship
                        //'students_id', 'scholarships_id', 'scholarship_application_id', 'scholarship_user_name', 'scholarship_password', 'status'
                        StudentScholarship::create([
                            'students_id' => $student->id,
                            'scholarships_id' => 1,
                            'created_by' => 1,
                        ]);

                        //placement
                        //'students_id', 'placements_id', 'placement_date', 'placement_salary', 'placement_location', 'placement_designation', 'status'
                        StudentPlacement::create([
                            'students_id' => $student->id,
                            'placements_id' => 1,
                            'created_by' => 1,
                        ]);

                        //degrees
                        $allDegrees = \App\Models\Degree::where('status',1)->get();
                        if($allDegrees && $allDegrees->count()>0){
                            foreach ($allDegrees as $degree){
                                StudentDegree::create([
                                    'students_id'               =>  $student->id,
                                    'degrees_id'                 =>  $degree->id,
                                    'created_by'                =>  1,
                                ]);
                            }
                        }
                    };
                }*/

        //AddressInfo
                /*Schema::table('addressinfos', function (Blueprint $table) {
                    $table->string('postal_code', '25')->nullable();
                    $table->string('temp_postal_code', '25')->nullable();
                });*/

        //Staff
               /* Schema::table('staff', function (Blueprint $table) {
                    $table->string('national_id_1', '25')->nullable();
                    $table->string('national_id_2', '25')->nullable();
                    $table->string('national_id_3', '25')->nullable();
                    $table->string('national_id_4', '25')->nullable();


                    $table->string('postal_code', '25')->nullable();
                    $table->string('temp_postal_code', '25')->nullable();

                    $table->integer('basic_salary')->nullable();
                    $table->dateTime('date_of_relieving')->nullable();
                    $table->dateTime('date_of_rejoin')->nullable();

                    $table->text('bank_name')->nullable();
                    $table->text('bank_account_number')->nullable();
                    $table->text('bank_code')->nullable();

                });*/

        //Transfer Certificate Colum Audit
        /*    Schema::table('transfer_certificates', function (Blueprint $table) {
                $table->date('application_date');
                $table->text('join_time_class')->nullable();
                $table->string('dob_certificate')->nullable();
                $table->string('birth_place')->nullable();
                $table->string('school_category')->nullable();
                $table->string('fee_concession_detail')->nullable();
                $table->string('last_taken_exam_with_result')->nullable();
                $table->string('exam_fail_status')->nullable();
                $table->string('promoted_class')->nullable();
                $table->text('subject_studies')->nullable();
                $table->string('cadet_detail')->nullable();
                $table->string('reason_to_leave')->nullable();
                $table->integer('total_attendance')->nullable();
                $table->integer('school_college_open_total')->nullable();
                $table->text('extra_activity_detail')->nullable();
                $table->text('any_other_remark')->nullable();
            });


            Schema::table('certificate_templates', function (Blueprint $table) {
                $table->text('custom_css')->nullable();
                $table->boolean('background_status')->default(0);
                $table->text('background_image')->nullable();
            });

        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->boolean('print_institute_head')->default(1)->after('student_photo');
        });

        Schema::table('provisional_certificates', function (Blueprint $table) {
           $table->date('result_publish_date');
        });*/

        Schema::disableForeignKeyConstraints();

            \App\Models\Permission::truncate();
            $this->call(PermissionTableSeeder::class);

            \App\Models\CertificateTemplate::truncate();
            $this->call(CertificateTemplateSeeder::class);

        Schema::enableForeignKeyConstraints();




        //Exam
        Schema::table('exam_schedules', function (Blueprint $table) {
            //$table->dateTime('publish_date')->nullable();
        });

        //online registration table - err
        Schema::table('online_registration_settings', function (Blueprint $table) {
            //$table->string('base', 50)->nullable();
        });

        \App\Models\OnlineRegistrationProgram::truncate();
        Schema::table('online_registration_programs', function (Blueprint $table) {
            //$table->unsignedInteger('semesters_id')->unique();
        });

    }

}


