<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMcqExamQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('mcq_exam_questions')) {
            Schema::create('mcq_exam_questions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();
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
        Schema::dropIfExists('mcq_exam_questions');
    }
}
