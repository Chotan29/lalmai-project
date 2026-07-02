<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOnlinePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('online_payments')) {
            Schema::create('online_payments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id');
                $table->dateTime('date');
                $table->integer('amount');

                $table->string('payment_status')->nullable();
                $table->string('payment_gateway')->nullable();

                $table->string('ref_no')->nullable();

                $table->string('ref_text')->nullable();
                $table->string('invoice_id', '30')->nullable();

                $table->text('note')->nullable();
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
        Schema::dropIfExists('online_payments');
    }
}
