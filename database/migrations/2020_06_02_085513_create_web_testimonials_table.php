<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebTestimonialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_testimonials')) {
            Schema::create('web_testimonials', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();
                $table->string('name', 100)->comment('name of the testimonial giver');
                $table->text('comment')->nullable();
                $table->string('address', 100)->nullable();
                $table->text('profile_image')->nullable();
                $table->string('office', 50)->nullable();
                $table->string('position', 50)->nullable();
                $table->text('link')->nullable();
                $table->string('email', 50)->nullable();
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
        Schema::dropIfExists('web_testimonials');
    }
}
