<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeBillingRunDetailsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_billing_run_details')) {
            Schema::create('fee_billing_run_details', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->unsignedInteger('billing_run_id');
                $table->unsignedInteger('student_id');
                $table->unsignedInteger('fee_master_id')->nullable(); // set after FeeMaster is created

                $table->decimal('amount', 10, 2)->default(0);

                $table->enum('status', ['created', 'skipped', 'failed'])->default('created');
                $table->string('skip_reason', 200)->nullable(); // "Already billed", "Inactive student"
                $table->string('error_message', 500)->nullable();

                $table->enum('sms_status', ['pending', 'queued', 'sent', 'failed', 'skipped'])->default('skipped');

                $table->foreign('billing_run_id')
                    ->references('id')->on('fee_billing_runs')
                    ->onDelete('cascade');

                $table->foreign('student_id')
                    ->references('id')->on('students')
                    ->onDelete('cascade');

                $table->foreign('fee_master_id')
                    ->references('id')->on('fee_masters')
                    ->onDelete('set null');

                // Index for fast lookup per run
                $table->index(['billing_run_id', 'status']);
                $table->index(['billing_run_id', 'student_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('fee_billing_run_details');
    }
}
