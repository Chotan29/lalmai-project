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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;

class StripePaymentController extends CollegeBaseController
{
    /*stripe*/
    public function stripePayment(Request $request)
    {
        $paymentSetting = parent::getPaymentSetting();
        $stripe = json_decode($paymentSetting['Stripe'],true);

        if(!isset($stripe['Publishable_Key']) && !isset($stripe['Secret_Key'])){
            $request->session()->flash($this->message_warning, 'Sorry, Stripe Key Not Configure Properly. Try Again.');
            return back();
        }

        $net_balance = $request->get('net_balance');
        $description = $request->get('description');
        $student_name = $request->get('student_name');
        $student_id = $request->get('student_id');
        $fee_masters_id = $request->get('fee_masters_id');
        $date = Carbon::now()->format('Y-m-d');
        $stripeToken = $request->get('stripeToken');
        $stripeEmail = $request->get('stripeEmail');

        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here: https://dashboard.stripe.com/account/apikeys
        Stripe::setApiKey($stripe['Secret_Key']);

        // Token is created using Checkout or Elements!
        // Get the payment token ID submitted by the form:
        $token = $request->get('stripeToken');

           /* $customer = \Stripe\Customer::create([
                'email' => $stripeEmail,
                'description' => $student_name,
                "source" => $stripeToken,
            ]);*/

            $charge = \Stripe\Charge::create([
                'amount' => $net_balance*100,
                'currency' => 'usd',
                "source" => $stripeToken,
                'description' => $description,

            ]);

            if($charge->status == "succeeded") {

                //Payment Store for Verification
                $data = [
                    'created_by'        => auth()->user()->id,
                    'students_id'       => $student_id,
                    'date'              => $date,
                    'amount'            => $charge->amount/100,
                    'payment_gateway'   => 'Strip',
                    'ref_no'            =>  $charge->payment_method,
                    'ref_text'          =>  $description
                ];

                $transaction =  OnlinePayment::create($data);

                $message = 'Online payment successfully. Thank you for payment. We Will Verify Your Payment Soon.';
                $request->session()->flash($this->message_success, $message);
            }else{
                $request->session()->flash($this->message_warning, 'Sorry, something went wrong. Please try again.');
            }

        return redirect()->back();
    }
}