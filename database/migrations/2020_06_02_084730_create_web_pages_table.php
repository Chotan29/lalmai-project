<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_pages')) {
            Schema::create('web_pages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->unsignedInteger('parent_id')->default(0);
                $table->string('title', '100');
                $table->string('slug', '100')->unique();
                $table->string('page_type', '100');
                $table->string('link', '100')->nullable();
                $table->text('image')->nullable();
                $table->text('short_desc')->nullable();
                $table->longText('detail_desc')->nullable();

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
        Schema::dropIfExists('web_pages');
    }
}
