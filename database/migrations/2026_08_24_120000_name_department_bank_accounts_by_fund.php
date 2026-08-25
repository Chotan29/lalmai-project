<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Say which fund each department account is, not just whose.
 *
 * A department holds two accounts - a seminar fund and an internal exam fund - and both rows were
 * saved under the department's name alone. On the transfer letter that came out as two lines
 * reading "BBA (Hon's) Department Of Management" against two different account numbers, with
 * nothing to tell the bank which fund either one was. The amounts were right and the numbers were
 * right; the letter simply could not be read.
 *
 * The wording is the college's own. Its second year letter names the incourse account
 * "অভ্যন্তরীণ পরীক্ষা তহবিল", and the handwritten note of the department numbers uses the same two
 * names, so this is what the bank has already been sent before.
 *
 * Only the label changes. No account number, no amount and no routing is touched.
 */
class NameDepartmentBankAccountsByFund extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_head_bank_accounts')) {
            return;
        }

        $funds = [
            103 => 'অভ্যন্তরীণ পরীক্ষা তহবিল',
            104 => 'সেমিনার তহবিল',
        ];

        $faculties = Schema::hasTable('faculties')
            ? DB::table('faculties')->pluck('faculty', 'id')
            : collect();

        foreach ($funds as $headId => $fund) {
            $rows = DB::table('fee_head_bank_accounts')->where('fee_head_id', $headId)->get();

            foreach ($rows as $row) {
                $department = $faculties[$row->faculty_id] ?? null;
                if (!$department) { continue; }

                /* Run twice this would otherwise read "সেমিনার তহবিল, সেমিনার তহবিল, ...". */
                if (mb_strpos((string) $row->account_name, $fund) === 0) { continue; }

                DB::table('fee_head_bank_accounts')
                    ->where('id', $row->id)
                    ->update(['account_name' => mb_substr($fund . ', ' . $department, 0, 191)]);
            }
        }
    }

    public function down()
    {
        /* Not reversed. The old label was the department name on its own, which is exactly the
           ambiguity this removed - putting it back would only make the letter unreadable again. */
    }
}
