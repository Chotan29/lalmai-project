<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediumOfInstructionCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('medium_of_instruction_certificates')) {
            Schema::create('medium_of_instruction_certificates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id')->unique();
                $table->date('date_of_issue');
                $table->string('ref_num')->unique();
                $table->string('moic_num')->unique();
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
        Schema::dropIfExists('medium_of_instruction_certificates');
    }
}
