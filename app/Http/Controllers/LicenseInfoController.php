<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Controllers;

use App\Models\Attendence;
use App\Models\AttendenceMaster;
use Carbon\Carbon;
use ViewHelper;

class LicenseInfoController extends CollegeBaseController
{
    protected $base_route = 'license-info';
    protected $view_path = 'license-info';
    protected $panel = 'License Info';



    public function __construct()
    {

    }

    public function index()
    {

        //LicenseInfoController
        $connected = @fsockopen("www.google.com", 80); //website, port  (try 80 or 443)
        if ($connected){

        }else{
            return true;
        }

        $code = getenv('PURCHASE_CODE');
        $personalToken = decrypt('eyJpdiI6ImNuaUtWRGZTK3hOcWR3VkJMOWd4Znc9PSIsInZhbHVlIjoibzhrRWlxVURtelpWdkJQbldVZEx6QTExTmxKTXE5V3U1STdhSjMyRUo5eTM4V2lBaGI0SUo3eTFUMnVjcHpNKyIsIm1hYyI6IjY5YTY1MGY4NWJiNWRjMmQ4ZDQ2MDhkMjU1YWQ0Mzc5YjEzZDQ4NjY0NzYzYzk0ZmFmMzI1NGQxNzQ2ZDY2NTkifQ==');
        $userAgent = "Purchase code verification on unlimitededufirm.com";


        if(isset($code) && $code != ''){

            $code = trim($code);

            if (!preg_match("/^(\w{8})-((\w{4})-){3}(\w{12})$/", $code)) {
                //throw new Exception("Invalid code");
                abort(401, "Input Correct Purchase Code on .env PURCHASE_CODE variable");
            }

            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => "https://api.envato.com/v3/market/author/sale?code={$code}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 20,

                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$personalToken}",
                    "User-Agent: {$userAgent}"
                )
            ));

            $response = @curl_exec($ch);

            if (curl_errno($ch) > 0) {
                //throw new Exception("Error connecting to API: " . curl_error($ch));
                abort(401, "API Err");
            }

            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);



            if ($responseCode === 400) {
                abort(401, "A parameter or argument in the request was invalid");
            }

            if ($responseCode === 401) {
                abort(401, "The authorization header is missing or malformed. Verify that your code is correct");
            }

            if ($responseCode === 403) {
                abort(401, "The personal token is incorrect or does not have the required permission(s)");
            }

            if ($responseCode === 404) {
                abort(401, "The purchase code was invalid, not real");
            }

            if ($responseCode !== 200) {
                abort(401, "Failed to validate code due to an error: HTTP {$responseCode}");
            }


            $body = @json_decode($response);

            if ($body === false && json_last_error() !== JSON_ERROR_NONE) {
                abort(401, "Error parsing response");
            }

            $soldDate = Carbon::parse($body->sold_at)->addYear();
            $supportEnd = Carbon::parse($body->supported_until);
            $date = new Carbon;
            if($soldDate > $supportEnd){
                $maxDate = $soldDate;
            }else{
                $maxDate = $supportEnd;
            }

            if($date > $maxDate)
            {
                //its already expired
                abort(401, "License Expired ");
            }else{
                //$body->expire = Carbon::parse($expire)->format('d-m-Y');

                //return $body;
            }
        }else{
            abort(401, "Input Correct Purchase Code on .env PURCHASE_CODE variable");
        }
        return view(parent::loadDataToView($this->view_path.'.index'), compact('body'));

    }

}
