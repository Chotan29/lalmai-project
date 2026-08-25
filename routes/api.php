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

/*
 * Under Attendance/Device, not API/. That folder is spelled with capitals while its namespaces
 * say Api - Windows treats the two as the same, Linux does not, and on live psr-4 could not find
 * the class at all. See the note in the controller.
 */
use App\Http\Controllers\Attendance\Device\TipsoiLanController;

/*
 * Tipsoi FastFace, calling in.
 *
 * These five paths are not ours to choose - they are what the device is configured to post to,
 * and they are the same paths the manufacturer's own cloud serves under api-inovace360.com.
 * Serving them here is what lets the college point the device at its own server and stop paying
 * for somebody else's. Taken from the vendor's demo, Fast_Face_python_demo/configure_callbacks.py.
 *
 * Another brand gets its own group beside this one, with its own controller. Both end up in the
 * same shared PunchIngestor, so attendance and the guardian message are written once.
 *
 * No auth middleware: the device cannot present a token or a session. It is identified by the
 * serial number it sends, and an unknown serial is recorded and otherwise ignored.
 */
Route::prefix('face/uface5/v1')->group(function () {
    Route::post('/',            [TipsoiLanController::class, 'heartbeat']);
    Route::post('recog',        [TipsoiLanController::class, 'recognition']);
    Route::post('img-reg',      [TipsoiLanController::class, 'imgReg']);
    Route::post('get-task',     [TipsoiLanController::class, 'getTask']);
    Route::post('task-result',  [TipsoiLanController::class, 'taskResult']);

    /* Some firmware sends GET for the polling calls. Answering both costs nothing and saves a
       silent failure that would look exactly like a dead device. */
    Route::get('/',             [TipsoiLanController::class, 'heartbeat']);
    Route::get('get-task',      [TipsoiLanController::class, 'getTask']);
});

/*
 * The earlier LAN paths, before the vendor demo showed what the device really calls. Kept so
 * anything already pointed at them keeps working; they forward to the same handlers.
 */
Route::post('tipsoi/lan/heartBeatCallback',   [TipsoiLanController::class,'heartBeatCallback']);
Route::post('tipsoi/lan/tasks',               [TipsoiLanController::class,'tasks']);
Route::post('tipsoi/lan/task-result',         [TipsoiLanController::class,'taskResult']);
Route::post('tipsoi/lan/finger-reg-callback', [TipsoiLanController::class,'fingerRegCallback']);
