<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMcqQuestionOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('mcq_question_options')) {
            Schema::create('mcq_question_options', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('mcq_questions_id');
                $table->text('option')->nullable();
                $table->text('image')->nullable();

                $table->boolean('answer_status')->default(0);
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
        Schema::dropIfExists('mcq_question_options');
    }
}
