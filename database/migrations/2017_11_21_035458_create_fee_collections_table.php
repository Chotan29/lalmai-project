<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('fee_collections')) {
            Schema::create('fee_collections', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('students_id');
                $table->unsignedInteger('fee_masters_id');
                $table->dateTime('date');
                $table->decimal('paid_amount',11,2);
                $table->decimal('discount',11,2)->nullable();
                $table->decimal('fine',11,2)->nullable();
                $table->string('payment_method', '25');   
                $table->integer('installment_number')->nullable();             
                $table->string('ref_no', '30')->nullable();
                $table->string('external_ref_no', '30')->nullable();
                $table->string('note', '100')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->text('response')->nullable();
                $table->boolean('status')->default(1);

                $table->foreign('students_id')->references('id')->on('students');
                $table->foreign('fee_masters_id')->references('id')->on('fee_masters');
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
        Schema::dropIfExists('fee_collections');
    }
}
