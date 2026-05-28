<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMcqNumbersToSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('subjects')) {
            return;
        }

        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'mcq_number_theory')) {
                $table->unsignedInteger('mcq_number_theory')->nullable()->after('pass_mark_practical');
            }

            if (!Schema::hasColumn('subjects', 'mcq_number_practical')) {
                $table->unsignedInteger('mcq_number_practical')->nullable()->after('mcq_number_theory');
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
        if (!Schema::hasTable('subjects')) {
            return;
        }

        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'mcq_number_practical')) {
                $table->dropColumn('mcq_number_practical');
            }

            if (Schema::hasColumn('subjects', 'mcq_number_theory')) {
                $table->dropColumn('mcq_number_theory');
            }
        });
    }
}
