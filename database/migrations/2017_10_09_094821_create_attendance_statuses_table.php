<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceStatusesTable extends Migration
{
    public function up()
    {
        Schema::create('attendance_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 10)->unique();   // P, A, L, E, HL
            $table->string('label', 50);
            $table->string('color', 20)->nullable(); // e.g. #10B981
            $table->unsignedInteger('order')->default(0); // avoid 'sorting' error
            $table->timestamps();
        });

        // seed basics (works on MySQL/MariaDB; adjust if needed)
        // DB::table('attendance_statuses')->insert([
        //     ['code'=>'P',  'label'=>'Present',         'color'=>'#10B981', 'order'=>1, 'created_at'=>now(),'updated_at'=>now()],
        //     ['code'=>'A',  'label'=>'Absent',          'color'=>'#EF4444', 'order'=>2, 'created_at'=>now(),'updated_at'=>now()],
        //     ['code'=>'L',  'label'=>'Late',            'color'=>'#F59E0B', 'order'=>3, 'created_at'=>now(),'updated_at'=>now()],
        //     ['code'=>'E',  'label'=>'Early Leave',     'color'=>'#3B82F6', 'order'=>4, 'created_at'=>now(),'updated_at'=>now()],
        //     ['code'=>'HL', 'label'=>'Half Leave',      'color'=>'#6B7280', 'order'=>5, 'created_at'=>now(),'updated_at'=>now()],
        // ]);
    }

    public function down()
    {
        Schema::dropIfExists('attendance_statuses');
    }
}
