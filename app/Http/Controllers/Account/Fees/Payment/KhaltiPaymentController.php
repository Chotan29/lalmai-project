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
use App\Models\OnlinePayment;
use App\Models\Student;
use App\Traits\UserScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;

class KhaltiPaymentController extends CollegeBaseController
{
    use UserScope;
    public function initiatePayment(Request $request) {

        //get settings
        $paymentSetting = parent::getPaymentSetting();
        if(!isset($paymentSetting)){
            $request->session()->flash($this->message_warning, 'Sorry, Setting Mismatch. Try it after some time.');
            return back();
        }

        $khalti = json_decode($paymentSetting['Khalti'],true);

        if(!isset($khalti['Mode']) && !isset($khalti['Public_Key']) && !isset($khalti['Secret_Key'])){
            $request->session()->flash($this->message_warning, 'Sorry, Setting Mismatch. Try it after some time.');
            return back();
        }else{
            $mode = $khalti['Mode'];
            if($mode == 'Test' || $mode == 'TEST'){
                $url = 'https://a.khalti.com/api/v2/';
            }elseif($mode == 'Live' || $mode == 'LIVE'){
                $url = 'https://khalti.com/api/v2/';
            }else{
                $url = '';
                $request->session()->flash($this->message_warning, 'Khalti mode is not setup now or Mismatch.');
                return back();
            }

            $Public_Key = $khalti['Public_Key'];
            $Secret_Key = $khalti['Secret_Key'];
        }

        //get student detail
        $student = Student::where('id',decrypt($request->get('student_id')))->first();
        $feeMaster = $student->feemaster()->sum('fee_amount');
        $feeCollection = $student->feeCollect()->get();
        $paidAmount = $feeCollection->sum('paid_amount');
        $discount = $feeCollection->sum('discount');
        $fine = $feeCollection->sum('fine');
        $balanceAmount = ($feeMaster - ($paidAmount+$discount))+$fine;

        //prepare variables
        $return_url             =       decrypt($request->get('return_url'));
        $website_url            =       decrypt($request->get('website_url'))!=null?decrypt($request->get('website_url')):decrypt($request->get('return_url'));
        $payingAmount           =       decrypt($request->get('amount'));
        $purchase_order_name    =       isset($request->fee_head)?decrypt($request->get('fee_head')):'Fee Payment';
        //get from student detail
        $purchase_order_id      =       $student->reg_no;
        $name                   =       $student->first_name.' '.$student->middle_name.' '.$student->last_name;
        $email                  =       $student->email;
        $phone                  =       $student->mobile_1;


        //compare actual balance and amount
        if($payingAmount > $balanceAmount){
            $request->session()->flash($this->message_warning, 'Amount is Greater than Balance Amount. You are not able to pay this amount. Contact Account Department.');
            return back();
        }else{
            $amount = $payingAmount*100;
        }

        $curl = curl_init();

        //Sandbox
        //https://a.khalti.com/api/v2/
        //Production
        //https://khalti.com/api/v2/

        curl_setopt_array($curl, array(
            CURLOPT_URL => ''.$url.'epayment/initiate/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                              "return_url": "'.$return_url.'",
                              "website_url": "'.$website_url.'",
                              "amount": '.$amount.',
                              "purchase_order_id": "'.$purchase_order_id.'",
                              "purchase_order_name": "'.$purchase_order_name.'",
                              "customer_info": {
                                  "name": "'.$name.'",
                                  "email": "'.$email.'",
                                  "phone": "'.$phone.'"
                              }
                            }
                            ',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Key '.$Secret_Key.'',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $responseValues = json_decode($response);
        //dd($responseValues);
        if(isset($responseValues->pidx) && isset($responseValues->payment_url)){
            return redirect($responseValues->payment_url);
        }else{
            $request->session()->flash($this->message_warning, 'Some thing is Wrong.');
            return back();
        }
    }

    public function handleSuccessResponse(Request $request)
    {
        //get user detail and prepare back url
        if($this->getRoleByUserId(auth()->user()->id) == 'Student'){
            $backUrlRoute = route('user-student');
        }else{
            $backUrlRoute = route('user-guardian');
        }

        //handle with failure
        if($request->message){
            $request->session()->flash($this->message_warning, $request->message);
            return redirect($backUrlRoute);
        }

        // handle with success
        //response store on database
        //Payment Store for Verification
        $student = Student::where('reg_no',$request->purchase_order_id)->first();
        $student_id = isset($student->id)?$student->id:'0000';
        $date = Carbon::now();
        $amount = $request->amount/100;
        $ref_no = $request->txnId;
        $ref_text = json_encode($request->all());

        //user detail for id and redirect to fee module
        $data = [
            'created_by'        => auth()->user()->id,
            'students_id'       => $student_id,
            'date'              => $date,
            'amount'            => $amount,
            'payment_gateway'   => 'Khalti',
            'ref_no'            =>  $ref_no,
            'ref_text'          =>  $ref_text
        ];

        $transaction =  OnlinePayment::create($data);

        $message = 'Online payment successfully. Thank you for payment. We Will Verify Your Payment Soon.';

        Session::flash($this->message_success, $message);
        return redirect($backUrlRoute);

    }

    public function paymentVerification(Request $request)
    {

    }
}