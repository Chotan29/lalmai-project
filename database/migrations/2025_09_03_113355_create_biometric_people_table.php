<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiometricPeopleTable extends Migration
{
    public function up()
    {
        Schema::create('biometric_people', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('attendable_type');          // App\Models\Student | App\Models\Staff
            $table->unsignedInteger('attendable_id');   // local id
            $table->string('person_identifier')->index(); // identifier used on device server (we use reg_no or fallback)
            $table->unsignedBigInteger('remote_person_id')->nullable(); // returned by API
            $table->string('rfid', 32)->nullable();
            $table->string('primary_display_text', 20)->nullable();
            $table->string('secondary_display_text', 20)->nullable();
            $table->string('photo_url')->nullable();
            $table->timestamp('last_pushed_at')->nullable();
            $table->timestamps();

            $table->unique(['attendable_type','attendable_id'], 'uniq_attendable');
        });
    }

    public function down()
    {
        Schema::dropIfExists('biometric_people');
    }
}
