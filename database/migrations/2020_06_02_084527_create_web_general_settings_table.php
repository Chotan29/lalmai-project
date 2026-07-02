<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_general_settings')) {
            Schema::create('web_general_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->string('site_title', '100');
                $table->string('site_slogan', '100')->nullable();
                $table->longText('site_desc')->nullable();
                $table->longText('site_keyword')->nullable();

                $table->text('favicon')->nullable();
                $table->text('site_logo')->nullable();

                $table->text('page_banner')->nullable();

                $table->boolean('top_nav_status')->default(0);
                $table->string('top_nav_text')->nullable();
                $table->string('top_nav_text_link')->nullable();

                $table->boolean('main_nav_status')->default(0);
                $table->boolean('main_nav_button_status')->default(0);
                $table->string('main_nav_button_button_text', '15')->nullable();
                $table->string('main_nav_button_link')->nullable();
                $table->boolean('sticky_nav_status')->default(0);

                $table->boolean('footer_status')->default(0);

                $table->boolean('quick_nav_status')->default(0);
                $table->boolean('footer_nav_status')->default(0);

                $table->text('header_codes')->nullable();
                $table->text('footer_codes')->nullable();
                $table->text('post_detail_foot')->nullable();

                $table->text('custom_css')->nullable();

                $table->text('analytics_view_id')->nullable();
                $table->text('analytics_json_file')->nullable();
                $table->text('recaptcha_site_key')->nullable();
                $table->text('recaptcha_secret_key')->nullable();

                $table->text('facebook_widget')->nullable();
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
        Schema::dropIfExists('web_general_settings');
    }
}
