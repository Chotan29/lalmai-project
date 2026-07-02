<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_notices')) {
            Schema::create('web_notices', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->dateTime('publish_date')->nullable();
                $table->text('image')->nullable();
                $table->text('file')->nullable();
                $table->string('seo_title')->nullable();
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
        Schema::dropIfExists('web_notices');
    }
}
