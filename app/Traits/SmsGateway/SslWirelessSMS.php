<?php
namespace App\Traits\SmsGateway;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

trait SslWirelessSMS
{
    /* SSL Wireless SMS */
    public function sslWirelessSMS($contactNumbers, $message)
    {
        /* Get Setting */
        $smsSetting = $this->getSmsSetting();
        $sms = json_decode($smsSetting['SslWireless'], true);
        $apiToken = $sms['ApiToken'];
        $sid = $sms['Sid'];
        
      
        
        // Convert contact numbers to array if they're not already
        $numbers = is_array($contactNumbers) ? $contactNumbers : explode(',', $contactNumbers);
        
        try {
            // Determine which API to use based on number of recipients
            if (count($numbers) === 1) {
                return $this->sendSslSingleSMS($apiToken, $sid, $numbers[0], $message);
            } else {
                return $this->sendSslBulkSMS($apiToken, $sid, $numbers, $message);
            }
            
        } catch (\Exception $e) {
            Log::error('SSL Wireless SMS Exception: ' . $e->getMessage());
            return false;
        }
    }
    
    private function sendSslSingleSMS($apiToken, $sid, $msisdn, $message)
    {
        $client = new \GuzzleHttp\Client();
    
        
        try {
            $response = $client->post('https://smsplus.sslwireless.com/api/v3/send-sms', [
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
    
    private function sendSslBulkSMS($apiToken, $sid, $numbers, $message)
    {
        // Format all numbers to 880 format
        $formattedNumbers = array_map([$this, 'formatNumber'], $numbers);
        
        // Generate unique batch CSMS ID
        //$batchCsmsId = uniqid('batch_', true);
        $batchCsmsId = uniqid();
       
       
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('https://smsplus.sslwireless.com/api/v3/send-sms/bulk', [
            'api_token' => $apiToken,
            'sid' => $sid,
            'msisdn' => $formattedNumbers,
            'sms' => $message,
            'batch_csms_id' => $batchCsmsId
        ]);
        
        return $this->handleSslResponse($response);
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
        // Get the response body as string
        $responseBody = $response->getBody()->getContents();
        
        
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
}