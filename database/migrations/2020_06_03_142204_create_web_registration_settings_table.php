<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebRegistrationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_registration_settings')) {
            Schema::create('web_registration_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->boolean('status')->default(0);
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();

                $table->string('title', '100');
                $table->string('sub_title', '100')->nullable();

                $table->text('logo')->nullable();

                $table->boolean('medical_info_status')->default(0);
                $table->boolean('guardian_detail_status')->default(0);
                $table->boolean('permanent_address_status')->default(0);
                $table->boolean('mailing_address_status')->default(0);

                $table->boolean('photo_status')->default(0);
                $table->boolean('applicant_photo_status')->default(0);
                $table->boolean('applicant_signature_status')->default(0);
                $table->boolean('guardian_photo_status')->default(0);

                $table->boolean('qualification')->default(0);
                $table->boolean('experience')->default(0);

                $table->boolean('rules_status')->default(0);
                $table->longText('rules')->nullable();

                $table->boolean('agreement_status')->default(0);
                $table->longText('agreement')->nullable();
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
        Schema::dropIfExists('web_registration_settings');
    }
}
