<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('fee_masters')) {
            Schema::create('fee_masters', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id');
                $table->string('semester', '100');
                $table->string('fee_head', '100');
                $table->dateTime('fee_due_date');
                $table->dateTime('fee_due_date2')->nullable();
                $table->dateTime('fee_due_date3')->nullable();
                $table->decimal('fee_amount',11,2);
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
        Schema::dropIfExists('fee_masters');
    }
}
