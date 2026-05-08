<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            DB::table('users')->insert([
                'name' => 'Super Admin',
                'email' => 'superadmin@edufirm.com',
                'password' => bcrypt('admin'),
                'status' => 1
            ]);


//        //faker data for testing
//        $faker = \Faker\Factory::create();
//
//        for($i=1;$i<=10000;$i++){
//            $user = new \App\User();
//                $user->name = $faker->name;
//                $user->email = rand(10,100000).$faker->email;
//                $user->password = $faker->password;
//                $user->status = 1;
//                $user->save();
//
//        }
    }
}
