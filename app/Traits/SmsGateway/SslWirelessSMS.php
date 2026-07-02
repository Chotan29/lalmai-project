<?php
namespace App\Traits\SmsGateway;

use Illuminate\Support\Facades\Log;

trait SslWirelessSMS
{
    /* SSL Wireless SMS */
    public function sslWirelessSMS($contactNumbers, $message, $providerKey = 'SslWireless')
    {
        /* Get Setting */
        $smsSetting = $this->getSmsSetting();
        if (!isset($smsSetting[$providerKey])) {
            return false;
        }

        $sms = json_decode($smsSetting[$providerKey], true);
        if (!is_array($sms)) {
            return false;
        }

        $apiToken = $sms['ApiToken'];
        $sid = $sms['Sid'];
        $baseUrl = $this->normalizeBaseUrl(isset($sms['BaseUrl']) ? $sms['BaseUrl'] : null);
        
      
        
        // Convert contact numbers to array if they're not already
        $numbers = is_array($contactNumbers) ? $contactNumbers : explode(',', $contactNumbers);
        
        try {
            // Determine which API to use based on number of recipients
            if (count($numbers) === 1) {
                return $this->sendSslSingleSMS($baseUrl, $apiToken, $sid, $numbers[0], $message);
            } else {
                return $this->sendSslBulkSMS($baseUrl, $apiToken, $sid, $numbers, $message);
            }
            
        } catch (\Exception $e) {
            Log::error('SSL Wireless SMS Exception: ' . $e->getMessage());
            return false;
        }
    }

    private function normalizeBaseUrl($baseUrl)
    {
        $baseUrl = !empty($baseUrl) ? rtrim($baseUrl, '/') : 'https://smsplus.sslwireless.com';

        return preg_replace('#/api/v3/send-sms(?:/bulk)?$#', '', $baseUrl);
    }
    
    private function sendSslSingleSMS($baseUrl, $apiToken, $sid, $msisdn, $message)
    {
        $client = new \GuzzleHttp\Client();
    
        
        try {
            $response = $client->post($baseUrl . '/api/v3/send-sms', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'api_token' => $apiToken,
                    'sid' => $sid,
                    'msisdn' => $this->formatNumber($msisdn),
                    'sms' => $message,
                    'csms_id' => uniqid()
                ],
                'http_errors' => false // To prevent exceptions on HTTP errors
            ]);
            
            return $this->handleSslResponse($response);
            
        } catch (\Exception $e) {
            Log::error('SSL Wireless Request Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    private function sendSslBulkSMS($baseUrl, $apiToken, $sid, $numbers, $message)
    {
        // Format all numbers to 880 format
        $formattedNumbers = array_map([$this, 'formatNumber'], $numbers);

        $batchCsmsId = uniqid();

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post($baseUrl . '/api/v3/send-sms/bulk', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
                'json' => [
                    'api_token'      => $apiToken,
                    'sid'            => $sid,
                    'msisdn'         => $formattedNumbers,
                    'sms'            => $message,
                    'batch_csms_id'  => $batchCsmsId,
                ],
                'http_errors' => false,
            ]);

            return $this->handleSslResponse($response);

        } catch (\Exception $e) {
            Log::error('SSL Wireless Bulk SMS Exception: ' . $e->getMessage());
            return false;
        }
    }
    
    private function formatNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Handle Bangladeshi numbers specifically
        if (strlen($number) == 11 && strpos($number, '0') === 0) {
            return '880'.substr($number, 1);
        }
        
        // Handle already formatted numbers (880...)
        if (strlen($number) == 13 && strpos($number, '880') === 0) {
            return $number;
        }
        
        // Default return if format doesn't match
        return $number;
    }
        
    private function handleSslResponse($response)
    {
        $responseBody = $this->extractResponseBody($response);

        if ($responseBody === null) {
            Log::error('Unable to read response body from SSL Wireless response object');
            return false;
        }
        
        
        // Decode the JSON response
        $data = json_decode($responseBody, true);
        
        // Check if JSON decoding was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON response from SSL Wireless', ['response' => $responseBody]);
            return false;
        }
        
        // Validate response structure
        if (!isset($data['status']) || !isset($data['status_code'])) {
            Log::error('Malformed response from SSL Wireless', $data);
            return false;
        }
        
        // Check for success
        if ($data['status'] === 'SUCCESS' && $data['status_code'] == 200) {
            // Additional check for bulk responses
            if (isset($data['smsinfo'])) {
                foreach ($data['smsinfo'] as $sms) {
                    if ($sms['sms_status'] !== 'SUCCESS') {
                        Log::error('SMS delivery failed for number', [
                            'msisdn' => $sms['msisdn'] ?? 'unknown',
                            'error' => $sms['status_message'] ?? 'no error message'
                        ]);
                        return false;
                    }
                }
            }
            return true;
        }
        
        Log::error('SSL Wireless API Error', [
            'status' => $data['status'],
            'code' => $data['status_code'],
            'message' => $data['error_message'] ?? 'No error message'
        ]);
        return false;
    }

    private function extractResponseBody($response)
    {
        if (is_object($response)) {
            if (method_exists($response, 'body')) {
                return $response->body();
            }

            if (method_exists($response, 'getBody')) {
                return (string) $response->getBody();
            }
        }

        return null;
    }
}