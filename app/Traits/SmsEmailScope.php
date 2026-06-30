<?php
namespace App\Traits;

use App\Mail\EmailAlerts;
use App\Models\EmailSetting;
use App\Models\SmsSetting;
use App\Models\Student;

use App\Traits\SmsGateway\AakashNepalSMS;
use App\Traits\SmsGateway\AfricasTalkingSMS;
use App\Traits\SmsGateway\AlphaSMS;
use App\Traits\SmsGateway\AmetechSolution;
use App\Traits\SmsGateway\AmetechSolutionVideocon;
use App\Traits\SmsGateway\BudgetSmsNet;
use App\Traits\SmsGateway\CallFireSMS;
use App\Traits\SmsGateway\ClickatelSMS;
use App\Traits\SmsGateway\DigimilesSMS;
use App\Traits\SmsGateway\FullTimeSMS;
use App\Traits\SmsGateway\InitiativeAaayoSMS;
use App\Traits\SmsGateway\KeswaTechSMS;
use App\Traits\SmsGateway\LifetimeSMS;
use App\Traits\SmsGateway\MarketsmsPK;
use App\Traits\SmsGateway\MessageBirdSMS;
use App\Traits\SmsGateway\Msg91SMS;
use App\Traits\SmsGateway\Msg94SMS;
use App\Traits\SmsGateway\MsgClub;
use App\Traits\SmsGateway\MySmsDealSMS;
use App\Traits\SmsGateway\NexmoSMS;
use App\Traits\SmsGateway\SendpkSMS;
use App\Traits\SmsGateway\SmartSmsSolutionSMS;
use App\Traits\SmsGateway\SmsAPI;
use App\Traits\SmsGateway\SmsCluster;
use App\Traits\SmsGateway\SmsToSMS;
use App\Traits\SmsGateway\SparrowSMS;
use App\Traits\SmsGateway\springEdgeSMS;
use App\Traits\SmsGateway\TextLocalSMS;
use App\Traits\SmsGateway\TheSMSCentralSMS;
use App\Traits\SmsGateway\TwillioSMS;
use App\Traits\SmsGateway\AdaReachSMS;
use App\Traits\SmsGateway\SslWirelessSMS;

use App\Traits\SmsGateway\WhatsAppGreenAPI;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\AllEmail;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

use App\Mail\PaymentReceipt;


trait SmsEmailScope{
    protected $message_success = 'message_success';
    protected $message_warning = 'message_warning';
    use SparrowSMS;
    use InitiativeAaayoSMS;
    use Msg91SMS;
    use Msg94SMS;
    use KeswaTechSMS;
    use TwillioSMS;
    use SmsAPI;
    use MessageBirdSMS;
    use ClickatelSMS;
    use NexmoSMS;
    use CallFireSMS;
    use MsgClub;
    use DigimilesSMS;
    use TextLocalSMS;
    use SmartSmsSolutionSMS;
    use SendpkSMS;
    use LifetimeSMS;
    use SmsCluster;
    use MarketsmsPK;
    use BudgetSmsNet;
    use springEdgeSMS;
    use AfricasTalkingSMS;
    use TheSMSCentralSMS;
    use AakashNepalSMS;
    use FullTimeSMS;
    use AmetechSolution;
    use SmsToSMS;
    use MySmsDealSMS;
    use AlphaSMS;
    use WhatsAppGreenAPI;
    use AdaReachSMS;
    use SslWirelessSMS;

