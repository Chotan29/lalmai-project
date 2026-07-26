<?php
namespace app\Libraries\SSLCommerz;
/**
 * SSLCommerz PHP Library
 *
 * This is the main class for integrating SSLCommerz payment gateway.
 * It handles initiation of payments and validation of callbacks.
 *
 * @version 1.0.0
 * @author SSLCommerz
 * @link https://github.com/sslcommerz/SSLCommerz-PHP
 */
class SSLCommerz
{
    private $store_id;
    private $store_passwd;
    private $is_live;

    // API URLs
    const INITIATE_URL_LIVE = "https://securepay.sslcommerz.com/gwprocess/v4/api.php";
    const INITIATE_URL_SANDBOX = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";
    const VALIDATION_URL_LIVE = "https://securepay.sslcommerz.com/validator/api/validationserverAPI.php";
    const VALIDATION_URL_SANDBOX = "https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php";
    const TRANSACTION_QUERY_URL_LIVE = "https://securepay.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php";
    const TRANSACTION_QUERY_URL_SANDBOX = "https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php";

    /**
     * Constructor for SSLCommerz class.
     *
     * @param string $store_id Your SSLCommerz Store ID.
     * @param string $store_passwd Your SSLCommerz Store Password.
     * @param bool $is_live True for live environment, false for sandbox.
     */
    public function __construct($store_id, $store_passwd, $is_live = false)
    {
        $this->store_id = $store_id;
        $this->store_passwd = $store_passwd;
        $this->is_live = $is_live;
    }

    /**
     * Get the appropriate API initiation URL based on the environment.
     *
     * @return string The initiation URL.
     */
    private function getInitiateURL()
    {
        return $this->is_live ? self::INITIATE_URL_LIVE : self::INITIATE_URL_SANDBOX;
    }

    /**
     * Get the appropriate API validation URL based on the environment.
     *
     * @return string The validation URL.
     */
    private function getValidationURL()
    {
        return $this->is_live ? self::VALIDATION_URL_LIVE : self::VALIDATION_URL_SANDBOX;
    }

    /**
     * Get the transaction-query API URL (search by tran_id, no val_id needed).
     *
     * @return string
     */
    private function getTransactionQueryURL()
    {
        return $this->is_live ? self::TRANSACTION_QUERY_URL_LIVE : self::TRANSACTION_QUERY_URL_SANDBOX;
    }

    /**
     * Query SSLCommerz by transaction id only.
     *
     * Used by payment recovery: when the browser callback is lost, the session
     * and the REG-xxxx reference are gone, so the only key left is tran_id.
     * This returns the gateway's own record - including value_a (our REG ref),
     * status and amount - so a paid-but-unfinished registration can be completed.
     *
     * @param string $tran_id
     * @return array ['found' => bool, 'valid' => bool, 'status' => string, 'amount' => float,
     *                'currency' => string, 'value_a' => string, 'value_b' => string,
     *                'tran_date' => string, 'bank_tran_id' => string, 'card_type' => string,
     *                'raw' => array, 'error' => string|null]
     */
    public function queryByTransactionId($tran_id)
    {
        $out = [
            'found' => false, 'valid' => false, 'status' => '', 'amount' => 0.0,
            'currency' => '', 'value_a' => '', 'value_b' => '', 'tran_date' => '',
            'bank_tran_id' => '', 'card_type' => '', 'raw' => [], 'error' => null,
        ];

        if (!function_exists('curl_init')) {
            $out['error'] = 'cURL is not available on the server.';
            return $out;
        }

        $tran_id = trim((string) $tran_id);
        if ($tran_id === '') {
            $out['error'] = 'Transaction ID is empty.';
            return $out;
        }

        $url = $this->getTransactionQueryURL()
            . '?tran_id=' . urlencode($tran_id)
            . '&store_id=' . urlencode($this->store_id)
            . '&store_passwd=' . urlencode($this->store_passwd)
            . '&v=1&format=json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $out['error'] = 'cURL Error: ' . curl_error($ch);
            curl_close($ch);
            return $out;
        }
        curl_close($ch);

