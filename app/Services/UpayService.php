<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class UpayService
{
    protected $client;
    protected $baseUrl;
    protected $merchantId;
    protected $merchantKey;
    protected $merchantCode;
    protected $merchantCountryCode;
    protected $merchantCity;
    protected $merchantCategoryCode;
    protected $merchantMobile;
    protected $transactionCurrencyCode;

    public function __construct()
    {
        $this->baseUrl = config('upay.base_url');
        $this->merchantId = config('upay.merchant_id');
        $this->merchantKey = config('upay.merchant_key');
        $this->merchantCode = config('upay.merchant_code');
        $this->merchantCountryCode = config('upay.merchant_country_code');
        $this->merchantCity = config('upay.merchant_city');
        $this->merchantCategoryCode = config('upay.merchant_category_code');
        $this->merchantMobile = config('upay.merchant_mobile');
        $this->transactionCurrencyCode = config('upay.transaction_currency_code');
        //dd($this->baseUrl, $this->merchantId, $this->merchantKey, $this->merchantCode, $this->merchantCountryCode, $this->merchantCity, $this->merchantCategoryCode, $this->merchantMobile, $this->transactionCurrencyCode);

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Authenticates the merchant and retrieves an access token.
     * @return string|null The access token on success, or null on failure.
     */
    public function authenticateMerchant()
    {
        try {
            //dd( $this->merchantId, $this->merchantKey);
            $response = $this->client->post('payment/merchant-auth/', [
                'json' => [
                    'merchant_id' => $this->merchantId,
                    'merchant_key' => $this->merchantKey,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
           // dd($this->merchantId, $this->merchantKey, $data);

            if (isset($data['data']['token'])) {
                return $data['data']['token'];
            }

            Log::error('Upay Merchant Authentication Failed: ' . json_encode($data));
            return null;
        } catch (RequestException $e) {
            Log::error('Upay Merchant Authentication Exception: ' . $e->getMessage());
            if ($e->hasResponse()) {
                Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
            }
            return null;
        }
    }

    /**
     * Initializes a merchant payment.
     * @param array $paymentDetails An array containing payment details.
     * @return array|null The response data on success, or null on failure.
     */
    public function initPayment(array $paymentDetails)
    {
        $token = $this->authenticateMerchant();

      //  dd( $token);

        if (!$token) {
            return null; // Could not authenticate
        }

        try {
            $response = $this->client->post('payment/merchant-payment-init/', [
                'headers' => [
                    'Authorization' => 'UPAY ' . $token,
                ],
                'json' => array_merge([
                    'date' => now()->format('Y-m-d'), // Current date
                    'merchant_id' => $this->merchantId,
                    'merchant_name' => 'Your College Name', // Use your college name here
                    'merchant_code' => $this->merchantCode,
                    'merchant_country_code' => $this->merchantCountryCode,
                    'merchant_city' => $this->merchantCity,
                    'merchant_category_code' => $this->merchantCategoryCode,
                    'merchant_mobile' => $this->merchantMobile,
                    'transaction_currency_code' => $this->transactionCurrencyCode,
                ], $paymentDetails),
            ]);

            //dd($response,$paymentDetails);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Upay Payment Initialization Exception: ' . $e->getMessage());
            if ($e->hasResponse()) {
                Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
            }
            return null;
        }
    }

    /**
     * Checks the status of a single payment.
     * @param string $txnId The transaction ID.
     * @return array|null The response data on success, or null on failure.
     */
    public function getSinglePaymentStatus(string $txnId)
    {
        $token = $this->authenticateMerchant();

        if (!$token) {
            return null;
        }

        try {
            $response = $this->client->get("payment/single-payment-status/{$txnId}/", [
                'headers' => [
                    'Authorization' => 'UPAY ' . $token,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Upay Single Payment Status Exception: ' . $e->getMessage());
            if ($e->hasResponse()) {
                Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
            }
            return null;
        }
    }
}