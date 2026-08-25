<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * The work queue the attendance device collects from us.
 *
 * App\Models\DeviceTask has been in the codebase since the FastFace work started, and
 * TipsoiLanController reads it on every heartbeat - but the table was never created. So the
 * moment the device called in, the heartbeat endpoint threw "Base table or view not found:
 * device_tasks" and answered with a 500. The device would have been ringing a doorbell wired to
 * nothing.
 *
 * The queue matters because of which way the connection runs. The device sits behind the college
 * wifi and cannot be reached from outside, so we cannot push anything to it. Instead it asks us
 * once a minute whether there is work; we put rows here - add this student, update this face -
 * and it collects them itself. That is how 1,190 students get enrolled without anyone standing
 * next to the device.
 *
 * Columns follow the model's own fillable list, which is what the controller already writes to.
 */
class CreateDeviceTasksTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('device_tasks')) {
            return;
        }

        Schema::create('device_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');

            /* Which device the task is for. Held as the address the device reports for itself,
               not the address the request arrives from - over the internet that is the college
               router, the same for every device behind it. */
            $table->string('device_ip', 100)->nullable();

            $table->string('action', 100);
            $table->text('payload')->nullable();

            /* queued -> sent -> done | failed */
            $table->string('status', 20)->default('queued');
            $table->text('last_error')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->timestamps();

            /* The heartbeat asks "is there anything queued for this device" once a minute, for
               every device, forever. That lookup should never read the whole table. */
            $table->index(['device_ip', 'status'], 'device_tasks_ip_status_index');
            $table->index('status', 'device_tasks_status_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_tasks');
    }
}
