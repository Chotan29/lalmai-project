<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipsoiAttendanceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipsoi_attendance_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('device_identifier')->nullable();
            $table->string('device_location')->nullable();
            $table->string('person_identifier')->nullable();
            $table->string('person_name')->nullable();
            $table->string('rfid')->nullable();
            $table->timestamp('logged_time')->nullable();
            $table->timestamp('sync_time')->nullable();
            $table->string('type')->nullable();
            $table->string('primary_display_text')->nullable();
            $table->string('secondary_display_text')->nullable();
            $table->string('uid')->nullable();
            $table->string('person_id_in_device')->nullable();
            $table->string('project_id')->nullable();
            $table->text('raw_data')->nullable();
            $table->timestamps();

            $table->index(['person_identifier']);
            $table->index(['device_identifier']);
            $table->index(['logged_time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipsoi_attendance_logs');
    }
}
