<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('department_programs')) {
            Schema::create('department_programs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('department_id');
                $table->unsignedInteger('faculty_id');

                $table->foreign('faculty_id')->references('id')->on('faculties');
                //$table->foreign('faculty_head_id')->references('id')->on('faculty_heads');
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
        Schema::dropIfExists('department_programs');
    }
}
