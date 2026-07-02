<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebGalleryImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_gallery_images')) {
            Schema::create('web_gallery_images', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->string('created_by');
                $table->unsignedInteger('gallery_id');
                $table->text('caption')->nullable();
                $table->text('alt_text')->nullable();
                $table->text('image');
                $table->tinyInteger('rank')->default(0);
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
        Schema::dropIfExists('web_gallery_images');
    }
}
