<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * A refund, broken down the same way the payment was.
 *
 * One fee of 7,400 does not arrive as one row. It arrives as twenty-six - one per sub head, each
 * with its own amount - because that is the only level at which the college can say who the money
 * belongs to. The head-wise report adds those rows up.
 *
 * So a refund recorded as a single lump of 7,400 cannot be taken off that report: nothing says
 * which heads to take it from. The money would go out of the drawer and stay on the sheet for
 * ever, and the department heads would still be showing income they no longer have.
 *
 * These rows carry the same breakdown back the other way, one per head, tied to the exact
 * collection row they reverse. Nothing in fee_collections is altered - the money genuinely was
 * collected on that day, and a report of last month should not change because of something done
 * this month.
 */
class CreatePaymentRefundItemsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('payment_refund_items')) {
            return;
        }

        Schema::create('payment_refund_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('payment_refund_id');

            /* The head the money is coming back out of - fee_masters.fee_head, the same value the
               head-wise report groups by. */
            $table->unsignedInteger('fee_head');

            /* The collection row being reversed, where there is one. Kept so a refund can always
               be traced to the exact receipt line rather than only to a head. */
            $table->unsignedInteger('fee_collection_id')->nullable();

            $table->decimal('amount', 12, 2);

            /* Copied from the collection so the report can filter by date without another join. */
            $table->date('date')->nullable();

            $table->timestamps();

            $table->index('payment_refund_id', 'refund_items_refund_id_index');
            $table->index('fee_head', 'refund_items_fee_head_index');
            $table->index('date', 'refund_items_date_index');
        });
    }

    public function down()
    {
        /* Not dropped on purpose - see the refunds migration. Losing this would leave refunds
           that no report could account for. */
    }
}
