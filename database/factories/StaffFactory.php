<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\Staff::class, function (Faker $faker) {
    return [
        'created_by'        => 1,
        'reg_no'        => str_random(10),
        'join_date'     => today(),
        'designation'       => 1,
        'first_name'        => 'First',
        'last_name'     => 'Last',
        'date_of_birth'     => '2020-09-11',
        'gender'        => 'Male',
        'nationality'       => 'Nepali',
        'mother_tongue'     => 'Maithili',
        'address'       =>'KTM',
        'state'     => '2',
        'country'       => 'Nepal',
        'mobile_1'      => rand(0,10000),
        'qualification'     => 'MBA',
        'email' => $faker->unique()->safeEmail,
    ];
});
