<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebContactUsSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_contact_us_settings')) {
            Schema::create('web_contact_us_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->string('contact_title')->nullable();
                $table->string('company')->nullable();
                $table->text('address')->nullable();

                $table->string('phone', '100')->nullable();
                $table->string('fax', '100')->nullable();
                $table->string('email', '100')->nullable();
                $table->string('webURL', '100')->nullable();

                $table->text('google_map')->nullable();
                $table->text('latitude')->nullable();
                $table->text('longitude')->nullable();

                //Social Media
                $table->string('facebook_link', '100')->nullable();
                $table->string('twitter_link', '100')->nullable();
                $table->string('googlePlus_link', '100')->nullable();
                $table->string('linkedIn_link', '100')->nullable();
                $table->string('instagram_link', '100')->nullable();
                $table->string('whatsApp_link', '100')->nullable();
                $table->string('skype_link', '100')->nullable();
                $table->string('pinterest_link', '100')->nullable();
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
        Schema::dropIfExists('web_contact_us_settings');
    }
}
