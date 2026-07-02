<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixSubjectFkDeleteRuleOnSemesterSubjectTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('semester_subject')) {
            return;
        }

        Schema::table('semester_subject', function (Blueprint $table) {
            if ($this->foreignKeyExists('semester_subject', 'fk_semester_subject_subject')) {
                $table->dropForeign('fk_semester_subject_subject');
            }

            if ($this->foreignKeyExists('semester_subject', 'semester_subject_subject_id_foreign')) {
                $table->dropForeign('semester_subject_subject_id_foreign');
            }

            $table->foreign('subject_id', 'fk_semester_subject_subject')
                ->references('id')
                ->on('subjects')
                ->onDelete('restrict');
        });
    }

    public function down()
    {
        if (!Schema::hasTable('semester_subject')) {
            return;
        }

        Schema::table('semester_subject', function (Blueprint $table) {
            if ($this->foreignKeyExists('semester_subject', 'fk_semester_subject_subject')) {
                $table->dropForeign('fk_semester_subject_subject');
            }

            $table->foreign('subject_id', 'fk_semester_subject_subject')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade');

            if (!$this->foreignKeyExists('semester_subject', 'semester_subject_subject_id_foreign')) {
                $table->foreign('subject_id', 'semester_subject_subject_id_foreign')
                    ->references('id')
                    ->on('subjects');
            }
        });
    }

    private function foreignKeyExists($tableName, $constraintName)
    {
        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $tableName)
            ->where('CONSTRAINT_NAME', $constraintName)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();
    }
}
