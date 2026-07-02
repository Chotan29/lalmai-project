<?php

use Illuminate\Database\Seeder;

class AcademicInfoLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('academic_info_levels')->insert([
            [
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'created_by' => 1,
                'title' => 'High School',
                'status' => 1
            ],
            [
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
                'created_by' => 1,
                'title' => 'Intermediate',
                'status' => 1
            ]
        ]);
    }
}



