<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebHomeSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_home_settings')) {
            Schema::create('web_home_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->boolean('slider_status')->default(0);
                $table->boolean('slider_caption_status')->default(0);
                $table->boolean('slider_call_to_action_status')->default(0);
                $table->string('slider_call_to_action_title', '100')->nullable();
                $table->string('slider_call_to_action_link')->nullable();

                $table->boolean('notice_status')->default(0);
                $table->string('notice_title', '15')->nullable();

                $table->boolean('blog_status')->default(0);
                $table->string('blog_title', '15')->nullable();

                $table->boolean('event_status')->default(0);
                $table->string('event_title', '15')->nullable();

                $table->boolean('welcome_status')->default(0);
                $table->string('welcome_title', '100')->nullable();
                $table->string('welcome_sub_title')->nullable();
                $table->text('welcome_description')->nullable();
                $table->text('welcome_image')->nullable();
                $table->string('welcome_button_text','15')->nullable();
                $table->string('welcome_link')->nullable();


                $table->boolean('introduction_status')->default(0);
                $table->string('introduction_title', '100')->nullable();
                $table->text('introduction_description')->nullable();
                $table->string('introduction_button_text','15')->nullable();
                $table->string('introduction_link')->nullable();

                $table->boolean('services_status')->default(0);
                $table->string('services_title', '100')->nullable();

                $table->string('counter_title', '100')->nullable();
                $table->boolean('counter_status')->default(0);

                $table->string('staff_title', '100')->nullable();
                $table->boolean('staff_status')->default(0);

                $table->string('testimonial_title', '100')->nullable();
                $table->boolean('testimonial_status')->default(0);

                $table->string('client_title', '100')->nullable();
                $table->boolean('client_status')->default(0);

                $table->boolean('email_call_to_action_status')->default(0);
                $table->string('email_call_to_action_title', '100')->nullable();
                $table->string('email_call_to_action_button_text','15')->nullable();
                $table->string('email_call_to_action_link')->nullable();
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
        Schema::dropIfExists('web_home_settings');
    }
}
