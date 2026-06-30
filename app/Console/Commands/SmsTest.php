<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SmsSetting;
use GuzzleHttp\Client;

class SmsTest extends Command
{
    protected $signature = 'sms:test {number : Bangladeshi mobile number (e.g. 01711000000)} {--message= : Custom message (optional)}';
    protected $description = 'Test SMS sending. Uses SMS_TEST_MODE if set, otherwise calls real provider.';

    public function handle()
    {
        $number  = $this->argument('number');
        $message = $this->option('message') ?: 'Test SMS from Lalmai GC [' . now()->format('d-M H:i:s') . ']';

        $this->info("Number  : {$number}");
        $this->info("Message : {$message}");
        $this->line('');

        // Test mode — just log
        if (env('SMS_TEST_MODE', false)) {
            $logLine = '[' . now() . '] TO=' . $number . ' MSG=' . $message . PHP_EOL;
            file_put_contents(storage_path('logs/sms_test.log'), $logLine, FILE_APPEND | LOCK_EX);
            $this->info('SMS_TEST_MODE=true → Logged to storage/logs/sms_test.log');
            $this->line($logLine);
            return 0;
        }

        // Real send — call provider directly
        $gateway = SmsSetting::active()->first();
        if (!$gateway) {
            $this->error('No active SMS gateway found in sms_settings.');
            return 1;
        }

        $this->info("Gateway : {$gateway->identity}");

        $config = json_decode($gateway->config, true);
        if (!$config) {
            $this->error('Invalid JSON config in sms_settings.');
            return 1;
        }

        // GenNet / SslWireless
        if (in_array($gateway->identity, ['GenNet', 'SslWireless'])) {
            $apiToken = $config['ApiToken'] ?? null;
            $sid      = $config['Sid'] ?? null;
            $baseUrl  = rtrim($config['BaseUrl'] ?? 'https://smsplus.sslwireless.com', '/');

            if (!$apiToken || !$sid) {
                $this->error('Missing ApiToken or Sid in GenNet config.');
                return 1;
            }

            // Format number
            $raw = preg_replace('/[^0-9]/', '', $number);
            if (strlen($raw) == 11 && $raw[0] === '0') {
                $msisdn = '880' . substr($raw, 1);
            } elseif (strlen($raw) == 13 && substr($raw, 0, 3) === '880') {
                $msisdn = $raw;
            } else {
                $msisdn = $raw;
            }

            $this->info("MSISDN  : {$msisdn}");
            $this->info("Calling : {$baseUrl}/api/v3/send-sms");
            $this->line('');

            $client = new Client();
            try {
                $response = $client->post($baseUrl . '/api/v3/send-sms', [
                    'headers'     => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                    'json'        => ['api_token' => $apiToken, 'sid' => $sid, 'msisdn' => $msisdn, 'sms' => $message, 'csms_id' => uniqid()],
                    'http_errors' => false,
                ]);

                $httpCode = $response->getStatusCode();
                $body     = (string) $response->getBody();
                $data     = json_decode($body, true);

                $this->line("HTTP Status : {$httpCode}");
                $this->line("Response    : {$body}");
                $this->line('');

                if (isset($data['status']) && $data['status'] === 'SUCCESS') {
                    $this->info('✔ SMS Sent Successfully!');
                } else {
                    $code = $data['status_code'] ?? 'N/A';
                    $msg  = $data['error_message'] ?? 'Unknown error';
                    $this->error("✘ Failed — Code: {$code}, Message: {$msg}");
                    if ($code == 4003) {
                        $this->warn('IP Blacklisted. Whitelist your public IP (203.202.255.231) with the SMS provider.');
                    }
                }
            } catch (\Exception $e) {
                $this->error('Exception: ' . $e->getMessage());
                return 1;
            }

            return 0;
        }

        $this->warn("Gateway '{$gateway->identity}' direct test not implemented in this command. Use SMS_TEST_MODE=true for logging.");
        return 1;
    }
}
