<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactDirectoryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('contact_directory_details')) {
            Schema::create('contact_directory_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by')->default(0);
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('contact_directory_groups_id');
                $table->string('name', '100');
                $table->string('sex', '10')->nullable();
                $table->date('date_of_birth')->nullable();

                $table->string('religion', '25')->nullable();
                $table->string('caste', '25')->nullable();
                $table->string('nationality', '25')->nullable();

                $table->string('mother_tongue', '25')->nullable();
                $table->string('blood_group', '5')->nullable();

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

                $table->text('image')->nullable();
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
        Schema::dropIfExists('contact_directory_details');
    }
}
