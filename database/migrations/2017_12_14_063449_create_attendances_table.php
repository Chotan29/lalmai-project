<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->bigIncrements('id');

            // daily scope
            $table->date('date')->index();

            // polymorphic person
            $table->unsignedBigInteger('attendable_id');
            $table->string('attendable_type'); // 'student' or 'staff' via morphMap (see Provider)
            $table->string('reg_no')->index(); // what was scanned / typed

            // source of capture
            $table->enum('source', ['manual','usb_scanner','qr_webcam','barcode_webcam','tipsoi_api','tipsoi_sdk'])->default('manual')->index();

            // status + times
            $table->unsignedInteger('attendance_status_id')->nullable()->index(); // FK -> attendance_statuses
            $table->dateTime('check_in_at')->nullable();
            $table->dateTime('check_out_at')->nullable();

            // Optional meta
            $table->json('meta')->nullable();

            // auditing (if you use users table)
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->timestamps();
            $table->boolean('status')->default(1);
            $table->boolean('logged_from_tipsoi')->default(false);

            $table->enum('notification_status', ['idle','pending','sent','failed'])
                  ->default('idle')
                  ->index();


            // one record per person per date
            $table->unique(['date','attendable_type','attendable_id'], 'uniq_attendance_person_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
