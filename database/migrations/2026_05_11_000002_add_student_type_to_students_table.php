<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStudentTypeToStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (!Schema::hasColumn('students', 'student_type')) {
                    $table->enum('student_type', ['new', 'old'])->default('new')->after('batch');
                }
                if (!Schema::hasColumn('students', 'registration_payment_status')) {
                    $table->enum('registration_payment_status', ['pending', 'completed'])->nullable()->after('student_type');
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
        if(Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn(['student_type', 'registration_payment_status']);
            });
        }
    }
}
