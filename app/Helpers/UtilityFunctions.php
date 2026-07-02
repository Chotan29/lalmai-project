<?php

namespace App\Helpers;

class UtilityFunctions
{
    /**
     * Recursively convert stdClass objects to arrays
     *
     * @param mixed $input
     * @return mixed
     */
    public static function objectToArray($input)
    {
        if (is_object($input)) {
            $input = (array) $input;
        }
        if (is_array($input)) {
            return array_map([self::class, 'objectToArray'], $input);
        }
        return $input;
    }
}