<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeBillingRunsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_billing_runs')) {
            Schema::create('fee_billing_runs', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->unsignedInteger('billing_profile_id');

                // PERIOD IDENTIFICATION
                $table->string('period_key', 20);     // "2026-06" / "2026-Q2" / "2026-H1" / "2026"
                $table->string('period_label', 100);  // "June 2026" / "Q2 2026" / "H1 2026"
                $table->smallInteger('period_year')->unsigned();
                $table->tinyInteger('period_month')->unsigned()->nullable(); // null for yearly/one_time

                // DATES
                $table->date('run_date');
                $table->date('due_date');

                // COUNTS
                $table->smallInteger('total_students')->unsigned()->default(0);
                $table->smallInteger('bills_created')->unsigned()->default(0);
                $table->smallInteger('bills_skipped')->unsigned()->default(0);
                $table->smallInteger('bills_failed')->unsigned()->default(0);
                $table->smallInteger('sms_queued')->unsigned()->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);

                // AUDIT
                $table->enum('triggered_by', ['schedule', 'manual'])->default('schedule');
                $table->unsignedInteger('initiated_by')->nullable(); // user who clicked "Run Now"
                $table->enum('status', ['pending', 'running', 'completed', 'partial', 'failed'])->default('pending');
                $table->timestamp('started_at')->nullable();
                $table->timestamp('finished_at')->nullable();
                $table->text('error_log')->nullable(); // reason if failed

                $table->foreign('billing_profile_id')
                    ->references('id')->on('fee_billing_profiles')
                    ->onDelete('cascade');

                // CRITICAL: prevent duplicate runs for same profile+period
                $table->unique(['billing_profile_id', 'period_key'], 'uq_billing_run_profile_period');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('fee_billing_runs');
    }
}
