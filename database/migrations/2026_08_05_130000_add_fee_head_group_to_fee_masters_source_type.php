<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Let a fee row say it came from a Main Fee Head.
 *
 * source_type was created as enum('manual','recurring'). Writing anything else does not fail -
 * MySQL quietly stores an empty string - so rows charged from a Main Fee Head were losing the
 * one field that says where they came from, with no error to show for it. Widening the enum is
 * what makes that field trustworthy.
 */
class AddFeeHeadGroupToFeeMastersSourceType extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_masters') || !Schema::hasColumn('fee_masters', 'source_type')) {
            return;
        }

        DB::statement("ALTER TABLE `fee_masters`
            MODIFY `source_type` ENUM('manual','recurring','fee_head_group') NOT NULL DEFAULT 'manual'");

        /* Rows already written by the new screens: the value was rejected, but their
           billing_period_key survived and identifies them exactly. */
        DB::table('fee_masters')
            ->where('billing_period_key', 'like', 'GROUP-%')
            ->update(['source_type' => 'fee_head_group']);
    }

    public function down()
    {
        if (!Schema::hasTable('fee_masters') || !Schema::hasColumn('fee_masters', 'source_type')) {
            return;
        }

        DB::table('fee_masters')->where('source_type', 'fee_head_group')->update(['source_type' => 'manual']);

        DB::statement("ALTER TABLE `fee_masters`
            MODIFY `source_type` ENUM('manual','recurring') NOT NULL DEFAULT 'manual'");
    }
}
