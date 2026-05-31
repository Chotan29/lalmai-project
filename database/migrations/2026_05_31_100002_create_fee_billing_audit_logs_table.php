<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeBillingAuditLogsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_billing_audit_logs')) {
            Schema::create('fee_billing_audit_logs', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                // What happened
                $table->string('action', 60); // bill_created, bill_cancelled, bill_restored, run_approved, run_cancelled, run_deleted, bulk_cancelled, setting_updated

                // On which entity
                $table->string('entity_type', 50); // billing_run | billing_run_detail | billing_setting
                $table->unsignedInteger('entity_id');

                // Cross-references for easy querying
                $table->unsignedInteger('billing_run_id')->nullable();
                $table->unsignedInteger('student_id')->nullable();

                // Change detail
                $table->text('notes')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();

                // Who & where
                $table->unsignedInteger('performed_by');
                $table->string('ip_address', 45)->nullable();

                $table->index(['entity_type', 'entity_id']);
                $table->index('billing_run_id');
                $table->index('performed_by');
                $table->index('created_at');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('fee_billing_audit_logs');
    }
}
