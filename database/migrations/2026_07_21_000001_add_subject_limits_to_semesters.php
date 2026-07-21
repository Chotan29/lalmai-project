<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubjectLimitsToSemesters extends Migration
{
    public function up()
    {
        Schema::table('semesters', function (Blueprint $table) {
            if (!Schema::hasColumn('semesters', 'max_compulsory_count')) {
                $table->integer('max_compulsory_count')->nullable()->after('major_subject_count');
            }
            if (!Schema::hasColumn('semesters', 'max_optional_count')) {
                $table->integer('max_optional_count')->nullable()->after('max_compulsory_count');
            }
        });
    }

    public function down()
    {
        Schema::table('semesters', function (Blueprint $table) {
            if (Schema::hasColumn('semesters', 'max_optional_count')) {
                $table->dropColumn('max_optional_count');
            }
            if (Schema::hasColumn('semesters', 'max_compulsory_count')) {
                $table->dropColumn('max_compulsory_count');
            }
        });
    }
}
