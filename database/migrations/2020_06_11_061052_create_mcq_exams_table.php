<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMcqExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('mcq_exams')) {
            Schema::create('mcq_exams', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->text('title');
                $table->text('description')->nullable();

                $table->unsignedInteger('faculty')->nullable();
                $table->unsignedInteger('semester')->nullable();
                $table->unsignedInteger('subjects_id');
                $table->unsignedInteger('mcq_instructions_id');

                $table->string('question_type')->default('dynamic');
                $table->integer('no_of_question')->nullable();
                $table->string('pass_mark');


                $table->string('exam_type')->default('date_duration');//duration,date_duration,date_time_duration
                $table->integer('duration');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();

                $table->dateTime('start_date_time')->nullable();
                $table->dateTime('end_date_time')->nullable();

                $table->string('exam_status')->default('one');
                $table->string('mark_type')->default('percent');

                $table->boolean('result_status')->default(0);

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
        Schema::dropIfExists('mcq_exams');
    }
}
