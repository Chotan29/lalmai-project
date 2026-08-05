<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Which Main Fee Head an admission charges.
 *
 * This one column is what keeps the 26 heads out of the code. The office builds the fee on the
 * Main Fee Head screen and picks it here; nothing in PHP knows that admission is 7,400 or that
 * the seminar fee is 1,800. Next session is a new Main Fee Head and a change of this dropdown.
 *
 * Left empty, the admission behaves exactly as it does today - one lump row under ADMISSION FEE
 * - so nothing breaks for a department that has not been set up yet.
 */
class AddFeeHeadGroupToOnlineRegistrationProgramsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('online_registration_programs')
            && !Schema::hasColumn('online_registration_programs', 'fee_head_group_id')) {
            Schema::table('online_registration_programs', function (Blueprint $table) {
                $table->unsignedInteger('fee_head_group_id')->nullable()->after('old_student_fee');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('online_registration_programs')
            && Schema::hasColumn('online_registration_programs', 'fee_head_group_id')) {
            Schema::table('online_registration_programs', function (Blueprint $table) {
                $table->dropColumn('fee_head_group_id');
            });
        }
    }
}
