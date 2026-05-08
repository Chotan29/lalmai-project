<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressinfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('addressinfos')) {
            Schema::create('addressinfos', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id');
                $table->foreign('students_id')->references('id')->on('students');
                $table->string('address')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->string('postal_code', '25')->nullable();

                $table->string('temp_address', '100')->nullable();
                $table->string('temp_state')->nullable();
                $table->string('temp_country', '25')->nullable();
                $table->string('temp_postal_code', '25')->nullable();

                $table->string('home_phone', '15')->nullable();
                $table->string('mobile_1', '15')->nullable();
                $table->string('mobile_2', '15')->nullable();

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
        Schema::dropIfExists('addressinfos');
    }
}
