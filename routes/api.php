<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PaymentController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

/*Route::post('login', 'API\LoginController@login')->name('login'); // User Login

Route::group(['middleware' => 'auth:api'], function(){

});*/

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::group(['middleware' => 'auth:api'], function(){
    Route::post('details', 'API\UserController@details');
});


//Route::get('students/{student}', 'StudentController@show');

Route::group([], function() {
    Route::post('/confirm-payment', [PaymentController::class, 'ConfirmPayment']);
});
//Route::group(['prefix' => 'UcbBankPaymentApi','middleware' => 'api.token'], function() {
//    //Route::get('/', [\App\Http\Controllers\API\Students\BankApiController::class, 'index']);
//    Route::get('QueryBill/{student}', [\App\Http\Controllers\API\UcbBankPaymentApiController::class, 'QueryBill']);
//    Route::post('ConfirmPayment', [\App\Http\Controllers\API\UcbBankPaymentApiController::class, 'ConfirmPayment']);
//    Route::get('VerifyPayment/{externalRefNo}', [\App\Http\Controllers\API\UcbBankPaymentApiController::class, 'VerifyPayment'])->where('externalRefNo', '[A-Za-z0-9]+');
//    //Route::get('VerifyPayment/{externalRefNo}', [\App\Http\Controllers\API\UcbBankPaymentApiController::class, 'VerifyPayment'])->where('externalRefNo', '[A-Za-z0-9]+');
//    //Route::get('VerifyPayment/{externalRefNo}', 'API\UcbBankPaymentApiController@VerifyPayment');
//
//
//
//});

// routes/api.php

//Route::group([
//    'prefix' => 'v1',  // Versioning
//    'middleware' => ['api.token', 'api.log'],  // Added logging middleware
//    'as' => 'api.v1.'  // Route naming
//], function () {
//
//    // Student Bill Inquiry
//    Route::get('students/{student_id}/bill', [
//        \App\Http\Controllers\API\UcbBankPaymentApiController::class,
//        'QueryBill'
//    ])/*->where('student_id', '\d{9}') */ // 9-digit student ID validation
//    ->name('students.bill.query');
//
//    // Payment Submission
//    Route::post('payments', [
//        \App\Http\Controllers\API\UcbBankPaymentApiController::class,
//        'ConfirmPayment'
//    ])->name('payments.submit');
//
//    // Payment Verification
//    Route::get('payments/verify/{reference_id}', [
//        \App\Http\Controllers\API\UcbBankPaymentApiController::class,
//        'VerifyPayment'
//    ])->where('reference_id', '[A-Za-z0-9]{8,30}')  // Alphanumeric validation
//
//    ->name('payments.verify');
//});
//


//Route::post('/auth/token', 'API\AuthController@login');

Route::group(['prefix' => 'v1'], function () {
    // Public routes (no auth required)
    Route::post('/auth/token', 'API\PaymentController@generateToken');

    // Authenticated routes (JWT required)
    Route::group(['middleware' => 'jwt.auth'], function () {
        // Student information
        Route::get('/students/{studentId}', 'API\PaymentController@getStudentInfo');
        Route::get('/students/details/{studentId}', 'API\PaymentController@getStudentDetailInfo');
        
        // Payment processing
        Route::post('/payments/confirm', 'API\PaymentController@confirmPayment');
        Route::get('/payments/verify/{bankRef}', 'API\PaymentController@verifyPayment');
        Route::post('/payments/cancel', 'API\PaymentController@cancelPayment');
        
        // Reporting
        Route::get('/payments/report-by-date/{date}', 'API\PaymentController@getReportByDate');
    });
});
// Route::group(['prefix' => 'v1','middleware' => 'jwt.auth'], function () {
//     Route::get('/students/{studentId}', 'API\UcbBankPaymentApiController@getStudentInfo');
//     Route::post('/payments', 'API\UcbBankPaymentApiController@pushPayment');
//     Route::post('/payments/cancel', 'API\UcbBankPaymentApiController@cancelPayment');
//     Route::get('/payments', 'API\UcbBankPaymentApiController@queryPayment');
// });

// Temporary test route in routes/web.php
// Route::get('/verify-user', function() {
//     $user = \App\User::where('email', 'testbank@ccnuniversity.com')->first();
    
//     if (!$user) {
//         return "User not found!";
//     }
    
//     // Verify password
//     $password = '123456';
//     $isValid = \Hash::check($password, $user->password);
    
//     return $isValid 
//         ? "Credentials are correct!" 
//         : "Password is invalid. Stored hash: " . $user->password;
// });

use App\Http\Controllers\Api\TipsoiLanController;

// FastFace LAN callbacks (SDK)
Route::post('tipsoi/lan/heartBeatCallback', [TipsoiLanController::class,'heartBeatCallback']); // returns {"result":bool} per SDK. :contentReference[oaicite:30]{index=30}
Route::post('tipsoi/lan/tasks',            [TipsoiLanController::class,'tasks']);
Route::post('tipsoi/lan/task-result',      [TipsoiLanController::class,'taskResult']);
Route::post('tipsoi/lan/finger-reg-callback', [TipsoiLanController::class,'fingerRegCallback']); // after successful finger enroll. :contentReference[oaicite:31]{index=31}
