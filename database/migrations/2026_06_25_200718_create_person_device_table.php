<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('person_device', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('person_id');
            $table->unsignedBigInteger('device_id');
            $table->timestamp('allocated_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->unsignedInteger('allocated_by')->nullable();
            $table->unsignedInteger('revoked_by')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->boolean('sync_failed')->default(false);
            $table->string('sync_notes')->nullable();
            $table->timestamp('last_sync_attempt')->nullable();
            $table->timestamps();

            $table->index(['person_id', 'device_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('person_device');
    }
}
