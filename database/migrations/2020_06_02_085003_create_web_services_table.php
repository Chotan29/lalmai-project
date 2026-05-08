<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_services')) {
            Schema::create('web_services', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();
                $table->string('title', 50);
                $table->string('sub_title');
                $table->text('description')->nullable();
                $table->text('image')->nullable();
                $table->text('video')->nullable();
                $table->text('button_text')->nullable();
                $table->text('link')->nullable();
                $table->string('open_in',25)->nullable();
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
        Schema::dropIfExists('web_services');
    }
}
