<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceAllocationsTable extends Migration
{
    public function up()
    {
        Schema::create('device_allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('device_identifier'); // "50065" etc.
            $table->string('person_identifier');
            $table->string('action', 20)->nullable(); // allocate|revoke
            $table->string('status', 32)->nullable(); // pending_sync|ok
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index(['device_identifier','person_identifier']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_allocations');
    }
}
