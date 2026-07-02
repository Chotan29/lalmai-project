<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use app\Libraries;
//use app\Libraries\SSLCommerz;

class SslCommerzService
{
    protected $storeId;
    protected $storePassword;
    protected $isLive;

    public function __construct()
    {    
        // $data['payment_setting'] = PaymentSetting::where('status',1)->get();
        // if(isset($data['payment_setting']) && $data['payment_setting']->count() > 0){
        //     $SettingInfo = json_decode($data['payment_setting'],true); 
        //     $paymentSetting = array_pluck($SettingInfo,'config','identity');
        //     if(!isset($paymentSetting)){
        //         $request->session()->flash($this->message_warning, 'Sorry, Setting Mismatch. Try it after some time.');
        //         return back();
        //     }

        //     $SSLCommerzSetting = json_decode($paymentSetting['SSLCommerz'],true);

        //     if(!isset($SSLCommerzSetting['Mode']) && !isset($SSLCommerzSetting['Public_Key']) && !isset($SSLCommerzSetting['Secret_Key'])){
        //         $request->session()->flash($this->message_warning, 'Sorry, Setting Mismatch. Try it after some time.');
        //         return back();
        //     }else{
        //         $this->storeId = $SSLCommerzSetting['SSLCOMMERZ_STORE_ID'];
        //         $this->storePassword = $SSLCommerzSetting['SSLCOMMERZ_STORE_PASSWORD'];
        //         $this->isLive = $SSLCommerzSetting['SSLCOMMERZ_IS_LIVE'];
        //     }
        // }     
        $this->storeId = env('SSLCOMMERZ_STORE_ID');
        $this->storePassword = env('SSLCOMMERZ_STORE_PASSWORD');
        $this->isLive = env('SSLCOMMERZ_IS_LIVE', false);
    }

    public function initiatePayment(array $paymentData): array
    {
        $post_data = [
            'total_amount' => number_format($paymentData['amount'], 2, '.', ''),
            'currency' => $paymentData['currency'],
            'tran_id' => $paymentData['orderId'],
            'cus_name' => $paymentData['studentName'] ?? 'Student Name',
            'cus_email' => $paymentData['studentEmail'] ?? 'student@mail.com',
            'cus_add1' => $paymentData['studentAddress'] ?? 'Dhaka',
            'cus_city' => $paymentData['studentCity'] ?? 'Dhaka',
            'cus_postcode' => $paymentData['studentPostcode'] ?? '1000',
            'cus_country' => $paymentData['studentCountry'] ?? 'Bangladesh',
            'cus_phone' => $paymentData['studentPhone'] ?? '01711111111',
            'shipping_method' => "NO",
            'product_name' => $paymentData['product_name'] ?? "Fee Payment",
            'product_category' => $paymentData['product_category'] ?? "Education",
            'product_profile' => "general",
            'success_url' => $paymentData['successUrl'],
            'fail_url' => $paymentData['failUrl'],
            'cancel_url' => $paymentData['cancelUrl'],
            'ipn_url' => $paymentData['ipnUrl'],
            'value_a' => $paymentData['value_a'] ?? '', // Store reg_no here
            'value_b' => $paymentData['value_b'] ?? '',
            'value_c' => $paymentData['value_c'] ?? '',
            'value_d' => $paymentData['value_d'] ?? ''
        ];

        try {
            $sslc = new \SSLCommerz($this->storeId, $this->storePassword, $this->isLive);
            $response = $sslc->initiate($post_data, false);

            Log::info("SSLCommerz Payment Initiation Response", [
                'order_id' => $paymentData['orderId'],
                'response' => $response
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error("SSLCommerz Payment Initiation Error: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function validateCallback(array $post_data, string $tran_id): bool
    {
        try {
            $sslc = new \SSLCommerz($this->storeId, $this->storePassword, $this->isLive);
            $validationResult = $sslc->validate($post_data, $tran_id);

            Log::info("SSLCommerz Payment Validation Result", [
                'transaction_id' => $tran_id,
                'status' => $validationResult ? 'valid' : 'invalid'
            ]);

            return $validationResult;

        } catch (\Exception $e) {
            Log::error("SSLCommerz Callback Validation Error: " . $e->getMessage());
            return false;
        }
    }
}