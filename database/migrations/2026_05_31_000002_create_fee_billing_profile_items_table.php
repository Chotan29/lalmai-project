<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeBillingProfileItemsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_billing_profile_items')) {
            Schema::create('fee_billing_profile_items', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->unsignedInteger('billing_profile_id');
                $table->unsignedInteger('fee_head_id');
                $table->decimal('amount_override', 10, 2)->nullable(); // NULL = use fee_heads.fee_head_amount
                $table->boolean('is_optional')->default(0);
                $table->tinyInteger('sort_order')->unsigned()->default(0);

                $table->foreign('billing_profile_id')
                    ->references('id')->on('fee_billing_profiles')
                    ->onDelete('cascade');

                $table->foreign('fee_head_id')
                    ->references('id')->on('fee_heads')
                    ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('fee_billing_profile_items');
    }
}
