@php
// This is a test/manual callback simulator for payment testing on localhost
// Use this ONLY for development/testing - NOT for production

$tran_id = $_GET['tran_id'] ?? $_POST['tran_id'] ?? null;

if (!$tran_id) {
    die('Error: Missing tran_id parameter');
}

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Student\RegistrationPaymentController;

echo "Manual SSL Commerz Callback Simulator\n";
echo "=====================================\n";
echo "Transaction ID: $tran_id\n\n";

// Simulate a successful SSL response
$fakeRequest = new Request([
    'tran_id' => $tran_id,
    'val_id' => 'unique_validation_id_' . $tran_id,
    'amount' => '10.00',
    'currency' => 'BDT',
    'status' => 'VALID',
    'card_type' => 'VISA',
    'card_no' => '4111111111111111',
    'card_issuer' => 'CREDIT',
    'card_issuer_country' => 'BD',
    'card_issuer_country_code' => '050',
    'currency_amount' => '10.00',
    'currency_code' => '050',
    'base_fair' => '0.00',
    'tds' => '0.00',
    'vai_id' => '',
    'eci' => '0',
    'risk_level' => '0',
    'risk_title' => 'ok',
    'payment_option' => 'creditdebit',
    'verify_sign' => 'test_signature',
    'verify_key' => 'test_key',
    'risk_summary' => 'SAFE',
    'APIConnect' => 'N/A'
]);

// Get session data to verify payment info exists
$sessionKey = 'registration_payment_data:' . $tran_id;
$paymentData = Illuminate\Support\Facades\Cache::get($sessionKey);

if (!$paymentData) {
    echo "ERROR: No payment data found for transaction $tran_id\n";
    echo "Payment data may have expired or was not properly cached.\n";
    die();
}

echo "Found payment data:\n";
echo "  Student Type: " . $paymentData['student_type'] . "\n";
echo "  Amount: " . $paymentData['amount'] . "\n";
echo "  Gateway: SSL Commerz\n\n";

// Create a controller instance
$controller = new RegistrationPaymentController(
    app('App\Services\SslCommerzService'),
    app('App\Services\UnitedCommercialBankLimitedService')
);

// Manually call the success callback
echo "Processing callback...\n";
try {
    // We need to manually set session data before calling
    $fakeRequest->session()->put('registration_payment_data', $paymentData);
    $fakeRequest->session()->put('registration_payment_ref', $tran_id);
    
    // Call the SSL success handler
    $response = $controller->sslSuccess($fakeRequest);
    
    echo "SUCCESS: Callback processed\n";
    echo "Response: " . $response->getTargetUrl() . "\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
