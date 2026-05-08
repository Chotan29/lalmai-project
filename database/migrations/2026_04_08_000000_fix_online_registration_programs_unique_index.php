<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixOnlineRegistrationProgramsUniqueIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('online_registration_programs')) {
            // Drop incorrect unique indexes if they exist
            try {
                DB::statement('ALTER TABLE online_registration_programs DROP INDEX online_registration_programs_faculties_id_unique');
            } catch (\Exception $e) {
                // ignore if index does not exist
            }
            try {
                DB::statement('ALTER TABLE online_registration_programs DROP INDEX online_registration_programs_semesters_id_unique');
            } catch (\Exception $e) {
                // ignore if index does not exist
            }

            Schema::table('online_registration_programs', function (Blueprint $table) {
                $table->unique(['faculties_id', 'semesters_id'], 'online_registration_programs_faculties_semesters_unique');
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
        if (Schema::hasTable('online_registration_programs')) {
            Schema::table('online_registration_programs', function (Blueprint $table) {
                $table->dropUnique('online_registration_programs_faculties_semesters_unique');
            });
        }
    }
}
