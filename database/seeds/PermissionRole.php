<?php
use Illuminate\Database\Seeder;

class PermissionRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionsCount = \App\Models\Permission::all()->count();
        for ($x = 1; $x <= $permissionsCount; $x++) {
            DB::table('permission_role')->insert([
                ['permission_id'=> $x, 'role_id' => 1]
            ]);
        }
    }
}
