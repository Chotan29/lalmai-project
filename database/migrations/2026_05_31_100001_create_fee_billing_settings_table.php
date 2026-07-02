<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeBillingSettingsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_billing_settings')) {
            Schema::create('fee_billing_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                // Scheduler time (for the daily auto-generate command)
                $table->tinyInteger('scheduler_hour')->unsigned()->default(6);    // 0–23
                $table->tinyInteger('scheduler_minute')->unsigned()->default(30); // 0–59
                $table->boolean('scheduler_enabled')->default(1);

                // Who last changed
                $table->unsignedInteger('updated_by')->nullable();
            });

            // Insert the single settings row
            DB::table('fee_billing_settings')->insert([
                'scheduler_hour'    => 6,
                'scheduler_minute'  => 30,
                'scheduler_enabled' => 1,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('fee_billing_settings');
    }
}
