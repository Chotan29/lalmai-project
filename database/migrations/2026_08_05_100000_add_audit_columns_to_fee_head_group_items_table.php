<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Who added a sub head to a fee, and who last changed its amount.
 *
 * Every other table in this system carries created_by / last_updated_by, and the models and
 * controllers write them by habit. These two were left off the items table when it was created,
 * so the first real save failed on an unknown column. Amounts inside a fee are exactly the kind
 * of thing someone will later need to ask "who changed this" about, so they belong here anyway.
 */
class AddAuditColumnsToFeeHeadGroupItemsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_head_group_items')) {
            return;
        }

        Schema::table('fee_head_group_items', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_head_group_items', 'created_by')) {
                $table->unsignedInteger('created_by')->nullable()->after('id');
            }
            if (!Schema::hasColumn('fee_head_group_items', 'last_updated_by')) {
                $table->unsignedInteger('last_updated_by')->nullable()->after('created_by');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('fee_head_group_items')) {
            return;
        }

        Schema::table('fee_head_group_items', function (Blueprint $table) {
            foreach (['last_updated_by', 'created_by'] as $col) {
                if (Schema::hasColumn('fee_head_group_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
