<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOnlineRegistrationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('online_registration_settings')) {
            Schema::create('online_registration_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->boolean('status')->default(0);
                $table->string('base', 50)->nullable();
                $table->string('title', 100)->default('Online Application for Admission');
                $table->text('logo')->nullable();

                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();

                $table->boolean('rules_status')->default(0);
                $table->longText('rules')->nullable();

                $table->boolean('agreement_status')->default(0);
                $table->longText('agreement')->nullable();

                $table->longText('registration_guidelines')->nullable();
                $table->longText('registration_close_message')->nullable();
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
        Schema::dropIfExists('online_registration_settings');
    }
}
