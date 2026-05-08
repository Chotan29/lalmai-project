<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacultiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('faculties')) {
            Schema::create('faculties', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->string('faculty', '100')->unique();
                $table->string('faculty_code', '15')->unique();

                $table->unsignedInteger('gradingType_id')->nullable();
                $table->string('scale')->nullable();
                $table->string('duration')->nullable();
                $table->string('credit_required')->nullable();
                $table->string('registration_validate')->nullable();

                $table->smallInteger('sorting')->default(0);
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
        Schema::dropIfExists('faculties');
    }
}
