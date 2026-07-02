<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranscriptCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('transcript_certificates')) {
            Schema::create('transcript_certificates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id')->unique();
                $table->date('date_of_issue');
                $table->string('trc_num')->unique();
                $table->string('year');
                $table->string('duration');
                $table->string('credit_required');
                $table->string('gpa');
                $table->string('verification_code');
                $table->string('mark_sheet_sn');
                $table->string('provisional_certificate_num');
                $table->text('ref_text')->nullable();

                $table->boolean('status')->default(1);
                $table->foreign('students_id')->references('id')->on('students');
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
        Schema::dropIfExists('transcript_certificates');
    }
}
