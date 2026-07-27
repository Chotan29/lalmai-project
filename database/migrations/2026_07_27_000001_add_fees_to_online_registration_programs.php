<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Per-department (program) registration fee.
 *
 * The admission fee used to be one global amount for every department. These two
 * nullable columns let each Faculty/Program row in Online Registration Setting carry
 * its own new/old student fee; when a row is left empty the global setting is used,
 * so existing installs keep working unchanged.
 */
class AddFeesToOnlineRegistrationPrograms extends Migration
{
    public function up()
    {
        if (Schema::hasTable('online_registration_programs')) {
            Schema::table('online_registration_programs', function (Blueprint $table) {
                if (!Schema::hasColumn('online_registration_programs', 'new_student_fee')) {
                    $table->decimal('new_student_fee', 10, 2)->nullable()->after('end_date');
                }
                if (!Schema::hasColumn('online_registration_programs', 'old_student_fee')) {
                    $table->decimal('old_student_fee', 10, 2)->nullable()->after('new_student_fee');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('online_registration_programs')) {
            Schema::table('online_registration_programs', function (Blueprint $table) {
                if (Schema::hasColumn('online_registration_programs', 'new_student_fee')) {
                    $table->dropColumn('new_student_fee');
                }
                if (Schema::hasColumn('online_registration_programs', 'old_student_fee')) {
                    $table->dropColumn('old_student_fee');
                }
            });
        }
    }
}
