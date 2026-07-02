<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentExtraInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('student_extra_infos')) {
            Schema::create('student_extra_infos', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by')->nullable();
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id');

                $table->integer('total_fee_per_year')->nullable();

                $table->text('bank_name')->nullable();
                $table->text('bank_code')->nullable();
                $table->text('bank_account_number')->nullable();

                $table->text('admission_portal_login_id')->nullable();
                $table->text('admission_portal_login_password')->nullable();

                $table->boolean('status')->default(1);

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
        Schema::dropIfExists('student_extra_infos');
    }
}
