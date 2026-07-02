<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentScholarshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('student_scholarships')) {
            Schema::create('student_scholarships', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by')->nullable();
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id');
                $table->unsignedInteger('scholarships_id')->nullable();
                $table->text('scholarship_application_id')->nullable();
                $table->text('scholarship_user_name')->nullable();
                $table->text('scholarship_password')->nullable();

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
        Schema::dropIfExists('student_scholarships');
    }
}
