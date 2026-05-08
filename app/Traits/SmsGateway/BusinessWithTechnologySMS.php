<?php
namespace App\Traits\SmsGateway;

trait BusinessWithTechnologySMS{


    public function BwTechSMS($contactNumbers, $message)
    {
        /*get Setting*/
        $smsSetting = $this->getSmsSetting();
        $sms = json_decode($smsSetting['BusinessWithTechnology'],true);
        $api_key    = $sms['ApiKey'];
        $from    = $sms['SenderId'];
        $campaign    = $sms['Campaign'];
        $routeId    = $sms['RouteId'];
        $type    = $sms['Type'];

        $message = str_replace(' ','%20',$message);
        //text
        if($type == 'unicode' || $type == 'UNICODE' || $type == 'Unicode'){
            $type='unicode';
            $api_url = "https://sms.businesswithtechnology.com/smsapi/index.php?key=".$api_key."&campaign=".$campaign."&routeid=".$routeId."&type=".$type."&contacts=".$contactNumbers."&senderid=".$from."&msg=".$message;
        }else{
            $type='text';
            $api_url = "https://sms.businesswithtechnology.com/smsapi/index.php?key=".$api_key."&campaign=".$campaign."&routeid=".$routeId."&type=".$type."&contacts=".$contactNumbers."&senderid=".$from."&msg=".$message;
        }
        //$api_url = "https://sms.businesswithtechnology.com/smsapi/index.php?key=264A0C9D94CC57&campaign=XXXXXX&routeid=XXXXXX&type=text&contacts=98411XXXXX,98012XXXXX&senderid=XXXXXX&msg=Hello+People%2C+have+a+great+day";
        //$api_url = "https://sms.businesswithtechnology.com/smsapi/index.php?key=264A0C9D94CC57&campaign=XXXXXX&routeid=XXXXXX&type=unicode&contacts=98412XXXXX,98012XXXXX&senderid=XXXXXX&msg=Nepali+-+%C3%A0%C2%A4%C2%A8%C3%A0%C2%A5%C2%87%C3%A0%C2%A4%C2%AA%C3%A0%C2%A4%C2%BE%C3%A0%C2%A4%C2%B2%C3%A0%C2%A4%C2%BF%2C+Hindi++-+%C3%A0%C2%A4%C2%B9%C3%A0%C2%A4%C2%BF%C3%A0%C2%A4%C2%82%C3%A0%C2%A4%C2%A6%C3%A0%C2%A5%C2%80+%2C+Chinese+-++%C3%A7%C2%97%C2%B4%C3%A5%C2%91%C2%A2%C3%A8%C2%89%C2%B2+%C3%AF%C2%BC%C2%8CRussian+-+%C3%91%C2%80%C3%91%C2%83%C3%91%C2%81%C3%91%C2%81%C3%90%C2%B8%C3%90%C2%B0%C3%90%C2%BD";
//        dd($api_url);
        //Submit to server
        $response = file_get_contents( $api_url);
        echo $response;
    }


}