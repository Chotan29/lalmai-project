<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Marks, per semester, which subjects a student may also take as their 4th (optional)
 * subject. Previously this was expressed by mapping a duplicate "(Optional)" subject to
 * the semester; now the real subject simply appears in both columns of the form.
 */
class AddAllowAsOptionalToSemesterSubjectTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('semester_subject') && !Schema::hasColumn('semester_subject', 'allow_as_optional')) {
            Schema::table('semester_subject', function (Blueprint $table) {
                $table->boolean('allow_as_optional')->default(0)->after('subject_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('semester_subject') && Schema::hasColumn('semester_subject', 'allow_as_optional')) {
            Schema::table('semester_subject', function (Blueprint $table) {
                $table->dropColumn('allow_as_optional');
            });
        }
    }
}
