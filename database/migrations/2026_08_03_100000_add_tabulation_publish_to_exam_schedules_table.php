<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * The tabulation sheet is released to students separately from the grade sheet.
 *
 * publish_status already exists and governs the old result/grade sheet, the routine and
 * the admit card in the student and guardian panels. The office wants to hand out the
 * tabulation on its own schedule - sometimes before, sometimes long after the grade sheet -
 * so it gets its own flag rather than riding on publish_status. Nothing here touches the
 * existing Result Publish behaviour.
 */
class AddTabulationPublishToExamSchedulesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('exam_schedules')) {
            return;
        }

        Schema::table('exam_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_schedules', 'tabulation_publish_status')) {
                $table->tinyInteger('tabulation_publish_status')->default(0)->after('publish_date');
            }
            if (!Schema::hasColumn('exam_schedules', 'tabulation_publish_date')) {
                $table->timestamp('tabulation_publish_date')->nullable()->after('tabulation_publish_status');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('exam_schedules')) {
            return;
        }

        Schema::table('exam_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('exam_schedules', 'tabulation_publish_date')) {
                $table->dropColumn('tabulation_publish_date');
            }
            if (Schema::hasColumn('exam_schedules', 'tabulation_publish_status')) {
                $table->dropColumn('tabulation_publish_status');
            }
        });
    }
}
