<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('subject_staff')) {
            Schema::create('subject_staff', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('subject_id');
                $table->unsignedInteger('staff_id');
                $table->timestamps();

                $table->unique(['subject_id', 'staff_id']);
                $table->index('staff_id');
            });

            /* Copy existing single-teacher assignments so no data is lost */
            DB::statement('
                INSERT INTO subject_staff (subject_id, staff_id, created_at, updated_at)
                SELECT id, staff_id, NOW(), NOW()
                FROM subjects
                WHERE staff_id IS NOT NULL AND staff_id > 0
            ');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subject_staff');
    }
}
