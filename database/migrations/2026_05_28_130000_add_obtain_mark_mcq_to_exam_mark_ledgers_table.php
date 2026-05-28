<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddObtainMarkMcqToExamMarkLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('exam_mark_ledgers')) {
            return;
        }

        Schema::table('exam_mark_ledgers', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_mark_ledgers', 'obtain_mark_mcq')) {
                $table->float('obtain_mark_mcq')->default(0)->after('obtain_mark_practical');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('exam_mark_ledgers')) {
            return;
        }

        Schema::table('exam_mark_ledgers', function (Blueprint $table) {
            if (Schema::hasColumn('exam_mark_ledgers', 'obtain_mark_mcq')) {
                $table->dropColumn('obtain_mark_mcq');
            }
        });
    }
}
