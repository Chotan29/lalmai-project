<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcademicInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('academic_infos')) {
            Schema::create('academic_infos', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id');
                $table->string('institution', '100')->nullable();
                $table->string('board', '50')->nullable();
                $table->string('pass_year', '4')->nullable();
                $table->string('roll_no', '15')->nullable();
                $table->double('mark_obtained', 10,2)->nullable();
                $table->double('maximum_mark', 10,2)->nullable();
                $table->double('percentage', 10,2)->nullable();
                $table->string('division_grade', '10')->nullable();
                $table->string('grade_point')->nullable();
                $table->string('grade_letter')->nullable();
                $table->text('major_subjects')->nullable();
                $table->text('remark')->nullable();

                $table->unsignedInteger('sorting_order')->nullable();
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
        Schema::dropIfExists('academic_infos');
    }
}
