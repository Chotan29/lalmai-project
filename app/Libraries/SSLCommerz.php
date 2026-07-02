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
