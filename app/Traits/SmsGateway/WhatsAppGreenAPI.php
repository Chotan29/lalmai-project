<?php
namespace App\Traits\SmsGateway;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait WhatsAppGreenAPI
{
    /**
     * Send WhatsApp message via Green-API
     *
     * @param string|array $contactNumbers Phone number(s) in international format
     * @param string $message Message content
     * @param bool $isGroup Whether to send to a group (default: false)
     * @return bool
     */
    public function WhatsAppGreenAPISMS($contactNumbers, $message, $isGroup = false)
    {
        /* Get Settings */
        $smsSetting = $this->getSmsSetting();
        $sms = json_decode($smsSetting['GreenAPI'], true);
        
        if (!$sms) {
            Log::error('GreenAPI settings missing or invalid');
            return false;
        }

        $idInstance = $sms['idInstance'] ?? null;
        $apiTokenInstance = $sms['apiTokenInstance'] ?? null;
        
        // Validate credentials
        if (empty($idInstance) || empty($apiTokenInstance)) {
            Log::error('GreenAPI credentials not configured');
            return false;
        }

        try {
            $baseUrl = "https://api.green-api.com/waInstance{$idInstance}";
            
            // Convert numbers to array if needed
            $numbers = is_array($contactNumbers) ? $contactNumbers : explode(',', $contactNumbers);
            
            $successCount = 0;
            
            foreach ($numbers as $number) {
                $number = $this->formatWhatsAppNumber($number, $isGroup);
                
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json'
                ])->post("{$baseUrl}/sendMessage/{$apiTokenInstance}", [
                    'chatId' => $number,
                    'message' => $message
                ]);
                
                if ($this->handleGreenAPIResponse($response)) {
                    $successCount++;
                }
            }
            
            return $successCount > 0;
            
        } catch (\Exception $e) {
            Log::error('GreenAPI WhatsApp Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Format WhatsApp number correctly
     *
     * @param string $number Phone number
     * @param bool $isGroup Whether it's a group ID
     * @return string
     */
    private function formatWhatsAppNumber($number, $isGroup = false)
    {
        // Remove all non-digit characters
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // For groups, just clean the input
        if ($isGroup) {
            return $number . '@g.us';
        }
        
        // For individual numbers:
        // 1. Remove leading 0 if present
        if (strpos($number, '0') === 0) {
            $number = substr($number, 1);
        }
        
        // 2. Add country code if missing (default to Nepal +977)
        if (strlen($number) === 10) {
            $number = '977' . $number;
        }
        
        return $number . '@c.us';
    }
    
    /**
     * Handle Green-API response
     *
     * @param \Illuminate\Http\Client\Response $response
     * @return bool
     */
    private function handleGreenAPIResponse($response)
    {
        $statusCode = $response->status();
        $data = $response->json();
        
        if ($statusCode === 200) {
            Log::info('GreenAPI WhatsApp message sent', [
                'idMessage' => $data['idMessage'] ?? null,
                'timestamp' => $data['timestamp'] ?? null
            ]);
            return true;
        }
        
        Log::error('GreenAPI WhatsApp Error', [
            'status_code' => $statusCode,
            'error' => $data['message'] ?? 'Unknown error',
            'response' => $data
        ]);
        
        return false;
    }
    
    /**
     * Check WhatsApp account status
     *
     * @return array|bool
     */
    public function checkGreenAPIStatus()
    {
        $smsSetting = $this->getSmsSetting();
        $sms = json_decode($smsSetting['GreenAPI'], true);
        
        if (!$sms) {
            Log::error('GreenAPI settings missing for status check');
            return false;
        }
        
        try {
            $response = Http::get(
                "https://api.green-api.com/waInstance{$sms['idInstance']}/getStateInstance/{$sms['apiTokenInstance']}"
            );
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'state' => $data['stateInstance'] ?? null,
                    'status' => $data['status'] ?? null,
                    'device' => $data['deviceData'] ?? null
                ];
            }
            
            Log::error('GreenAPI Status Check Failed', $response->json());
            return false;
            
        } catch (\Exception $e) {
            Log::error('GreenAPI Status Check Error', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}