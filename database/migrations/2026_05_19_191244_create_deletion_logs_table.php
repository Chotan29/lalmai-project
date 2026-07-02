<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeletionLogsTable extends Migration
{
    public function up()
    {
        Schema::create('deletion_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model');
            $table->unsignedBigInteger('model_id');
            $table->text('data');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deletion_logs');
    }
}
