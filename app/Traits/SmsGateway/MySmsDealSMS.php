<?php
namespace App\Traits\SmsGateway;

trait MySmsDealSMS{


    public function mySmsDeal($contactNumbers, $message)
    {
        /*get Setting*/
        $smsSetting = $this->getSmsSetting();
        $sms = json_decode($smsSetting['mySmsDeal'],true);
        $LoginID    = $sms['LoginID'];
        $Password    = $sms['Password'];
        $Sender    = $sms['Sender'];
        $RouteId    = $sms['RouteId'];
        $Unicode    = $sms['Unicode'];
        $message = str_replace(' ','%20',$message);

        //http://198.15.103.106/API/pushsms.aspx?loginID=prakash&password=123456&mobile=6301518431&text=HI%20HOW%20ARE%20YOU&senderid=DEMOOO&route_id=1&Unicode=0&Template_id=*
        $url =  'http://198.15.103.106/API/pushsms.aspx?loginID=='.$LoginID.'&password='.$Password.'&mobile='.$contactNumbers.
            '&text='.$message.'&sender_id='.$Sender.'&route_id='.$RouteId.'&Unicode='.$Unicode.'&Template_id=*';
        $response = file_get_contents($url);
    }


}