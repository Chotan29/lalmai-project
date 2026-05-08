<?php

namespace App\Support;

use App\Models\Student;
use App\Models\Staff;

class PersonFinder
{
    public static function resolveByRegNo($regNo)
    {
        if (!$regNo) return null;
        if ($s = Student::findByRegNo($regNo)) return $s;
        if ($t = Staff::findByRegNo($regNo))   return $t;
        return null;
    }
}
