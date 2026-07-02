<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSemestersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('semesters')) {
            Schema::create('semesters', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();;

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('last_updated_by')->nullable();

                $table->string('semester', '50');

                $table->string('slug')->unique();

                $table->unsignedInteger('staff_id')->nullable();
                $table->unsignedInteger('gradingType_id')->nullable();

                $table->decimal('semester_fee',13,2)->default(0);
                $table->integer('major_subject_count')->nullable();

                $table->boolean('status')->default(1);

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
        Schema::dropIfExists('semesters');
    }
}
