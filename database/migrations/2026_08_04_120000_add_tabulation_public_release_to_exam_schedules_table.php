<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Releasing a result to the open web is a bigger step than releasing it inside the
 * student panel, so it gets its own switch rather than riding on either flag next door:
 *
 *   publish_status            - the old grade sheet, routine and admit card (student + guardian panel)
 *   tabulation_publish_status - the tabulation inside the logged-in student's own panel
 *   tabulation_public_status  - the tabulation on lalmaigc.edu.bd, no login at all   <- this one
 *
 * tabulation_public_token guards the whole-class sheet, which has every student's marks on
 * it and so must never be reachable by guessing a URL. It is regenerated on every publish
 * and set back to null on un-publish, so an old link dies the moment the office withdraws
 * the result.
 */
class AddTabulationPublicReleaseToExamSchedulesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('exam_schedules')) {
            return;
        }

        Schema::table('exam_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_schedules', 'tabulation_public_status')) {
                $table->tinyInteger('tabulation_public_status')->default(0)->after('tabulation_publish_date');
            }
            if (!Schema::hasColumn('exam_schedules', 'tabulation_public_date')) {
                $table->timestamp('tabulation_public_date')->nullable()->after('tabulation_public_status');
            }
            if (!Schema::hasColumn('exam_schedules', 'tabulation_public_token')) {
                $table->string('tabulation_public_token', 64)->nullable()->after('tabulation_public_date');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('exam_schedules')) {
            return;
        }

        Schema::table('exam_schedules', function (Blueprint $table) {
            foreach (['tabulation_public_token', 'tabulation_public_date', 'tabulation_public_status'] as $col) {
                if (Schema::hasColumn('exam_schedules', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
