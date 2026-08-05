<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * The order money fills a student's dues in.
 *
 * Collection walks fee_masters by fee_due_date and pays each one until the money runs out. That
 * is fine while dues arrive on different dates, but an admission fee creates 26 rows on the same
 * day - and rows that tie on the sort key come back in whatever order the database feels like.
 *
 * That matters here in a way it never did before: the college's 23 heads add up to exactly the
 * 4,600 a student may pay without the department's share. Fill them in order and 4,600 stops
 * precisely at the last college head, leaving the department at zero. Fill them in a shuffled
 * order and the same 4,600 part-pays the seminar fee, and nobody notices until the department
 * asks where its money is.
 *
 * So each row carries the position its head had in the fee, and collection uses it to break the
 * tie. Existing rows default to 0 and keep behaving exactly as they do today.
 */
class AddSortOrderToFeeMastersTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('fee_masters') && !Schema::hasColumn('fee_masters', 'sort_order')) {
            Schema::table('fee_masters', function (Blueprint $table) {
                $table->unsignedSmallInteger('sort_order')->default(0)->after('fee_amount');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('fee_masters') && Schema::hasColumn('fee_masters', 'sort_order')) {
            Schema::table('fee_masters', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
}
