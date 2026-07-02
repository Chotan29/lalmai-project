<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentDegreesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('student_degrees')) {
            Schema::create('student_degrees', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by')->nullable();
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id');
                $table->unsignedInteger('degrees_id');

                $table->integer('obtained_mark')->nullable();
                $table->integer('total_marks')->nullable();

                $table->dateTime('receive_in_college_date')->nullable();
                $table->dateTime('issue_date')->nullable();
                $table->boolean('status')->default(1);


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
        Schema::dropIfExists('student_degrees');
    }
}
