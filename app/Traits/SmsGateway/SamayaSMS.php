<?php
namespace App\Traits\SmsGateway;

trait SamayaSMS{


    public function SamayaSMS($contactNumbers, $message)
    {
        /*get Setting*/
        $smsSetting = $this->getSmsSetting();
        $sms = json_decode($smsSetting['SamayaSms'],true);
        $api_key    = $sms['ApiKey'];
        $from    = $sms['SenderId'];
        $campaign    = $sms['Campaign'];
        $routeId    = $sms['RouteId'];
        $type    = $sms['Type'];

        $message = str_replace(' ','%20',$message);
        //text
        $api_url = "https://samayasms.com.np/smsapi/index.php?key=".$api_key."&campaign=".$campaign."&routeid=".$routeId."&type=".$type."&contacts=".$contactNumbers."&senderid=".$from."&msg=".$message;

        //Submit to server
        $response = file_get_contents( $api_url);
        echo $response;
    }


}