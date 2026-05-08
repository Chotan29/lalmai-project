<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_registrations')) {
            Schema::create('web_registrations', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by')->default(0);
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('web_registration_programmes_id');

                $table->string('reg_no', '50')->unique();
                $table->date('reg_date');

                $table->string('name', '100');
                $table->string('sex', '10');
                $table->date('date_of_birth');

                $table->string('religion', '25')->nullable();
                $table->string('caste', '25')->nullable();
                $table->string('nationality', '25')->nullable();

                $table->string('state', '100');
                $table->string('mother_tongue', '25')->nullable();
                $table->string('blood_group', '5')->nullable();

                $table->string('medicine_info', '100')->nullable();

                $table->string('disease_info', '100')->nullable();

                $table->string('guardian_name', '100')->nullable();
                $table->string('guardian_relation', '50')->nullable();
                $table->string('guardian_occupation', '50')->nullable();
                $table->integer('guardian_annual_income')->nullable();

                $table->string('address', '100')->nullable();
                $table->string('tel', '15')->nullable();
                $table->string('cell_1', '15')->nullable();
                $table->string('cell_2', '15')->nullable();
                $table->string('email', '100')->nullable();

                $table->string('mailing_address', '100')->nullable();
                $table->string('mailing_tel', '15')->nullable();
                $table->string('mailing_cell_1', '15')->nullable();
                $table->string('mailing_cell_2', '15')->nullable();
                $table->string('mailing_email', '100')->nullable();


                $table->text('student_image')->nullable();
                $table->text('student_signature')->nullable();
                $table->text('guardian_signature')->nullable();

                $table->boolean('status')->default(0);
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
        Schema::dropIfExists('web_registrations');
    }
}
