<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFeeBillingRunDetailsAddCancelColumns extends Migration
{
    public function up()
    {
        Schema::table('fee_billing_run_details', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_billing_run_details', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('error_message');
            }
            if (!Schema::hasColumn('fee_billing_run_details', 'cancelled_by')) {
                $table->unsignedInteger('cancelled_by')->nullable()->after('cancelled_at');
            }
            if (!Schema::hasColumn('fee_billing_run_details', 'cancel_reason')) {
                $table->string('cancel_reason', 300)->nullable()->after('cancelled_by');
            }
        });

        // Add 'cancelled' to the status enum
        DB::statement("ALTER TABLE fee_billing_run_details MODIFY COLUMN status ENUM('created','skipped','failed','cancelled') NOT NULL DEFAULT 'created'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE fee_billing_run_details MODIFY COLUMN status ENUM('created','skipped','failed') NOT NULL DEFAULT 'created'");

        Schema::table('fee_billing_run_details', function (Blueprint $table) {
            $table->dropColumn(['cancelled_at','cancelled_by','cancel_reason']);
        });
    }
}
