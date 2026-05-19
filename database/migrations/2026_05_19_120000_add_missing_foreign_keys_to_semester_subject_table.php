<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMissingForeignKeysToSemesterSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('semester_subject') || !Schema::hasTable('subjects') || !Schema::hasTable('semesters')) {
            return;
        }

        DB::table('semester_subject as ss')
            ->leftJoin('subjects as sub', 'sub.id', '=', 'ss.subject_id')
            ->whereNull('sub.id')
            ->select('ss.id')
            ->orderBy('ss.id')
            ->chunk(1000, function ($rows) {
                $ids = collect($rows)->pluck('id')->all();
                if (!empty($ids)) {
                    DB::table('semester_subject')->whereIn('id', $ids)->delete();
                }
            });

        DB::table('semester_subject as ss')
            ->leftJoin('semesters as sem', 'sem.id', '=', 'ss.semester_id')
            ->whereNull('sem.id')
            ->select('ss.id')
            ->orderBy('ss.id')
            ->chunk(1000, function ($rows) {
                $ids = collect($rows)->pluck('id')->all();
                if (!empty($ids)) {
                    DB::table('semester_subject')->whereIn('id', $ids)->delete();
                }
            });

        Schema::table('semester_subject', function (Blueprint $table) {
            if (!$this->foreignKeyExists('semester_subject', 'fk_semester_subject_semester')) {
                $table->foreign('semester_id', 'fk_semester_subject_semester')
                    ->references('id')
                    ->on('semesters')
                    ->onDelete('cascade');
            }

            if (!$this->foreignKeyExists('semester_subject', 'fk_semester_subject_subject')) {
                $table->foreign('subject_id', 'fk_semester_subject_subject')
                    ->references('id')
                    ->on('subjects')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('semester_subject')) {
            return;
        }

        Schema::table('semester_subject', function (Blueprint $table) {
            if ($this->foreignKeyExists('semester_subject', 'fk_semester_subject_semester')) {
                $table->dropForeign('fk_semester_subject_semester');
            }

            if ($this->foreignKeyExists('semester_subject', 'fk_semester_subject_subject')) {
                $table->dropForeign('fk_semester_subject_subject');
            }
        });
    }

    private function foreignKeyExists($tableName, $constraintName)
    {
        $databaseName = DB::getDatabaseName();
        $result = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $databaseName)
            ->where('TABLE_NAME', $tableName)
            ->where('CONSTRAINT_NAME', $constraintName)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();

        return $result;
    }
}
