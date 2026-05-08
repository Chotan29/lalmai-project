<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMcqQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('mcq_questions')) {
            Schema::create('mcq_questions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('subjects_id');
                $table->unsignedInteger('mcq_question_groups_id');
                $table->unsignedInteger('mcq_question_levels_id');

                $table->text('question');
                $table->text('explanation')->nullable();
                $table->text('image')->nullable();
                $table->text('hints')->nullable();
                $table->float('mark');
                $table->string('type', '20')->default('Single');

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
        Schema::dropIfExists('mcq_questions');
    }
}
