<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvisionalCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('provisional_certificates')) {
            Schema::create('provisional_certificates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id')->unique();
                $table->date('date_of_issue');
                $table->date('result_publish_date');
                $table->string('pc_num')->unique();
                $table->string('year');
                $table->string('gpa');
                $table->string('scale');
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
        Schema::dropIfExists('provisional_certificates');
    }
}
