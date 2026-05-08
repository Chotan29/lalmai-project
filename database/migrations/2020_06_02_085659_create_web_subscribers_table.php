<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_subscribers')) {
            Schema::create('web_subscribers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->string('name',100)->nullable();
                $table->string('email',100)->default('');
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
        Schema::dropIfExists('web_subscribers');
    }
}
