<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * A tabulation sheet has one pair of columns per subject, so a full title like
 * "Information and Communication Technology -I" makes the sheet far too wide and the last
 * subjects fall off the page. This holds the short label used in those narrow columns.
 * Left empty, the app derives one automatically (ICT-I, CGG-I, Bangla-I ...).
 */
class AddShortNameToSubjectsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('subjects') && !Schema::hasColumn('subjects', 'short_name')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->string('short_name', 20)->nullable()->after('code');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('subjects') && Schema::hasColumn('subjects', 'short_name')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->dropColumn('short_name');
            });
        }
    }
}
