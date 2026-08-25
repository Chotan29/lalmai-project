<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * পরিচয়পত্র (SMART ID CARD FEE) belongs on the college letter, not the department one.
 *
 * It was recorded as a department head, but every piece of the college's own paper says otherwise:
 * the printed list of accounts has 1335901017119 among the college accounts, and the college's own
 * admission transfer letter carries it as line 24 of the college letter. Nothing about the fee is
 * departmental - one account takes all of it, whichever department the student is in.
 *
 * This only moves which letter the money is listed on. The amount, the account and the report are
 * untouched: collected_by decides which of the two letters a head's accounts are printed under, and
 * both letters go to the same branch on the same day.
 */
class SmartIdCardFeeIsACollegeHead extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_heads') || !Schema::hasColumn('fee_heads', 'collected_by')) {
            return;
        }

        /* Matched on the title as well as the id, so this cannot quietly rewrite some other head if
           the ids ever differ between the two databases. */
        DB::table('fee_heads')
            ->where('id', 105)
            ->where('fee_head_title', 'SMART ID CARD FEE')
            ->update(['collected_by' => 'college']);
    }

    public function down()
    {
        if (!Schema::hasTable('fee_heads') || !Schema::hasColumn('fee_heads', 'collected_by')) {
            return;
        }

        DB::table('fee_heads')
            ->where('id', 105)
            ->where('fee_head_title', 'SMART ID CARD FEE')
            ->update(['collected_by' => 'department']);
    }
}
