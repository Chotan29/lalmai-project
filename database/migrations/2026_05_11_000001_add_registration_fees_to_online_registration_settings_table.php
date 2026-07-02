<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRegistrationFeesToOnlineRegistrationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('online_registration_settings')) {
            Schema::table('online_registration_settings', function (Blueprint $table) {
                // Student type permissions
                if (!Schema::hasColumn('online_registration_settings', 'new_student_enabled')) {
                    $table->boolean('new_student_enabled')->default(true)->after('registration_close_message');
                }
                if (!Schema::hasColumn('online_registration_settings', 'old_student_enabled')) {
                    $table->boolean('old_student_enabled')->default(false)->after('new_student_enabled');
                }
                
                // Registration fees
                if (!Schema::hasColumn('online_registration_settings', 'new_student_registration_fee')) {
                    $table->decimal('new_student_registration_fee', 10, 2)->default(0)->after('old_student_enabled');
                }
                if (!Schema::hasColumn('online_registration_settings', 'old_student_registration_fee')) {
                    $table->decimal('old_student_registration_fee', 10, 2)->default(0)->after('new_student_registration_fee');
                }
                
                // Payment requirement
                if (!Schema::hasColumn('online_registration_settings', 'payment_required')) {
                    $table->boolean('payment_required')->default(true)->after('old_student_registration_fee');
                }
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
        if(Schema::hasTable('online_registration_settings')) {
            Schema::table('online_registration_settings', function (Blueprint $table) {
                $table->dropColumn([
                    'new_student_enabled',
                    'old_student_enabled',
                    'new_student_registration_fee',
                    'old_student_registration_fee',
                    'payment_required'
                ]);
            });
        }
    }
}
