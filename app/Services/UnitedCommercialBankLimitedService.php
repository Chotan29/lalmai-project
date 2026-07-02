<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // For generating UUID

class UnitedCommercialBankLimitedService
{
    protected $accessKey;
    protected $secretKey;
    protected $profileId;
    protected $transactionType;
    protected $secureAcceptanceUrl;
    protected $isLive;

    public function __construct()
    {
        // Load credentials from environment variables
        $this->accessKey = env('UCBL_ACCESS_KEY');
        $this->secretKey = env('UCBL_SECRET_KEY');
        $this->profileId = env('UCBL_PROFILE_ID');
        $this->transactionType = env('UCBL_TRANSACTION_TYPE', 'sale');
        $this->isLive = env('UCBL_LIVE_MODE', false);

        // Determine the Secure Acceptance URL based on environment
        // For the test environment, use the '/pay' endpoint as observed in your original PHP files.
        // For production, this might be 'https://secureacceptance.cybersource.com/pay' or
        // 'https://secureacceptance.cybersource.com/checkout/pay' depending on your specific
        // UNITED COMMERCIAL BANK LIMITED (CyberSource) account configuration.
        $this->secureAcceptanceUrl = env('UCBL_SECURE_ACCEPTANCE_URL', 'https://secureacceptance.cybersource.com/pay');
        if (!$this->isLive) {
            $this->secureAcceptanceUrl = 'https://testsecureacceptance.cybersource.com/pay'; // Corrected endpoint for test
        }
    }

    /**
     * Initiates a payment by preparing the data and generating the signature.
     * Returns an array containing the form URL and the signed POST data.
     *
     * @param array $paymentData
     * @return array
     */
    public function initiatePayment(array $paymentData): array
    {
        // Define the fields that must be signed. This list is crucial and MUST exactly match
        // the 'signed_field_names' string provided by the bank in their payment_form.php.
        // Any discrepancy will lead to signature validation failures.
        $signedFieldNames = [
            'bill_to_forename', 'bill_to_surname', 'bill_to_address_city',
            'bill_to_address_country', 'bill_to_address_line1', 'bill_to_address_postal_code',
            'bill_to_address_state', 'bill_to_email', 'access_key', 'profile_id',
            'transaction_uuid', 'signed_field_names', 'signed_date_time', 'locale',
            'transaction_type', 'reference_number', 'amount', 'currency', 'auth_trans_ref_no'
        ];

        // Prepare the base data for the request
        $data = [
            'access_key'            => $this->accessKey,
            'profile_id'            => $this->profileId,
            'transaction_uuid'      => (string) Str::uuid(), // Unique transaction ID
            'signed_field_names'    => implode(',', $signedFieldNames), // This string is also signed
            'unsigned_field_names'  => '', // No unsigned fields for this integration type based on bank's sample
            'signed_date_time'      => gmdate("Y-m-d\TH:i:s\Z"), // UTC date and time
            'locale'                => 'en-us', // Changed to 'en' to match bank's sample
            'transaction_type'      => $this->transactionType,
            'reference_number'      => $paymentData['orderId'], // Merchant's unique order ID
            'auth_trans_ref_no'     => $paymentData['orderId'], // Must be same as reference_number

            // Amount and Currency
            'amount'                => number_format($paymentData['amount'], 2, '.', ''),
            'currency'              => $paymentData['currency'],

            // Billing Information (Populated from student data)
            'bill_to_forename'      => $paymentData['studentName'] ?? 'N/A',
            'bill_to_surname'       => 'Student', // Assuming last name is 'Student' if not provided
            'bill_to_email'         => $paymentData['studentEmail'] ?? 'student@example.com',
            'bill_to_address_line1' => $paymentData['studentAddress'] ?? 'N/A',
            'bill_to_address_city'  => $paymentData['studentCity'] ?? 'N/A',
            'bill_to_address_state' => $paymentData['studentState'] ?? 'N/A', // Assuming a state field
            'bill_to_address_postal_code' => $paymentData['studentPostcode'] ?? '1205',
            'bill_to_address_country' => $paymentData['studentCountry'] ?? 'BD',

            // Product Information (These are sent, but NOT included in signed_field_names based on bank's sample)
            'product_name'          => $paymentData['product_name'] ?? 'Fee Payment',
            'product_category'      => $paymentData['product_category'] ?? 'Education',

            // Callback URLs (These are sent, but NOT included in signed_field_names based on bank's sample)
           // 'override_custom_receipt_page' => $paymentData['successUrl'],
            //'override_custom_cancel_page'  => $paymentData['cancelUrl'],

            // Custom fields (These are sent, but NOT included in signed_field_names based on bank's sample)
            //'v_a'                   => $paymentData['value_a'] ?? '',
           // 'v_b'                   => $paymentData['value_b'] ?? '',
            //'v_c'                   => $paymentData['value_c'] ?? '',
           // 'v_d'                   => $paymentData['value_d'] ?? '',
        ];

        // Generate the signature using only the explicitly signed fields
        $data['signature'] = $this->sign($data, $signedFieldNames);

        Log::info("UNITED COMMERCIAL BANK LIMITED Payment Initiation Data", [
            'order_id' => $paymentData['orderId'],
            'data' => $data
        ]);

        return [
            'formUrl' => $this->secureAcceptanceUrl,
            'postData' => $data,
        ];
    }

