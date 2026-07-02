<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebRegistrationAcademicQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_registration_academic_qualifications')) {
            Schema::create('web_registration_academic_qualifications', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('web_registrations_id');
                $table->string('examination', '50')->nullable();
                $table->string('institution', '100')->nullable();
                $table->string('board_university', '50')->nullable();
                $table->string('year_of_pass', '4')->nullable();
                $table->string('percentage_grade','25')->nullable();

                /*$table->unsignedInteger('sorting_order')->nullable();*/
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
        Schema::dropIfExists('web_registration_academic_qualifications');
    }
}
