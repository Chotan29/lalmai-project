<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmitCardPrintLogsTable extends Migration
{
    public function up()
    {
        Schema::create('admit_card_print_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('years_id');
            $table->unsignedInteger('months_id');
            $table->unsignedInteger('exams_id');
            $table->unsignedInteger('faculty_id');
            $table->unsignedInteger('semesters_id');
            $table->tinyInteger('print_type')->default(1);
            $table->unsignedInteger('printed_by')->nullable();
            $table->date('print_date');
            $table->timestamp('printed_at');

            $table->index(['student_id', 'years_id', 'months_id', 'exams_id', 'faculty_id', 'semesters_id'], 'acpl_exam_student');
            $table->index('print_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('admit_card_print_logs');
    }
}
