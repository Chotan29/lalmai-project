<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The order the accounts appear in on the letter.
 *
 * Until now the letter listed accounts by account number, which is tidy and wrong: the college's
 * own letters have always run in a fixed order of its own - the religious fees first, then the
 * clubs, then the big service heads, then the treasury ones, and the two department fees last. The
 * bank reads these letters against the previous one, so an order that changes between letters is a
 * letter that gets queried.
 *
 * Held on the account rather than on the head because a head can own two accounts that sit at
 * different points in the list - milad is first on the college's letter and puja is second, and
 * both belong to the one religious head.
 *
 * Seeded from the college's own admission letter, which is the only authority for what the order
 * is. Anything the letter did not list keeps serial 0 and falls to the end, sorted by number, so a
 * newly opened account still appears rather than silently vanishing.
 */
class AddSerialToFeeHeadBankAccounts extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_head_bank_accounts')) {
            return;
        }

        if (!Schema::hasColumn('fee_head_bank_accounts', 'serial')) {
            Schema::table('fee_head_bank_accounts', function (Blueprint $table) {
                $table->unsignedSmallInteger('serial')->default(0)->after('fee_head_id');
            });
        }

        /*
         * The college's order, read straight off its own letter. Account number is the key rather
         * than the head, because the religious head owns two of these and they are not adjacent to
         * each other by any rule a computer could infer - the college simply puts milad first.
         */
        $order = [
            '1335901017122' => 1,   // ধর্মীয় অনুষ্ঠান মিলাদ
            '1335901017125' => 2,   // ধর্মীয় অনুষ্ঠান পূজা
            '1335901017130' => 3,   // সাহিত্য ও সংস্কৃতি
            '1335901017108' => 4,   // বহিঃক্রীড়া
            '1335901017112' => 5,   // আন্তঃক্রীড়া এবং কমনরুম
            '1335901017132' => 6,   // ম্যাগাজিন
            '1335901017133' => 7,   // রেঞ্জার
            '1335901017131' => 8,   // বিএনসিসি
            '1335901017106' => 9,   // লাইব্রেরি
            '1335901017107' => 10,  // মসজিদ
            '1335901017118' => 11,  // অধিভুক্তি ফি
            '1335901017124' => 12,  // চিকিৎসা সেবা
            '1335901017121' => 13,  // শিক্ষা সফর
            '1335901017116' => 14,  // পাঠদান উন্নয়ন / কম্পিউটার
            '1335901017129' => 15,  // বিবিধ
            '1335901017110' => 16,  // কলেজ পরিবহন
            '1335901017109' => 17,  // অত্যাবশ্যকীয় কর্মচারী
            '1335901017126' => 18,  // ব্যবস্থাপনা
            '1335901017113' => 19,  // বিদ্যুৎ
            '1335901017117' => 20,  // উন্নয়ন তহবিল
            '1335901017123' => 21,  // বোর্ড / বিশ্ববিদ্যালয় ফি (সোনালী সেবা এর সাথে)
            '1335901017114' => 22,  // ভর্তি
            '1335901017115' => 23,  // বেতন
            '1335901017119' => 24,  // পরিচয়পত্র
        ];

        foreach ($order as $accountNo => $serial) {
            DB::table('fee_head_bank_accounts')
                ->where('account_no', $accountNo)
                ->update(['serial' => $serial]);
        }

        /*
         * The two department fees close the letter, in the college's order: incourse then seminar.
         * Every department's account for one of them shares that head's serial - they are one line
         * of the college's list that happens to have several accounts under it, and among
         * themselves the account number decides.
         */
        foreach ([103 => 25, 104 => 26] as $headId => $serial) {
            DB::table('fee_head_bank_accounts')
                ->where('fee_head_id', $headId)
                ->update(['serial' => $serial]);
        }
    }

    public function down()
    {
        /* The column is left in place. Dropping it would throw away an order that was read off
           paper, and re-deriving it means finding the letter again. */
    }
}