    /*SMS SENDER*/
    public function sendSMS($contactNumbers, $message)
    {
        if($contactNumbers == "")
            return back()->with($this->message_warning, "No Any Contact Found. So Message Not Send In This Time. Please Try Again.");

        /* TEST MODE: log SMS instead of sending, useful for local development */
        if(env('SMS_TEST_MODE', false)) {
            $logLine = '[' . now() . '] TO=' . (is_array($contactNumbers) ? implode(',', $contactNumbers) : $contactNumbers) . ' MSG=' . $message . PHP_EOL;
            file_put_contents(storage_path('logs/sms_test.log'), $logLine, FILE_APPEND | LOCK_EX);
            \Illuminate\Support\Facades\Log::info('SMS_TEST_MODE', ['to' => $contactNumbers, 'message' => $message]);
            return true;
        }

        /*get Setting – load-balance across all active gateways (random pick)*/
        $smsSetting     = SmsSetting::active()->inRandomOrder()->first();

        if($smsSetting == null)
            return back()->with($this->message_warning, "SMS Setting Not Detected. Please Setting Your SMS Detail.");

        $activeProvider = $smsSetting->identity;

        /*Switch Target SMS Service Provider*/
        switch ($activeProvider){
            case "Sparrow":
                return $this->sparrowSMS($contactNumbers, $message);

            case "InitiativeNepal":
                return $this->aayoSMS($contactNumbers, $message);

            case "Msg91":
                return $this->msg91SMS($contactNumbers, $message);

            case "Msg94":
                return $this->msg94SMS($contactNumbers, $message);

            case "KeswaTech":
                return $this->keswaSMS($contactNumbers, $message);

            case "Twilio":
                return $this->twilioSMS($contactNumbers, $message);

            case "MessageBird":
                return $this->messageBird($contactNumbers, $message);

            case "smsAPI":
                return $this->smsAPI($contactNumbers, $message);

            case "Clickatell":
                return $this->clickatelSMS($contactNumbers, $message);

            case "BudgetSmsNet":
                return $this->BudgetSMS($contactNumbers, $message);

            case "Nexmo":
                return $this->nexmoSMS($contactNumbers, $message);

            //todo::
            case "CallFire":
                return $this->callFireSMS($contactNumbers, $message);

            case "MsgClub":
                return $this->MsgClubSMS($contactNumbers, $message);

            case "Digimiles":
                return $this->digimilesSMS($contactNumbers, $message);

            case "Textlocal":
                return $this->textLocalSMS($contactNumbers, $message);

            case "SmartSMS":
                return $this->SmartSolutionSMS($contactNumbers, $message);

            case "SendPK":
                return $this->SendPkSMS($contactNumbers, $message);

            case "LifetimeSMS":
                return $this->LifeTimeSMS($contactNumbers, $message);

            case "SmsCluster":
                return $this->SmsClusterSMS($contactNumbers, $message);

            case "marketsmsPK":
                return $this->MarketSmsPK($contactNumbers, $message);

            case "springEdge":
                return $this->SpringEdge($contactNumbers, $message);

            case "africastalking":
                return $this->africastalkingSMS($contactNumbers, $message);

            case "TheSMSCentral":
                return $this->thesmscentralSMS($contactNumbers, $message);

            case "AakashNepal":
                return $this->aakashSMS($contactNumbers, $message);

            case "FullTimeBulk":
                return $this->fullTimeBulkSms($contactNumbers, $message);

            case "AmetechSolution":
                return $this->AmetechSolutionSMS($contactNumbers, $message);

            case "SmsToSMS":
                return $this->SmsToSMS($contactNumbers, $message);

            case "mySmsDeal":
                return $this->mySmsDeal($contactNumbers, $message);

            case "AlphaSMS":
                return $this->alphaSMS($contactNumbers, $message);

            case "GreenAPI":
                return $this->WhatsAppGreenAPISMS($contactNumbers, $message);

            case "AdaReach":
                return $this->adaReachSMS($contactNumbers, $message);

            case "SslWireless":
                return $this->sslWirelessSMS($contactNumbers, $message);

            case "GenNet":
                return $this->sslWirelessSMS($contactNumbers, $message, 'GenNet');

            default:
                return back()->with($this->message_warning, "No Any SMS Service Provider Active. Please, Active First.");

        }

    }


