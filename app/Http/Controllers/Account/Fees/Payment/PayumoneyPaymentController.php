<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\Account\Fees\Payment;
use App\Http\Controllers\CollegeBaseController;
use App\Models\FeeCollection;
use App\Models\PaymentSetting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayumoneyPaymentController extends CollegeBaseController
{

    protected $base_route = 'account.fees';
    protected $view_path = 'account.fees';
    protected $panel = 'PayuMoney';

    public function payumoneyForm(Request $request)
    {
        $data=[];
        $student = Student::select('students.id','students.reg_no','students.email','students.first_name','students.last_name','ai.mobile_1')
            ->where('students.id',$request->student_id)
            ->join('addressinfos as ai','ai.students_id','=','students.id')
            ->first();

        if($student) {
            $reg = $student->reg_no;
            $amount = $request->net_balance;
            $fee_masters_id = $request->fee_masters_id;
            //$productInfo = 'REG NO: ' . $reg . ' | : ' . $request->fee_masters_id . ' | PAY FOR: ' . $request->description;
            $productInfo = [
                'STUD_ID'        => $request->student_id,
                'REG_NO'        => $reg,
                'FEE_MASTER_ID' => $request->fee_masters_id,
                'DESCRIPTION'   => $request->description
            ];
            $productInfo = json_encode($productInfo);

            $data = [
                'student_id' => $request->student_id,
                'fee_masters_id' => $fee_masters_id,
                'amount' => $amount,
                'email' => $student->email,
                'firstname' => $student->first_name,
                'lastname' => $student->last_name,
                'phone' => $student->mobile_1,
                'productinfo' => $productInfo,
            ];
        }

        $data['payment_setting'] = PaymentSetting::where('identity','PayUMoney')->first();


        return view(parent::loadDataToView('account.fees.payment.payumoney.form'), compact('data'));

    }

    public function payumoneyPaymentSuccess(Request $request)
    {
        //dd('Payment Successfully done!');

        $net_balance = $request->get('net_balance');
        $description = $request->get('description');
        $student_id = $request->get('student_id');
        $fee_masters_id = $request->get('fee_masters_id');
        $date = Carbon::now()->format('Y-m-d');

        /*Preapre Data for Store*/
       /* $studInfo = json_decode($request->productinfo, true);
        $student_id = $studInfo['STUD_ID'];
        $fee_masters_id = $studInfo['FEE_MASTER_ID'];
        $date = Carbon::now()->format('Y-m-d');*/
        $responseJson = json_encode($request->all());

        /*for verification*/
        $status         = $request->status;
        $firstname      = $request->firstname;
        $amount         = $request->amount;
        $txnid          = $request->txnid;

        $posted_hash    = $request->hash;
        $key            = $request->key;
        $productinfo    = $request->productinfo;
        $email          = $request->email;

        /*get salt*/
        $payumoneySetting = parent::getPaymentSetting();
        $payumoney = json_decode($payumoneySetting['PayUMoney'],true);
        $MERCHANT_KEY   = $payumoney['Merchant_Key'];
        $SALT           = $payumoney['Merchant_Salt'];
        $salt           = $SALT;
        // Salt should be same Post Request
        //hashSequence = salt|status||||||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key
        //$hash = hash("sha512", $hashSequence);
        //Where salt is available on the PayUMoney dashboard.
        If (isset($request->additionalCharges)) {
            $additionalCharges= $request->additionalCharges;
            $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
        } else {
            $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
        }
        $hash = hash("sha512", $retHashSeq);
        if ($hash != $posted_hash) {
            $message = "Invalid Transaction. Please try again";
            $request->session()->flash($this->message_warning, $message);
        } else {

            $feecollect =  FeeCollection::create([
                'students_id' => $student_id,
                'fee_masters_id' => $fee_masters_id,
                'date' => $date,
                'paid_amount' => $net_balance,
                'discount' => 0,
                'fine' => 0,
                'payment_method' => 'PayUmoney',
                'note' => 'Online Payment : PayUmoney',
                'created_by' => auth()->user()->id
            ]);

            if ($feecollect) {
                $request->session()->flash($this->message_success, 'Successfully Charge : ' . $net_balance);
            } else {
                $request->session()->flash($this->message_warning, 'Not Collect Yet');
            }

        }

        return back();

    }

    public function payumoneyPaymentFailure(Request $request)
    {
        dd('fail');
        $status         = $request->status;
        $firstname      = $request->firstname;
        $amount         = $request->amount;
        $txnid          = $request->txnid;

        $posted_hash    = $request->hash;
        $key            = $request->key;
        $productinfo    = $request->productinfo;
        $email          = $request->email;

        /*get salt*/
        $payumoneySetting = parent::getPaymentSetting();
        $payumoney = json_decode($payumoneySetting['PayUMoney'],true);
        $MERCHANT_KEY   = $payumoney['Merchant_Key'];
        $SALT           = $payumoney['Merchant_Salt'];
        $salt           = $SALT;

        // Salt should be same Post Request

        If (isset($_POST["additionalCharges"])) {
            $additionalCharges=$_POST["additionalCharges"];
            $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
        } else {
            $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
        }
        $hash = hash("sha512", $retHashSeq);

        if ($hash != $posted_hash) {
            $message = "Invalid Transaction. Please try again";
            $request->session()->flash($this->message_warning, $message);
        } else {
            $message = "Your payment status is ". $status ;
            //echo "<h4>Your transaction id for this transaction is ".$txnid.". You may try making the payment by clicking the link below.</h4>";
            $request->session()->flash($this->message_warning, $message);
        }

        return back();
    }
}