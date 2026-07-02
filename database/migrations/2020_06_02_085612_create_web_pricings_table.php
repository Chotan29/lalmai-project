<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebPricingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_pricings')) {
            Schema::create('web_pricings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->string('title', '100')->unique();
                $table->double('old_price', 10,2)->nullable();
                $table->double('new_price', 10,2)->nullable();
                $table->string('per_term', '25')->nullable();
                $table->text('description')->nullable();
                $table->text('image')->nullable();
                $table->string('tag', '100')->nullable();
                $table->string('tag_color', '100')->nullable();

                $table->text('button_text')->nullable();
                $table->text('link')->nullable();
                $table->string('open_in',25)->nullable();

                $table->smallInteger('rank')->default(0);

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
        Schema::dropIfExists('web_pricings');
    }
}
