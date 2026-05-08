<?php
namespace App\Traits\SmsGateway;

trait SmsToSMS{

    /*Sparrow SMS*/
    public function SmsToSMS($contactNumbers, $message)
    {
        /*get Setting*/
        $smsSetting = $this->getSmsSetting();
        $sms = json_decode($smsSetting['SmsToSMS'], true);

        $api_token = $sms['ApiToken'];
        $sender = $sms['Sender'];;
        $message = $message;
        $to = $contactNumbers;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sms.to/sms/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n    \"message\": \"$message\",\n    \"to\": \"$to\",\n    \"sender_id\": \"$sender\"    \n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Accept: application/json",
                "Authorization:Bearer $api_token"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        //echo $response;
    }
}