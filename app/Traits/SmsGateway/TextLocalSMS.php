<?php
namespace App\Traits\SmsGateway;

use Rahulreghunath\Textlocal\Textlocal;
use Twilio\Rest\Client;


trait TextLocalSMS{

    /*Sparrow SMS*/
    public function textLocalSMS($contactNumbers, $message)
    {
        /*get Setting*/
        $smsSetting = $this->getSmsSetting();
        $sms = json_decode($smsSetting['Textlocal'],true);
        $apiKey = $sms['apiKey'];
        $sender = $sms['sender'];
        //$contactNumbers = implode(',',$contactNumbers);
        //$contactNumbers = str_replace(' ','',$contactNumbers);

       // require('../textlocal.class.php');

        //$textlocal = new Textlocal('iifadegreecollegelancaster@gmail.com', $apiKey);

//        $numbers = array(447123456789);
//        $sender = 'Textlocal';
//        $message = 'This is a message';
        //dd($apiKey);
        $url = "https://api.textlocal.in/send/?".'apikey='.$apiKey.'&numbers='.urlencode($contactNumbers).'&sender='.urlencode($sender).'&message='.rawurlencode($message);
//        $url = "https://api.textlocal.in/send/?".'apikey='.$apiKey.'&numbers='.urlencode($contactNumbers).'&sender='.urlencode($sender).'&message='.rawurlencode($message);
       // $url = 'apikey=' . $apiKey . '&numbers=' . $numbers . "&sender=" . $sender . "&message=" . $message;
        // Send the GET request with cURL
       // dd($url);
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
        // Process your response here
        //        $response = file_get_contents($url);

//        try {
//            $result = $textlocal->sendSms(array($contactNumbers), $message, $sender);
//            print_r($result);
//        } catch (Exception $e) {
//            die('Error: ' . $e->getMessage());
//        }



       // $sms = new Textlocal();

       // $sms->send($message,6363697483,$sender); //sender is optional
        //dd($contactNumbers);
//        https://api.textlocal.in/send/?apiKey=G0npbkelkeY-pe9fSYX91y8ImaLXiV2A1QQlU5M0lF&sender=SATYAA&numbers=919028121378&message=Dear%20Parent,%20abc
//        $url = "https://api.textlocal.in/send/?".'apiKey='.$apiKey.'&sender='.$sender.'&numbers='.$contactNumbers.'&message='.$message;
//        $response = file_get_contents($url);

        // Send the GET request with cURL
//        $ch = curl_init($url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $response = curl_exec($ch);
//        curl_close($ch);

        // Process your response here
       // echo $response;




        // Account details
        //$apiKey = urlencode($apiKey);

        // Message details
//        $numbers = $contactNumbers;
//        $sender = urlencode($sender);
//        $message = rawurlencode($message);
//
//        //$numbers = implode(',', $numbers);
//
//        // Prepare data for POST request
//        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
//
//        // Send the POST request with cURL
//        $ch = curl_init('https://api.textlocal.in/send/');
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $response = curl_exec($ch);
        //curl_close($ch);

        // Process your response here
        //echo $response;

    }

}