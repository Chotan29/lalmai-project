<?php
namespace App\Traits;

use Carbon\Carbon;

trait PurchaseVerification {
    use EnvironmentScope;

    public function getPurchaseDetail()
    {
        /*
         $connected = @fsockopen("www.google.com", 80); //website, port  (try 80 or 443)
        if ($connected){

        }else{
            return true;
        }
        $dateCheck = Carbon::today()->format('Ymd');
        //if(getenv('CERTIFICATE_DATE')!=$dateCheck){
        if(getenv('CERTIFICATE_DATE') !== $dateCheck){
            //dd($dateCheck);
            //20221216
            //20221218
           // dd(getenv('CERTIFICATE_DATE') == $dateCheck);
            $this->setEnv('CERTIFICATE_DATE', $dateCheck);
            $licenseExpired = false;
            $lsupportExpired = false;
            $code = getenv('PURCHASE_CODE');
            $personalToken = decrypt('eyJpdiI6ImNuaUtWRGZTK3hOcWR3VkJMOWd4Znc9PSIsInZhbHVlIjoibzhrRWlxVURtelpWdkJQbldVZEx6QTExTmxKTXE5V3U1STdhSjMyRUo5eTM4V2lBaGI0SUo3eTFUMnVjcHpNKyIsIm1hYyI6IjY5YTY1MGY4NWJiNWRjMmQ4ZDQ2MDhkMjU1YWQ0Mzc5YjEzZDQ4NjY0NzYzYzk0ZmFmMzI1NGQxNzQ2ZDY2NTkifQ==');
            $userAgent = "Purchase code verification for Unlimited Edu Firm.";

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

                //Purchase+1Year
                if($date > $soldDate){
                    $this->setEnv('APP_STATUS', true);
                    $licenseExpired = true;
                }else{
                    $this->setEnv('APP_STATUS', false);
                }

                //support
                if($date > $supportEnd){
                    $this->setEnv('HELP_STATUS', true);
                    $lsupportExpired = true;
                }else{
                    $this->setEnv('HELP_STATUS', false);
                }

                if($licenseExpired or $lsupportExpired){
                    //dd(encrypt('License Issue ! Check System License Info Or Contact System Admin.'));
                    //dd(encrypt('License Issue ! Check System <a href="'. route('license-info') . '"> License Info </a>  Or Contact System Admin.'));

                    request()->session()->flash('message_warning', decrypt('eyJpdiI6IitjbUQ5Q2pnSks4WDFSdnpLSHFvcGc9PSIsInZhbHVlIjoiVnJoMkorZmt1U1BtXC91WHNHR0JqdjZVd3RVd0piXC8zcFlycW5ESWNrbXluU2ZTMjk1UlFoVUFOanc1RTlaOVRlNVg3Y1pOVkVrdDByRkdvemJTTmJcL0NaYnU5U29ESkJwbzVuN0w3ZUJBN3c9IiwibWFjIjoiNTRjN2Y4NDgyMmMxYWIzNTEzZGZjMDJhNDljN2JlMjhhOWRkYmRiOTIxZDk5YTY1YTIxNjk0OGZiZGU5MTUwOSJ9'));
                }
//                if($lsupportExpired){
//                    request()->session()->flash('message_info',decrypt('eyJpdiI6IlpiMnl4OUZsZkpFUm40OXpjbnI5RFE9PSIsInZhbHVlIjoiRjFDUmE0Z0lQb0Y0U01xMUo3ZXUydktTZUpveVNwaUVSRzFEVWlhdnFzRTJpb2I1YndTNm9RZ0UwQ1VIREVRc0JsZDkya0tvWHVLa1NnMnVEY29PMHRkVUROb3QxYjMwQ3Z2Zlg5N0x1cU09IiwibWFjIjoiZTg3NTE5MmNmMjUzYzNlZTIyNDZlZmI0NDI0ZDI5OGZmM2Q3ZjUzZWM4ZDNkODhlNzdlMjcwMzczMDVhZDQxOSJ9'));
//                }

            }else{
                abort(401, "Input Correct Purchase Code on .env PURCHASE_CODE variable");
            }
        }

         * */

    }

}



