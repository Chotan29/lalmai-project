<?php

use App\Models\StudentAnnexure;
use App\Models\StudentDegree;
use App\Models\StudentExtraInfo;
use App\Models\StudentScholarship;
use App\Models\StudentPlacement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableSeederBackup extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //empty premission table and run
        //php artisan db:seed --class=UpdateTableSeeder --force

        //1. Migrate the new
                //php artisan migrate
                \Illuminate\Support\Facades\Artisan::call('migrate:fresh --seed');
        //2. News Seeders Call Forecefully
                //Call Updated Seeders
                $this->call(AnnexureSeeder::class);
                $this->call(DegreesSeeder::class);
                $this->call(AcademicInfoLevelSeeder::class);
                $this->call(ApplicationTypeSeeder::class);
                $this->call(StateSeeder::class);
                $this->call(ScholarshipSeeder::class);
                $this->call(PlacementSeeder::class);
                $this->call(PermissionTableSeeder::class);


        //3. modify tables with data
                //General Setting Table
                Schema::table('general_settings', function (Blueprint $table) {
                    $table->boolean('application')->default(1);
                });

                //faculties
                Schema::table('faculties', function (Blueprint $table) {
                    $table->smallInteger('sorting')->default(0);
                });

                //semester
                Schema::table('semesters', function (Blueprint $table) {
                    $table->decimal('semester_fee',13,2)->default(0);
                    $table->integer('major_subject_count')->nullable();
                });
                //subject
                Schema::table('subjects', function (Blueprint $table) {
                    $table->decimal('course_fee',13,2)->nullable();
                });

                //student
                Schema::table('students', function (Blueprint $table) {
                    $table->string('national_id_1', '25')->nullable();
                    $table->string('national_id_2', '25')->nullable();
                    $table->string('national_id_3', '25')->nullable();
                    $table->string('national_id_4', '25')->nullable();

                });
                //get students record
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
                }


        //AddressInfo
                Schema::table('addressinfos', function (Blueprint $table) {
                    $table->string('postal_code', '25')->nullable();
                    $table->string('temp_postal_code', '25')->nullable();
                });

        //online registration table
                Schema::table('online_registration_programs', function (Blueprint $table) {
                    $table->unsignedInteger('semesters_id')->unique();
                });

        //Staff
                Schema::table('staff', function (Blueprint $table) {
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

                });





        //Certificate
            Schema::table('transfer_certificates', function (Blueprint $table) {
                $table->date('application_date');
                $table->text('join_time_class')->nullable();
                $table->string('birth_place')->nullable();
                $table->string('dob_certificate')->nullable();
                $table->string('school_category')->nullable();
                $table->string('promoted_class')->nullable();
                $table->string('fee_concession_detail')->nullable();
                $table->string('exam_fail_status')->nullable();
                $table->text('subject_studies')->nullable();
                $table->string('last_taken_exam_with_result')->nullable();
                $table->string('cadet_detail')->nullable();
                $table->string('reason_to_leave')->nullable();
                $table->integer('total_attendance')->nullable();
                $table->integer('school_college_open_total')->nullable();
                $table->text('extra_activity_detail')->nullable();
                $table->text('any_other_remark')->nullable();
            });

        //Tranfer Certificate Template

            DB::table('certificate_templates')->insert([
                [
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                    'created_by' => 1,
                    'certificate' => 'TRANSFER_1',
                    'template' => '<table class="table table-bordered"> <tbody> <tr> <td style="text-align: right;"><span style="text-align: right;">TC No. :&nbsp;</span><br></td> <td style="text-align: left;"><span style="font-weight: 700;">{{tc_num}}</span><br></td> <td rowspan="5">{{student_image}}</td> </tr> <tr> <td style="text-align: right; "><span style="text-align: right;">Faculty/Level :&nbsp;</span><br></td> <td style="text-align: left;"><span style="font-weight: 700;">{{faculty}}</span><br></td> </tr> <tr> <td style="text-align: right; "><span style="text-align: right;">Admission No./Reg.No. :</span><br></td> <td style="text-align: left;"><span style="font-weight: 700;">{{reg_no}}</span><br></td> </tr> <tr> <td style="text-align: right;"><span style="text-align: right;">University Reg. No. :</span><br></td> <td style="text-align: left;"><b>{{university_reg}}</b><br></td> </tr><tr><td style="text-align: right;">Batch. :<br></td><td style="text-align: left;"><span style="font-weight: 700;">{{batch}}</span><br></td></tr> </tbody></table><table class="table table-bordered"> <tbody> <tr> <td>01.</td> <td style="text-align: right; ">Name of Student :&nbsp;</td> <td width="50%" style="text-align: left;"><b>{{student_name}}</b><br></td> </tr> <tr> <td>02.</td> <td style="text-align: right; ">Name of Father.&nbsp;:&nbsp;</td> <td width="50%" style="text-align: left;"><b>{{parents_name}}</b><br></td> </tr> <tr> <td>03.</td> <td style="text-align: right; ">Date of Birth :&nbsp;</td> <td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{date_of_birth}}</span><br></td> </tr> <tr> <td>04.</td> <td style="text-align: right; ">Gender :&nbsp;</td> <td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{gender}}</span><br></td> </tr> <tr> <td>05.</td> <td style="text-align: right; ">Nationality :&nbsp;</td> <td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{nationality}}</span><br></td> </tr> <tr> <td>06.</td> <td style="text-align: right; ">Religion :&nbsp;</td> <td width="50%" style="text-align: left;"><b>{{religion}}</b><br></td> </tr> <tr> <td>07.</td> <td style="text-align: right; ">Community &amp; Caste :&nbsp;</td> <td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{caste}}</span><br></td> </tr> <tr> <td>08.</td> <td style="text-align: right; ">Date of Admission :&nbsp;</td> <td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{reg_date}}</span><br></td> </tr> <tr> <td>09.</td> <td style="text-align: right; ">Date of Leaving :&nbsp;</td> <td width="50%" style="text-align: left;"><b>{{date_of_leaving}}</b><br></td> </tr> <tr> <td>10.</td> <td style="text-align: right;">Class studied at the time of leaving :&nbsp;</td> <td width="50%" style="text-align: left;"><b>{{leaving_time_class}}</b><br></td> </tr> <tr> <td>11.</td> <td style="text-align: right; ">Whether qualified for promotion to higher class :&nbsp;</td> <td width="50%" style="text-align: left;"><b>{{qualified_to_promote}}</b><br></td> </tr> <tr> <td>12.</td> <td style="text-align: right; ">Whether the student has paid all Balance fees to the college :&nbsp;</td> <td width="50%" style="text-align: left;"><b>{{paid_fee_status}}</b><br></td> </tr> <tr> <td>13.</td> <td style="text-align: right; ">Conduct and Character :&nbsp;</td> <td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{character}}</span><br></td> </tr> </tbody></table>',
                    'custom_css' => '.subpage{padding: 10px;}',
                    'student_photo' => 0,
                    'status' => 1
                ],
                [
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                    'created_by' => 1,
                    'certificate' => 'TRANSFER_2',
                    'template' => '<p></p><table class="table table-bordered"><tbody><tr><td><span style="text-align: right;">TC No. :&nbsp;</span><b>{{tc_num}}</b><br></td><td><span style="text-align: right;">बिद्यालय सं. / School No :&nbsp;</span><span style="font-weight: 700;">{{reg_no}}</span><br></td><td>Affiliation No. :&nbsp;<span style="font-weight: 700;">{{university_reg}}</span><br></td><td><span style="text-align: right;">Renewed upto. :&nbsp;</span><br></td><td><span style="text-align: right;">प्रवेश सं./Admission No. :&nbsp;</span><br></td></tr></tbody></table><table class="table table-bordered"> <tbody> <tr> <td>01.</td> <td style="text-align: left;">विद्यार्थीका नाम / Name of Student :&nbsp;</td> <td width="50%" style="text-align: left;"><b>{{student_name}}</b><br></td> </tr> <tr> <td>02.</td> <td style="text-align: left; " class="field_title">माताका नाम / Mother\'s Name :&nbsp;</td> <td width="50%" style="text-align: left;"><b>{{mother_name}}</b><br></td> </tr><tr><td>03.</td><td style="text-align: left; " class="field_title">पिताका नाम /Name of Father\'s / Guardian\'s Name :&nbsp;<br></td><td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{father_name}}</span><br></td></tr> <tr><td>04.</td><td style="text-align: left; " class="field_title">Date of birth(in Christain Era) according to Admission &amp; Withdrawal Register :<br></td><td width="50%" style="text-align: left;"><p><span style="background-color: transparent;">In figures:&nbsp;</span><span style="font-weight: 700;">{{date_of_birth}}</span></p><p><span style="background-color: transparent;">In words:</span></p><p><span style="font-weight: 700;">{{date_of_birth_in_word}}</span></p></td></tr><tr><td>05.</td><td style="text-align: left; " class="field_title">&nbsp;Proof for Date of Birth submitted at the time of admission :</td><td width="50%" style="text-align: left;"><b>{{dob_certificate}}</b></td></tr><tr><td>06.</td><td style="text-align: left; " class="field_title">राष्ट्रियता / Nationality :&nbsp;<br></td><td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{nationality}}</span><br></td></tr><tr><td>07.</td><td style="text-align: left; " class="field_title"><p>क्या अनु.जाति/ज.जा./पिछडा वर्गसे सम्बन्धित है</p><p>Whether the candidate belongs to Scheduled Caste or Schedule Tribe, or OBC:</p></td><td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{caste}}</span><br></td></tr><tr><td>08.</td><td style="text-align: left; " class="field_title">Date of the first admission in the School with the class :<br></td><td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{reg_date}}&nbsp; &nbsp; &nbsp;</span>Class Joined :<span style="font-weight: 700;">&nbsp;&nbsp;</span><span style="font-weight: 700;">{{join_time_class}}</span><br></td></tr><tr><td>09.</td><td style="text-align: left; " class="field_title"><p>पिछली कक्षा जिसमे विद्यार्थी अध्ययनरत था (अंकों में)</p><p>Class in which the pupil last studied (in words):</p></td><td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{leaving_time_class}}</span><br></td></tr><tr><td>10.</td><td style="text-align: left; " class="field_title"><p><span style="background-color: transparent;">पिछले विद्यालय / वोर्ड परीक्षा एवं परिमाण / School/Board Annual Examination last taken with the result:&nbsp;</span><br></p></td><td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{last_taken_exam_with_result}}</span><br></td></tr><tr><td>11.</td><td style="text-align: left; " class="field_title">क्या विद्यार्थीका परीक्षा परिणाम अनुत्तीर्ण है ? / Whether failed, if so once/twice in the same class :</td><td width="50%" style="text-align: left;"><b>{{exam_fail_status}}</b></td></tr><tr><td>12.</td><td style="text-align: left; " class="field_title">प्रस्तावित विषय / Subjects Studied:&nbsp;<br></td><td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{subject_studies}}</span><br></td></tr><tr><td>13.</td><td style="text-align: left; " class="field_title"><p>क्या उच्च कक्षा में पदोन्‍नतका अधिकारी है / Whether qualified for promotion to the next higher class :</p><p>If so, to which class:</p></td><td width="50%" style="text-align: left;"><p><span style="font-weight: 700;">{{qualified_to_promote}}</span></p><p><b>{{promoted_class}}</b><br></p></td></tr><tr><td>14.</td><td style="text-align: left; " class="field_title">विद्यालय दिवसों की कुल संख्या / Total No. of working days in the academic session :</td><td width="50%" style="text-align: left;"><p><span style="font-weight: 700;">{{school_college_open_total}}</span><br></p></td></tr><tr><td>15.</td><td style="text-align: left; " class="field_title">अन्तिम तिथि तक उपस्थितियों की कुल संख्या / Total No. of presence in the academic session :&nbsp;<br></td><td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{total_attendance}}</span><br></td></tr><tr><td>16.</td><td style="text-align: left; " class="field_title"><p>क्या विद्यार्थी ने विद्यालय की सभी देय राशि का भुगतान कर दिया है /&nbsp;&nbsp;<span style="background-color: transparent;">Month up to which the people has paid school dues :</span></p></td><td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{paid_fee_status}}</span><br></td></tr><tr><td>17.</td><td style="text-align: left; " class="field_title">क्या विद्यार्थी को कोई शुल्क रियायत प्रदान की गई थी, यदि हाँऽ तो उसकी प्रकृतीः / Any Fee concession availed of, if so, the nature of such concession :<br></td><td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{fee_concession_detail}}</span><br></td></tr><tr><td>18.</td><td style="text-align: left; " class="field_title">क्या विद्यार्थी&nbsp; एन.सी.सी. कैडेट/ स्काउट है? विवरण देः / Whether the pupil is NCC cadet / Boy scout / Girl Guide (give details):<br></td><td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{cadet_detail}}</span><br></td></tr><tr><td>19.</td><td style="text-align: left; " class="field_title"><p>Whether the school is under Govt/Minority/Independent Category :<br></p></td><td width="50%" style="text-align: left;"><b>{{school_category}}</b></td></tr><tr><td>20.</td><td style="text-align: left; " class="field_title">Games Played on extracurricular activities in which the pupil usually took part (mention achievement level therein)<br></td><td width="50%" style="text-align: left;"><b>{{extra_activity_detail}}</b></td></tr><tr><td>21.</td><td style="text-align: left; " class="field_title"><p>Date of application for certificate :</p></td><td width="50%" style="text-align: left;"><b>{{application_date}}</b><br></td></tr><tr><td>22.</td><td style="text-align: left; " class="field_title">विद्यालय विद्यार्थीके नाम काटनेकी तिथि / Date on which pupils name was struck off the rolls of the Vidyalaya :<br></td><td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{date_of_leaving}}</span><br></td></tr><tr><td>23.</td><td style="text-align: left; " class="field_title">प्रमाण-पत्र जारी करने की तिथी / Date of issue of certificate :&nbsp;<br></td><td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{date_of_issue}}</span><br></td></tr><tr><td>24.</td><td style="text-align: left; " class="field_title">कोइ अन्य टिप्पणी / Any other remarks :<br></td><td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{any_other_remark}}</span><br></td></tr>    </tbody></table><br><table class="table table-bordered" style="text-align: center; "><tbody><tr><td><p><br></p><p>_______________________</p><p><b>तैयारकर्ता / Prepared By</b></p></td><td><p><br></p><p>_______________________<br></p><p><b>जाँचकर्ता / Checked By</b></p></td><td><p><br></p><p><span style="background-color: transparent;">_______________________</span><br></p><p><b>ह. प्रधानाचार्य / कार्यालय मोहर</b></p><p><b>Sign of Principal with Official Seal</b></p></td></tr></tbody></table><p style="text-align: center; "><br></p>',
                    'custom_css' => '.subpage{padding: 0px;}.field_title{width:65%;}p{margin: 0 0 0px;}tr{font-size: 10px;}',
                    'student_photo' => 0,
                    'status' => 1
                ],
                [
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                    'created_by' => 1,
                    'certificate' => 'TRANSFER_3',
                    'template' => '<table class="table table-bordered" style="text-align: right;"> <tbody> <tr> <td><span style="text-align: right;">TC No. :</span><br></td> <td style="text-align: left;"><span style="text-align: right;">&nbsp;</span><span style="font-weight: 700;">{{tc_num}}</span><br></td> <td><span style="text-align: right;">Admission No./Reg.No. :&nbsp;</span><br></td> <td style="text-align: left;"><span style="font-weight: 700;">{{reg_no}}</span><br></td> </tr> <tr> <td>&nbsp;{{national_id_1}}<span style="background-color: transparent;">&nbsp;:</span><br></td> <td style="text-align: left;"><span style="font-weight: 700; background-color: transparent;">{{national_id_2}}</span><br></td> <td>&nbsp;{{national_id_3}}<span style="background-color: transparent; text-align: right;">&nbsp;:</span><br></td> <td style="text-align: left;"> <span style="background-color: transparent; text-align: right;">&nbsp;</span><span style="font-weight: 700;"><span style="background-color: transparent;">{{national_id_4}}</span><br> </span></td> </tr> </tbody></table><table class="table table-bordered" style="text-align: right;"> <tbody> <tr> <td>01.</td> <td style="text-align: left;">Name of Pupil :&nbsp;</td> <td width="50%" style="text-align: left;"><b>{{student_name}}</b><br></td> </tr> <tr> <td>02.</td> <td style="text-align: left;" class="field_title">Father\'s / Guardian\'s Name :&nbsp;</td> <td width="50%" style="text-align: left;"><b>{{guardian_name}}</b><br></td> </tr> <tr> <td>03.</td> <td style="text-align: left;" class="field_title">Mother\'s Name :<br></td> <td width="50%" style="text-align: left;"><b>{{father_name}}</b><br></td> </tr> <tr> <td>04.</td> <td style="text-align: left;" class="field_title">Place of Birth :</td> <td width="50%" style="text-align: left;"><b>{{birth_place}}</b><br></td> </tr> <tr> <td>05.</td> <td style="text-align: left;" class="field_title">Nationality :&nbsp;<br></td> <td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{nationality}}</span><br></td> </tr> <tr> <td>06.</td> <td style="text-align: left;" class="field_title">Caste / Category</td> <td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{caste}}</span><br></td> </tr> <tr> <td>07.</td> <td style="text-align: left;" class="field_title"><p>Date of the first admission in the School with the class</p></td> <td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{reg_date}}</span><br></td> </tr> <tr> <td>08.</td> <td style="text-align: left;" class="field_title"><p>Date of birth(in Christain Era) according to Admission Register:</p></td> <td width="50%" style="text-align: left;"> <p><span style="background-color: transparent;">In figures:&nbsp;</span><span style="font-weight: 700;">{{date_of_birth}}</span></p> <p><span style="background-color: transparent;">In words:</span></p> <p><b>{{date_of_birth_in_word}}</b><br></p> </td> </tr> <tr> <td>09.</td> <td style="text-align: left;" class="field_title">Class in which the pupil last studied/studying:</td> <td width="50%" style="text-align: left;"><b>{{leaving_time_class}}</b><br></td> </tr> <tr> <td>10.</td> <td style="text-align: left;" class="field_title">School/Board Annual Examination last taken with the result:&nbsp;<br></td> <td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{last_taken_exam_with_result}}</span><br></td> </tr> <tr> <td>11.</td> <td style="text-align: left;" class="field_title">Promoted Class :&nbsp;</td> <td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{promoted_class}}</span><br></td> </tr> <tr> <td>12.</td> <td style="text-align: left;" class="field_title">General Conduct :&nbsp;</td> <td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{character}}</span><br></td> </tr> <tr> <td>13.</td> <td style="text-align: left;" class="field_title"><p>Date of application for Transfer certificate :</p></td> <td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{date_of_issue}}</span><br></td> </tr> <tr> <td>14.</td> <td style="text-align: left;" class="field_title">Date of issue of Transfer certificate :</td> <td width="50%" style="text-align: left;"><span style="font-weight: 700;">{{date_of_issue}}</span><br></td> </tr> <tr> <td>15.</td> <td style="text-align: left;" class="field_title"><p>Reason for leaving the school:</p></td> <td width="50%" style="text-align: left;"><b>{{qualified_to_promote}}</b><br></td> </tr> <tr> <td>16.</td> <td style="text-align: left;" class="field_title">Any other remarks:&nbsp;</td> <td width="50%" style="text-align: left;"><b>{{any_other_remark}}</b><br></td> </tr> </tbody></table><p></p><p></p><p style="text-align: right;"><br></p><table class="table table-bordered" style="text-align: right;"> <tbody> <tr> <td> <p><br></p> <p style="text-align: center; "><br></p> <p style="text-align: center; ">_______________________</p> <p style="text-align: center; "><b>&nbsp;Prepared By</b></p> </td> <td> <p><br></p> <p style="text-align: center; "><br></p> <p style="text-align: center; ">_______________________<br></p> <p style="text-align: center; "><b>Checked By</b></p> </td> <td> <p><br></p> <p><br></p> <p style="text-align: center; ">_______________________<br></p> <p style="text-align: center; "><b style="background-color: transparent;">Sign of Principal with Official Seal</b><br></p> </td> </tr> </tbody></table><p style="text-align: right;"><br></p>',
                    'custom_css' => '.subpage{padding: 0px;}.field_title{width:60%;}p{margin: 0 0 0px;}',                'student_photo' => 0,
                    'status' => 1
                ]
            ]);


    }
}
