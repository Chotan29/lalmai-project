<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNirgamUtaraCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('nirgam_utara_certificates')) {
            Schema::create('nirgam_utara_certificates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id')->unique();

                $table->date('date_of_issue');
                $table->string('nu_num')->unique();

                $table->date('date_of_leaving');
                $table->text('leaving_time_class');
                $table->string('join_time_class');
                $table->string('previous_school_name');

                $table->string('reason_to_leave')->nullable();

                $table->string('mention_body_mark')->nullable();

                $table->text('any_other_remark')->nullable();
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
        Schema::dropIfExists('nirgam_utara_certificates');
    }
}
