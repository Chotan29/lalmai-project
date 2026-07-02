<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPublicVerifyToCertificateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('certificate_templates') && !Schema::hasColumn('certificate_templates', 'public_verify')) {
            Schema::table('certificate_templates', function (Blueprint $table) {
                $table->boolean('public_verify')->default(1)->after('background_status');
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
        if (Schema::hasTable('certificate_templates') && Schema::hasColumn('certificate_templates', 'public_verify')) {
            Schema::table('certificate_templates', function (Blueprint $table) {
                $table->dropColumn('public_verify');
            });
        }
    }
}