<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Room for more than one device, and for more than one manufacturer.
 *
 * The table was built when there was going to be a single FastFace at the gate, so it records
 * what a device is but not who made it or what it is for. A college with a main gate, a back gate
 * and a hostel needs three rows that behave differently, and the moment a second brand appears
 * the application has to know which driver understands which row.
 *
 * The table keeps its name. Renaming tipsoi_devices would touch the model, the repository, the
 * dashboard and every query already written against it, for no gain beyond tidiness - the vendor
 * column is what actually makes it brand-neutral.
 *
 * Existing rows are marked tipsoi, which is what they are.
 */
class AddVendorAndEnrolmentToTipsoiDevicesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('tipsoi_devices', 'vendor')) {
            Schema::table('tipsoi_devices', function (Blueprint $table) {
                /* Keyed to config/devices.php - this is how a row finds its driver. */
                $table->string('vendor', 40)->default('tipsoi')->after('identifier')->index();
            });

            DB::table('tipsoi_devices')->whereNull('vendor')->update(['vendor' => 'tipsoi']);
        }

        if (!Schema::hasColumn('tipsoi_devices', 'enrol_students')) {
            Schema::table('tipsoi_devices', function (Blueprint $table) {
                /* Not every device should hold every face. A hostel reader wants the residents,
                   the main gate wants everybody. Off means "this device is watched but nobody is
                   pushed to it", which is also how a device is taken out of service without
                   deleting its history. */
                $table->boolean('enrol_students')->default(true)->after('status');
            });
        }

        if (!Schema::hasColumn('tipsoi_devices', 'notes')) {
            Schema::table('tipsoi_devices', function (Blueprint $table) {
                $table->string('notes', 255)->nullable()->after('location');
            });
        }
    }

    public function down()
    {
        foreach (['vendor', 'enrol_students', 'notes'] as $column) {
            if (Schema::hasColumn('tipsoi_devices', $column)) {
                Schema::table('tipsoi_devices', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
}
