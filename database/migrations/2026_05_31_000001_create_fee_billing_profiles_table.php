<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeBillingProfilesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_billing_profiles')) {
            Schema::create('fee_billing_profiles', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->unsignedInteger('created_by');
                $table->unsignedInteger('updated_by')->nullable();

                $table->string('profile_name', 200);
                $table->text('description')->nullable();

                // SCOPE — class-level targeting (individual removed by design)
                $table->enum('scope_type', ['all', 'faculty', 'semester', 'batch'])->default('all');
                $table->unsignedInteger('faculty_id')->nullable();
                $table->unsignedInteger('semester_id')->nullable();
                $table->unsignedInteger('batch_id')->nullable();

                $table->boolean('only_active_students')->default(1); // status=1 only
                $table->boolean('only_regular_status')->default(1);  // academic_status regular

                // BILLING CYCLE
                $table->enum('billing_cycle', ['monthly', 'quarterly', 'half_yearly', 'yearly', 'one_time'])->default('monthly');
                $table->tinyInteger('billing_day')->unsigned()->nullable(); // 1-28
                $table->json('billing_months')->nullable(); // [1,4,7,10] for quarterly etc.
                $table->date('one_time_date')->nullable(); // only for one_time cycle

                // DUE & FINE
                $table->tinyInteger('due_days')->unsigned()->default(15); // days after billing date
                $table->enum('fine_type', ['none', 'flat', 'per_day'])->default('none');
                $table->decimal('fine_amount', 10, 2)->default(0);
                $table->tinyInteger('fine_grace_days')->unsigned()->default(0); // fine starts after grace
                $table->decimal('max_fine', 10, 2)->nullable(); // cap for per_day fine

                // INSTALLMENTS (replaces hardcoded 30/40/30)
                $table->tinyInteger('installment_count')->unsigned()->default(1);
                $table->json('installment_splits')->nullable(); // [30,40,30] — null = single payment

                // SMS / NOTIFICATION (linked to AlertSetting event)
                $table->boolean('sms_on_generation')->default(0);
                $table->string('alert_event_key', 100)->nullable(); // matches alert_settings.event

                $table->boolean('status')->default(1);

                // FOREIGN KEYS (soft — no cascade, scope tables may vary)
                $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('set null');
                $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('set null');
                $table->foreign('batch_id')->references('id')->on('student_batches')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('fee_billing_profiles');
    }
}
