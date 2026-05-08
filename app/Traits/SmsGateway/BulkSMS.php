<?php
//https://www.bulksms.com/developer/json/v1/#tag/Message%2Fpaths%2F~1messages~1%7Bid%7D%2Fget
namespace App\Traits\SmsGateway;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait BulkSMS
{
    public function bulkSMS($contactNumbers, $message)
    {
        /* Get Settings */
        $smsSetting = $this->getSmsSetting();
        $sms = json_decode($smsSetting['BulkSMS'], true);
        
        if (!$sms) {
            Log::error('BulkSMS settings missing or invalid');
            return false;
        }

        $apiToken = $sms['ApiToken'] ?? null;
        $apiSecret = $sms['ApiSecret'] ?? null;
        
        // Convert numbers to array if needed
        $numbers = is_array($contactNumbers) ? $contactNumbers : explode(',', $contactNumbers);
        
        try {
            // Format numbers to international format
            $formattedNumbers = array_map([$this, 'formatNumber'], $numbers);
            
            $response = Http::withBasicAuth($apiToken, $apiSecret)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post('https://api.bulksms.com/v1/messages', [
                    'to' => $formattedNumbers,
                    'body' => $message,
                    'encoding' => $this->determineEncoding($message),
                    'dca' => '7bit' // 7bit or 16bit (for unicode)
                ]);
            
            return $this->handleBulkSMSResponse($response);
            
        } catch (\Exception $e) {
            Log::error('BulkSMS API Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    private function formatNumber($number)
    {
        // Remove all non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Convert local format to international (e.g., 017... to 88017...)
        if (strlen($number) === 11 && strpos($number, '0') === 0) {
            return '880' . substr($number, 1);
        }
        
        // Ensure 880 prefix for Bangladesh numbers
        if (strlen($number) === 10) {
            return '880' . $number;
        }
        
        return $number;
    }
    
    private function determineEncoding($message)
    {
        // Check if message contains unicode characters
        if (strlen($message) !== mb_strlen($message)) {
            return 'UNICODE';
        }
        
        // Check for special characters that require 16bit encoding
        if (preg_match('/[^\x20-\x7E]/', $message)) {
            return 'UNICODE';
        }
        
        return 'TEXT';
    }
    
    private function handleBulkSMSResponse($response)
    {
        $statusCode = $response->status();
        $data = $response->json();
        
        if ($statusCode === 201) {
            Log::info('BulkSMS Message Sent Successfully', [
                'batch_id' => $data['batch_id'] ?? null,
                'message_count' => count($data['messages'] ?? [])
            ]);
            return true;
        }
        
        // Handle specific error cases
        $errorMessage = $data['error']['description'] ?? 'Unknown error';
        $errorCode = $data['error']['code'] ?? $statusCode;
        
        Log::error('BulkSMS API Error', [
            'status_code' => $statusCode,
            'error_code' => $errorCode,
            'message' => $errorMessage,
            'response' => $data
        ]);
        
        return false;
    }
    
    public function checkBulkSMSBalance()
    {
        $smsSetting = $this->getSmsSetting();
        $sms = json_decode($smsSetting['BulkSMS'], true);
        
        if (!$sms) {
            Log::error('BulkSMS settings missing for balance check');
            return false;
        }
        
        try {
            $response = Http::withBasicAuth($sms['ApiToken'], $sms['ApiSecret'])
                ->get('https://api.bulksms.com/v1/profile/balance');
                
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'balance' => $data['balance'] ?? 0,
                    'currency' => $data['currency'] ?? 'USD'
                ];
            }
            
            Log::error('BulkSMS Balance Check Failed', $response->json());
            return false;
            
        } catch (\Exception $e) {
            Log::error('BulkSMS Balance Check Error', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}