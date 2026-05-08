<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', [HomeController::class, 'welcome'])->name('home');

Route::middleware('auth','user-dashboard')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
});

// Account dashboard
Route::get('/account/dashboard', [HomeController::class, 'accountDashboard'])->name('account.dashboard');

// Online Registration Routes
Route::group(['prefix' => 'setting'], function () {
    Route::get('online-registration', 'Setting\OnlineRegistrationSettingController@index')->name('setting.online-registration');
    Route::post('online-registration/find-semester', 'Setting\OnlineRegistrationSettingController@findSemester')->name('setting.online-registration.find-semester');
});

// Add more routes as needed...