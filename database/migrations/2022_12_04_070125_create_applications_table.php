<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('applications')) {
            Schema::create('applications', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('application_type_id');

                $table->dateTime('date');
                $table->dateTime('end_date');

                $table->string('subject', '100');
                $table->text('message');
                $table->text('file')->nullable();
                $table->boolean('approve_status')->default(0);
                $table->boolean('status')->default(0);
                $table->unsignedInteger('years_id');

                $table->foreign('years_id')->references('id')->on('years');

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
        Schema::dropIfExists('applications');
    }
}

