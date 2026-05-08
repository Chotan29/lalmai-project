<?php
namespace App\Traits\SmsGateway;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait AdaReachSMS
{
    /* adaReach SMS */
    public function adaReachSMS($contactNumbers, $message)
    {
        /* Get Setting */
        $smsSetting = $this->getSmsSetting();
        $sms = json_decode($smsSetting['AdaReach'], true);
        $username = $sms['UserName'];
        $password = $sms['Password'];
        $sender = $sms['Sender'] ?? 'adaReach'; // Default sender if not provided
        
        // Convert contact numbers to array if they're not already
        $numbers = is_array($contactNumbers) ? $contactNumbers : explode(',', $contactNumbers);
        
        try {
            // Step 1: Get Auth Token
            $tokenResponse = $this->getAdaReachToken($username, $password);
            
            if (!$tokenResponse['success']) {
                Log::error('AdaReach SMS Failed - Authentication Error: ' . $tokenResponse['message']);
                return false;
            }
            
            $token = $tokenResponse['token'];
            
            // Step 2: Send SMS
            $sendResponse = $this->sendAdaReachSMS($sender, $numbers, $message, $token);
            
            if (!$sendResponse['success']) {
                Log::error('AdaReach SMS Failed - Send Error: ' . $sendResponse['message']);
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('AdaReach SMS Exception: ' . $e->getMessage());
            return false;
        }
    }
    
    private function getAdaReachToken($username, $password)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('https://api.mobireach.com.bd/auth/tokens', [
            'username' => $username,
            'password' => $password
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            return [
                'success' => true,
                'token' => $data['token'],
                'refresh_token' => $data['refresh_token']
            ];
        } else {
            $error = $response->json();
            return [
                'success' => false,
                'message' => $error['message'] ?? 'Authentication failed'
            ];
        }
    }
    
    private function sendAdaReachSMS($sender, $numbers, $message, $token)
    {
        // Format numbers to 13 digits (assuming BD numbers)
        $formattedNumbers = array_map(function($number) {
            // Remove any non-digit characters
            $number = preg_replace('/[^0-9]/', '', $number);
            
            // If number starts with 0, replace with 880
            if (strpos($number, '0') === 0) {
                $number = '880' . substr($number, 1);
            }
            
            // Ensure it's 13 digits (880 + 10 digits)
            if (strlen($number) === 11 && strpos($number, '0') === 0) {
                $number = '880' . substr($number, 1);
            }
            
            return $number;
        }, $numbers);
        
        $requestType = count($formattedNumbers) > 1 ? 'B' : 'S';
        
        // Determine content type (1=regular, 2=unicode)
        $contentType = $this->isUnicode($message) ? '2' : '1';
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->post('https://api.mobireach.com.bd/sms/send', [
            'sender' => $sender,
            'receiver' => $formattedNumbers,
            'contentType' => $contentType,
            'content' => $message,
            'msgType' => 'T', // Assuming transactional by default
            'requestType' => $requestType
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            if ($data['status'] === 'SUCCESS') {
                return [
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'messageId' => $data['messageId']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $data['description'] ?? 'Message sending failed'
                ];
            }
        } else {
            $error = $response->json();
            return [
                'success' => false,
                'message' => $error['message'] ?? 'API request failed'
            ];
        }
    }
    
    private function isUnicode($message)
    {
        return strlen($message) != mb_strlen($message);
    }
}