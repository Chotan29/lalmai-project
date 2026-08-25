<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * The parts of the bank transfer letter that never change.
 *
 * The letter is mostly fixed text: who it is from, who it goes to, which branch, which account the
 * money is moved out of, and who signs it. Only the table in the middle changes from one letter to
 * the next. Typing the rest again every time is how a branch name ends up spelled two ways in the
 * same file, and how a letter goes out over the name of a principal who left last year.
 *
 * One row. Kept as a table rather than config so the office can change it without anyone editing
 * a file on the server.
 */
class CreateBankLetterSettingsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('bank_letter_settings')) {
            return;
        }

        Schema::create('bank_letter_settings', function (Blueprint $table) {
            $table->increments('id');

            /* From */
            $table->string('college_name', 191)->nullable();
            $table->string('college_address', 191)->nullable();
            $table->string('from_designation', 191)->nullable();

            /* To */
            $table->string('bank_name', 191)->nullable();
            $table->string('branch_name', 191)->nullable();
            $table->string('to_designation', 191)->nullable();

            /* The account the money is moved out of, and how the subject line names it. */
            $table->string('source_account_no', 50)->nullable();
            $table->string('source_account_title', 191)->nullable();

            /* Who signs. Held here so a change of principal is one edit, not thirty letters. */
            $table->string('principal_name', 191)->nullable();
            $table->string('principal_designation', 191)->nullable();

            $table->string('contact_mobile', 50)->nullable();

            /* The sentence between the salutation and the table. Kept editable because the wording
               is the college's, not ours. */
            $table->text('body_text')->nullable();

            $table->unsignedInteger('last_updated_by')->nullable();
            $table->timestamps();
        });

        /*
         * Seeded from the college's own letter, so the first one printed is already right.
         * Everything here is visible on the sample file and on the printed account list.
         */
        \Illuminate\Support\Facades\DB::table('bank_letter_settings')->insert([
            'college_name'          => 'লালমাই সরকারি কলেজ, কুমিল্লা',
            'college_address'       => 'কুমিল্লা সদর দক্ষিণ, কুমিল্লা',
            'from_designation'      => 'অধ্যক্ষ',
            'bank_name'             => 'সোনালী ব্যাংক পিএলসি',
            'branch_name'           => 'কুমিল্লা সদর দক্ষিণ উপজেলা শাখা',
            'to_designation'        => 'ব্যবস্থাপক',
            'source_account_no'     => '1335902000895',
            'source_account_title'  => 'অধ্যক্ষ, লালমাই সরকারি কলেজ, কুমিল্লা',
            'principal_name'        => 'প্রফেসর ড. আ ক ম খলিলুর রহমান',
            'principal_designation' => 'অধ্যক্ষ',
            'contact_mobile'        => '01309-105746',
            'body_text'             => 'উপর্যুক্ত বিষয়ের আলোকে লালমাই সরকারি কলেজের নিম্নবর্ণিত হিসাব নম্বরসমূহে নিম্নোক্ত টাকা স্থানান্তর করার জন্য প্রয়োজনীয় ব্যবস্থা গ্রহণ করবেন।',
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);
    }

    public function down()
    {
        /* Not dropped - it holds text the office typed, not anything this code generated. */
    }
}
