<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebAboutUsSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_about_us_settings')) {
            Schema::create('web_about_us_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();
                $table->string('title', '100');
                $table->string('slogan', '100')->nullable();
                $table->longText('description')->nullable();
                $table->text('image')->nullable();


                $table->boolean('staffs_status')->default(0);
                $table->string('staffs_title', '100')->nullable();

                $table->boolean('counter_status')->default(0);
                $table->string('counter_title', '100')->nullable();

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
        Schema::dropIfExists('web_about_us_settings');
    }
}
