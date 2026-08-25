<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Which bank account each fee head's money is transferred into.
 *
 * A separate table rather than two columns on fee_heads, because "one head, one account" turned
 * out not to be true in this college. Three shapes exist and all three are in the letters the
 * office already sends by hand:
 *
 *   - Most college heads have one account. Transport, library, mosque - one number each.
 *
 *   - The department heads have one account per department. Seminar money from Marketing goes to
 *     Marketing's seminar account, Management's to Management's. Same head, different account,
 *     decided by whose money it is.
 *
 *   - The religious head has two accounts and splits by the student's religion. In the sample
 *     letter its 6,450 arrived as 5,880 to the milad account and 570 to the puja account - 196
 *     students and 19 students at 30 taka each. Nobody would guess that from the head alone.
 *
 * A row with no department and no religion rule is the plain case. is_default catches whatever
 * matches nothing, so the letter always adds back to the head's total - a student whose religion
 * was never filled in must still have their money land somewhere, or the bank returns the letter.
 */
class CreateFeeHeadBankAccountsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('fee_head_bank_accounts')) {
            return;
        }

        Schema::create('fee_head_bank_accounts', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('fee_head_id');

            /* Set only for heads whose account depends on the department. Null means the account
               serves every student. */
            $table->unsignedInteger('faculty_id')->nullable();

            /* The name as it must appear in the letter - the bank matches on this as well as the
               number, and the college writes it in Bangla. */
            $table->string('account_name', 191);
            $table->string('account_no', 50);

            /* A comma separated list of religions this account takes, e.g. "Hinduism,Buddhism".
               Empty means the account is not chosen by religion. */
            $table->string('match_religion', 191)->nullable();

            /* The account that takes anything the rules above did not claim. One per head. */
            $table->boolean('is_default')->default(false);

            /* Free text for the office - which branch, when it was opened, anything worth keeping. */
            $table->string('note', 191)->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('last_updated_by')->nullable();

            /* An account closed by the bank is switched off, never deleted: old letters have to
               remain explainable. */
            $table->tinyInteger('status')->default(1);

            $table->timestamps();

            $table->index('fee_head_id', 'fhba_fee_head_index');
            $table->index(['fee_head_id', 'faculty_id'], 'fhba_head_faculty_index');
        });
    }

    public function down()
    {
        /* Not dropped. These are bank account numbers copied by hand from paper, and re-entering
           them is exactly the sort of work where a digit goes astray. */
    }
}
