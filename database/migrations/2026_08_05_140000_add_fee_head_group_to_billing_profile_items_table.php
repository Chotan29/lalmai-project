<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Let a recurring bill charge a whole Main Fee Head, not just one head at a time.
 *
 * A profile line becomes either a single head, as before, or a Main Fee Head that generates one
 * fee_master per sub head when the bill runs. Storing the link rather than copying the 26 heads
 * into the profile matters: correct an amount on the fee and next month's bill is already right,
 * instead of every profile that used it quietly billing last year's figures.
 *
 * fee_head_id has to become nullable for that - a line is one or the other, never both.
 */
class AddFeeHeadGroupToBillingProfileItemsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_billing_profile_items')) {
            return;
        }

        if (!Schema::hasColumn('fee_billing_profile_items', 'fee_head_group_id')) {
            Schema::table('fee_billing_profile_items', function (Blueprint $table) {
                $table->unsignedInteger('fee_head_group_id')->nullable()->after('fee_head_id');
            });
        }

        /* Drop the foreign key before relaxing the column, or MySQL refuses the change. The key
           is put back afterwards so a head still cannot be billed out of existence. */
        try {
            DB::statement('ALTER TABLE `fee_billing_profile_items` DROP FOREIGN KEY `fee_billing_profile_items_fee_head_id_foreign`');
        } catch (\Exception $e) {
            // already gone, or named differently on this install
        }

        DB::statement('ALTER TABLE `fee_billing_profile_items` MODIFY `fee_head_id` INT UNSIGNED NULL');

        try {
            DB::statement('ALTER TABLE `fee_billing_profile_items`
                ADD CONSTRAINT `fee_billing_profile_items_fee_head_id_foreign`
                FOREIGN KEY (`fee_head_id`) REFERENCES `fee_heads` (`id`) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // constraint survived the drop attempt above
        }
    }

    public function down()
    {
        if (!Schema::hasTable('fee_billing_profile_items')) {
            return;
        }

        if (Schema::hasColumn('fee_billing_profile_items', 'fee_head_group_id')) {
            Schema::table('fee_billing_profile_items', function (Blueprint $table) {
                $table->dropColumn('fee_head_group_id');
            });
        }
    }
}
