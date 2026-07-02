<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentPlacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('student_placements')) {
            Schema::create('student_placements', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by')->nullable();
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id');
                $table->unsignedInteger('placements_id')->nullable();

                $table->dateTime('placement_date')->nullable();
                $table->integer('placement_salary')->nullable();
                $table->text('placement_location')->nullable();
                $table->text('placement_designation')->nullable();
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
        Schema::dropIfExists('student_placements');
    }
}
