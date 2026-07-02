<?php
namespace App\Traits;

use App\Models\Day;
use App\Models\Month;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Nilambar\NepaliDate\NepaliDate;

trait DateTimeScope{

    use NumberToWordScope;

    public function getActiveYear()
    {
        $year = Year::where('active_status',1)->first();
        if ($year) {
            return $year->title;
        }else{
            return Carbon::now()->format('Y');
        }
    }

    public function getYearById($id)
    {
        $year = Year::find($id);
        if ($year) {
            return $year->title;
        }else{
            return "Unknown";
        }
    }

    public function getMonthById($id)
    {
        $month = Month::find($id);
        if ($month) {
            return $month->title;
        }else{
            return "Unknown";
        }
    }

    public function getDayById($id)
    {
        $day = Day::find($id);
        if ($day) {
            return $day->title;
        }else{
            return "Unknown";
        }
    }

    public function activeYears()
    {
        $years = Year::Active()->orderBy('title')->pluck('title','id')->toArray();
        return array_prepend($years,'Select Year','0');
    }

    public function activeMonths()
    {
        $years = Month::Active()->pluck('title','id')->toArray();
        return array_prepend($years,'Select Year','0');
    }


    public function dateToWord($date)
    {
        $th = array(
            1 => "first", 2 => "second", 3 => "third", 4 => "fourth", 5 => "fifth", 6 => "sixth",
            7 => "seventh", 8 => "eighth", 9 => "nineth", 10 => "tenth", 11 => "eleventh", 12 => "twelfth", 13 => "thirteenth",
            14 => "fourteenth", 15 => "fifteenth", 16 => "sixteenth", 17 => "seventeenth", 18 => "eighteenth", 19 => "nineteenth", 20 => "twentyth"
        );

        $ones = array(
            1 => "one", 2 => "two", 3 => "three", 4 => "four", 5 => "five", 6 => "six",
            7 => "seven", 8 => "eight", 9 => "nine", 10 => "ten", 11 => "eleven", 12 => "twelve", 13 => "thirteen",
            14 => "fourteen", 15 => "fifteen", 16 => "sixteen", 17 => "seventeen", 18 => "eighteen", 19 => "nineteen"
        );

        $tens = array(
            1 => "ten",2 => "twenty", 3 => "thirty", 4 => "forty", 5 => "fifty",
            6 => "sixty", 7 => "seventy", 8 => "eighty", 9 => "ninety"
        );

        $dateString = $date;

        /*$day = date("j", strtotime($dateString));
        $day = $this->numberTowords($day);*/

        $day = date("j", strtotime($dateString));
        if ($day <= 20) $day = $th[$day];
        if($day > 20 ){
            $day = strval($day);
            //dd($day);
            $second = intval($day[1]);
            $str1 = $tens[intval($day[0])];
            //$secNum = $th[intval($day[1])];
            if(intval($day[1]) ==0){
                $str2='';
            }else{
                $str2 = $th[intval($day[1])];
            }
            $day = $str1." ".$str2;
        }

        $month = date("F", strtotime($dateString));
        $year = strval(date("Y", strtotime($dateString)));
        if($year < 2000) {
            $first_half = $this->numberTowords(intval($year[0] . $year[1]));
            $second_half = $this->numberTowords(intval($year[2] . $year[3]));
            $years = $first_half." ".$second_half;
        }else{
            $years = $this->convertNumberToWord($year);

        }

        $dateInwordString = $day." ".strtolower($month)." ".$years;
        return  Str::upper($dateInwordString);
    }

    /*public function dateToWordBackup($date)
    {
        $th = array(
            1 => "first", 2 => "second", 3 => "third", 4 => "fourth", 5 => "fifth", 6 => "sixth",
            7 => "seventh", 8 => "eighth", 9 => "nineth", 10 => "tenth", 11 => "eleventh", 12 => "twelfth", 13 => "thirteenth",
            14 => "fourteenth", 15 => "fifteenth", 16 => "sixteenth", 17 => "seventeenth", 18 => "eighteenth", 19 => "nineteenth", 20 => "twentyth"
        );

        $ones = array(
            1 => "one", 2 => "two", 3 => "three", 4 => "four", 5 => "five", 6 => "six",
            7 => "seven", 8 => "eight", 9 => "nine", 10 => "ten", 11 => "eleven", 12 => "twelve", 13 => "thirteen",
            14 => "fourteen", 15 => "fifteen", 16 => "sixteen", 17 => "seventeen", 18 => "eighteen", 19 => "nineteen"
        );

        $tens = array(
            1 => "ten",2 => "twenty", 3 => "thirty", 4 => "forty", 5 => "fifty",
            6 => "sixty", 7 => "seventy", 8 => "eighty", 9 => "ninety"
        );

        $dateString = $date;

        /*$day = date("j", strtotime($dateString));
        $day = $this->numberTowords($day);

        $day = date("j", strtotime($dateString));
        if ($day <= 20) $day = $th[$day];
        if($day > 20 ){
            $day = strval($day);
            $second = intval($day[1]);
            $str1 = $tens[intval($day[0])];
            $str2 = $th[intval($day[1])];
            $day = $str1." ".$str2;
        }

        $month = date("F", strtotime($dateString));
        $year = strval(date("Y", strtotime($dateString)));
        $first_half = $this->numberTowords(intval($year[0].$year[1]));
        $second_half = $this->numberTowords(intval($year[2].$year[3]));
        $years = $first_half." ".$second_half;

        $dateInwordString = $day." ".strtolower($month)." ".$years;
        return  Str::upper($dateInwordString);
    }*/



