<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFeeBillingRunsAddApprovalCancelColumns extends Migration
{
    public function up()
    {
        Schema::table('fee_billing_runs', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_billing_runs', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('finished_at');
            }
            if (!Schema::hasColumn('fee_billing_runs', 'approved_by')) {
                $table->unsignedInteger('approved_by')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('fee_billing_runs', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('fee_billing_runs', 'cancelled_by')) {
                $table->unsignedInteger('cancelled_by')->nullable()->after('cancelled_at');
            }
            if (!Schema::hasColumn('fee_billing_runs', 'cancel_reason')) {
                $table->string('cancel_reason', 500)->nullable()->after('cancelled_by');
            }
        });

        // Extend status enum to include 'cancelled' and 'approved'
        DB::statement("ALTER TABLE fee_billing_runs MODIFY COLUMN status ENUM('pending','running','completed','partial','failed','cancelled','approved') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        // Revert enum first (remove new values)
        DB::statement("ALTER TABLE fee_billing_runs MODIFY COLUMN status ENUM('pending','running','completed','partial','failed') NOT NULL DEFAULT 'pending'");

        Schema::table('fee_billing_runs', function (Blueprint $table) {
            $table->dropColumn(['approved_at','approved_by','cancelled_at','cancelled_by','cancel_reason']);
        });
    }
}
