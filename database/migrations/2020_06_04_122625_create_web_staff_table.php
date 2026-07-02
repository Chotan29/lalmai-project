<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('web_staff')) {
            Schema::create('web_staff', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamps();
                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();
                $table->string('slug')->unique();
                $table->string('name', 100)->comment('name of the staff');
                $table->string('position', 50)->nullable();
                $table->string('address', '100')->nullable();
                $table->string('tel', '15')->nullable();
                $table->string('cell_1', '15')->nullable();
                $table->string('cell_2', '15')->nullable();
                $table->string('email', '100')->nullable();

                $table->text('description')->nullable();

                $table->text('profile_image')->nullable();

                $table->string('twitter_url', 100)->nullable();
                $table->string('linkedIn_url', 100)->nullable();
                $table->string('facebook_url', 100)->nullable();
                $table->string('instagram_url', 100)->nullable();
                $table->string('whatsapp_url', 100)->nullable();
                $table->string('skype_url', 100)->nullable();
                $table->string('pinterest_url', 100)->nullable();

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
        Schema::dropIfExists('web_staff');
    }
}
