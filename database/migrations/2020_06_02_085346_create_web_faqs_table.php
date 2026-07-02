<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebFaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_faqs')) {
            Schema::create('web_faqs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();
                $table->text('question');
                $table->text('answer')->nullable();
                $table->smallInteger('rank')->default(0);
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
        Schema::dropIfExists('web_faqs');
    }
}
