<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHidePaymentForOldStudentToOnlineRegistrationSettingsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('online_registration_settings')) {
            Schema::table('online_registration_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('online_registration_settings', 'hide_payment_for_old_student')) {
                    $table->boolean('hide_payment_for_old_student')->default(false)->after('payment_required');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('online_registration_settings')) {
            Schema::table('online_registration_settings', function (Blueprint $table) {
                $table->dropColumn('hide_payment_for_old_student');
            });
        }
    }
}
