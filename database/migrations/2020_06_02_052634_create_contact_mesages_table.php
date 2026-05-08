<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactMesagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('contact_mesages')) {
            Schema::create('contact_mesages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->string('first_name', '50');
                $table->string('last_name', '50');
                $table->string('email', '100');
                $table->string('address', '100')->nullable();
                $table->string('phone', '50')->nullable();
                $table->text('message');

                $table->boolean('view_status')->default(0);
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
        Schema::dropIfExists('contact_mesages');
    }
}
