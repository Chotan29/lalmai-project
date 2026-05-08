<?php
//https://sms.net.bd/api
namespace App\Traits\SmsGateway;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait AlphaSMS
{
    public function alphaSMS($contactNumbers, $message)
    {
        /* Get Settings */
        $smsSetting = $this->getSmsSetting();
        $sms = json_decode($smsSetting['AlphaSMS'], true);
        
        if (!$sms) {
            Log::error('AlphaSMS settings missing or invalid');
            return false;
        }

        $apiKey = $sms['ApiKey'] ?? null;
        $senderId = $sms['SenderId'] ?? 'ALPHASMS';
        
        // Convert numbers to array if needed
        $numbers = is_array($contactNumbers) ? $contactNumbers : explode(',', $contactNumbers);
        
        try {
            // Format numbers to international format
            $formattedNumbers = array_map([$this, 'formatNumber'], $numbers);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post('https://sms.net.bd/api/v1/send', [
                'sender_id' => $senderId,
                'recipients' => $formattedNumbers,
                'message' => $message,
                //'unicode' => $this->isUnicode($message) // Auto-detect unicode
            ]);
            
            return $this->handleAlphaResponse($response);
            
        } catch (\Exception $e) {
            Log::error('AlphaSMS API Error: ' . $e->getMessage());
            return false;
        }
    }
    
    // private function formatNumber($number)
    // {
    //     // Remove all non-numeric characters
    //     $number = preg_replace('/[^0-9]/', '', $number);
        
    //     // Convert local format to international (e.g., 017... to 88017...)
    //     if (strlen($number) === 11 && strpos($number, '0') === 0) {
    //         return '880' . substr($number, 1);
    //     }
        
    //     // Ensure 880 prefix for Bangladesh numbers
    //     if (strlen($number) === 10) {
    //         return '880' . $number;
    //     }
        
    //     return $number;
    // }
    
    // private function isUnicode($message)
    // {
    //     return strlen($message) != mb_strlen($message);
    // }
    
    private function handleAlphaResponse($response)
    {
        if (!$response->successful()) {
            Log::error('AlphaSMS API Request Failed', [
                'status' => $response->status(),
                'error' => $response->body()
            ]);
            return false;
        }
        
        $data = $response->json();
        
        // Check response structure
        if (!isset($data['status'])) {
            Log::error('Invalid AlphaSMS Response Format', $data);
            return false;
        }
        
        if ($data['status'] === 'success') {
            Log::info('AlphaSMS Message Sent', [
                'message_id' => $data['message_id'] ?? null,
                'recipients' => $data['recipients'] ?? null
            ]);
            return true;
        }
        
        Log::error('AlphaSMS Delivery Failed', [
            'error' => $data['message'] ?? 'Unknown error',
            'code' => $data['code'] ?? null
        ]);
        return false;
    }
}