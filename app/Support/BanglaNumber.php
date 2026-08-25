<?php

namespace App\Support;

/**
 * Numbers written out in Bangla, the way the bank letter ends: "কথায় : ষোল হাজার একশত টাকা"।
 *
 * Bengali counts in lakh and crore, not in millions, and it has a distinct word for every number
 * up to ninety-nine rather than building them from tens and units - উনিশ is not "ten nine". So the
 * only honest way is to hold all hundred words and compose above that.
 */
class BanglaNumber
{
    /** 0-99, each with its own name. */
    protected static $words = [
        'শূন্য', 'এক', 'দুই', 'তিন', 'চার', 'পাঁচ', 'ছয়', 'সাত', 'আট', 'নয়',
        'দশ', 'এগারো', 'বারো', 'তেরো', 'চৌদ্দ', 'পনেরো', 'ষোল', 'সতেরো', 'আঠারো', 'উনিশ',
        'বিশ', 'একুশ', 'বাইশ', 'তেইশ', 'চব্বিশ', 'পঁচিশ', 'ছাব্বিশ', 'সাতাশ', 'আটাশ', 'ঊনত্রিশ',
        'ত্রিশ', 'একত্রিশ', 'বত্রিশ', 'তেত্রিশ', 'চৌত্রিশ', 'পঁয়ত্রিশ', 'ছত্রিশ', 'সাঁইত্রিশ', 'আটত্রিশ', 'ঊনচল্লিশ',
        'চল্লিশ', 'একচল্লিশ', 'বিয়াল্লিশ', 'তেতাল্লিশ', 'চুয়াল্লিশ', 'পঁয়তাল্লিশ', 'ছেচল্লিশ', 'সাতচল্লিশ', 'আটচল্লিশ', 'ঊনপঞ্চাশ',
        'পঞ্চাশ', 'একান্ন', 'বায়ান্ন', 'তিপ্পান্ন', 'চুয়ান্ন', 'পঞ্চান্ন', 'ছাপ্পান্ন', 'সাতান্ন', 'আটান্ন', 'ঊনষাট',
        'ষাট', 'একষট্টি', 'বাষট্টি', 'তেষট্টি', 'চৌষট্টি', 'পঁয়ষট্টি', 'ছেষট্টি', 'সাতষট্টি', 'আটষট্টি', 'ঊনসত্তর',
        'সত্তর', 'একাত্তর', 'বাহাত্তর', 'তিয়াত্তর', 'চুয়াত্তর', 'পঁচাত্তর', 'ছিয়াত্তর', 'সাতাত্তর', 'আটাত্তর', 'ঊনআশি',
        'আশি', 'একাশি', 'বিরাশি', 'তিরাশি', 'চুরাশি', 'পঁচাশি', 'ছিয়াশি', 'সাতাশি', 'আটাশি', 'ঊননব্বই',
        'নব্বই', 'একানব্বই', 'বিরানব্বই', 'তিরানব্বই', 'চুরানব্বই', 'পঁচানব্বই', 'ছিয়ানব্বই', 'সাতানব্বই', 'আটানব্বই', 'নিরানব্বই',
    ];

    /**
     * The whole amount in words, ending in টাকা.
     *
     * Paisa are dropped rather than spelled: every figure in these letters is whole taka, and a
     * stray "শূন্য পয়সা" on a bank letter reads like a mistake.
     */
    public static function taka($amount)
    {
        $taka = (int) round((float) $amount);
        $words = static::words($taka);

        return $words . ' টাকা';
    }

    /** Just the number. */
    public static function words($number)
    {
        $number = (int) $number;

        if ($number < 0)   { return 'ঋণাত্মক ' . static::words(-$number); }
        if ($number < 100) { return static::$words[$number]; }

        $parts = [];

        /* Crore and lakh first - this is where Bengali parts company with English grouping. */
        foreach ([10000000 => 'কোটি', 100000 => 'লক্ষ', 1000 => 'হাজার'] as $unit => $name) {
            if ($number >= $unit) {
                $count  = intdiv($number, $unit);
                $number = $number % $unit;
                $parts[] = static::words($count) . ' ' . $name;
            }
        }

        /* Hundreds join their word without a space: একশত, দুইশত. */
        if ($number >= 100) {
            $parts[] = static::$words[intdiv($number, 100)] . 'শত';
            $number  = $number % 100;
        }

        if ($number > 0) {
            $parts[] = static::$words[$number];
        }

        return implode(' ', $parts);
    }

    /** Bengali digits, for the figures printed beside the words. */
    public static function digits($value)
    {
        return strtr((string) $value, [
            '0' => '০', '1' => '১', '2' => '২', '3' => '৩', '4' => '৪',
            '5' => '৫', '6' => '৬', '7' => '৭', '8' => '৮', '9' => '৯',
        ]);
    }
}
