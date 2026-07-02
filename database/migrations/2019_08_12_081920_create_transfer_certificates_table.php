<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('transfer_certificates')) {
            Schema::create('transfer_certificates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id')->unique();
                $table->date('application_date');
                $table->date('date_of_issue');
                $table->date('date_of_leaving');
                $table->string('tc_num')->unique();
                $table->text('leaving_time_class');
                $table->text('qualified_to_promote');
                $table->string('paid_fee_status');
                $table->string('character');

                //cbse
                //application_date, dob_certificate, school_category, extra_activity_detail
                //Proof for Date of Birth submitted at the time of admission :
                //Whether the school is under Govt/Minority/Independent Category :
                //Games Played on extracurricular activities in which the pupil usually took part (mention achievement level therein)
                //Date of application for certificate :

                //fee_concession_detail, exam_fail_status, subject_studies, last_taken_exam_with_result, cadet_detail, reason_to_leave, total_attendance, school_college_open_total
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
                $table->text('ref_text')->nullable();

                $table->boolean('status')->default(1);

                $table->foreign('students_id')->references('id')->on('students');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_certificates');
    }
}

