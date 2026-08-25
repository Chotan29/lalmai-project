<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Somebody paid, then did not take admission, and the money has to go back.
 *
 * Two separate facts, kept separately on purpose.
 *
 * The first is that the admission did not happen. That is a mark on the student, not a deletion:
 * the application, the photo and the payment all still happened and the college may need to show
 * that later. The mark can be lifted, and the student returns to the counts exactly as before.
 *
 * The second is that money was handed back. That is a new event, not an edit of the old one. It
 * would be simpler to set the original payment to "refunded" and be done, but then the fact that
 * the money was ever collected disappears, and the month's collection figure quietly changes after
 * the fact - which is the sort of thing that is only noticed when the books do not balance and
 * nobody can say why. Money in and money out are recorded as two rows, and the total is the
 * difference.
 *
 * Nothing here drops or alters an existing column.
 */
class AddAdmissionCancellationAndRefunds extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'admission_cancelled_at')) {
                /* Null means a normal student. A date means the admission did not go ahead. */
                $table->timestamp('admission_cancelled_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('students', 'admission_cancel_note')) {
                $table->string('admission_cancel_note', 255)->nullable()->after('admission_cancelled_at');
            }
            if (!Schema::hasColumn('students', 'admission_cancelled_by')) {
                $table->unsignedInteger('admission_cancelled_by')->nullable()->after('admission_cancel_note');
            }
        });

        /* Every report that counts students has to ask this question, so it should not be a scan. */
        if (!$this->indexExists('students', 'students_admission_cancelled_at_index')) {
            Schema::table('students', function (Blueprint $table) {
                $table->index('admission_cancelled_at', 'students_admission_cancelled_at_index');
            });
        }

        if (!Schema::hasTable('payment_refunds')) {
            Schema::create('payment_refunds', function (Blueprint $table) {
                $table->bigIncrements('id');

                /* Which payment is being given back, and to whom. Kept as plain ids rather than
                   foreign keys, to match the rest of this database. */
                $table->unsignedInteger('online_payment_id')->nullable();
                $table->unsignedInteger('students_id');

                $table->decimal('amount', 12, 2);
                $table->date('date');

                /* cash | bank | bkash | nagad | gateway | other - how the money actually left. */
                $table->string('method', 30)->default('cash');

                /* Cheque number, bKash transaction id, gateway reference - whatever proves it. */
                $table->string('ref_no', 100)->nullable();
                $table->string('note', 255)->nullable();

                /* Its own receipt number, so a refund can be produced on paper and found again. */
                $table->string('voucher_no', 50)->nullable();

                $table->unsignedInteger('created_by')->nullable();
                $table->unsignedInteger('last_updated_by')->nullable();

                /* 1 = stands, 0 = entered by mistake and reversed. Refunds are never deleted. */
                $table->tinyInteger('status')->default(1);

                $table->timestamps();

                $table->index('students_id', 'payment_refunds_students_id_index');
                $table->index('online_payment_id', 'payment_refunds_payment_id_index');
                $table->index('date', 'payment_refunds_date_index');
            });
        }
    }

    /**
     * Laravel 5.8 has no hasIndex(), and running the migration twice would otherwise fail on a
     * duplicate key name rather than doing nothing.
     */
    protected function indexExists($table, $index)
    {
        try {
            $rows = \Illuminate\Support\Facades\DB::select(
                'SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?', [$index]
            );
            return count($rows) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function down()
    {
        /*
         * Deliberately does not drop the columns or the table.
         *
         * Rolling this back would destroy the record of money returned to people, which is the one
         * thing here that cannot be reconstructed from anywhere else. If it truly has to go, it
         * should be done by hand, on purpose, with a backup taken first.
         */
    }
}
