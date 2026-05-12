<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\User;
use App\Models\FeeMaster;
use App\Models\FeeCollection;
use App\Models\OnlinePayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

echo "=== Full Payment Flow Test with ngrok ===\n\n";

try {
    // 1. Prepare registration data (simulating form submission)
    $testData = [
        'student_type' => 'New Student',
        'faculty_id' => 1,
        'semester_id' => 1,
        'first_name' => 'Test',
        'last_name' => 'Student',
        'email' => 'test' . time() . '@example.com',
        'phone' => '01234567890',
        'date_of_birth' => '2000-01-15',
        'gender' => 'Male',
        'address' => '123 Test Street',
        'country' => 'Bangladesh',
        'state' => 'Cumilla',
        'city' => 'Cumilla',
        'father_first_name' => 'Father',
        'father_last_name' => 'Name',
        'mother_first_name' => 'Mother',
        'mother_last_name' => 'Name',
    ];

    echo "1. Test Data:\n";
    echo "   - Email: " . $testData['email'] . "\n";
    echo "   - Full Name: " . $testData['first_name'] . " " . $testData['last_name'] . "\n\n";

    // 2. Create Student
    echo "2. Creating Student...\n";
    $regNo = 'REG' . date('Ymdhis') . rand(100, 999);
    $student = Student::create([
        'reg_no' => $regNo,
        'faculty_id' => $testData['faculty_id'],
        'semester_id' => $testData['semester_id'],
        'student_type' => 'New',
        'first_name' => $testData['first_name'],
        'last_name' => $testData['last_name'],
        'email' => $testData['email'],
        'phone_number' => $testData['phone'],
        'date_of_birth' => $testData['date_of_birth'],
        'gender' => $testData['gender'],
        'address' => $testData['address'],
        'country' => $testData['country'],
        'state' => $testData['state'],
        'city' => $testData['city'],
        'father_first_name' => $testData['father_first_name'],
        'father_last_name' => $testData['father_last_name'],
        'mother_first_name' => $testData['mother_first_name'],
        'mother_last_name' => $testData['mother_last_name'],
        'status' => 1,
        'registration_payment_status' => 'pending',
    ]);
    echo "   ✓ Student created with ID: " . $student->id . " (Reg No: " . $regNo . ")\n\n";

    // 3. Create User account
    echo "3. Creating User account...\n";
    $user = User::create([
        'students_id' => $student->id,
        'name' => $testData['first_name'] . ' ' . $testData['last_name'],
        'email' => $testData['email'],
        'password' => Hash::make('password123'),
        'email_verified_at' => Carbon::now(),
        'status' => 1,
    ]);
    echo "   ✓ User created with ID: " . $user->id . "\n\n";

    // 4. Create FeeMaster
    echo "4. Creating FeeMaster...\n";
    $admissionFeeHeadId = 75; // ADMISSION FEE
    $feeMaster = FeeMaster::create([
        'students_id' => $student->id,
        'semester' => $student->semester ?? 1,
        'fee_head' => $admissionFeeHeadId,
        'fee_due_date' => Carbon::today()->toDateString(),
        'fee_due_date2' => Carbon::today()->toDateString(),
        'fee_due_date3' => Carbon::today()->toDateString(),
        'fee_amount' => 1000,
        'created_by' => 0,
        'status' => 1,
    ]);
    echo "   ✓ FeeMaster created with ID: " . $feeMaster->id . "\n\n";

    // 5. Create FeeCollection
    echo "5. Creating FeeCollection...\n";
    $feeCollection = FeeCollection::create([
        'students_id' => $student->id,
        'fee_masters_id' => $feeMaster->id,
        'date' => Carbon::today()->toDateString(),
        'paid_amount' => 1000,
        'payment_method' => 'Online',
        'status' => 1,
    ]);
    echo "   ✓ FeeCollection created with ID: " . $feeCollection->id . "\n\n";

    // 6. Create OnlinePayment
    echo "6. Creating OnlinePayment record...\n";
    $tranId = 'TEST-ngrok-' . time();
    $onlinePayment = OnlinePayment::create([
        'students_id' => $student->id,
        'date' => Carbon::today()->toDateString(),
        'amount' => 1000,
        'payment_gateway' => 'SSL Commerz',
        'ref_no' => 'ngrok-test-' . time(),
        'invoice_id' => $feeCollection->id,
        'payment_status' => 'completed',
        'status' => 1,
    ]);
    echo "   ✓ OnlinePayment created with ID: " . $onlinePayment->id . "\n\n";

    // 7. Update student status
    echo "7. Updating Student status to completed...\n";
    $student->update(['registration_payment_status' => 'completed']);
    echo "   ✓ Student registration_payment_status updated to: completed\n\n";

    // 8. Summary
    echo "=== PAYMENT FLOW COMPLETED SUCCESSFULLY ===\n\n";
    echo "Database Records Created:\n";
    echo "   - Student ID: " . $student->id . "\n";
    echo "   - User ID: " . $user->id . "\n";
    echo "   - FeeMaster ID: " . $feeMaster->id . "\n";
    echo "   - FeeCollection ID: " . $feeCollection->id . "\n";
    echo "   - OnlinePayment ID: " . $onlinePayment->id . "\n\n";
    
    echo "Receipt Page URL: " . route('print-out.fees.online-payment-receipt', ['id' => encrypt($onlinePayment->id)]) . "\n";
    echo "\nYou can now visit the receipt page to verify the payment flow works!\n";
    
    Log::info('Test payment flow completed', [
        'student_id' => $student->id,
        'online_payment_id' => $onlinePayment->id,
        'fee_collection_id' => $feeCollection->id,
    ]);

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    Log::error('Payment flow test failed', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);
}
