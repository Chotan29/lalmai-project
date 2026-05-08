<?php

namespace App\Http\Controllers\Account\Fees\Payment;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;

class PayPalController extends CollegeBaseController
{
    public function payment(Request $request)
    {
        $net_balance = $request->get('net_balance', 0);
        return view('account.fees.payment.paypal', compact('net_balance'));
    }

    public function cancel(Request $request)
    {
        $request->session()->flash($this->message_warning, 'PayPal payment was cancelled.');
        return redirect()->back();
    }

    public function success(Request $request)
    {
        $request->session()->flash($this->message_success, 'PayPal payment completed successfully.');
        return redirect()->route('dashboard');
    }
}