    /*EMAIL SENDING*/
    public function sendEmail($emailIds, $subject, $message){
        /*check internet connection for email sending*/
        $connection = Parent::checkConnection();
        if(!$connection)
            return back()->with($this->message_warning, $this->internet_status);

        $emailSetting = EmailSetting::first();

        if($emailSetting == null){
            return back()->with($this->message_warning, "Email Setting Not Detected. Please Setting Your Out Going Email Detail.");
        }

        if($emailSetting->status == "in-active")
            return back()->with($this->message_warning, "Email Setting Not Active. Please Active First.");

        /*sending email*/
        $emailIds = explode(',',$emailIds);

        /*sending email*/

        Mail::to($emailIds)->send(new EmailAlerts([
            'subject' => $subject,
            'message' => $message,
        ]));

        /*Mail Queue*/
       // dispatch(new AllEmail($emailIds, $subject, $message));
        //dispatch(new AllEmail($emailIds, $subject, $message))->delay(Carbon::now()->addSeconds(10));

    }

    /*Common Helper Function for this class*/
    public function emailFilter($emailCollections)
    {
        if(!empty($emailCollections)){
            //remove unwanted space from email address
            $emailCollections=array_map('trim',$emailCollections);
            $emailIds‍‍= [];
            foreach($emailCollections as $email){
                /*chek email id is correct or not if correct add on array other wise not*/
                $filterMail = filter_var($email,FILTER_VALIDATE_EMAIL);
                if($filterMail){
                    $emailIds[] = $filterMail;
                }
            }

            if(!isset($emailIds)) {
                return back()->with($this->message_warning, "No Any Email Id Found. Please, Select Your Target With Valid Email Group");
            }

            $emailIds = array_unique($emailIds);
            /*array to string separated with comma*/
            return $emailIds = implode(",",$emailIds);

        }else{
            return back()->with($this->message_warning, "No Any Email Id Found. Please, Select Your Target With Valid Email Group");
        }
    }

    public function contactFilter($contactNumbers){
        /*The Contact Number length and filter array*/
        /*$contactNumbers =array_values((array_filter($numbers, function($v){
            return strlen($v) == 10;
        })));*/
        /*Filter Duplicate Number get unique number*/
        $contactNumbers = array_unique($contactNumbers);
        /*array to string comma separated number*/
        return $contactNumbers = implode(",",$contactNumbers);
    }

    /*Check SMS CREDIT*/
    public function checkSmsCredit(Request $request)
    {
        /*Check Internet connectivity*/
        $connection = Parent::checkConnection();
        if(!$connection)
            return back()->with($this->message_warning, $this->internet_status);

        $smsSetting = SmsSetting::select('setting')->first();
        if($smsSetting == null){
            return back()->with($this->message_warning, "SMS Setting Not Detected. Please Setting Your SMS Detail First.");
        }

        $api_url = "http://api.sparrowsms.com/v2/credit/?" .
            http_build_query(array(
                'token' => $smsSetting->setting));
        $response = file_get_contents($api_url);
        $response = json_decode($response);

        if($response->credits_available > 0){
            return back()->with($this->message_success,  "You Have ".$response->credits_available." SMS CREDIT AVAILABLE");
        }else{
            return back()->with($this->message_warning, "You Have No Any SMS Credit/".$response->credits_available." SMS CREDIT AVAILABLE");
        }
    }

    /*Text Replace*/
    public function msgTextReplace($query, $message)
    {

    }

    protected function getSmsSetting()
    {
        $data['sms_setting'] = SmsSetting::where('status',1)->get();
        if(isset($data['sms_setting']) && $data['sms_setting']->count() > 0){
            $d = json_decode($data['sms_setting'],true);
            $manageSetting = array_pluck($d,'config','identity');
            return $manageSetting;
        }
    }

    public function sendPaymentReceipt($payment)
    {
        try {
            $student = Student::findOrFail($payment->students_id);
            //dd($student);
            
            Mail::to($student->email)
                //->cc(config('app.accounts_email')) // Optional CC to accounts department
                ->send(new PaymentReceipt($payment, $student));
                
        } catch (\Exception $e) {
            \Log::error("Failed to send payment receipt: ".$e->getMessage());
        }
    }


}