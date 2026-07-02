<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntegrationRunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('integration_runs')) {
            Schema::create('integration_runs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('type', 40);                 // batch_update | sync_logs
                $table->string('status', 20)->default('queued'); // queued|running|finished|failed
                $table->unsignedInteger('total_steps')->default(0);
                $table->unsignedInteger('done_steps')->default(0);
                $table->json('payload')->nullable();        // input params
                $table->json('result')->nullable();         // aggregated result/progress
                $table->text('error')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('finished_at')->nullable();
                $table->timestamps();

                $table->index(['type', 'status']);
                $table->index(['status']);
                $table->index(['created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('integration_runs');
    }
}
