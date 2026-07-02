<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOnlineRegistrationProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('online_registration_programs')) {
            Schema::create('online_registration_programs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('faculties_id');
                $table->unsignedInteger('semesters_id');
                $table->unique(['faculties_id', 'semesters_id'], 'online_registration_programs_faculties_semesters_unique');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();

                $table->boolean('status')->default(0);
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
        Schema::dropIfExists('online_registration_programs');
    }
}
