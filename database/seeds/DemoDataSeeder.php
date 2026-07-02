<?php

use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //php artisan db:seed --class=DemoDataSeeder --force
        $studentDemoData = 100000;
        $staffDemoData = 10000;
        $booksDemoData = 5000;

        //faker data for testing
        $faker = \Faker\Factory::create();

        //faculty
        //'created_by', 'last_updated_by', 'faculty', 'faculty_code', 'sorting', 'status'
            $faculty = new \App\Models\Faculty();
            $faculty->created_by = 1;
            $faculty->faculty = 'BACHELOR OF BUSINESS ADMINISTRATION'.rand(10,100000).$faker->randomDigit;;
            $faculty->faculty_code = rand(10,100000).$faker->randomDigit;
            $faculty->sorting = 1;
            $faculty->status = 1;
            $faculty->save();
        //semester
        //'created_by', 'last_updated_by', 'semester', 'slug', 'semester_fee', 'staff_id', 'gradingType_id', 'major_subject_count', 'status'
            $semester = new \App\Models\Semester();
            $semester->created_by = 1;
            $semester->semester = 'BBA-I-SEM'.rand(10,100000).$faker->randomDigit;;
            $semester->slug ='BBA-I-SEM'.rand(10,100000).$faker->randomDigit;;
            $semester->status = 1;
            $semester->save();

        //facultySemester
        //'faculty_id','semester_id'
            $facultySemester = new \App\Models\FacultySemester();
            $facultySemester->faculty_id = 1;
            $facultySemester->semester_id = 1;
            $facultySemester->save();

        //batch
            $batch = new \App\Models\StudentBatch();
            $batch->created_by = 1;
            $batch->title = '2023-26'.rand(10,100000).$faker->randomDigit;;
            $batch->status = 1;
            $batch->save();


        //student
            ////'created_by', 'last_updated_by', 'reg_no', 'reg_date', 'university_reg','faculty','semester','batch',
            //        'academic_status', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender', 'blood_group',
            //        'nationality', 'national_id_1', 'national_id_2', 'national_id_3','national_id_4',
            //        'religion', 'caste','mother_tongue', 'email', 'extra_info', 'student_image','student_signature','status'
        for($i=1;$i<=$studentDemoData;$i++){
            $student = new \App\Models\Student();
            $student->created_by = 1;
            $student->reg_no = rand(10,10000000).$faker->randomDigit.rand(10,10000000);
            $student->reg_date = $faker->date();
            $student->faculty = 1;
            $student->semester = 1;
            $student->batch = 1;
            $student->first_name = $faker->name;
            $student->date_of_birth = $faker->date();
            $student->email = rand(10,10000000).$faker->email;
            $student->academic_status = 1;
            $student->status = 1;
            $student->save();


            //Parents
            //'created_by', 'last_updated_by', 'students_id', 'grandfather_first_name', 'grandfather_middle_name',
            //        'grandfather_last_name', 'father_first_name', 'father_middle_name', 'father_last_name', 'father_eligibility',
            //        'father_occupation', 'father_office', 'father_office_number', 'father_residence_number', 'father_mobile_1',
            //        'father_mobile_2', 'father_email', 'mother_first_name', 'mother_middle_name', 'mother_last_name',
            //        'mother_eligibility', 'mother_occupation', 'mother_office', 'mother_office_number', 'mother_residence_number',
            //        'mother_mobile_1', 'mother_mobile_2', 'mother_email', 'father_image','mother_image','status'

            $parentsDetail = new \App\Models\ParentDetail();
            $parentsDetail->created_by = 1;
            $parentsDetail->students_id = $student->id;
            $parentsDetail->father_first_name = $faker->name;
            $parentsDetail->mother_first_name = $faker->name;
            $parentsDetail->status = 1;
            $parentsDetail->save();

            //Guardian
            //'created_by', 'last_updated_by', 'guardian_first_name', 'guardian_middle_name', 'guardian_last_name',
            //        'guardian_eligibility', 'guardian_occupation', 'guardian_office', 'guardian_office_number', 'guardian_residence_number',
            //        'guardian_mobile_1', 'guardian_mobile_2', 'guardian_email', 'guardian_relation', 'guardian_address','guardian_image',  'status'
            $guardianDetail = new \App\Models\GuardianDetail();
            $guardianDetail->created_by = 1;
            $guardianDetail->guardian_first_name = $faker->name;
            $guardianDetail->guardian_email = rand(10,10000000).$faker->email;
            $guardianDetail->guardian_relation = 'Father';
            $guardianDetail->status = 1;
            $guardianDetail->save();

            // Student & Guardian
            //'students_id', 'guardians_id'
            $studentGuardian = new \App\Models\StudentGuardian();
            $studentGuardian->students_id = $student->id;
            $studentGuardian->guardians_id = $guardianDetail->id;
            $studentGuardian->save();


            //Address
            //state
            $state = new \App\Models\State();
            $state->created_by = 1;
            $state->title = $faker->state.'-'.$student->id;
            $state->status = 1;
            $state->save();
            //'created_by', 'last_updated_by', 'students_id', 'address', 'state', 'country','postal_code', 'temp_address',
            //        'temp_state', 'temp_country', 'temp_postal_code', 'home_phone', 'mobile_1', 'mobile_2', 'status'
            $batch = new \App\Models\Addressinfo();
            $batch->created_by = 1;
            $batch->students_id = $student->id;
            $batch->address = $faker->address;
            $batch->state = $state->title;
            $batch->country = 'Nepal';
            $batch->postal_code = $faker->postcode;
            $batch->temp_address = $faker->address;
            $batch->temp_state = $state->title;
            $batch->temp_country = 'Nepal';
            $batch->temp_postal_code = $faker->postcode;
            $batch->home_phone = '';
            $batch->mobile_1 = '9868156047';
            $batch->mobile_2 = '9868156047';
            $batch->status = 1;
            $batch->save();

            //extra info
            //'students_id', 'total_fee_per_year', 'bank_name', 'bank_code', 'bank_account_number',
            // 'admission_portal_login_id', 'admission_portal_login_password', 'status'
            $studentExtraInfo = new \App\Models\StudentExtraInfo();
            $studentExtraInfo->created_by = 1;
            $studentExtraInfo->students_id = $student->id;
            $studentExtraInfo->total_fee_per_year = '8000';
            $studentExtraInfo->bank_name ='Bank Name Ltd.';
            $studentExtraInfo->bank_code = $faker->randomDigit;
            $studentExtraInfo->bank_account_number = $faker->bankAccountNumber;
            $studentExtraInfo->status = 1;
            $studentExtraInfo->save();

            //student degrees
            $studentScholarship = new \App\Models\StudentDegree();
            $studentScholarship->created_by = 1;
            $studentScholarship->students_id = $student->id;
            $studentScholarship->degrees_id = 1;
            $studentScholarship->status = 1;
            $studentScholarship->save();

            //student_scholarships
            //'students_id', 'scholarships_id', 'scholarship_application_id', 'scholarship_user_name',
            // 'scholarship_password', 'status'
            $studentScholarship = new \App\Models\StudentScholarship();
            $studentScholarship->created_by = 1;
            $studentScholarship->students_id = $student->id;
            $studentScholarship->scholarships_id = 1;
            $studentScholarship->status = 1;
            $studentScholarship->save();

            //student_placements
            //'students_id', 'placements_id', 'placement_date', 'placement_salary',
            // 'placement_location', 'placement_designation', 'status'
            $studentPlacement = new \App\Models\StudentPlacement();
            $studentPlacement->created_by = 1;
            $studentPlacement->students_id = $student->id;
            $studentPlacement->placements_id = 1;
            $studentPlacement->status = 1;
            $studentPlacement->save();




            //Student User
            //'name', 'email', 'password','last_login_at','last_login_ip', 'profile_image', 'contact_number', 'address',  'role_id', 'hook_id', 'status'
                $user = new \App\User();
                $user->name = $student->first_name;
                $user->email = $student->email;
                $user->password = bcrypt(123);
                $user->role_id = 6;
                $user->hook_id = $student->id;
                $user->save();

                //Student User Role
                    DB::table('role_user')->insert([
                        'role_id' => 6,
                        'user_id' => $user->id,
                    ]);


        }


        //staff
            //Staff Designation
            //'created_by', 'last_updated_by', 'title', 'status'
            $staffDesignation = new \App\Models\StaffDesignation();
            $staffDesignation->created_by = 1;
            $staffDesignation->title = $faker->jobTitle;
            $staffDesignation->status = 1;
            $staffDesignation->save();

        for($i=1;$i<=$staffDemoData;$i++){
            //        'created_by', 'last_updated_by', 'reg_no', 'join_date', 'designation', 'first_name',  'middle_name', 'last_name',
            //        'father_name', 'mother_name', 'date_of_birth', 'gender', 'blood_group', 'nationality','mother_tongue',
            //        'national_id_1', 'national_id_2', 'national_id_3', 'national_id_4',
            //        'home_phone', 'mobile_1', 'mobile_2', 'email',
            //        'address', 'state', 'country','postal_code',
            //        'temp_address', 'temp_state', 'temp_country','temp_postal_code',
            //        'qualification',  'basic_salary', 'date_of_relieving', 'date_of_rejoin', 'bank_name', 'bank_account_number', 'bank_code',
            //        'experience', 'experience_info', 'other_info', 'staff_image', 'status'

            $staff = new \App\Models\Staff();
            $staff->created_by = 1;
            $staff->reg_no = rand(10,10000000).$faker->randomDigit.rand(10,10000000);
            $staff->join_date = $faker->date();
            $staff->designation = $staffDesignation->id;
            $staff->first_name = $faker->name;
            $staff->date_of_birth = $faker->date();
            $staff->email = rand(10,10000000).$faker->email;
            $staff->status = 1;
            $staff->save();



            //Staff User
            //'name', 'email', 'password','last_login_at','last_login_ip', 'profile_image', 'contact_number', 'address',  'role_id', 'hook_id', 'status'
            $user = new \App\User();
            $user->name = $staff->first_name;
            $user->email = $staff->email;
            $user->password = bcrypt(123);
            $user->role_id = 5;
            $user->hook_id = $staff->id;
            $user->save();

            //Student User Role
            DB::table('role_user')->insert([
                'role_id' => 5,
                'user_id' => $user->id,
            ]);

        }

        //Book
        for($i=1;$i<=$booksDemoData;$i++){
            //Book Categories
                //'created_by', 'last_updated_by', 'title', 'slug', 'status'
                $bookCategories = new \App\Models\BookCategory();
                $bookCategories->created_by = 1;
                $bookCategories->title = $faker->colorName;
                $bookCategories->slug = $faker->slug;
                $bookCategories->status = 1;
                $bookCategories->save();

            //Book Master
                //'created_by', 'last_updated_by', 'isbn_number', 'code', 'title', 'sub_title', 'image',
                //        'language', 'editor', 'categories', 'edition', 'edition_year', 'publisher', 'publish_year', 'series', 'author',
                //        'rack_location', 'price', 'total_pages', 'source', 'notes', 'status'
            $bookMaster = new \App\Models\BookMaster();
            $bookMaster->created_by = 1;
            $bookMaster->categories = $bookCategories->id;
            $bookMaster->code = rand(10,10000000).$faker->randomDigit.rand(10,10000000);
            $bookMaster->title = $faker->name;
            $bookMaster->author = $faker->name;
            $bookMaster->publish_year = $faker->date('Y');
            $bookMaster->status = 1;
            $bookMaster->save();

            //'created_by', 'last_updated_by', 'book_masters_id', 'book_code', 'book_status'
            for($j=1;$j<=2;$j++) {
                $book = new \App\Models\Book();
                $book->created_by = 1;
                $book->book_masters_id = $bookMaster->id;
                $book->book_code = rand(10, 10000000) . $faker->randomDigit . rand(10, 10000000);
                $book->book_status = 1;
                $book->save();
            }

        }


    }
}
