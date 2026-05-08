<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestimonialCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('testimonial_certificates')) {
            Schema::create('testimonial_certificates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id')->unique();
                $table->date('date_of_issue');
                $table->string('ref_num')->unique();
                $table->string('tmc_num')->unique();
                $table->string('program_duration');
                $table->string('year');
                $table->string('gpa');
                $table->string('scale');
                $table->string('average_grade');
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
        Schema::dropIfExists('testimonial_certificates');
    }
}