    /*public function dateToWord1($date)
    {
        $th = array(
            1 => "first", 2 => "second", 3 => "third", 4 => "fourth", 5 => "fifth", 6 => "sixth",
            7 => "seventh", 8 => "eighth", 9 => "nineth", 10 => "tenth", 11 => "eleventh", 12 => "twelfth", 13 => "thirteenth", 14 => "fourteenth", 15 => "fifteenth", 16 => "sixteenth", 17 => "seventeenth", 18 => "eighteenth", 19 => "nineteenth", 20 => "twentyth"
        );

        $ones = array(
            1 => "one", 2 => "two", 3 => "three", 4 => "four", 5 => "five", 6 => "six",
            7 => "seven", 8 => "eight", 9 => "nine", 10 => "ten", 11 => "eleven", 12 => "twelve", 13 => "thirteen",
            14 => "fourteen", 15 => "fifteen", 16 => "sixteen", 17 => "seventeen", 18 => "eighteen", 19 => "nineteen"
        );

        $tens = array(
            1 => "ten",2 => "twenty", 3 => "thirty", 4 => "forty", 5 => "fifty",
            6 => "sixty", 7 => "seventy", 8 => "eighty", 9 => "ninety"
        );

        //$dateString = '25-04-2020';
        $dateString = $date;
        //dd($dateString);
        //"07-06-2020"

        $day = date("j", strtotime($dateString));
        //dd($day);
        if ($day <= 20) $day = $th[$day];
        if($day > 20 ){
            $day = strval($day);
            $second = intval($day[1]);
            $str1 = $tens[intval($day[0])];
            $str2 = $th[intval($day[1])];
            $day = $str1." ".$str2;
        }

        $month = date("F", strtotime($dateString));
//        dd($month);
        $year = strval(date("Y", strtotime($dateString)));
        //dd($year);

        $first_half = intval($year[0].$year[1]);
        //dd($first_half);
        if($first_half < 20 ) $first_half = $ones[$first_half];
        if($first_half >= 20) {
            $first_half = $tens[$year[0]]." ".$ones[$year[1]];
        }

        $second_half = intval($year[2].$year[3]);
        if($second_half < 20 ) $second_half = $ones[$second_half];
        if($second_half >= 20) {
            $second_half = $tens[$year[2]]." ".$ones[$year[3]];
        }

        $years = $first_half." ".$second_half;
        return $day." ".strtolower($month)." ".$years;

    }*/

    public function convertAdToBs($date)
    {
        //dd($date);
        $obj = new NepaliDate();
        $split = explode('-',$date);
        $y = $split[0];
        $m = $split[1];
        $d = $split[2];

        $date = $obj->convertAdToBs($y, $m, $d);
        //dd($date['year'].'-'.$date['month'].'-'.$date['day']);

        return $date = $date['year'].'-'.$date['month'].'-'.$date['day'];

    }

    public function convertDateWithMonth($date)
    {
        //dd($date);
        $obj = new NepaliDate();
        $split = explode('-',$date);
        $y = $split[0];
        $m = $split[1];
        $d = $split[2];

        $date = $obj->convertAdToBs($y, $m, $d);
        $monthName = $this->getMonthText($date['month'], 'en');
        return $date = $date['year'].' '.$monthName.' '.$date['day'];

    }

    private function getMonthText($month, $language = 'en')
    {
        $output = '';

        $details = $this->getNepaliMonthDetails();

        if (isset($details[ $month ][ $language ])) {
            $output = $details[ $month ][ $language ];
        }

        return $output;
    }

    /**
     * Get month details.
     *
     * @since 1.0.0
     *
     * @return array Month details.
     */
    private function getNepaliMonthDetails()
    {
        $output = array(
            '1'  => array(
                'en' => 'Baishakh',
                'np' => 'बैसाख',
            ),
            '2'  => array(
                'en' => 'Jeth',
                'np' => 'जेठ',
            ),
            '3'  => array(
                'en' => 'Ashar',
                'np' => 'असार',
            ),
            '4'  => array(
                'en' => 'Shrawan',
                'np' => 'श्रावन',
            ),
            '5'  => array(
                'en' => 'Bhadra',
                'np' => 'भाद्र',
            ),
            '6'  => array(
                'en' => 'Ashoj',
                'np' => 'असोज',
            ),
            '7'  => array(
                'en' => 'Kartik',
                'np' => 'कार्तिक',
            ),
            '8'  => array(
                'en' => 'Mangshir',
                'np' => 'मंसिर',
            ),
            '9'  => array(
                'en' => 'Poush',
                'np' => 'पुष',
            ),
            '10' => array(
                'en' => 'Magh',
                'np' => 'माघ',
            ),
            '11' => array(
                'en' => 'Falgun',
                'np' => 'फाल्गुण',
            ),
            '12' => array(
                'en' => 'Chaitra',
                'np' => 'चैत्र',
            ),
        );

        return $output;
    }


}