        $result = json_decode($response, true);
        if (!is_array($result)) {
            $out['error'] = 'Unreadable response from SSLCommerz.';
            return $out;
        }

        $out['raw'] = $result;

        /* The API returns matching transactions under element_data (may hold more
           than one attempt for the same tran_id); fall back to the top level. */
        $row = null;
        if (!empty($result['element_data']) && is_array($result['element_data'])) {
            foreach ($result['element_data'] as $candidate) {
                if (!is_array($candidate)) { continue; }
                $st = strtoupper((string) ($candidate['status'] ?? ''));
                if (in_array($st, ['VALID', 'VALIDATED'], true)) { $row = $candidate; break; }
                if ($row === null) { $row = $candidate; }
            }
        } elseif (!empty($result['tran_id'])) {
            $row = $result;
        }

        if (!$row) {
            $out['error'] = $result['errorReason'] ?? 'No transaction found for this ID.';
            return $out;
        }

        $status = strtoupper((string) ($row['status'] ?? ''));

        $out['found'] = true;
        $out['status'] = $status;
        $out['valid'] = in_array($status, ['VALID', 'VALIDATED'], true);
        $out['amount'] = (float) ($row['currency_amount'] ?? $row['amount'] ?? 0);
        $out['currency'] = (string) ($row['currency_type'] ?? $row['currency'] ?? '');
        $out['value_a'] = (string) ($row['value_a'] ?? '');
        $out['value_b'] = (string) ($row['value_b'] ?? '');
        $out['tran_date'] = (string) ($row['tran_date'] ?? '');
        $out['bank_tran_id'] = (string) ($row['bank_tran_id'] ?? '');
        $out['card_type'] = (string) ($row['card_type'] ?? '');

        return $out;
    }

    /**
     * Initiates a payment request to SSLCommerz.
     *
     * @param array $post_data An associative array of payment parameters.
     * @param bool $redirect If true, redirects the user to SSLCommerz gateway.
     * @return mixed Returns the redirect URL if $redirect is true, otherwise an array of response data.
     * @throws Exception If cURL is not available or other errors occur.
     */
    public function initiate($post_data, $redirect = false)
    {
        if (!function_exists('curl_init')) {
            throw new Exception("cURL is not available. Please enable cURL in your PHP configuration.");
        }

        $post_data['store_id'] = $this->store_id;
        $post_data['store_passwd'] = $this->store_passwd;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getInitiateURL());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For development/testing, set to true in production
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // For development/testing, set to 2 in production

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL Error: " . $error_msg);
        }
        curl_close($ch);

        $result = json_decode($response, true);

        if ($redirect && isset($result['GatewayPageURL']) && $result['GatewayPageURL'] != "") {
            header("Location: " . $result['GatewayPageURL']);
            exit();
        }

        return $result;
    }

    /**
     * Validates the transaction status from SSLCommerz callback.
     *
     * @param array $post_data An associative array of parameters received from SSLCommerz callback.
     * @param string $tran_id The transaction ID to validate.
     * @return bool True if validation is successful and transaction is valid, false otherwise.
     * @throws Exception If cURL is not available or other errors occur.
     */
    public function validate($post_data, $tran_id)
    {
        if (!function_exists('curl_init')) {
            throw new Exception("cURL is not available. Please enable cURL in your PHP configuration.");
        }

        $val_id = $post_data['val_id'] ?? '';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getValidationURL() . "?val_id=" . $val_id . "&store_id=" . $this->store_id . "&store_passwd=" . $this->store_passwd . "&v=1&format=json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For development/testing, set to true in production
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // For development/testing, set to 2 in production

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL Error: " . $error_msg);
        }
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['status']) && $result['status'] == 'VALID' || $result['status'] == 'VALIDATED') {
            if ($result['tran_id'] == $tran_id && $result['currency_type'] == $post_data['currency_type'] && $result['currency_amount'] == $post_data['currency_amount']) {
                return true;
            }
        }
        return false;
    }
}
