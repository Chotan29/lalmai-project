<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToSemesterSubjectTable extends Migration
{
    public function up()
    {
        Schema::table('semester_subject', function (Blueprint $table) {
            //$table->unsignedBigInteger('subject_id')->change();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('semester_subject', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
        });
    }
}
