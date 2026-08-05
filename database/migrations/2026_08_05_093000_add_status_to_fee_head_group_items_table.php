<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Sub heads are switched off, never removed.
 *
 * When a head is taken out of a fee on the edit screen, deleting its row would erase the record
 * of how that fee was once composed. Deactivating keeps the history: the row stays, the fee
 * stops counting it, and anyone looking back can still see what the fee used to contain.
 */
class AddStatusToFeeHeadGroupItemsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('fee_head_group_items')
            && !Schema::hasColumn('fee_head_group_items', 'status')) {
            Schema::table('fee_head_group_items', function (Blueprint $table) {
                $table->boolean('status')->default(1)->after('sort_order');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('fee_head_group_items')
            && Schema::hasColumn('fee_head_group_items', 'status')) {
            Schema::table('fee_head_group_items', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
}
