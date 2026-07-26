<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SslCommerzService
{
    protected string $storeId;
    protected string $storePassword;
    protected bool   $isLive;
    protected string $currency;
    protected string $liveBaseUrl;
    protected string $sandboxBaseUrl;
    protected Client $http;

    public function __construct()
    {
        $cfg = config('services.sslcommerz');

        $this->storeId       = $this->sanitizeConfigString($cfg['store_id'] ?? '');
        $this->storePassword = $this->sanitizeConfigString($cfg['store_password'] ?? '');
        $this->isLive        = filter_var(($cfg['is_live'] ?? false), FILTER_VALIDATE_BOOLEAN);
        $this->currency      = (string) ($cfg['currency'] ?? 'BDT');
        $this->liveBaseUrl   = rtrim((string) ($cfg['live_base_url'] ?? 'https://securepay.sslcommerz.com'), '/');
        $this->sandboxBaseUrl= rtrim((string) ($cfg['sandbox_base_url'] ?? 'https://sandbox.sslcommerz.com'), '/');
        $this->http          = new Client(['timeout' => 25]);
    }

    protected function baseUrl(): string
    {
        return $this->isLive
            ? $this->liveBaseUrl
            : $this->sandboxBaseUrl;
    }

    /**
     * Create payment session (init)
     * Returns array with GatewayPageURL on success.
     */
    public function initiatePayment(array $data): array
    {
        if ($this->storeId === '' || $this->storePassword === '') {
            Log::error('SSLCommerz config missing', [
                'has_store_id' => $this->storeId !== '',
                'has_store_password' => $this->storePassword !== '',
                'is_live' => $this->isLive,
            ]);

            return ['error' => 'SSLCommerz configuration missing: store_id/store_password'];
        }

        $endpoint = $this->baseUrl() . '/gwprocess/v4/api.php';

        // SSLCOMMERZ required fields
        $payload = [
            'store_id'     => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => (float) $data['amount'],
            'currency'     => $data['currency'] ?? $this->currency,
            'tran_id'      => $data['orderId'],           // unique
            'success_url'  => $data['successUrl'],
            'fail_url'     => $data['failUrl'],
            'cancel_url'   => $data['cancelUrl'],
            'ipn_url'      => $data['ipnUrl'] ?? $data['successUrl'],

            // Customer
            'cus_name'     => $data['studentName'] ?? 'Customer',
            'cus_email'    => $data['studentEmail'] ?? 'no-reply@example.com',
            'cus_add1'     => $data['studentAddress'] ?? 'N/A',
            'cus_city'     => $data['studentCity'] ?? 'N/A',
            'cus_postcode' => $data['studentPostcode'] ?? '0000',
            'cus_country'  => $data['studentCountry'] ?? 'Bangladesh',
            'cus_phone'    => $data['studentPhone'] ?? '00000000000',

            // Product
            'shipping_method'  => 'NO',
            'product_name'     => $data['product_name'] ?? 'Fee Payment',
            'product_category' => $data['product_category'] ?? 'Education',
            'product_profile'  => 'general',

            // Pass back vars
            'value_a' => $data['value_a'] ?? null, // reg_no
            'value_b' => $data['value_b'] ?? null, // student_id
            'value_c' => $data['value_c'] ?? null, // user_id (created_by)
        ];

        try {
            $res = $this->http->post($endpoint, ['form_params' => $payload]);
            $rawBody = (string) $res->getBody();
            $body = json_decode($rawBody, true);
            if (!is_array($body)) {
                $body = [];
            }

            Log::info('SSLCommerz initiate response', [
                'http_status' => $res->getStatusCode(),
                'status' => $body['status'] ?? null,
                'failedreason' => $body['failedreason'] ?? null,
                'gateway_url' => $body['GatewayPageURL'] ?? ($body['redirectGatewayURL'] ?? null),
            ]);

            $gatewayUrl = $body['GatewayPageURL']
                ?? $body['redirectGatewayURL']
                ?? $body['redirectGatewayURLFailed']
                ?? null;

            if (!empty($gatewayUrl)) {
                return [
                    'GatewayPageURL' => $gatewayUrl,
                    'sessionkey'     => $body['sessionkey'] ?? null,
                    'raw'            => $body,
                ];
            }

            $status = isset($body['status']) ? strtoupper((string) $body['status']) : null;
            $error = $body['failedreason']
                ?? ($status ? 'SSLCommerz status: ' . $status : null)
                ?? ('Unknown error from SSLCommerz init. Raw: ' . substr($rawBody, 0, 300));

            return ['error' => $error, 'raw' => $body];

        } catch (\Throwable $e) {
            Log::error('SSLCommerz initiate error: '.$e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    protected function sanitizeConfigString($value): string
    {
        return trim((string) $value, " \t\n\r\0\x0B\"'");
    }

    /**
     * Query SSLCommerz by transaction id only (no val_id needed).
     *
     * Used by payment recovery: when the browser callback is lost, the session and
     * the REG-xxxx reference are gone, so tran_id is the only key left. The gateway's
     * own record gives back value_a (our REG ref), status and amount, which lets a
     * paid-but-unfinished registration be completed safely.
     *
     * @return array ['found'=>bool,'valid'=>bool,'status'=>string,'amount'=>float,
     *                'currency'=>string,'value_a'=>string,'value_b'=>string,
     *                'tran_date'=>string,'bank_tran_id'=>string,'card_type'=>string,
     *                'raw'=>array,'error'=>string|null]
     */
    public function queryByTransactionId(string $tranId): array
    {
        $out = [
            'found' => false, 'valid' => false, 'status' => '', 'amount' => 0.0,
            'currency' => '', 'value_a' => '', 'value_b' => '', 'tran_date' => '',
            'bank_tran_id' => '', 'card_type' => '', 'raw' => [], 'error' => null,
        ];

        $tranId = trim($tranId);
        if ($tranId === '') {
            $out['error'] = 'Transaction ID is empty.';
            return $out;
        }

        if ($this->storeId === '' || $this->storePassword === '') {
            $out['error'] = 'SSLCommerz configuration missing: store_id/store_password.';
            return $out;
        }

        $endpoint = $this->baseUrl() . '/validator/api/merchantTransIDvalidationAPI.php';
        $query = [
            'tran_id'      => $tranId,
            'store_id'     => $this->storeId,
            'store_passwd' => $this->storePassword,
            'v'            => 1,
            'format'       => 'json',
        ];

        try {
            $res  = $this->http->get($endpoint, ['query' => $query]);
            $body = json_decode((string) $res->getBody(), true) ?? [];
            $out['raw'] = $body;

            /* Matching transactions come under element_data (a tran_id can have more
               than one attempt). Prefer a VALID/VALIDATED row, else keep the first. */
            $row = null;
            $rows = $body['element_data'] ?? $body['element'] ?? null;
            if (is_array($rows) && !empty($rows)) {
                foreach ($rows as $candidate) {
                    if (!is_array($candidate)) { continue; }
                    $st = strtoupper((string) ($candidate['status'] ?? ''));
                    if (in_array($st, ['VALID', 'VALIDATED'], true)) { $row = $candidate; break; }
                    if ($row === null) { $row = $candidate; }
                }
            } elseif (!empty($body['tran_id'])) {
                $row = $body;
            }

            if (!$row) {
                $out['error'] = $body['errorReason'] ?? 'No transaction found for this ID.';
                Log::info('SSLCommerz query: no transaction found', ['tran_id' => $tranId]);
                return $out;
            }

            $status = strtoupper((string) ($row['status'] ?? ''));

            $out['found']        = true;
            $out['status']       = $status;
            $out['valid']        = in_array($status, ['VALID', 'VALIDATED'], true);
            $out['amount']       = (float) ($row['currency_amount'] ?? $row['amount'] ?? 0);
            $out['currency']     = (string) ($row['currency_type'] ?? $row['currency'] ?? '');
            $out['value_a']      = (string) ($row['value_a'] ?? '');
            $out['value_b']      = (string) ($row['value_b'] ?? '');
            $out['tran_date']    = (string) ($row['tran_date'] ?? '');
            $out['bank_tran_id'] = (string) ($row['bank_tran_id'] ?? '');
            $out['card_type']    = (string) ($row['card_type'] ?? '');

            Log::info('SSLCommerz query result', [
                'tran_id' => $tranId, 'status' => $status, 'value_a' => $out['value_a'],
            ]);

            return $out;
        } catch (\Throwable $e) {
            $out['error'] = 'Query failed: ' . $e->getMessage();
            Log::error('SSLCommerz query error: ' . $e->getMessage(), ['tran_id' => $tranId]);
            return $out;
        }
    }

    /**
     * Validate callback by calling the SSLCommerz validation API with val_id.
     * Returns the remote JSON on success or false.
     */
    public function validateCallback(array $callbackData, string $tranId)
    {
        $valId = $callbackData['val_id'] ?? null;
        if (!$valId) {
            Log::warning("SSLCommerz validate: missing val_id for tran_id={$tranId}, trying signature fallback");
            return $this->validateSignedCallbackFallback($callbackData, $tranId);
        }

        $endpoint = $this->baseUrl() . '/validator/api/validationserverAPI.php';
        $query = [
            'val_id'       => $valId,
            'store_id'     => $this->storeId,
            'store_passwd' => $this->storePassword,
            'v'            => 1,
            'format'       => 'json',
        ];

        try {
            $res  = $this->http->get($endpoint, ['query' => $query]);
            $body = json_decode((string) $res->getBody(), true) ?? [];
            Log::info('SSLCommerz validate response', $body);

            // Valid statuses: VALID / VALIDATED
            $status = strtoupper($body['status'] ?? '');
            if (!in_array($status, ['VALID', 'VALIDATED'], true)) {
                return $this->validateSignedCallbackFallback($callbackData, $tranId);
            }

            // Sanity check tran_id
            if (!empty($body['tran_id']) && $body['tran_id'] !== $tranId) {
                Log::warning("SSLCommerz validate: tran_id mismatch. expected={$tranId}, got={$body['tran_id']}");
                return false;
            }

            return $body;
        } catch (\Throwable $e) {
            Log::error('SSLCommerz validate error: '.$e->getMessage());
            return $this->validateSignedCallbackFallback($callbackData, $tranId);
        }
    }

    protected function validateSignedCallbackFallback(array $callbackData, string $tranId)
    {
        $status = strtoupper((string) ($callbackData['status'] ?? ''));
        if (!in_array($status, ['VALID', 'VALIDATED'], true)) {
            return false;
        }

        if (($callbackData['tran_id'] ?? null) !== $tranId) {
            Log::warning("SSLCommerz fallback validate: tran_id mismatch. expected={$tranId}, got=" . ($callbackData['tran_id'] ?? 'null'));
            return false;
        }

        if (!$this->isCallbackSignatureValid($callbackData)) {
            Log::warning("SSLCommerz fallback validate: invalid signature for tran_id={$tranId}");
            return false;
        }

        Log::info("SSLCommerz fallback validate: signature verified for tran_id={$tranId}");

        return array_merge($callbackData, [
            'status' => $status,
            '_validated_via' => 'signed-callback-fallback',
        ]);
    }

    protected function isCallbackSignatureValid(array $callbackData): bool
    {
        $verifyKeyRaw = trim((string) ($callbackData['verify_key'] ?? ''));
        $givenSha2 = strtolower((string) ($callbackData['verify_sign_sha2'] ?? ''));
        $givenMd5  = strtolower((string) ($callbackData['verify_sign'] ?? ''));

        if ($verifyKeyRaw === '' || ($givenSha2 === '' && $givenMd5 === '')) {
            return false;
        }

        $keys = array_filter(array_map('trim', explode(',', $verifyKeyRaw)));
        if (empty($keys)) {
            return false;
        }

        $parts = [];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $callbackData)) {
                return false;
            }
            $parts[] = $key . '=' . (string) $callbackData[$key];
        }

        $parts[] = 'store_passwd=' . md5($this->storePassword);
        $verifyString = implode('&', $parts);

        $calculatedSha2 = hash('sha256', $verifyString);
        $calculatedMd5 = md5($verifyString);

        $sha2Match = $givenSha2 !== '' && hash_equals($givenSha2, strtolower($calculatedSha2));
        $md5Match  = $givenMd5 !== '' && hash_equals($givenMd5, strtolower($calculatedMd5));

        return $sha2Match || $md5Match;
    }
}
