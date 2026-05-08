<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('subjects')) {
            Schema::create('subjects', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->string('title', '100');
                $table->string('code', '50')->unique();

                $table->float('course_fee')->nullable();

                $table->float('full_mark_theory')->nullable();
                $table->float('pass_mark_theory')->nullable();
                $table->float('full_mark_practical')->nullable();
                $table->float('pass_mark_practical')->nullable();
                $table->float('credit_hour')->nullable();

                $table->string('sub_type', '15')->nullable();
                $table->string('class_type', '15')->nullable();
                $table->unsignedInteger('staff_id')->nullable();

                $table->string('description', '100')->nullable();
                $table->boolean('status')->default(1);


                //$table->foreign('staff_id')->references('id')->on('staff');
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
        Schema::dropIfExists('subjects');
    }
}
