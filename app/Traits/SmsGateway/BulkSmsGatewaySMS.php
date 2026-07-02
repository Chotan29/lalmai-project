<?php
namespace App\Traits\SmsGateway;

trait BulkSmsGatewaySMS{


    public function BulkSmsGatewaySMS($contactNumbers, $message)
    {
        /*get Setting*/
        $smsSetting = $this->getSmsSetting();
        $sms = json_decode($smsSetting['BulkSmsGateway'],true);
        $username    = $sms['Username'];
        $password    = $sms['Password'];
        $sender    = $sms['Sender'];

        //Don't change below code use as it is
        $url="https://www.bulksmsgateway.in/sendmessage.php?user=".urlencode($username)."&password=".urlencode($password)."&mobile=".urlencode($contactNumbers)."&message=".urlencode($message)."&sender=".urlencode($sender)."&type=".urlencode('3');

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $curl_scraped_page = curl_exec($ch);

        curl_close($ch);
    }


}