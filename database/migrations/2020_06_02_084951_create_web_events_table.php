<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_events')) {
            Schema::create('web_events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();
                $table->string('title', '100');
                $table->string('slug', '100')->unique();
                $table->text('description')->nullable();
                $table->text('image')->nullable();
                $table->dateTime('event_date')->nullable();
                $table->string('event_time', '100')->nullable();
                $table->string('event_place', '100')->nullable();
                $table->string('seo_title', '100')->nullable();
                $table->text('seo_keywords')->nullable();
                $table->text('seo_description')->nullable();

                $table->unsignedInteger('view_count')->default(0);
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
        Schema::dropIfExists('web_events');
    }
}