    /**
     * Generates the HMAC-SHA256 signature for the request data.
     *
     * @param array $params The parameters to sign.
     * @param array $signedFieldNames The list of field names to include in the signature.
     * @return string The generated signature.
     */
    protected function sign(array $params, array $signedFieldNames): string
    {
        $dataToSign = [];
        foreach ($signedFieldNames as $field) {
            // Ensure the field exists in the parameters before adding to dataToSign
            if (isset($params[$field])) {
                $dataToSign[] = $field . '=' . $params[$field];
            }
        }
        $dataToSign = implode(',', $dataToSign);

        return base64_encode(hash_hmac('sha256', $dataToSign, $this->secretKey, true));
    }

    /**
     * Validates the callback data received from CyberSource.
     *
     * @param array $callbackData The data received from CyberSource.
     * @return bool True if the signature is valid and transaction status is acceptable, false otherwise.
     */
    public function validateCallback(array $callbackData): bool
    {
        // Check if essential fields are present
        if (!isset($callbackData['signature'], $callbackData['signed_field_names'])) {
            Log::error("UNITED COMMERCIAL BANK LIMITED Callback Validation Failed: Missing signature or signed_field_names.", ['data' => $callbackData]);
            return false;
        }

        // Use the signed_field_names provided IN THE CALLBACK to validate
        $signedFieldNames = explode(',', $callbackData['signed_field_names']);
        $expectedSignature = $this->sign($callbackData, $signedFieldNames);

        if ($expectedSignature !== $callbackData['signature']) {
            Log::error("UNITED COMMERCIAL BANK LIMITED Callback Validation Failed: Signature mismatch.", [
                'received_signature' => $callbackData['signature'],
                'expected_signature' => $expectedSignature,
                'data' => $callbackData
            ]);
            return false;
        }

        // Check the decision and reason code for transaction status
        $decision = $callbackData['decision'] ?? 'ERROR';
        $reasonCode = $callbackData['reason_code'] ?? '000';

        // Log the decision and reason code
        Log::info("UNITED COMMERCIAL BANK LIMITED Callback Decision and Reason Code", [
            'decision' => $decision,
            'reason_code' => $reasonCode,
            'transaction_id' => $callbackData['transaction_id'] ?? 'N/A'
        ]);

        // Define what constitutes a successful transaction based on CyberSource documentation (Reason Code 100 for success)
        return ($decision === 'ACCEPT' && $reasonCode === '100');
    }

    /**
     * Get the payment status from the callback data.
     * This method maps CyberSource decision/status to your internal statuses.
     *
     * @param array $callbackData
     * @return string 'completed', 'cancelled', 'failed', or 'pending'
     */
    public function getPaymentStatusFromCallback(array $callbackData): string
    {
        $decision = strtolower($callbackData['decision'] ?? 'error');
        $reasonCode = $callbackData['reason_code'] ?? '000';

        switch ($decision) {
            case 'accept':
                // Reason code 100 for successful transaction
                if ($reasonCode === '100') {
                    return 'completed';
                }
                break;
            case 'decline':
                // Specific reason codes for decline, e.g., customer entered 3-D Secure credentials incorrectly
                // or processor declined.
                return 'failed';
            case 'cancel':
                // Customer cancelled the transaction
                return 'cancelled';
            case 'error':
                // Access denied, page not found, internal server error (reason codes 102, 104, 150, 151, 152)
                return 'failed';
            case 'review':
                // Authorization declined, but capture might be possible (reason codes 200, 201, 230, 520)
                // You might treat this as pending or failed, depending on your business logic.
                return 'pending'; // Or 'failed' if you don't want to review manually
        }

        // Default to failed for any unhandled or ambiguous cases
        return 'failed';
    }
}
