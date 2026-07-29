<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * A paper is ONE subject. Whether a student studies it as a compulsory subject or as the
 * 4th (optional) subject is a property of the ENROLMENT, not of the subject.
 *
 * Until now the only way to express "Biology is compulsory for 220 students and the 4th
 * subject for 42 others" was to duplicate the subject with an O- code, which split the
 * exam schedule and the marks in two. This column removes the need for that.
 */
class AddSubjectRoleToStudentSubjectTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('student_subject') && !Schema::hasColumn('student_subject', 'subject_role')) {
            Schema::table('student_subject', function (Blueprint $table) {
                $table->string('subject_role', 20)->default('compulsory')->after('subjects_id');
                $table->index(['students_id', 'subject_role'], 'student_subject_role_idx');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('student_subject') && Schema::hasColumn('student_subject', 'subject_role')) {
            Schema::table('student_subject', function (Blueprint $table) {
                $table->dropIndex('student_subject_role_idx');
                $table->dropColumn('subject_role');
            });
        }
    }
}
