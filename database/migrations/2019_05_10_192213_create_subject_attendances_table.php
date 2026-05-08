<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('subject_attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->index();

            $table->unsignedBigInteger('attendance_id')->nullable()->index(); // link to day-level row
            $table->unsignedInteger('student_id')->index();
            $table->unsignedInteger('subject_id')->index();
            $table->unsignedBigInteger('class_routine_detail_id')->nullable()->index();
            $table->unsignedInteger('attendance_status_id')->nullable()->index(); // default P

            $table->dateTime('in_at')->nullable();
            $table->dateTime('out_at')->nullable();

            $table->json('meta')->nullable();

             // auditing (if you use users table)
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            
            $table->timestamps();

            $table->unique(['date','student_id','subject_id'], 'uniq_subject_attendance');
        });
    }

    public function down()
    {
        Schema::dropIfExists('subject_attendances');
    }
}
