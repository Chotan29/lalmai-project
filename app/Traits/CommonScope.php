<?php
namespace App\Traits;

use App\Models\GuardianDetail;
use App\Models\Staff;
use App\Models\Student;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait CommonScope
{
    protected function profileImageSrc()
    {
        if (Auth::check()) {
            if (auth()->user()->hasRole('student')) {
                $id = auth()->user()->hook_id;
                $student = Student::select('student_image')->find($id);
                if ($student && $student->student_image) {
                    $profileImageSrc = 'images/studentProfile/' . $student->student_image;
                } else {
                    $profileImageSrc = null;
                }
            } elseif (auth()->user()->hasRole('guardian')) {
                $id = auth()->user()->hook_id;
                $guardian = GuardianDetail::select('guardian_image')->find($id);
                if ($guardian && $guardian->guardian_image) {
                    $profileImageSrc = 'images/parents/' . $guardian->guardian_image;
                } else {
                    $profileImageSrc = null;
                }
            } elseif (auth()->user()->hasRole('staff')) {
                $id = auth()->user()->hook_id;
                $staff = Staff::select('staff_image')->find($id);
                if ($staff && $staff->staff_image) {
                    $profileImageSrc = 'images/staff/' . $staff->staff_image;
                } else {
                    $profileImageSrc = null;
                }
            } else {
                $id = auth()->user()->id;
                $image = User::select('profile_image')->find($id);
                if ($image && $image->profile_image) {
                    $profileImageSrc = 'images/user/' . $image->profile_image;
                } else {
                    $profileImageSrc = null;
                }
            }
        } else {
            $profileImageSrc = null;
        }

        return $profileImageSrc;
    }

    public function getGreeting()
    {
        /* This sets the $time variable to the current hour in the 24 hour clock format */
        $time = date("H");
        /* Set the $timezone variable to become the current timezone */
        $timezone = date(env('APP_TIMEZONE', 'Asia/Katmandu'));
        /* If the time is less than 1200 hours, show good morning */
        if ($time < "12") {
            $greeting = "Good morning";
        } elseif ($time >= "12" && $time < "17") {
            /* If the time is greater than or equal to 1200 hours, but less than 1700 hours, show good afternoon */
            $greeting = "Good afternoon";
        } elseif ($time >= "17" && $time < "19") {
            /* Should the time be between or equal to 1700 and 1900 hours, show good evening */
            $greeting = "Good evening";
        } else {
            /* Finally, show good night if the time is greater than or equal to 1900 hours */
            $greeting = "Good night";
        }

        return $greeting;
    }

    public function randomNum($prefix, $size)
    {
        $key = '';
        $keys = range(0, 9);

        for ($i = 0; $i < $size; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $prefix . $key;
    }

    public function convertNumberToWord($num = false)
    {
        // Handle null or empty input
        if ($num === null || $num === '' || $num === false) {
            Log::warning('Invalid input to convertNumberToWord: null or empty', ['input' => $num]);
            return 'zero';
        }

        // Convert to string and clean input
        $num = str_replace([',', ' '], '', trim((string) $num));

        // Check if the input is numeric
        if (!is_numeric($num)) {
            Log::warning('Invalid non-numeric input to convertNumberToWord', ['input' => $num]);
            return 'invalid';
        }

        // Convert to float to handle decimals, then take integer part
        $num = (int) floor((float) $num);

        // Handle zero explicitly
        if ($num === 0) {
            return 'zero';
        }

        // Handle negative numbers
        if ($num < 0) {
            return 'negative ' . $this->convertNumberToWord(abs($num));
        }

        $words = [];
        $list1 = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
            'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
        $list2 = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
        $list3 = ['', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
            'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
            'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'];

        $num_length = strlen($num);
        $levels = (int) (($num_length + 2) / 3);
        $max_length = $levels * 3;
        $num = substr('00' . $num, -$max_length);
        $num_levels = str_split($num, 3);

        for ($i = 0; $i < count($num_levels); $i++) {
            $levels--;
            $hundreds = (int) ($num_levels[$i] / 100);
            $hundreds = ($hundreds ? $list1[$hundreds] . ' hundred' : '');
            $tens = (int) ($num_levels[$i] % 100);
            $singles = '';

            if ($tens < 20) {
                $singles = ($tens ? $list1[$tens] : '');
            } else {
                $singles = $list2[(int) ($tens / 10)];
                $tens = (int) ($tens % 10);
                $singles .= ($tens ? '-' . $list1[$tens] : '');
            }

            $words[] = trim($hundreds . ($hundreds && $singles ? ' ' : '') . $singles .
                        (($levels && (int) $num_levels[$i]) ? ' ' . $list3[$levels] : ''));
        }

        $words = array_filter($words); // Remove empty strings
        return implode(' ', $words);
    }
}