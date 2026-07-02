<?php
namespace App\Traits\SmsGateway;

use Twilio\Rest\Client;


trait AmetechSolution{

    /*Sparrow SMS*/
    public function AmetechSolutionSMS($contactNumbers, $message)
    {

        /*get Setting*/
        $smsSetting = $this->getSmsSetting();
        $sms = json_decode($smsSetting['AmetechSolution'],true);
        $User = $sms['User'];
        $Auth = $sms['Auth'];
        $Sender = $sms['Sender'];
        //$contactNumbers = implode(',',$contactNumbers);
        $contactNumbers = str_replace(' ','',$contactNumbers);
        //$message = str_replace(' ','%26',$message);
        //$smsurl="http://".$host."/vapi/pushsms?usr=".$user."&authkey=".$pwd."&sender=".urlencode($sender)."&mobile=".$phoneno."&text=".urlencode($message)."&rpt=".$rpt;
        //	$response = file_get_contents($smsurl);
        //	return $response;
        //http://zipping.vispl.in/vapi/pushsms?user=test&authkey=test&sender=MPNEDU&mobile=test&text=test&rpt=1
        $url = "http://zipping.vispl.in/vapi/pushsms?user=".$User."&authkey=".$Auth."&sender=".urlencode($Sender)."&mobile=".$contactNumbers."&text=".urlencode($message)."&rpt=1";

        $response = file_get_contents($url);
    }

}