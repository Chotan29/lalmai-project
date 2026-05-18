<?php

use Illuminate\Support\Facades\Route;

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

// Online Registration Routes
Route::group(['prefix' => 'setting'], function () {
    Route::get('online-registration', 'Setting\OnlineRegistrationSettingController@index')->name('setting.online-registration');
    Route::post('online-registration/find-semester', 'Setting\OnlineRegistrationSettingController@findSemester')->name('setting.online-registration.find-semester');
});

// routes/web.php

// (Optional but recommended) force the {routine} param to be numeric
// Force numeric id for resource {routine} so /routine/manage isn't captured
// Route::pattern('routine', '[0-9]+');

// // Manage flow (BEFORE the resource)
// Route::get('routine/manage', 'Academic\ClassRoutineController@manage')->name('routine.manage');

// // Existing schedules (new name)
// Route::get('routine/schedules', 'Academic\ClassRoutineController@getExistingSchedules')->name('routine.schedules');

// // Legacy alias used by your Blade
// Route::get('routine/existing', 'Academic\ClassRoutineController@getExistingSchedules')->name('routine.existing');

// Route::post('routine/save', 'Academic\ClassRoutineController@save')->name('routine.save');

// // Resource after custom routes
// Route::resource('routine', 'Academic\ClassRoutineController');

// // Route::get('routine/print/{departmentId}/{batchId}', 'Academic\ClassRoutineController@printBatch')
// //     ->where(['departmentId' => '[0-9]+', 'batchId' => '[0-9]+'])
// //     ->name('routine.print.batch');

// // Printing
// Route::prefix('routine/print')->name('routine.print.')->group(function () {
//     Route::get('department/{departmentId}',               'Academic\ClassRoutineController@printDepartment')->name('department');
//     Route::get('faculty/{departmentId}/{facultyId}',      'Academic\ClassRoutineController@printFaculty')->name('faculty');
//     Route::get('semester/{departmentId}/{facultyId}/{semesterId}', 'Academic\ClassRoutineController@printSemester')->name('semester');
//     Route::get('batch/{departmentId}/{batchId}',          'Academic\ClassRoutineController@printBatch')->name('batch');
//     Route::get('subject/{departmentId}/{facultyId}/{semesterId}/{subjectId}', 'Academic\ClassRoutineController@printSubjectAllBatches')->name('subject.all');
//     Route::get('subject/{departmentId}/{facultyId}/{semesterId}/{subjectId}/batch/{batchId}', 'Academic\ClassRoutineController@printSubjectForBatch')->name('subject.batch');
// });


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

/*Auth Routes*/
Auth::routes();






/*for Dashboard's*/
//Route::get('/',                                      ['as' => 'dashboard',                                                    'uses' => 'HomeController@welcome']);
// routes/web.php
// Route::get('/dashboard', [\App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

// Route::get('/dashboard/summary', [\App\Http\Controllers\HomeController::class, 'summary'])->name('dashboard.summary');

// routes/web.php
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'welcome'])->name('home');

Route::middleware('auth','user-dashboard')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    // Route::get('/dashboard/summary', [HomeController::class, 'summary'])->name('dashboard.summary');
});

// routes/web.php

Route::group(['middleware' => ['ability:super-admin,student-index']], function () {
    Route::get('students/dashboard', [
        'as'   => 'students.dashboard',
        'uses' => 'Student\StudentDashboardController@index',
    ]);
    Route::get('students/dashboard/summary', [
        'as'   => 'students.summary.chart',
        'uses' => 'Student\StudentDashboardController@index',
    ]);

    Route::get('students/export', [
        'as'   => 'students.dashboard.export',
        'uses' => 'Student\StudentDashboardController@summary',
    ]);

    // Route::get('students/summary-chart', [
    //     'as'   => 'students.summary.chart',
    //     'uses' => 'Student\StudentDashboardController@summary',
    // ]);
});


// Account dashboard (fix method name)
Route::get('/account/dashboard', [HomeController::class, 'accountDashboard'])->name('account.dashboard');
// Route::get('/account/summary-chart', [HomeController::class, 'accountSummaryChart'])->name('account.summary.chart');

// Fees dashboard
Route::group(['middleware' => ['ability:super-admin,fees-index']], function () {
    Route::get('fees/dashboard', [
        'as'   => 'fees.dashboard',
        'uses' => 'Account\Fees\FeesDashboardController@index',
    ]);
    Route::get('fees/summary-chart', [
        'as'   => 'fees.summary.chart',
        'uses' => 'Account\Fees\FeesDashboardController@summary',
    ]);
});

// Academic dashboard
Route::get('academic/dashboard', [
    'as'         => 'academic.dashboard',
    'middleware' => ['ability:super-admin,academic-dashboard'],
    'uses'       => 'Academic\AcademicDashboardController@index'
]);

Route::get('academic/summary-chart', [
    'as'         => 'academic.summary.chart',
    'middleware' => ['ability:super-admin,academic-dashboard'],
    'uses'       => 'Academic\AcademicDashboardController@summaryChart'
]);



/*Route::get('/',                    ['as' => 'home',                        'uses' => 'Controller@index']);*/

/*Roles Routes*/
Route::get('role',                    ['as' => 'role',                  'middleware' => ['ability:super-admin,role-index'],         'uses' => 'RoleController@index']);
Route::get('role/add',                ['as' => 'role.add',              'middleware' => ['ability:super-admin,role-add'],           'uses' => 'RoleController@create']);
Route::post('role/store',             ['as' => 'role.store',            'middleware' => ['ability:super-admin,role-add'],           'uses' => 'RoleController@store']);
Route::get('role/{id}/edit',          ['as' => 'role.edit',             'middleware' => ['ability:super-admin,role-edit'],          'uses' => 'RoleController@edit']);
Route::post('role/{id}/update',       ['as' => 'role.update',           'middleware' => ['ability:super-admin,role-edit'],          'uses' => 'RoleController@update']);
Route::get('role/{id}/view',          ['as' => 'role.view',             'middleware' => ['ability:super-admin,role-view'],          'uses' => 'RoleController@show']);
Route::get('role/{id}/delete',        ['as' => 'role.delete',           'middleware' => ['ability:super-admin,role-delete'],        'uses' => 'RoleController@destroy']);

/*User Routes*/
Route::get('user',                          ['as' => 'user',                  'middleware' => ['ability:super-admin,user-index'],             'uses' => 'UserController@index']);
Route::get('user/add',                      ['as' => 'user.add',              'middleware' => ['ability:super-admin,user-add'],               'uses' => 'UserController@add']);
Route::post('user/store',                   ['as' => 'user.store',            'middleware' => ['ability:super-admin,user-add'],               'uses' => 'UserController@store']);
Route::get('user/{id}/edit',                ['as' => 'user.edit',             'middleware' => ['ability:super-admin,user-edit'],              'uses' => 'UserController@edit']);
Route::post('user/{id}/update',             ['as' => 'user.update',           'middleware' => ['ability:super-admin,user-edit'],              'uses' => 'UserController@update']);
Route::post('user/{id}/password-change',    ['as' => 'user.password-change',                                                               'uses' => 'UserController@changePassword']);
Route::get('user/{id}/view',                ['as' => 'user.view',                                                                             'uses' => 'UserController@view']);
Route::get('user/{id}/delete',              ['as' => 'user.delete',           'middleware' => ['ability:super-admin,user-delete'],            'uses' => 'UserController@delete']);
Route::get('user/{id}/active',              ['as' => 'user.active',           'middleware' => ['ability:super-admin,user-active'],            'uses' => 'UserController@Active']);
Route::get('user/{id}/in-active',           ['as' => 'user.in-active',        'middleware' => ['ability:super-admin,user-in-active'],         'uses' => 'UserController@inActive']);
Route::post('user/bulk-action',             ['as' => 'user.bulk-action',      'middleware' => ['ability:super-admin,user-bulk-action'],       'uses' => 'UserController@bulkAction']);

/*user-student route group*/
Route::group(['prefix' => 'user-student/',          'as' => 'user-student',         'middleware' => ['role:student'],     'namespace' => 'UserStudent\\'], function () {

    Route::get('',                                  ['as' => '',                        'middleware' => ['permission:student-dashboard'],           'uses' => 'HomeController@index']);

    Route::get('profile',                           ['as' => '.profile',                'middleware' => ['permission:student-profile'],         'uses' => 'HomeController@profile']);
    Route::get('profile/{id}/edit',                 ['as' => '.profile.edit',           'middleware' => ['permission:student-profile-edit'],         'uses' => 'HomeController@editProfile']);
    Route::post('profile/{id}/update',              ['as' => '.profile.update',         'middleware' => ['permission:student-profile-edit'],         'uses' => 'HomeController@updateProfile']);
    Route::post('{id}/password',                    ['as' => '.password',               'middleware' => ['permission:student-profile-edit'],         'uses' => 'HomeController@password']);

    Route::get('fees',                              ['as' => '.fees',                   'middleware' => ['permission:student-fees'],         'uses' => 'HomeController@fees']);

    Route::get('library',                           ['as' => '.library',                        'middleware' => ['permission:student-library'],     'uses' => 'HomeController@library']);
    Route::get('library/book-list',                 ['as' => '.library.book-list',              'middleware' => ['permission:student-library'],     'uses' => 'HomeController@bookList']);
    Route::get('library/{id}/request-book',         ['as' => '.library.request-book',            'middleware' => ['permission:student-library'],    'uses' => 'HomeController@requestBook']);

    Route::get('attendance',                        ['as' => '.attendance',                         'middleware' => ['permission:student-attendance'],  'uses' => 'HomeController@attendance']);
    // Student panel → Attendance JSON feeds
    Route::get('attendance/regular-summary', [
        'as' => '.attendance.regular-summary',
        'middleware' => ['permission:student-attendance'],
        'uses' => 'HomeController@attendanceRegularSummary'
    ]);

    Route::get('attendance/subject-summary', [
        'as' => '.attendance.subject-summary',
        'middleware' => ['permission:student-attendance'],
        'uses' => 'HomeController@attendanceSubjectSummary'
    ]);

    // JSON endpoints for the student panel attendance page
    Route::get('attendance/regular-json', [
        'as'   => '.attendance.regular.json',
        'middleware' => ['permission:student-attendance'],
        'uses' => 'HomeController@attendanceRegularJson'
    ]);

    Route::get('attendance/subject-json', [
        'as'   => '.attendance.subject.json',
        'middleware' => ['permission:student-attendance'],
        'uses' => 'HomeController@attendanceSubjectJson'
    ]);



    Route::get('exams',                                                            ['as' => '.exams',               'middleware' => ['permission:student-exam'],     'uses' => 'HomeController@exams']);
    Route::get('exam-schedule/{year}/{month}/{exam}/{faculty}/{semester}',         ['as' => '.exam-schedule',       'middleware' => ['permission:student-exam'],     'uses' => 'HomeController@examSchedule']);
    Route::get('exam-admit-card/{year}/{month}/{exam}/{faculty}/{semester}',       ['as' => '.exam-admit-card',     'middleware' => ['permission:student-exam'],     'uses' => 'HomeController@admitCard']);
    Route::get('exam-score/{year}/{month}/{exam}/{faculty}/{semester}',            ['as' => '.exam-score',          'middleware' => ['permission:student-exam'],     'uses' => 'HomeController@examScore']);

    Route::get('hostel',                ['as' => '.hostel',             'middleware' => ['permission:student-hostel'],         'uses' => 'HomeController@hostel']);
    Route::get('transport',             ['as' => '.transport',          'middleware' => ['permission:student-transport'],         'uses' => 'HomeController@transport']);
    Route::get('subject',               ['as' => '.subject',            'middleware' => ['permission:student-course'],         'uses' => 'HomeController@subject']);
    Route::get('routine',               ['as' => '.routine',            'middleware' => ['permission:student-routine'],         'uses' => 'HomeController@routine']);
    Route::get('routine',               ['as' => '.routine',                  'uses' => 'HomeController@routine']);
    Route::get('notice',                ['as' => '.notice',             'middleware' => ['permission:student-notice'],         'uses' => 'HomeController@notice']);
    Route::get('download',              ['as' => '.download',           'middleware' => ['permission:student-download'],          'uses' => 'HomeController@download']);

    Route::get('assignment',                                    ['as' => '.assignment',                     'middleware' => ['permission:student-assignment'],         'uses' => 'HomeController@assignment']);
    Route::get('assignment/answer/{id}/add',                    ['as' => '.assignment.answer.add',          'middleware' => ['permission:student-assignment'],         'uses' => 'HomeController@addAnswer']);
    Route::post('assignment/answer/store',                      ['as' => '.assignment.answer.store',        'middleware' => ['permission:student-assignment'],         'uses' => 'HomeController@storeAnswer']);
    Route::get('assignment/answer/{id}/edit',                   ['as' => '.assignment.answer.edit',         'middleware' => ['permission:student-assignment'],         'uses' => 'HomeController@editAnswer']);
    Route::post('assignment/answer/{id}/update',                ['as' => '.assignment.answer.update',       'middleware' => ['permission:student-assignment'],         'uses' => 'HomeController@updateAnswer']);
    Route::get('assignment/answer/{id}/{answer}/view',          ['as' => '.assignment.answer.view',         'middleware' => ['permission:student-assignment'],         'uses' => 'HomeController@viewAssignmentAnswer']);

    Route::get('application',                           ['as' => '.application',              'middleware' => ['permission:student-application'],         'uses' => 'HomeController@applicationIndex']);
    Route::get('application/add',                       ['as' => '.application.add',          'middleware' => ['permission:student-application'],         'uses' => 'HomeController@applicationAdd']);
    Route::post('application/store',             ['as' => '.application.store',        'middleware' => ['permission:student-application'],         'uses' => 'HomeController@applicationStore']);
    Route::get('application/{id}/edit',                      ['as' => '.application.edit',         'middleware' => ['permission:student-application'],         'uses' => 'HomeController@applicationEdit']);
    Route::post('application/{id}/update',                   ['as' => '.application.update',       'middleware' => ['permission:student-application'],         'uses' => 'HomeController@applicationUpdate']);
    Route::get('application/{id}/delete',                   ['as' => '.application.delete',       'middleware' => ['permission:student-application'],         'uses' => 'HomeController@applicationDelete']);
    Route::get('application/{id}/view',                      ['as' => '.application.view',         'middleware' => ['permission:student-application'],         'uses' => 'HomeController@applicationView']);

    Route::get('meeting',              ['as' => '.meeting',           'middleware' => ['permission:student-meeting'],          'uses' => 'HomeController@meeting']);

});

/*User Guardian User Route group*/
Route::group(['prefix' => 'user-guardian/',          'as' => 'user-guardian',    'middleware' => ['role:guardian'],   'namespace' => 'UserGuardian\\'], function () {
    /*user-student route group*/
    Route::get('',                              ['as' => '',                'middleware' => ['permission:guardian-dashboard'],      'uses' => 'HomeController@index']);
    Route::get('profile',                       ['as' => '.profile',        'middleware' => ['permission:guardian-profile'],        'uses' => 'HomeController@profile']);
    Route::post('{id}/password',                ['as' => '.password',       'middleware' => ['permission:guardian-profile-edit'],   'uses' => 'HomeController@password']);
    Route::get('notice',                        ['as' => '.notice',         'middleware' => ['permission:guardian-notice'],         'uses' => 'HomeController@notice']);

    //guardian's student wise summary routes
    Route::get('students',                      ['as' => '.students',               'middleware' => ['permission:guardian-student-list'],           'uses' => 'HomeController@students']);
    Route::get('students/{id}/profile',         ['as' => '.students.profile',       'middleware' => ['permission:guardian-student-profile'],        'uses' => 'HomeController@studentProfile']);
    Route::get('students/{id}/fees',            ['as' => '.students.fees',          'middleware' => ['permission:guardian-student-fees'],           'uses' => 'HomeController@fees']);
    Route::get('students/{id}/library',         ['as' => '.students.library',       'middleware' => ['permission:guardian-student-library'],        'uses' => 'HomeController@library']);
    Route::get('students/{id}/attendance',      ['as' => '.students.attendance',    'middleware' => ['permission:guardian-student-attendance'],     'uses' => 'HomeController@attendance']);
    Route::get('students/{id}/hostel',          ['as' => '.students.hostel',        'middleware' => ['permission:guardian-student-hostel'],         'uses' => 'HomeController@hostel']);
    Route::get('students/{id}/transport',       ['as' => '.students.transport',     'middleware' => ['permission:guardian-student-transport'],      'uses' => 'HomeController@transport']);
    Route::get('students/{id}/subject',         ['as' => '.students.subject',       'middleware' => ['permission:guardian-student-course'],        'uses' => 'HomeController@subject']);
    Route::get('students/{id}/download',        ['as' => '.students.download',      'middleware' => ['permission:guardian-student-download'],       'uses' => 'HomeController@download']);

    Route::get('students/{id}/exams',                                                            ['as' => '.students.exams',              'middleware' => ['permission:guardian-student-exam'],      'uses' => 'HomeController@exams']);
    Route::get('students/{id}/exam-schedule/{year}/{month}/{exam}/{faculty}/{semester}',         ['as' => '.students.exam-schedule',      'middleware' => ['permission:guardian-student-exam'],      'uses' => 'HomeController@examSchedule']);
    Route::get('students/{id}/exam-admit-card/{year}/{month}/{exam}/{faculty}/{semester}',       ['as' => '.students.exam-admit-card',    'middleware' => ['permission:guardian-student-exam'],      'uses' => 'HomeController@admitCard']);
    Route::get('students/{id}/exam-score/{year}/{month}/{exam}/{faculty}/{semester}',            ['as' => '.students.exam-score',         'middleware' => ['permission:guardian-student-exam'],      'uses' => 'HomeController@examScore']);

    Route::get('students/{id}assignment/',                                                 ['as' => '.students.assignment',                  'middleware' => ['permission:guardian-student-assignment'],        'uses' => 'HomeController@assignment']);
    Route::get('students/{id}/assignment/answer/{assignment}/{answer}/view',               ['as' => '.students.assignment.answer.view',      'middleware' => ['permission:guardian-student-assignment'],        'uses' => 'HomeController@viewAssignmentAnswer']);

});

/*user-Staff route group*/
Route::group(['prefix' => 'user-staff/',          'as' => 'user-staff',    'middleware' => ['role:staff'],   'namespace' => 'UserStaff\\'], function () {
    Route::get('',                                  ['as' => '',                                'uses' => 'HomeController@index']);
    Route::post('{id}/password',                    ['as' => '.password',                       'uses' => 'HomeController@password']);
    Route::get('payroll',                           ['as' => '.payroll',                        'uses' => 'HomeController@payroll']);

    Route::get('library',                           ['as' => '.library',                        'uses' => 'HomeController@library']);
    Route::get('library/book-list',                 ['as' => '.library.book-list',              'uses' => 'HomeController@bookList']);
    Route::get('library/{id}/request-book',         ['as' => '.library.request-book',           'uses' => 'HomeController@requestBook']);

    Route::get('attendance',            ['as' => '.attendance',         'uses' => 'HomeController@attendance']);
    Route::get('exam-score',            ['as' => '.exam-score',         'uses' => 'HomeController@examScore']);
    Route::get('hostel',                ['as' => '.hostel',             'uses' => 'HomeController@hostel']);
    Route::get('transport',             ['as' => '.transport',          'uses' => 'HomeController@transport']);
    Route::get('subject',               ['as' => '.subject',            'uses' => 'HomeController@subject']);
    Route::get('notice',                ['as' => '.notice',              'uses' => 'HomeController@notice']);

});

/*Front Desk Grouping */
Route::group(['prefix' => 'front/',                                    'as' => 'front',                                       'namespace' => 'Front\\'], function () {

    /*Postal Exchange Routes*/
    Route::get('postal-exchange',                    ['as' => '.postal-exchange',                     'middleware' => ['ability:super-admin,postal-exchange-index'],                'uses' => 'PostalExchangeController@index']);
    Route::get('postal-exchange/add',                ['as' => '.postal-exchange.add',                 'middleware' => ['ability:super-admin,postal-exchange-add'],                  'uses' => 'PostalExchangeController@add']);
    Route::post('postal-exchange/store',             ['as' => '.postal-exchange.store',               'middleware' => ['ability:super-admin,postal-exchange-add'],                  'uses' => 'PostalExchangeController@store']);
    Route::get('postal-exchange/{id}/edit',          ['as' => '.postal-exchange.edit',                'middleware' => ['ability:super-admin,postal-exchange-edit'],                 'uses' => 'PostalExchangeController@edit']);
    Route::post('postal-exchange/{id}/update',       ['as' => '.postal-exchange.update',              'middleware' => ['ability:super-admin,postal-exchange-edit'],                 'uses' => 'PostalExchangeController@update']);
    Route::get('postal-exchange/{id}/delete',        ['as' => '.postal-exchange.delete',              'middleware' => ['ability:super-admin,postal-exchange-delete'],               'uses' => 'PostalExchangeController@delete']);
    Route::post('postal-exchange/bulk-action',       ['as' => '.postal-exchange.bulk-action',         'middleware' => ['ability:super-admin,postal-exchange-bulk-action'],          'uses' => 'PostalExchangeController@bulkAction']);

    /*Postal Exchange Type*/
    Route::get('postal-exchange/type',                   ['as' => '.postal-exchange.type',                'middleware' => ['ability:super-admin,postal-exchange-type-index'],        'uses' => 'PostalExchangeTypeController@index']);
    Route::post('postal-exchange/type/store',            ['as' => '.postal-exchange.type.store',          'middleware' => ['ability:super-admin,postal-exchange-type-add'],          'uses' => 'PostalExchangeTypeController@store']);
    Route::get('postal-exchange/type/{id}/edit',         ['as' => '.postal-exchange.type.edit',           'middleware' => ['ability:super-admin,postal-exchange-type-edit'],         'uses' => 'PostalExchangeTypeController@edit']);
    Route::post('postal-exchange/type/{id}/update',      ['as' => '.postal-exchange.type.update',         'middleware' => ['ability:super-admin,postal-exchange-type-edit'],         'uses' => 'PostalExchangeTypeController@update']);
    Route::get('postal-exchange/type/{id}/delete',       ['as' => '.postal-exchange.type.delete',         'middleware' => ['ability:super-admin,postal-exchange-type-delete'],       'uses' => 'PostalExchangeTypeController@delete']);
    Route::get('postal-exchange/type/{id}/active',       ['as' => '.postal-exchange.type.active',         'middleware' => ['ability:super-admin,postal-exchange-type-active'],       'uses' => 'PostalExchangeTypeController@Active']);
    Route::get('postal-exchange/type/{id}/in-active',    ['as' => '.postal-exchange.type.in-active',      'middleware' => ['ability:super-admin,postal-exchange-type-in-active'],    'uses' => 'PostalExchangeTypeController@inActive']);
    Route::post('postal-exchange/type/bulk-action',      ['as' => '.postal-exchange.type.bulk-action',    'middleware' => ['ability:super-admin,postal-exchange-type-bulk-action'],  'uses' => 'PostalExchangeTypeController@bulkAction']);

    /*Visitor Log Routes*/
    Route::get('visitor',                    ['as' => '.visitor',                     'middleware' => ['ability:super-admin,visitor-index'],                'uses' => 'VisitorController@index']);
    Route::get('visitor/add',                ['as' => '.visitor.add',                 'middleware' => ['ability:super-admin,visitor-add'],                  'uses' => 'VisitorController@add']);
    Route::post('visitor/store',             ['as' => '.visitor.store',               'middleware' => ['ability:super-admin,visitor-add'],                  'uses' => 'VisitorController@store']);
    Route::get('visitor/{id}/edit',          ['as' => '.visitor.edit',                'middleware' => ['ability:super-admin,visitor-edit'],                 'uses' => 'VisitorController@edit']);
    Route::post('visitor/{id}/update',       ['as' => '.visitor.update',              'middleware' => ['ability:super-admin,visitor-edit'],                 'uses' => 'VisitorController@update']);
    Route::get('visitor/{id}/delete',        ['as' => '.visitor.delete',              'middleware' => ['ability:super-admin,visitor-delete'],               'uses' => 'VisitorController@delete']);
    Route::get('visitor/{id}/active',        ['as' => '.visitor.active',               'middleware' => ['ability:super-admin,visitor-active'],              'uses' => 'VisitorController@Active']);
    Route::get('visitor/{id}/in-active',     ['as' => '.visitor.in-active',            'middleware' => ['ability:super-admin,visitor-in-active'],           'uses' => 'VisitorController@inActive']);
    Route::post('visitor/bulk-action',       ['as' => '.visitor.bulk-action',         'middleware' => ['ability:super-admin,visitor-bulk-action'],          'uses' => 'VisitorController@bulkAction']);

    /*Visitor Purpose Routes*/
    Route::get('visitor/purpose',                    ['as' => '.visitor.purpose',                     'middleware' => ['ability:super-admin,visitor-purpose-index'],                'uses' => 'VisitorPurposeController@index']);
    Route::get('visitor/purpose/add',                ['as' => '.visitor.purpose.add',                 'middleware' => ['ability:super-admin,visitor-purpose-add'],                  'uses' => 'VisitorPurposeController@add']);
    Route::post('visitor/purpose/store',             ['as' => '.visitor.purpose.store',               'middleware' => ['ability:super-admin,visitor-purpose-add'],                  'uses' => 'VisitorPurposeController@store']);
    Route::get('visitor/purpose/{id}/edit',          ['as' => '.visitor.purpose.edit',                'middleware' => ['ability:super-admin,visitor-purpose-edit'],                 'uses' => 'VisitorPurposeController@edit']);
    Route::post('visitor/purpose/{id}/update',       ['as' => '.visitor.purpose.update',              'middleware' => ['ability:super-admin,visitor-purpose-edit'],                 'uses' => 'VisitorPurposeController@update']);
    Route::get('visitor/purpose/{id}/delete',        ['as' => '.visitor.purpose.delete',              'middleware' => ['ability:super-admin,visitor-purpose-delete'],               'uses' => 'VisitorPurposeController@delete']);
    Route::get('visitor/purpose/{id}/active',        ['as' => '.visitor.purpose.active',         'middleware' => ['ability:super-admin,postal-exchange-type-active'],               'uses' => 'VisitorPurposeController@Active']);
    Route::get('visitor/purpose/{id}/in-active',     ['as' => '.visitor.purpose.in-active',      'middleware' => ['ability:super-admin,postal-exchange-type-in-active'],            'uses' => 'VisitorPurposeController@inActive']);
    Route::post('visitor/purpose/bulk-action',       ['as' => '.visitor.purpose.bulk-action',         'middleware' => ['ability:super-admin,visitor-purpose-bulk-action'],          'uses' => 'VisitorPurposeController@bulkAction']);

});

/*Students Grouping*/
Route::group(['prefix' => 'student/',                                   'as' => 'student',                                     'namespace' => 'Student\\'], function () {

    Route::get('',                          ['as' => '',                         'middleware' => ['ability:super-admin,student-index'],                  'uses' => 'StudentController@index']);
    //Route::get('/get-students',              ['as' => '.get-students',                         'middleware' => ['ability:super-admin,student-index'],                  'uses' => 'StudentController@getStudentsList']);


    Route::get('registration',              ['as' => '.registration',            'middleware' => ['ability:super-admin,student-registration'],           'uses' => 'StudentController@registration']);
    Route::post('register',                 ['as' => '.register',                'middleware' => ['ability:super-admin,student-registration'],            'uses' => 'StudentController@register']);
    Route::get('{id}/view',                 ['as' => '.view',                    'middleware' => ['ability:super-admin,student-view'],                   'uses' => 'StudentController@view']);

        /* Attendance summaries (AJAX JSON) */
        Route::get('{id}/attendance/summary', [
            'as'         => '.attendance.summary',
            'middleware' => ['ability:super-admin,student-view'],
            'uses'       => 'StudentController@attendanceSummary',
        ]);

        Route::get('{id}/attendance/subject-summary', [
            'as'         => '.attendance.subject_summary',
            'middleware' => ['ability:super-admin,student-view'],
            'uses'       => 'StudentController@subjectAttendanceSummary',
        ]);


    Route::get('{id}/edit',                 ['as' => '.edit',                    'middleware' => ['ability:super-admin,student-edit'],                   'uses' => 'StudentController@edit']);
    Route::post('{id}/update',              ['as' => '.update',                  'middleware' => ['ability:super-admin,student-edit'],                    'uses' => 'StudentController@update']);
    Route::get('{id}/delete',               ['as' => '.delete',                  'middleware' => ['ability:super-admin,student-delete'],                 'uses' => 'StudentController@delete']);
    Route::post('bulk-action',              ['as' => '.bulk-action',             'middleware' => ['ability:super-admin,student-bulk-action'],            'uses' => 'StudentController@bulkAction']);
    Route::get('{id}/active',               ['as' => '.active',                  'middleware' => ['ability:super-admin,student-active'],                 'uses' => 'StudentController@Active']);
    Route::get('{id}/in-active',            ['as' => '.in-active',               'middleware' => ['ability:super-admin,student-in-active'],              'uses' => 'StudentController@inActive']);

    Route::post('find-semester',            ['as' => '.find-semester',                                                                                   'uses' => 'StudentController@findSemester']);
    Route::post('academicInfo-html',        ['as' => '.academicInfo-html',                                                                               'uses' => 'StudentController@academicInfoHtml']);
    Route::get('{id}/delete-academicInfo',  ['as' => '.delete-academicInfo',     'middleware' => ['ability:super-admin,student-delete-academic-info'],   'uses' => 'StudentController@deleteAcademicInfo']);
    Route::get('guardian-name-autocomplete',  ['as' => '.guardian-name-autocomplete',                                                                     'uses' => 'StudentController@guardianNameAutocomplete']);
    Route::post('guardianInfo-html',            ['as' => '.guardianInfo-html',                                                                            'uses' => 'StudentController@guardianInfo']);

    Route::get('student-name-autocomplete',     ['as' => '.student-name-autocomplete',                                                                     'uses' => 'StudentController@studentNameAutocomplete']);

    Route::get('import',                      ['as' => '.import',             'middleware' => ['ability:super-admin,student-registration'],           'uses' => 'StudentController@importStudent']);
    Route::post('import',                     ['as' => '.bulk.import',        'middleware' => ['ability:super-admin,student-registration'],             'uses' => 'StudentController@handleImportStudent']);

    Route::get('{id}/generate-pdf',               ['as' => '.generate-pdf',                  'middleware' => ['ability:super-admin,student-generate-pdf'],                 'uses' => 'StudentController@generatePdf']);

    /*Student transfer */
    Route::get('transfer',                  ['as' => '.transfer',                  'middleware' => ['ability:super-admin,student-transfer'],      'uses' => 'StudentController@transfer']);
    Route::post('transfering',              ['as' => '.transfering',               'middleware' => ['ability:super-admin,student-transfer'],      'uses' => 'StudentController@transfering']);

    /*Student login access*/
    Route::post('user/create',             ['as' => '.user.create',                  'middleware' => ['ability:super-admin,user-add'],                    'uses' => 'StudentController@createUser']);
    Route::post('{id}/user/update',        ['as' => '.user.update',                  'middleware' => ['ability:super-admin,user-edit'],                   'uses' => 'StudentController@updateUser']);
    Route::get('{id}/user/active',         ['as' => '.user.active',                  'middleware' => ['ability:super-admin,user-active'],                 'uses' => 'StudentController@activeUser']);
    Route::get('{id}/user/in-active',      ['as' => '.user.in-active',               'middleware' => ['ability:super-admin,user-in-active'],              'uses' => 'StudentController@inActiveUser']);
    Route::get('{id}/user/delete',         ['as' => '.user.delete',                  'middleware' => ['ability:super-admin,user-delete'],                 'uses' => 'StudentController@deleteUser']);

    /*Guardian login access*/
    Route::post('guardian/user/create',             ['as' => '.guardian.user.create',                  'middleware' => ['ability:super-admin,user-add'],                    'uses' => 'StudentController@createUser']);
    Route::post('guardian/{id}/user/update',        ['as' => '.guardian.user.update',                  'middleware' => ['ability:super-admin,user-edit'],                   'uses' => 'StudentController@updateUser']);
    Route::get('guardian/{id}/user/active',         ['as' => '.guardian.user.active',                  'middleware' => ['ability:super-admin,user-active'],                 'uses' => 'StudentController@activeUser']);
    Route::get('guardian/{id}/user/in-active',      ['as' => '.guardian.user.in-active',               'middleware' => ['ability:super-admin,user-in-active'],              'uses' => 'StudentController@inActiveUser']);
    Route::get('guardian/{id}/user/delete',         ['as' => '.guardian.user.delete',                  'middleware' => ['ability:super-admin,user-delete'],                 'uses' => 'StudentController@deleteUser']);


    /*Student Document Upload*/
    Route::get('document',                      ['as' => '.document',                'middleware' => ['ability:super-admin,student-document-index'],      'uses' => 'DocumentController@index']);
    Route::post('document/store',               ['as' => '.document.store',          'middleware' => ['ability:super-admin,student-document-add'],      'uses' => 'DocumentController@store']);
    Route::get('document/{id}/edit',            ['as' => '.document.edit',           'middleware' => ['ability:super-admin,student-document-edit'],      'uses' => 'DocumentController@edit']);
    Route::post('document/{id}/update',         ['as' => '.document.update',         'middleware' => ['ability:super-admin,student-document-edit'],      'uses' => 'DocumentController@update']);
    Route::get('document/{id}/delete',          ['as' => '.document.delete',         'middleware' => ['ability:super-admin,student-document-delete'],      'uses' => 'DocumentController@delete']);
    Route::get('document/{id}/active',          ['as' => '.document.active',         'middleware' => ['ability:super-admin,student-document-active'],      'uses' => 'DocumentController@Active']);
    Route::get('document/{id}/in-active',       ['as' => '.document.in-active',      'middleware' => ['ability:super-admin,student-document-in-active'],      'uses' => 'DocumentController@inActive']);
    Route::post('document/bulk-action',         ['as' => '.document.bulk-action',    'middleware' => ['ability:super-admin,student-document-bulk-action'],      'uses' => 'DocumentController@bulkAction']);

    /*Student Notes Creating*/
    Route::get('note',                          ['as' => '.note',                'middleware' => ['ability:super-admin,student-note-index'],      'uses' => 'NoteController@index']);
    Route::post('note/store',                   ['as' => '.note.store',          'middleware' => ['ability:super-admin,student-note-add'],      'uses' => 'NoteController@store']);
    Route::get('note/{id}/edit',                ['as' => '.note.edit',           'middleware' => ['ability:super-admin,student-note-edit'],      'uses' => 'NoteController@edit']);
    Route::post('note/{id}/update',             ['as' => '.note.update',         'middleware' => ['ability:super-admin,student-note-edit'],      'uses' => 'NoteController@update']);
    Route::get('note/{id}/delete',              ['as' => '.note.delete',         'middleware' => ['ability:super-admin,student-note-delete'],      'uses' => 'NoteController@delete']);
    Route::get('note/{id}/active',              ['as' => '.note.active',         'middleware' => ['ability:super-admin,student-note-active'],      'uses' => 'NoteController@Active']);
    Route::get('note/{id}/in-active',           ['as' => '.note.in-active',      'middleware' => ['ability:super-admin,student-note-in-active'],      'uses' => 'NoteController@inActive']);
    Route::post('note/bulk-action',             ['as' => '.note.bulk-action',    'middleware' => ['ability:super-admin,student-note-bulk-action'],      'uses' => 'NoteController@bulkAction']);

    Route::post('online-registration/register',       ['as' => '.online-registration.register',                       'uses' => 'OnlineRegistrationController@register']);
Route::post('online-registration/prepare-payment', ['as' => '.online-registration.prepare-payment', 'uses' => 'OnlineRegistrationController@preparePayment']);

});

//Public Online Registration
Route::get('online-registration',                    ['as' => 'online-registration.registration',      'uses' => 'Student\OnlineRegistrationController@registration']);
Route::post('online-registration/prepare-payment',  ['as' => 'online-registration.prepare-payment',   'uses' => 'Student\OnlineRegistrationController@preparePayment']);
Route::post('online-registration/find-semester',     ['as' => 'online-registration.find-semester',      'uses' => 'Student\OnlineRegistrationController@findSemester']);
Route::post('online-registration/find-subject',      ['as' => 'online-registration.find-subject',       'uses' => 'Student\OnlineRegistrationController@findSubject']);
Route::post('online-registration/check-email',       ['as' => 'online-registration.check-email',        'uses' => 'Student\OnlineRegistrationController@checkEmail']);
//print registration
Route::get('online-registration/find',             ['as' => 'online-registration.find',         'uses' => 'Student\OnlineRegistrationController@findRegistration']);
Route::get('online-registration/{id}/print',       ['as' => 'online-registration.print',        'uses' => 'Student\OnlineRegistrationController@print']);
Route::get('online-registration/{id}/pdf',     ['as' => 'online-registration.pdf',        'uses' => 'Student\OnlineRegistrationController@pdf']);

//Registration Payment Routes
Route::group(['prefix' => 'registration-payment', 'as' => 'registration-payment.'], function () {
    Route::post('pay',                          ['as' => 'pay',              'uses' => 'Student\RegistrationPaymentController@pay']);
    // Gateways may call callbacks with POST (server-to-server) or GET (browser return).
    Route::match(['get', 'post'], 'ssl-success', ['as' => 'ssl-success', 'uses' => 'Student\RegistrationPaymentController@sslSuccess']);
    Route::match(['get', 'post'], 'ssl-fail',    ['as' => 'ssl-fail',    'uses' => 'Student\RegistrationPaymentController@sslFail']);
    Route::match(['get', 'post'], 'ssl-cancel',  ['as' => 'ssl-cancel',  'uses' => 'Student\RegistrationPaymentController@sslCancel']);
    Route::match(['get', 'post'], 'ssl-ipn',     ['as' => 'ssl-ipn',     'uses' => 'Student\RegistrationPaymentController@sslSuccess']);
    Route::match(['get', 'post'], 'ucb-success', ['as' => 'ucb-success', 'uses' => 'Student\RegistrationPaymentController@ucbSuccess']);
    Route::match(['get', 'post'], 'ucb-cancel',  ['as' => 'ucb-cancel',  'uses' => 'Student\RegistrationPaymentController@sslCancel']);
});

//certificate verification
Route::get('certificate-verification',              ['as' => 'verification.certificate',         'uses' => 'VerificationController@certificate']);

/*Students Guardian Grouping*/
Route::group(['prefix' => 'guardian/',                                   'as' => 'guardian',                                     'namespace' => 'Student\\'], function () {

    Route::get('',                              ['as' => '',                         'middleware' => ['ability:super-admin,guardian-index'],                  'uses' => 'GuardianController@index']);
    Route::get('registration',                  ['as' => '.registration',            'middleware' => ['ability:super-admin,guardian-registration'],           'uses' => 'GuardianController@registration']);
    Route::post('register',                     ['as' => '.register',                'middleware' => ['ability:super-admin,guardian-register'],               'uses' => 'GuardianController@register']);
    Route::get('{id}/view',                     ['as' => '.view',                    'middleware' => ['ability:super-admin,guardian-view'],                   'uses' => 'GuardianController@view']);
    Route::get('{id}/edit',                     ['as' => '.edit',                    'middleware' => ['ability:super-admin,guardian-edit'],                   'uses' => 'GuardianController@edit']);
    Route::post('{id}/update',                  ['as' => '.update',                  'middleware' => ['ability:super-admin,guardian-edit'],                    'uses' => 'GuardianController@update']);
    Route::get('{id}/delete',                   ['as' => '.delete',                  'middleware' => ['ability:super-admin,guardian-delete'],                 'uses' => 'GuardianController@delete']);

    Route::get('{id}/active',               ['as' => '.active',                  'middleware' => ['ability:super-admin,guardian-active'],                 'uses' => 'GuardianController@Active']);
    Route::get('{id}/in-active',            ['as' => '.in-active',               'middleware' => ['ability:super-admin,guardian-in-active'],              'uses' => 'GuardianController@inActive']);
    Route::post('bulk-action',              ['as' => '.bulk-action',             'middleware' => ['ability:super-admin,student-bulk-action'],            'uses' => 'GuardianController@bulkAction']);

    Route::post('/link',                        ['as' => '.link',                    'middleware' => ['ability:super-admin,guardian-link'],                    'uses' => 'GuardianController@link']);
    Route::get('{student}/{guardian}/unlink',   ['as' => '.unlink',                  'middleware' => ['ability:super-admin,guardian-unlink'],                    'uses' => 'GuardianController@unlink']);

});

/*Staff Grouping*/
Route::group(['prefix' => 'staff/',                                     'as' => 'staff',                                       'namespace' => 'Staff\\'], function () {
    /*Staff Routes*/
    Route::get('',                          ['as' => '',                    'middleware' => ['ability:super-admin,staff-index'],        'uses' => 'StaffController@index']);
    Route::get('add',                       ['as' => '.add',                'middleware' => ['ability:super-admin,staff-add'],          'uses' => 'StaffController@add']);
    Route::post('store',                    ['as' => '.store',              'middleware' => ['ability:super-admin,staff-add'],          'uses' => 'StaffController@store']);
    Route::get('{id}/edit',                 ['as' => '.edit',               'middleware' => ['ability:super-admin,staff-edit'],         'uses' => 'StaffController@edit']);
    Route::post('{id}/update',              ['as' => '.update',             'middleware' => ['ability:super-admin,staff-edit'],         'uses' => 'StaffController@update']);
    Route::get('{id}/view',                 ['as' => '.view',               'middleware' => ['ability:super-admin,staff-view'],         'uses' => 'StaffController@view']);
    // JSON for the staff regular attendance card (AJAX)
    Route::get('{id}/attendance/summary', [
        'as'   => '.attendance.summary',
        'middleware' => ['ability:super-admin,staff-view'],
        'uses' => 'StaffController@attendanceSummary'
    ]);

    Route::get('{id}/delete',               ['as' => '.delete',             'middleware' => ['ability:super-admin,staff-delete'],       'uses' => 'StaffController@delete']);
    Route::get('{id}/active',               ['as' => '.active',             'middleware' => ['ability:super-admin,staff-active'],       'uses' => 'StaffController@Active']);
    Route::get('{id}/in-active',            ['as' => '.in-active',          'middleware' => ['ability:super-admin,staff-in-active'],    'uses' => 'StaffController@inActive']);
    Route::post('bulk-action',              ['as' => '.bulk-action',        'middleware' => ['ability:super-admin,staff-bulk-action'],  'uses' => 'StaffController@bulkAction']);

    Route::get('import',                      ['as' => '.import',             'middleware' => ['ability:super-admin,staff-add'],           'uses' => 'StaffController@importStaff']);
    Route::post('import',                     ['as' => '.bulk.import',        'middleware' => ['ability:super-admin,staff-add'],             'uses' => 'StaffController@handleImportStaff']);


    /*Staff login access*/
    Route::post('user/create',             ['as' => '.user.create',                  'middleware' => ['ability:super-admin,user-add'],                    'uses' => 'StaffController@createUser']);
    Route::post('{id}/user/update',        ['as' => '.user.update',                  'middleware' => ['ability:super-admin,user-edit'],                   'uses' => 'StaffController@updateUser']);
    Route::get('{id}/user/active',         ['as' => '.user.active',                  'middleware' => ['ability:super-admin,user-active'],                 'uses' => 'StaffController@activeUser']);
    Route::get('{id}/user/in-active',      ['as' => '.user.in-active',               'middleware' => ['ability:super-admin,user-in-active'],              'uses' => 'StaffController@inActiveUser']);
    Route::get('{id}/user/delete',         ['as' => '.user.delete',                  'middleware' => ['ability:super-admin,user-delete'],                 'uses' => 'StaffController@deleteUser']);

    /*Staff Document Upload*/
    Route::get('document',                  ['as' => '.document',               'middleware' => ['ability:super-admin,staff-document-index'],       'uses' => 'DocumentController@index']);
    Route::post('document/store',           ['as' => '.document.store',         'middleware' => ['ability:super-admin,staff-document-add'],         'uses' => 'DocumentController@store']);
    Route::get('document/{id}/edit',        ['as' => '.document.edit',          'middleware' => ['ability:super-admin,staff-document-edit'],        'uses' => 'DocumentController@edit']);
    Route::post('document/{id}/update',     ['as' => '.document.update',        'middleware' => ['ability:super-admin,staff-document-edit'],        'uses' => 'DocumentController@update']);
    Route::get('document/{id}/delete',      ['as' => '.document.delete',        'middleware' => ['ability:super-admin,staff-document-delete'],      'uses' => 'DocumentController@delete']);
    Route::get('document/{id}/active',      ['as' => '.document.active',        'middleware' => ['ability:super-admin,staff-document-active'],      'uses' => 'DocumentController@Active']);
    Route::get('document/{id}/in-active',   ['as' => '.document.in-active',     'middleware' => ['ability:super-admin,staff-document-in-active'],   'uses' => 'DocumentController@inActive']);
    Route::post('document/bulk-action',     ['as' => '.document.bulk-action',   'middleware' => ['ability:super-admin,staff-document-bulk-action'], 'uses' => 'DocumentController@bulkAction']);

    /*Staff Notes Creating*/
    Route::get('note',                      ['as' => '.note',                'middleware' => ['ability:super-admin,staff-note-index'],      'uses' => 'NoteController@index']);
    Route::post('note/store',               ['as' => '.note.store',          'middleware' => ['ability:super-admin,staff-note-add'],        'uses' => 'NoteController@store']);
    Route::get('note/{id}/edit',            ['as' => '.note.edit',           'middleware' => ['ability:super-admin,staff-note-edit'],       'uses' => 'NoteController@edit']);
    Route::post('note/{id}/update',         ['as' => '.note.update',         'middleware' => ['ability:super-admin,staff-note-edit'],       'uses' => 'NoteController@update']);
    Route::get('note/{id}/delete',          ['as' => '.note.delete',         'middleware' => ['ability:super-admin,staff-note-delete'],     'uses' => 'NoteController@delete']);
    Route::get('note/{id}/active',          ['as' => '.note.active',         'middleware' => ['ability:super-admin,staff-note-acctive'],    'uses' => 'NoteController@Active']);
    Route::get('note/{id}/in-active',       ['as' => '.note.in-active',      'middleware' => ['ability:super-admin,staff-note-in-active'],   'uses' => 'NoteController@inActive']);
    Route::post('note/bulk-action',         ['as' => '.note.bulk-action',    'middleware' => ['ability:super-admin,staff-note-bulk-action'], 'uses' => 'NoteController@bulkAction']);

    /*Staff Designation*/
    Route::get('designation',                   ['as' => '.designation',                'middleware' => ['ability:super-admin,staff-designation-index'],        'uses' => 'DesignationController@index']);
    Route::post('designation/store',            ['as' => '.designation.store',          'middleware' => ['ability:super-admin,staff-designation-add'],          'uses' => 'DesignationController@store']);
    Route::get('designation/{id}/edit',         ['as' => '.designation.edit',           'middleware' => ['ability:super-admin,staff-designation-edit'],         'uses' => 'DesignationController@edit']);
    Route::post('designation/{id}/update',      ['as' => '.designation.update',         'middleware' => ['ability:super-admin,staff-designation-edit'],       'uses' => 'DesignationController@update']);
    Route::get('designation/{id}/delete',       ['as' => '.designation.delete',         'middleware' => ['ability:super-admin,staff-designation-delete'],       'uses' => 'DesignationController@delete']);
    Route::get('designation/{id}/active',       ['as' => '.designation.active',         'middleware' => ['ability:super-admin,staff-designation-active'],       'uses' => 'DesignationController@Active']);
    Route::get('designation/{id}/in-active',    ['as' => '.designation.in-active',      'middleware' => ['ability:super-admin,staff-designation-in-active'],    'uses' => 'DesignationController@inActive']);
    Route::post('designation/bulk-action',      ['as' => '.designation.bulk-action',    'middleware' => ['ability:super-admin,staff-designation-bulk-action'],  'uses' => 'DesignationController@bulkAction']);
});

/*Accounting Grouping*/
Route::group(['prefix' => 'account/',                                   'as' => 'account.',                                    'namespace' => 'Account\\'], function () {
 

    /*Payroll Group*/
    /*Balance Payroll*/
    Route::get('payroll/',                    ['as' => 'payroll',                   'middleware' => ['ability:super-admin,payroll-index'],                   'uses' => 'Payroll\PayrollBaseController@index']);
    Route::get('payroll/balance',             ['as' => 'payroll.balance',           'middleware' => ['ability:super-admin,payroll-balance'],                   'uses' => 'Payroll\PayrollBaseController@balance']);

    /*Payroll Head*/
    Route::get('payroll/head',                    ['as' => 'payroll.head',                  'middleware' => ['ability:super-admin,payroll-head-index'],             'uses' => 'Payroll\PayrollHeadController@index']);
    Route::post('payroll/head/store',             ['as' => 'payroll.head.store',            'middleware' => ['ability:super-admin,payroll-head-add'],               'uses' => 'Payroll\PayrollHeadController@store']);
    Route::get('payroll/head/{id}/edit',          ['as' => 'payroll.head.edit',             'middleware' => ['ability:super-admin,payroll-head-edit'],              'uses' => 'Payroll\PayrollHeadController@edit']);
    Route::post('payroll/head/{id}/update',       ['as' => 'payroll.head.update',           'middleware' => ['ability:super-admin,payroll-head-edit'],              'uses' => 'Payroll\PayrollHeadController@update']);
    Route::get('payroll/head/{id}/delete',        ['as' => 'payroll.head.delete',           'middleware' => ['ability:super-admin,payroll-head-delete'],            'uses' => 'Payroll\PayrollHeadController@delete']);
    Route::get('payroll/head/{id}/active',        ['as' => 'payroll.head.active',           'middleware' => ['ability:super-admin,payroll-head-active'],            'uses' => 'Payroll\PayrollHeadController@Active']);
    Route::get('payroll/head/{id}/in-active',     ['as' => 'payroll.head.in-active',        'middleware' => ['ability:super-admin,payroll-head-in-active'],         'uses' => 'Payroll\PayrollHeadController@inActive']);
    Route::post('payroll/head/bulk-action',       ['as' => 'payroll.head.bulk-action',      'middleware' => ['ability:super-admin,payroll-head-bulk-action'],       'uses' => 'Payroll\PayrollHeadController@bulkAction']);

    /*Payroll Master*/
    Route::get('payroll/master',                    ['as' => 'payroll.master',                  'middleware' => ['ability:super-admin,payroll-master-index'],           'uses' => 'Payroll\PayrollMasterController@index']);
    Route::get('payroll/master/add',                ['as' => 'payroll.master.add',              'middleware' => ['ability:super-admin,payroll-master-add'],             'uses' => 'Payroll\PayrollMasterController@add']);
    Route::post('payroll/master/store',             ['as' => 'payroll.master.store',            'middleware' => ['ability:super-admin,payroll-master-add'],             'uses' => 'Payroll\PayrollMasterController@store']);
    Route::get('payroll/master/{id}/edit',          ['as' => 'payroll.master.edit',             'middleware' => ['ability:super-admin,payroll-master-edit'],            'uses' => 'Payroll\PayrollMasterController@edit']);
    Route::post('payroll/master/{id}/update',       ['as' => 'payroll.master.update',           'middleware' => ['ability:super-admin,payroll-master-edit'],            'uses' => 'Payroll\PayrollMasterController@update']);
    Route::get('payroll/master/{id}/delete',        ['as' => 'payroll.master.delete',           'middleware' => ['ability:super-admin,payroll-master-delete'],          'uses' => 'Payroll\PayrollMasterController@delete']);
    Route::post('payroll/master/bulk-action',       ['as' => 'payroll.master.bulk-action',      'middleware' => ['ability:super-admin,payroll-master-bulk-action'],     'uses' => 'Payroll\PayrollMasterController@bulkAction']);
    Route::get('payroll/master/{id}/active',        ['as' => 'payroll.master.active',           'middleware' => ['ability:super-admin,payroll-master-active'],          'uses' => 'Payroll\PayrollMasterController@Active']);
    Route::get('payroll/master/{id}/in-active',     ['as' => 'payroll.master.in-active',        'middleware' => ['ability:super-admin,payroll-master-in-active'],       'uses' => 'Payroll\PayrollMasterController@inActive']);
    Route::post('payroll/master/payroll-html',       ['as' => 'payroll.master.payroll-html',                                                                            'uses' => 'Payroll\PayrollMasterController@payrollHtmlRow']);

    /*Pay Salary*/
    Route::get('salary/payment',                    ['as' => 'salary.payment',                  'middleware' => ['ability:super-admin,salary-payment-index'],           'uses' => 'Payroll\SalaryPayController@index']);
    Route::get('salary/payment/{id}/add',           ['as' => 'salary.payment.add',              'middleware' => ['ability:super-admin,salary-payment-add'],             'uses' => 'Payroll\SalaryPayController@add']);
    Route::post('salary/payment/store',             ['as' => 'salary.payment.store',            'middleware' => ['ability:super-admin,salary-payment-add'],             'uses' => 'Payroll\SalaryPayController@store']);
    Route::get('salary/payment/{id}/view',          ['as' => 'salary.payment.view',             'middleware' => ['ability:super-admin,salary-payment-view'],            'uses' => 'Payroll\SalaryPayController@view']);
    Route::get('salary/payment/{id}/delete',        ['as' => 'salary.payment.delete',           'middleware' => ['ability:super-admin,salary-payment-delete'],          'uses' => 'Payroll\SalaryPayController@delete']);


    /*Account Group Routes*/
    Route::get('account-group',                    ['as' => 'transaction.account-group',                  'middleware' => ['ability:super-admin,account-group-index'],               'uses' => 'Transaction\AccountGroupController@index']);
    Route::post('account-group/store',             ['as' => 'transaction.account-group.store',            'middleware' => ['ability:super-admin,account-group-add'],                 'uses' => 'Transaction\AccountGroupController@store']);
    Route::get('account-group/{id}/edit',          ['as' => 'transaction.account-group.edit',             'middleware' => ['ability:super-admin,account-group-edit'],                'uses' => 'Transaction\AccountGroupController@edit']);
    Route::post('account-group/{id}/update',       ['as' => 'transaction.account-group.update',           'middleware' => ['ability:super-admin,account-group-edit'],                'uses' => 'Transaction\AccountGroupController@update']);
    Route::get('account-group/{id}/delete',        ['as' => 'transaction.account-group.delete',           'middleware' => ['ability:super-admin,account-group-delete'],              'uses' => 'Transaction\AccountGroupController@delete']);
    Route::get('account-group/{id}/active',        ['as' => 'transaction.account-group.active',           'middleware' => ['ability:super-admin,account-group-active'],              'uses' => 'Transaction\AccountGroupController@Active']);
    Route::get('account-group/{id}/in-active',     ['as' => 'transaction.account-group.in-active',        'middleware' => ['ability:super-admin,account-group-in-active'],           'uses' => 'Transaction\AccountGroupController@inActive']);
    Route::post('account-group/bulk-action',       ['as' => 'transaction.account-group.bulk-action',      'middleware' => ['ability:super-admin,account-group-bulk-action'],         'uses' => 'Transaction\AccountGroupController@bulkAction']);
    Route::get('account-group/chart-of-account',    ['as' => 'transaction.account-group.chart-of-account',             'middleware' => ['ability:super-admin,account-group-chart-of-account'],  'uses' => 'Transaction\AccountGroupController@chartOfAccount']);

    /*Transaction Head*/
    Route::get('transaction-head',                    ['as' => 'transaction-head',                  'middleware' => ['ability:super-admin,transaction-head-index'],         'uses' => 'Transaction\TransactionHeadController@index']);
    Route::post('transaction-head/store',             ['as' => 'transaction-head.store',            'middleware' => ['ability:super-admin,transaction-head-add'],           'uses' => 'Transaction\TransactionHeadController@store']);
    Route::get('transaction-head/{id}/edit',          ['as' => 'transaction-head.edit',             'middleware' => ['ability:super-admin,transaction-head-edit'],          'uses' => 'Transaction\TransactionHeadController@edit']);
    Route::post('transaction-head/{id}/update',       ['as' => 'transaction-head.update',           'middleware' => ['ability:super-admin,transaction-head-edit'],          'uses' => 'Transaction\TransactionHeadController@update']);
    Route::get('transaction-head/{id}/delete',        ['as' => 'transaction-head.delete',           'middleware' => ['ability:super-admin,transaction-head-delete'],        'uses' => 'Transaction\TransactionHeadController@delete']);
    Route::get('transaction-head/{id}/active',        ['as' => 'transaction-head.active',           'middleware' => ['ability:super-admin,transaction-head-active'],        'uses' => 'Transaction\TransactionHeadController@Active']);
    Route::get('transaction-head/{id}/in-active',     ['as' => 'transaction-head.in-active',        'middleware' => ['ability:super-admin,transaction-head-in-active'],     'uses' => 'Transaction\TransactionHeadController@inActive']);
    Route::post('transaction-head/bulk-action',       ['as' => 'transaction-head.bulk-action',      'middleware' => ['ability:super-admin,transaction-head-bulk-action'],   'uses' => 'Transaction\TransactionHeadController@bulkAction']);

    Route::get('transaction-head/view',                 ['as' => 'transaction-head.view',                          'middleware' => ['ability:super-admin,transaction-head-view'],                           'uses' => 'Transaction\TransactionHeadController@view']);
    Route::get('transaction-head/balance-statement',    ['as' => 'transaction-head.balance-statement',             'middleware' => ['ability:super-admin,transaction-head-balance-statement'],              'uses' => 'Transaction\TransactionHeadController@viewBalanceStatement']);

    /*Transaction*/
    Route::get('transaction',                    ['as' => 'transaction',                  'middleware' => ['ability:super-admin,transaction-index'],                'uses' => 'Transaction\TransactionController@index']);
    Route::get('transaction/add',                ['as' => 'transaction.add',              'middleware' => ['ability:super-admin,transaction-add'],                  'uses' => 'Transaction\TransactionController@add']);
    Route::post('transaction/store',             ['as' => 'transaction.store',            'middleware' => ['ability:super-admin,transaction-add'],                  'uses' => 'Transaction\TransactionController@store']);
    Route::get('transaction/{id}/delete',        ['as' => 'transaction.delete',           'middleware' => ['ability:super-admin,transaction-delete'],               'uses' => 'Transaction\TransactionController@delete']);
    Route::post('transaction/bulk-action',       ['as' => 'transaction.bulk-action',      'middleware' => ['ability:super-admin,transaction-bulk-action'],          'uses' => 'Transaction\TransactionController@bulkAction']);

    /* Multi Transaction*/
    Route::get('transaction/multi-add',                ['as' => 'transaction.multi-add',              'middleware' => ['ability:super-admin,transaction-add'],                  'uses' => 'Transaction\TransactionController@multiAdd']);
    Route::post('transaction/multi-store',             ['as' => 'transaction.multi-store',            'middleware' => ['ability:super-admin,transaction-add'],                  'uses' => 'Transaction\TransactionController@multiStore']);
    Route::post('transaction/tr-html',                 ['as' => 'multi-transaction.tr-html',                                                                              'uses' => 'Transaction\TransactionController@trHtmlRow']);

    //Ac to Ac Transfer
    Route::get('transfer',                    ['as' => 'transfer',                  'middleware' => ['ability:super-admin,transaction-add'],                  'uses' => 'Transaction\TransferController@actransfer']);
    Route::post('transfer/store',             ['as' => 'transfer.store',            'middleware' => ['ability:super-admin,transaction-add'],                  'uses' => 'Transaction\TransferController@transferstore']);

    /*Bank */
    Route::get('bank',                    ['as' => 'bank',                  'middleware' => ['ability:super-admin,bank-index'],            'uses' => 'Bank\BankController@index']);
    Route::get('bank/add',                ['as' => 'bank.add',              'middleware' => ['ability:super-admin,bank-add'],              'uses' => 'Bank\BankController@add']);
    Route::post('bank/store',             ['as' => 'bank.store',            'middleware' => ['ability:super-admin,bank-add'],              'uses' => 'Bank\BankController@store']);
    Route::get('bank/{id}/edit',          ['as' => 'bank.edit',             'middleware' => ['ability:super-admin,bank-edit'],             'uses' => 'Bank\BankController@edit']);
    Route::post('bank/{id}/update',       ['as' => 'bank.update',           'middleware' => ['ability:super-admin,bank-edit'],             'uses' => 'Bank\BankController@update']);
    Route::get('bank/{id}/view',          ['as' => 'bank.view',             'middleware' => ['ability:super-admin,bank-view'],             'uses' => 'Bank\BankController@view']);
    Route::get('bank/{id}/delete',        ['as' => 'bank.delete',           'middleware' => ['ability:super-admin,bank-delete'],           'uses' => 'Bank\BankController@delete']);
    Route::post('bank/bulk-action',       ['as' => 'bank.bulk-action',      'middleware' => ['ability:super-admin,bank-bulk-action'],      'uses' => 'Bank\BankController@bulkAction']);
    Route::get('bank/{id}/active',        ['as' => 'bank.active',           'middleware' => ['ability:super-admin,bank-active'],           'uses' => 'Bank\BankController@Active']);
    Route::get('bank/{id}/in-active',     ['as' => 'bank.in-active',        'middleware' => ['ability:super-admin,bank-in-active'],        'uses' => 'Bank\BankController@inActive']);

    /*Bank */
    Route::get('bank-transaction',                    ['as' => 'bank-transaction',                  'middleware' => ['ability:super-admin,bank-transaction-index'],            'uses' => 'Bank\BankTrController@index']);
    Route::get('bank-transaction/add',                ['as' => 'bank-transaction.add',              'middleware' => ['ability:super-admin,bank-transaction-add'],              'uses' => 'Bank\BankTrController@add']);
    Route::post('bank-transaction/store',             ['as' => 'bank-transaction.store',            'middleware' => ['ability:super-admin,bank-transaction-add'],              'uses' => 'Bank\BankTrController@store']);
    Route::get('bank-transaction/{id}/delete',        ['as' => 'bank-transaction.delete',           'middleware' => ['ability:super-admin,bank-transaction-delete'],           'uses' => 'Bank\BankTrController@delete']);
    Route::post('bank-transaction/bulk-action',       ['as' => 'bank-transaction.bulk-action',      'middleware' => ['ability:super-admin,bank-transaction-bulk-action'],      'uses' => 'Bank\BankTrController@bulkAction']);


    /*Account Report*/
    Route::get('report/cash-book',                      ['as' => 'report.cash-book',                  'middleware' => ['ability:super-admin,report-cash-book'],                 'uses' => 'Report\CashBookReportController@cashBook']);
    Route::get('report/fee-collection',                 ['as' => 'report.fee-collection',             'middleware' => ['ability:super-admin,report-fee-collection'],            'uses' => 'Report\FeeCollectionReportController@feeCollection']);
    Route::get('report/fee-collection-head',            ['as' => 'report.fee-collection-head',        'middleware' => ['ability:super-admin,report-fee-collection-head'],       'uses' => 'Report\FeeCollectionHeadReportController@feeCollectionHead']);
    Route::get('report/fee-online-payment',             ['as' => 'report.fee-online-payment',         'middleware' => ['ability:super-admin,report-fee-online-payment'],        'uses' => 'Report\OnlineFeePaymentReportController@onlinePayments']);
    Route::get('report/balance-fee',                    ['as' => 'report.balance-fee',                'middleware' => ['ability:super-admin,report-balance-fee'],               'uses' => 'Report\BalanceFeeReportController@balanceFees']);


    
    /*Fees Group*/

    /*Balance Fees*/
    Route::get('fees/',                    ['as' => 'fees',                          'middleware' => ['ability:super-admin,fees-index'],            'uses' => 'Fees\FeesBaseController@index']);
    Route::get('fees/balance',             ['as' => 'fees.balance',                  'middleware' => ['ability:super-admin,fees-balance'],          'uses' => 'Fees\FeesBaseController@balance']);

    /*Fee Head*/
    Route::get('fees/head',                    ['as' => 'fees.head',                  'middleware' => ['ability:super-admin,fees-head-index'],                  'uses' => 'Fees\FeesHeadController@index']);
    Route::post('fees/head/store',             ['as' => 'fees.head.store',            'middleware' => ['ability:super-admin,fees-head-add'],                    'uses' => 'Fees\FeesHeadController@store']);
    Route::get('fees/head/{id}/edit',          ['as' => 'fees.head.edit',             'middleware' => ['ability:super-admin,fees-head-edit'],                   'uses' => 'Fees\FeesHeadController@edit']);
    Route::post('fees/head/{id}/update',       ['as' => 'fees.head.update',           'middleware' => ['ability:super-admin,fees-head-edit'],                   'uses' => 'Fees\FeesHeadController@update']);
    Route::get('fees/head/{id}/delete',        ['as' => 'fees.head.delete',           'middleware' => ['ability:super-admin,fees-head-delete'],                 'uses' => 'Fees\FeesHeadController@delete']);
    Route::get('fees/head/{id}/active',        ['as' => 'fees.head.active',           'middleware' => ['ability:super-admin,fees-head-active'],                 'uses' => 'Fees\FeesHeadController@Active']);
    Route::get('fees/head/{id}/in-active',     ['as' => 'fees.head.in-active',        'middleware' => ['ability:super-admin,fees-head-in-active'],              'uses' => 'Fees\FeesHeadController@inActive']);
    Route::post('fees/head/bulk-action',       ['as' => 'fees.head.bulk-action',      'middleware' => ['ability:super-admin,fees-head-bulk-action'],            'uses' => 'Fees\FeesHeadController@bulkAction']);

    Route::post('fees/head/import',            ['as' => 'fees.head.bulk.import',        'middleware' => ['ability:super-admin,fees-head-add'],             'uses' => 'Fees\FeesHeadController@handleImportFeeHead']);

    /*Fee Master*/
    Route::get('fees/master',                    ['as' => 'fees.master',                  'middleware' => ['ability:super-admin,fees-master-index'],            'uses' => 'Fees\FeesMasterController@index']);
    Route::get('fees/master/add',                ['as' => 'fees.master.add',              'middleware' => ['ability:super-admin,fees-master-add'],              'uses' => 'Fees\FeesMasterController@add']);
    Route::post('fees/master/store',             ['as' => 'fees.master.store',            'middleware' => ['ability:super-admin,fees-master-add'],              'uses' => 'Fees\FeesMasterController@store']);
    Route::get('fees/master/{id}/edit',          ['as' => 'fees.master.edit',             'middleware' => ['ability:super-admin,fees-master-edit'],             'uses' => 'Fees\FeesMasterController@edit']);
    Route::post('fees/master/{id}/update',       ['as' => 'fees.master.update',           'middleware' => ['ability:super-admin,fees-master-edit'],             'uses' => 'Fees\FeesMasterController@update']);
    Route::get('fees/master/{id}/delete',        ['as' => 'fees.master.delete',           'middleware' => ['ability:super-admin,fees-master-delete'],           'uses' => 'Fees\FeesMasterController@delete']);
    Route::post('fees/master/bulk-action',       ['as' => 'fees.master.bulk-action',      'middleware' => ['ability:super-admin,fees-master-bulk-action'],      'uses' => 'Fees\FeesMasterController@bulkAction']);
    Route::get('fees/master/{id}/active',        ['as' => 'fees.master.active',           'middleware' => ['ability:super-admin,fees-master-active'],           'uses' => 'Fees\FeesMasterController@Active']);
    Route::get('fees/master/{id}/in-active',     ['as' => 'fees.master.in-active',        'middleware' => ['ability:super-admin,fees-master-in-active'],        'uses' => 'Fees\FeesMasterController@inActive']);
    Route::post('fees/master/fee-html',          ['as' => 'fees.master.fee-html',                                                                               'uses' => 'Fees\FeesMasterController@feeHtmlRow']);

    /*Quick Fee Receive */
    Route::get('fees/quick-receive',                    ['as' => 'fees.quick-receive',                  'middleware' => ['ability:super-admin,fees-quick-receive-add'],            'uses' => 'Fees\FeesCollectionController@quickReceive']);
    Route::post('fees/quick-receive/store',             ['as' => 'fees.quick-receive.store',            'middleware' => ['ability:super-admin,fees-quick-receive-add'],              'uses' => 'Fees\FeesCollectionController@quickReceiveStore']);
    Route::post('student-detail-html',                  ['as' => 'student-detail-html',                                                                                             'uses' => 'Fees\FeesCollectionController@studentDetail']);

    //Receive Selected Due
    Route::get('fees/due/{id}/view',          ['as' => 'fees.due.view',             'middleware' => ['ability:super-admin,fees-collection-view'],             'uses' => 'Fees\FeesCollectionController@dueView']);
    Route::post('fees/due/store',             ['as' => 'fees.due.store',            'middleware' => ['ability:super-admin,fees-collection-add'],              'uses' => 'Fees\FeesCollectionController@dueStore']);

    /*Collect Fee */
    Route::get('fees/collection',                    ['as' => 'fees.collection',                  'middleware' => ['ability:super-admin,fees-collection-index'],            'uses' => 'Fees\FeesCollectionController@index']);
    Route::get('fees/collection/{id}/add',           ['as' => 'fees.collection.add',              'middleware' => ['ability:super-admin,fees-collection-add'],              'uses' => 'Fees\FeesCollectionController@add']);
    Route::post('fees/collection/store',             ['as' => 'fees.collection.store',            'middleware' => ['ability:super-admin,fees-collection-add'],              'uses' => 'Fees\FeesCollectionController@store']);
    Route::get('fees/collection/{id}/view',          ['as' => 'fees.collection.view',             'middleware' => ['ability:super-admin,fees-collection-view'],             'uses' => 'Fees\FeesCollectionController@view']);
    Route::get('fees/collection/{id}/delete',        ['as' => 'fees.collection.delete',           'middleware' => ['ability:super-admin,fees-collection-delete'],           'uses' => 'Fees\FeesCollectionController@delete']);

    //Online Payment Verification
    Route::get('fees/online-payment',                       ['as' => 'fees.online-payment',                  'middleware' => ['ability:super-admin,fees-online-payment-index'],                 'uses' => 'Fees\OnlinePaymentController@onlinePayment']);
    Route::post('fees/online-payment/{id}/verify',          ['as' => 'fees.online-payment.verify',              'middleware' => ['ability:super-admin,fees-online-payment-verify'],              'uses' => 'Fees\OnlinePaymentController@paymentVerify']);
    Route::get('fees/online-payment/{id}/view',             ['as' => 'fees.online-payment.view',              'middleware' => ['ability:super-admin,fees-online-payment-view'],              'uses' => 'Fees\OnlinePaymentController@paymentView']);
    

    /*online payment*/
    Route::get('fees/pay-with-paypal',              ['as' => 'fees.paypal-form',                'uses' => 'Fees\Payment\PayPalController@payment']);
    //Route::get('fees/pay-with-paypal', 'PayPalController@payment')->name('payment');
    Route::get('fees/paypal-payment-cancel',        ['as' => 'fees.paypal-cancel',                'uses' => 'Fees\Payment\PayPalController@cancel']);
    //Route::get('fees/paypal-payment-cancel', 'PayPalController@cancel')->name('payment.cancel');
    Route::get('fees/paypal-payment-success',       ['as' => 'fees.paypal-success',                'uses' => 'Fees\Payment\PayPalController@success']);
    //Route::get('fees/paypal-payment-success', 'PayPalController@success')->name('payment.success');

    //stripe
    Route::post('fees/pay-with-stripe',               ['as' => 'fees.stripePayment',                   'uses' => 'Fees\Payment\StripePaymentController@stripePayment']);

    //Route::get('fees/pay-with-instamojo/index',           ['as' => 'fees.instamojoPayment.index',                   'uses' => 'Fees\Payment\InstamojoPaymentController@index']);
    Route::post('fees/pay-with-instamojo/index',            ['as' => 'fees.instamojoPayment.index',                    'uses' => 'Fees\Payment\InstamojoPaymentController@index']);
    Route::post('fees/pay-with-instamojo/pay',              ['as' => 'fees.instamojoPayment.pay',                       'uses' => 'Fees\Payment\InstamojoPaymentController@pay']);
    Route::get('fees/pay-with-instamojo/pay-success',       ['as' => 'fees.instamojoPayment.success',                   'uses' => 'Fees\Payment\InstamojoPaymentController@success']);

    //Route::post('fees/online-payment',                  ['as' => 'fees.online-payment',                'uses' => 'Fees\OnlinePaymentController@paymentProcessed']);
    //Route::post('fees/pay-with-stripe',               ['as' => 'fees.stripePayment',                 'middleware' => ['ability:super-admin,fees-payment-stripe-payment'],     'uses' => 'Fees\Payment\StripePaymentController@stripePayment']);

    Route::post('fees/payumoney-form',                 ['as' => 'fees.payumoney-form',                'uses' => 'Fees\Payment\PayumoneyPaymentController@payumoneyForm']);
    Route::post('fees/pay-with-payumoney/success',     ['as' => 'fees.payumoney.success',             'uses' => 'Fees\Payment\PayumoneyPaymentController@payumoneyPaymentSuccess']);
    Route::post('fees/pay-with-payumoney/failure',     ['as' => 'fees.payumoney.failure',             'uses' => 'Fees\Payment\PayumoneyPaymentController@payumoneyPaymentFailure']);

    Route::post('fees/pay-stack-form',                 ['as' => 'fees.pay-stack-form',                'uses' => 'Fees\Payment\PayStackPaymentController@redirectToGateway']);
    Route::get('fees/pay-with-pay-stack/success',     ['as' => 'fees.pay-stack.success',             'uses' => 'Fees\Payment\PayStackPaymentController@handleGatewayCallback']);

    //Route::post('fees/pay-with-razorpay-form',                 ['as' => 'fees.pay-with-razorpay-form',                'uses' => 'Fees\Payment\RozorPayPaymentController@paywithrazorpay']);
    Route::get('fees/pay-with-razorpay-form',                 ['as' => 'fees.pay-with-razorpay-form',                'uses' => 'Fees\Payment\RozorPayPaymentController@paywithrazorpay']);
    Route::get('fees/pay-with-razorpay/success',               ['as' => 'fees.pay-with-razorpay.success',            'uses' => 'Fees\Payment\RozorPayPaymentController@payment']);


    // SSLCommerz Payment Routes (using UcbBankPaymentController for consistency with your structure)
    // Route::get('/sslcommerz-pay', ['as' => 'fees.pay-with-sslcommerz.index', 'uses' => 'Fees\Payment\SslCommerzPaymentController@index']);
    // Route::post('/sslcommerz-pay', ['as' => 'fees.pay-with-sslcommerz.initiate', 'uses' => 'Fees\Payment\SslCommerzPaymentController@pay']);
    // Route::post('/sslcommerz-ipn', ['as' => 'fees.pay-with-sslcommerz.ipn', 'uses' => 'Fees\Payment\SslCommerzPaymentController@ipn']);

        // SSLCommerz Routes 
    Route::get('fees/sslcommerz', ['as' => 'fees.sslcommerz.index', 'uses' => 'Fees\Payment\SslCommerzPaymentController@index']);
    Route::post('fees/sslcommerz', ['as' => 'fees.sslcommerz.initiate', 'uses' => 'Fees\Payment\SslCommerzPaymentController@pay']);
    // Add these if not already present for SSLCommerz callbacks
    Route::post('fees/sslcommerz/success', ['as' => 'fees.sslcommerz.success', 'uses' => 'Fees\Payment\SslCommerzPaymentController@success']);
    Route::post('fees/sslcommerz/fail', ['as' => 'fees.sslcommerz.fail', 'uses' => 'Fees\Payment\SslCommerzPaymentController@fail']);
    Route::post('fees/sslcommerz/cancel', ['as' => 'fees.sslcommerz.cancel', 'uses' => 'Fees\Payment\SslCommerzPaymentController@cancel']);
    Route::post('fees/sslcommerz/ipn', ['as' => 'fees.sslcommerz.ipn', 'uses' => 'Fees\Payment\SslCommerzPaymentController@ipn']);

    // cybersource:
    // Show payment form
    // Route::get('/cybersource-pay', [
    //     'as' => 'fees.pay-with-cybersource.index',
    //     'uses' => 'Fees\Payment\CyberSourcePaymentController@index'
    // ]);

    // // Initiate payment (builds and posts to CyberSource)
    // Route::post('/cybersource-pay', [
    //     'as' => 'fees.pay-with-cybersource.initiate',
    //     'uses' => 'Fees\Payment\CyberSourcePaymentController@pay'
    // ]);

    // // Handle CyberSource callback/response (POST from CyberSource)
    // Route::post('/cybersource-callback', [
    //     'as' => 'fees.pay-with-cybersource.callback',
    //     'uses' => 'Fees\Payment\CyberSourcePaymentController@callback'
    // ]);


    // Display payment form
    // Route::get('fees/cybersource-pay', [
    //     'as' => 'fees.pay-with-cybersource.index',
    //     'uses' => 'Fees\Payment\CyberSourcePaymentController@index'
    // ]);

    // // Initiate payment (builds and posts to CyberSource)
    // Route::post('fees/cybersource-pay', [
    //     'as' => 'fees.pay-with-cybersource.initiate',
    //     'uses' => 'Fees\Payment\CyberSourcePaymentController@pay'
    // ]);

    // // Handle CyberSource callback/response (POST from CyberSource)
    // Route::post('fees/cybersource-callback', [
    //     'as' => 'fees.pay-with-cybersource.callback',
    //     'uses' => 'Fees\Payment\CyberSourcePaymentController@callback'
    // ]);
    
    // // Payment success page
    // Route::get('fees/payment-success', [
    //     'as' => 'fees.payment.success',
    //     'uses' => 'Fees\Payment\CyberSourcePaymentController@success'
    // ])->middleware('verified.payment');

    // Show payment page (form or button)
        // Route::get('/cybersource-pay', [
        //     'as' => 'fees.pay-with-cybersource.index',
        //     'uses' => 'Fees\Payment\CyberSourcePaymentController@index'
        // ]);

        // // Submit to CyberSource (POST) - This will be your confirmation page where the form auto-submits
        // Route::post('/cybersource-pay', [
        //     'as' => 'fees.pay-with-cybersource.initiate',
        //     'uses' => 'Fees\Payment\CyberSourcePaymentController@pay'
        // ]);

        // // Handle CyberSource callback (POST)
        // Route::post('/cybersource-callback', [
        //     'as' => 'fees.pay-with-cybersource.callback',
        //     'uses' => 'Fees\Payment\CyberSourcePaymentController@callback'
        // ]);


    // UNITED COMMERCIAL BANK LIMITED Payment Gateway Routes
    //Route::get('fees/ucbl-pay', ['as' => 'fees.pay-with-ucbl.index', 'uses' => 'Fees\Payment\UcbPaymentController@index']);
   // Route::post('fees/ucbl-pay', ['as' => 'fees.pay-with-ucbl.initiate', 'uses' => 'Fees\Payment\UcbPaymentController@pay']);

   

    // Get Route For Show Payment Form
    //Route::get('paywithrazorpay', 'RazorpayController@payWithRazorpay')->name('paywithrazorpay');
    // Post Route For Makw Payment Request
    //Route::post('payment', 'RazorpayController@payment')->name('payment');


    //Route::post('fees/pay-with-pay-stack/failure',     ['as' => 'fees.pay-stack.failure',             'uses' => 'Fees\Payment\PayStackPaymentController@']);

    //Route::post('/pay', 'PaymentController@redirectToGateway')->name('pay');

    /*Route::post('/pay', [
        'uses' => 'PaymentController@redirectToGateway',
        'as' => 'pay'
    ]);
    Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');*/

    //Route::post('fees/pay-with-khalti',               ['as' => 'fees.khaltiPayment',                 'middleware' => ['ability:super-admin,fees-payment-khalti-payment'],     'uses' => 'Fees\FeesCollectionController@khaltiPayment']);

    //Route::post('fees/pesapal-form',                 ['as' => 'fees.pesapal-form',                      'middleware' => ['ability:super-admin,fees-payment-pesapal'],              'uses' => 'Fees\Payment\PesapalPaymentController@pesapalForm']);
    //Route::post('fees/pay-with-pesapal',             ['as' => 'fees.pesapal',                           'middleware' => ['ability:super-admin,fees-payment-pesapal'],             'uses' => 'Fees\Payment\PesapalPaymentController@payment']);
    //Route::get('fees/pesapal/donepayment',           ['as' => 'fees.pesapal.paymentsuccess',            'middleware' => ['ability:super-admin,fees-payment-pesapal'],             'uses' => 'Fees\Payment\PesapalPaymentController@paymentsuccess']);
    //Route::get('fees/pesapal/paymentconfirmation',   ['as' => 'fees.pesapal.paymentconfirmation',       'middleware' => ['ability:super-admin,fees-payment-pesapal'],             'uses' => 'Fees\Payment\PesapalPaymentController@paymentconfirmation']);


});







/*Library Grouping*/
Route::group(['prefix' => 'library/',                                   'as' => 'library.',                                    'namespace' => 'Library\\'], function () {

    Route::get('',                          ['as' => '',                        'middleware' => ['ability:super-admin,library-index'],           'uses' => 'LibraryBaseController@index']);
    Route::post('issue',                    ['as' => 'issue',                   'middleware' => ['ability:super-admin,library-book-issue'],      'uses' => 'LibraryBaseController@issueBook']);

    /*Book Requests*/
    Route::get('request-staff',                     ['as' => 'staff-request',                   'middleware' => ['ability:super-admin,library-staff-request-index'],       'uses' => 'LibraryBaseController@staffRequest']);
    Route::get('request-student',                   ['as' => 'student-request',                 'middleware' => ['ability:super-admin,library-student-request-index'],     'uses' => 'LibraryBaseController@studentRequest']);
    Route::get('{id}/{member}/request-cancel',      ['as' => 'request-cancel',                                                                                              'uses' => 'LibraryBaseController@bookRequestCancel']);

    Route::get('{id}/return',               ['as' => 'return',                  'middleware' => ['ability:super-admin,library-book-return'],     'uses' => 'LibraryBaseController@returnBook']);
    Route::get('return-over',               ['as' => 'return-over',             'middleware' => ['ability:super-admin,library-return-over'],     'uses' => 'LibraryBaseController@returnOver']);
    Route::get('issue-history',             ['as' => 'issue-history',           'middleware' => ['ability:super-admin,library-issue-history'],   'uses' => 'LibraryBaseController@issueHistory']);
    Route::post('book-detail-html',         ['as' => 'book-detail-html',                                                                         'uses' => 'LibraryBaseController@bookDetail']);
    Route::get('book-name-autocomplete',    ['as' => 'book-name-autocomplete',                                                                   'uses' => 'LibraryBaseController@bookNameAutocomplete']);

    /*Book Master*/
    Route::get('book',                          ['as' => 'book',                'middleware' => ['ability:super-admin,book-index'],         'uses' => 'BookController@index']);
    Route::get('book/add',                      ['as' => 'book.add',            'middleware' => ['ability:super-admin,book-add'],           'uses' => 'BookController@add']);
    Route::post('book/store',                   ['as' => 'book.store',          'middleware' => ['ability:super-admin,book-add'],           'uses' => 'BookController@store']);
    Route::get('book/{id}/edit',                ['as' => 'book.edit',           'middleware' => ['ability:super-admin,book-edit'],          'uses' => 'BookController@edit']);
    Route::post('book/{id}/update',             ['as' => 'book.update',         'middleware' => ['ability:super-admin,book-edit'],          'uses' => 'BookController@update']);

    Route::get('book/import',                      ['as' => 'book.import',            'middleware' => ['ability:super-admin,book-add'],           'uses' => 'BookController@importBook']);
    Route::post('book/import',                     ['as' => 'book.bulk.import',        'middleware' => ['ability:super-admin,book-add'],             'uses' => 'BookController@handleImportBook']);


    Route::get('book/{id}/view',                ['as' => 'book.view',           'middleware' => ['ability:super-admin,book-view'],          'uses' => 'BookController@view']);
    Route::get('book/{id}/delete',              ['as' => 'book.delete',         'middleware' => ['ability:super-admin,book-delete'],        'uses' => 'BookController@delete']);
    Route::get('book/{id}/active',              ['as' => 'book.active',         'middleware' => ['ability:super-admin,book-active'],        'uses' => 'BookController@Active']);
    Route::get('book/{id}/in-active',           ['as' => 'book.in-active',      'middleware' => ['ability:super-admin,book-in-active'],     'uses' => 'BookController@inActive']);
    Route::post('book/bulk-action',             ['as' => 'book.bulk-action',    'middleware' => ['ability:super-admin,book-bulk-action'],   'uses' => 'BookController@bulkAction']);

    /*Book Level*/
    Route::post('book/add/copies',                  ['as' => 'book.add.copies',             'middleware' => ['ability:super-admin,book-add-copies'],              'uses' => 'BookController@addCopies']);
    Route::get('book/{id}/book-status/{status}',    ['as' => 'book.book-status',            'middleware' => ['ability:super-admin,book-status'],                  'uses' => 'BookController@bookStatus']);
    Route::post('book/bulk-copies-delete',          ['as' => 'book.bulk-copies-delete',     'middleware' => ['ability:super-admin,book-bulk-copies-delete'],      'uses' => 'BookController@bulkCopiesDelete']);

    /*Books Category Routes*/
    Route::get('book/category',                     ['as' => 'book.category',               'middleware' => ['ability:super-admin,book-category-index'],            'uses' => 'BookCategoryController@index']);
    Route::post('book/category/store',              ['as' => 'book.category.store',         'middleware' => ['ability:super-admin,book-category-add'],              'uses' => 'BookCategoryController@store']);
    Route::get('book/category/{id}/edit',           ['as' => 'book.category.edit',          'middleware' => ['ability:super-admin,book-category-edit'],             'uses' => 'BookCategoryController@edit']);
    Route::post('book/category/{id}/update',        ['as' => 'book.category.update',        'middleware' => ['ability:super-admin,book-category-edit'],             'uses' => 'BookCategoryController@update']);
    Route::get('book/category/{id}/delete',         ['as' => 'book.category.delete',        'middleware' => ['ability:super-admin,book-category-delete'],           'uses' => 'BookCategoryController@delete']);
    Route::get('book/category/{id}/active',         ['as' => 'book.category.active',        'middleware' => ['ability:super-admin,book-category-active'],           'uses' => 'BookCategoryController@Active']);
    Route::get('book/category/{id}/in-active',      ['as' => 'book.category.in-active',     'middleware' => ['ability:super-admin,book-category-in-active'],        'uses' => 'BookCategoryController@inActive']);
    Route::post('book/category/bulk-action',        ['as' => 'book.category.bulk-action',   'middleware' => ['ability:super-admin,book-category-bulk-action'],      'uses' => 'BookCategoryController@bulkAction']);

    /*Books Category Routes*/
    Route::get('circulation',                       ['as' => 'circulation',                 'middleware' => ['ability:super-admin,library-circulation-index'],          'uses' => 'CirculationController@index']);
    Route::post('circulation/store',                ['as' => 'circulation.store',           'middleware' => ['ability:super-admin,library-circulation-add'],            'uses' => 'CirculationController@store']);
    Route::get('circulation/{id}/edit',             ['as' => 'circulation.edit',            'middleware' => ['ability:super-admin,library-circulation-edit'],           'uses' => 'CirculationController@edit']);
    Route::post('circulation/{id}/update',          ['as' => 'circulation.update',          'middleware' => ['ability:super-admin,library-circulation-edit'],           'uses' => 'CirculationController@update']);
    Route::get('circulation/{id}/delete',           ['as' => 'circulation.delete',          'middleware' => ['ability:super-admin,library-circulation-delete'],         'uses' => 'CirculationController@delete']);
    Route::get('circulation/{id}/active',           ['as' => 'circulation.active',          'middleware' => ['ability:super-admin,library-circulation-active'],         'uses' => 'CirculationController@Active']);
    Route::get('circulation/{id}/in-active',        ['as' => 'circulation.in-active',       'middleware' => ['ability:super-admin,library-circulation-in-active'],      'uses' => 'CirculationController@inActive']);
    Route::post('circulation/bulk-action',          ['as' => 'circulation.bulk-action',     'middleware' => ['ability:super-admin,library-circulation-bulk-action'],    'uses' => 'CirculationController@bulkAction']);

    /*Library Member Routes*/
    Route::get('member',                        ['as' => 'member',              'middleware' => ['ability:super-admin,library-member-index'],           'uses' => 'MemberController@index']);
    Route::get('member/add',                    ['as' => 'member.add',          'middleware' => ['ability:super-admin,library-member-add'],             'uses' => 'MemberController@add']);
    Route::post('member/store',                 ['as' => 'member.store',        'middleware' => ['ability:super-admin,library-member-add'],             'uses' => 'MemberController@store']);
    Route::get('member/quick-membership',  ['as' => 'member.quick-membership',         'middleware' => ['ability:super-admin,library-member-add'],            'uses' => 'MemberController@quickMembership']);
    Route::get('member/{id}/edit',              ['as' => 'member.edit',         'middleware' => ['ability:super-admin,library-member-edit'],            'uses' => 'MemberController@edit']);
    Route::post('member/{id}/update',           ['as' => 'member.update',       'middleware' => ['ability:super-admin,library-member-edit'],            'uses' => 'MemberController@update']);
    Route::get('member/{id}/delete',            ['as' => 'member.delete',       'middleware' => ['ability:super-admin,library-member-delete'],          'uses' => 'MemberController@delete']);
    Route::get('member/{id}/active',            ['as' => 'member.active',       'middleware' => ['ability:super-admin,library-member-active'],          'uses' => 'MemberController@Active']);
    Route::get('member/{id}/in-active',         ['as' => 'member.in-active',    'middleware' => ['ability:super-admin,library-member-in-active'],       'uses' => 'MemberController@inActive']);
    Route::post('member/bulk-action',           ['as' => 'member.bulk-action',  'middleware' => ['ability:super-admin,library-member-bulk-action'],     'uses' => 'MemberController@bulkAction']);

    /*Library Member Staff Routes*/
    /*Staff Member*/
    Route::get('staff',                 ['as' => 'staff',                   'middleware' => ['ability:super-admin,library-member-staff'],      'uses' => 'StaffMemberController@staff']);
    Route::get('staff/{id}/view',       ['as' => 'staff.view',              'middleware' => ['ability:super-admin,library-member-staff-view'],      'uses' => 'StaffMemberController@staffView']);

    /*Student Member*/
    Route::get('student',                   ['as' => 'student',                 'middleware' => ['ability:super-admin,library-member-student'],      'uses' => 'StudentMemberController@student']);
    Route::get('student/{id}/view',         ['as' => 'student.view',            'middleware' => ['ability:super-admin,library-member-student-view'],      'uses' => 'StudentMemberController@studentView']);

});



use App\Http\Controllers\Attendance\LiveAttendanceController;
use App\Http\Controllers\Attendance\ActionController;
use App\Http\Controllers\Attendance\Device\TipsoiUnifiedController;
use App\Http\Controllers\Attendance\Dashboard\StudentAttendanceDashboardController;
use App\Http\Controllers\Attendance\Dashboard\StaffAttendanceDashboardController;
use App\Http\Controllers\Attendance\Reports\StudentMonthlyReportController;
use App\Http\Controllers\Attendance\Reports\StaffMonthlyReportController; 
use App\Http\Controllers\Attendance\Reports\IndividualAttendanceReportController;


Route::prefix('attendance')
    ->name('attendance.')
    ->middleware(['auth'])
    ->group(function () {

        /* ===== Legacy route aliases for existing menus/buttons ===== */
        Route::get('/student', [LiveAttendanceController::class, 'students'])
            ->name('student')
            ->middleware(['ability:super-admin,attendance']);

        Route::get('/subject', [LiveAttendanceController::class, 'students'])
            ->name('subject')
            ->middleware(['ability:super-admin,attendance']);

        Route::get('/staff', [LiveAttendanceController::class, 'staff'])
            ->name('staff')
            ->middleware(['ability:super-admin,attendance']);

        /* ===== Manual Live Attendance ===== */
        Route::get('/live',           [LiveAttendanceController::class, 'redirectToStudents'])
            ->name('live.index')
            ->middleware(['ability:super-admin,attendance-live-index']);

        Route::get('/live/students',  [LiveAttendanceController::class, 'students'])
            ->name('live.students')
            ->middleware(['ability:super-admin,attendance-live-students']);

        Route::get('/live/staff',     [LiveAttendanceController::class, 'staff'])
            ->name('live.staff')
            ->middleware(['ability:super-admin,attendance-live-staff']);

        Route::get('/live/list',      [LiveAttendanceController::class, 'list'])
            ->name('live.list')
            ->middleware(['ability:super-admin,attendance-live-list']);

        Route::get('/scan',           [LiveAttendanceController::class, 'scan'])
            ->name('scan')
            ->middleware(['ability:super-admin,attendance-scan']);

        Route::post('/identify',      [LiveAttendanceController::class, 'identify'])
            ->name('identify')
            ->middleware(['ability:super-admin,attendance-identify','throttle:120,1']);

        Route::post('/row/{attendance}/mark',  [LiveAttendanceController::class, 'mark'])
            ->name('row.mark')
            ->middleware(['ability:super-admin,attendance-row-mark']);

        Route::post('/row/{attendance}/check', [LiveAttendanceController::class, 'check'])
            ->name('row.check')
            ->middleware(['ability:super-admin,attendance-row-check']);

        Route::post('/quick/mark',  [LiveAttendanceController::class, 'quickMark'])
            ->name('quick.mark')
            ->middleware(['ability:super-admin,attendance-quick-mark']);

        Route::post('/quick/check', [LiveAttendanceController::class, 'quickCheck'])
            ->name('quick.check')
            ->middleware(['ability:super-admin,attendance-quick-check']);

        Route::post('/bulk/mark',   [LiveAttendanceController::class, 'bulkMark'])
            ->name('bulk.mark')
            ->middleware(['ability:super-admin,attendance-bulk-mark']);

        Route::post('/bulk/check',  [LiveAttendanceController::class, 'bulkCheck'])
            ->name('bulk.check')
            ->middleware(['ability:super-admin,attendance-bulk-check']);

        /* ===== TIPSOI (Devices) ===== */
        Route::get('tipsoi', [TipsoiUnifiedController::class, 'dashboard'])
            ->name('tipsoi.dashboard')
            ->middleware(['ability:super-admin,tipsoi-dashboard']);

        Route::prefix('tipsoi')->name('tipsoi.')->group(function () {
            Route::get('devices',            [TipsoiUnifiedController::class, 'getAllDevices'])
                ->name('devices.index')
                ->middleware(['ability:super-admin,tipsoi-devices-index']);

            Route::get('search',             [TipsoiUnifiedController::class, 'searchPeople'])
                ->name('search')
                ->middleware(['ability:super-admin,tipsoi-search']);

            Route::post('sdk/push-person',   [TipsoiUnifiedController::class, 'pushPersonToDevice'])
                ->name('sdk.push-person')
                ->middleware(['ability:super-admin,tipsoi-sdk-push-person']);

            Route::post('allocations/batch', [TipsoiUnifiedController::class, 'batchAllocations'])
                ->name('allocations.batch')
                ->middleware(['ability:super-admin,tipsoi-allocations-batch']);

            // Queue run + poll
            Route::post('runs',              [TipsoiUnifiedController::class, 'storeRun'])
                ->name('runs.store')
                ->middleware(['ability:super-admin,tipsoi-runs-store']);

            Route::get('runs/{run}',         [TipsoiUnifiedController::class, 'showRun'])
                ->name('runs.show')
                ->middleware(['ability:super-admin,tipsoi-runs-show']);

            // Legacy/direct ops
            Route::post('batch/update',      [TipsoiUnifiedController::class, 'batchUpdate'])
                ->name('batch.update')
                ->middleware(['ability:super-admin,tipsoi-batch-update']);

            Route::post('logs',              [TipsoiUnifiedController::class, 'storeAttendanceLogs'])
                ->name('logs.store')
                ->middleware(['ability:super-admin,tipsoi-logs-store']);

            Route::get('logs/test',          [TipsoiUnifiedController::class, 'testLogsSnapshot'])
                ->name('logs.test')
                ->middleware(['ability:super-admin,tipsoi-logs-test']);

            Route::get('status',             [TipsoiUnifiedController::class, 'syncStatus'])
                ->name('status')
                ->middleware(['ability:super-admin,tipsoi-status']);

            Route::post('sdk/heartbeat',     [TipsoiUnifiedController::class, 'setHeartbeat'])
                ->name('sdk.heartbeat')
                ->middleware(['ability:super-admin,tipsoi-sdk-heartbeat']);
        });

        /* ===== Dashboards ===== */
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            // Students
            Route::get('students', [StudentAttendanceDashboardController::class, 'index'])
                ->name('students.index')
                ->middleware(['ability:super-admin,attendance-dashboard-students-index']);

            Route::get('students/summary', [StudentAttendanceDashboardController::class, 'summary'])
                ->name('students.summary')
                ->middleware(['ability:super-admin,attendance-dashboard-students-summary']);

            // Staff
            Route::get('staff', [StaffAttendanceDashboardController::class, 'index'])
                ->name('staff.index')
                ->middleware(['ability:super-admin,attendance-dashboard-staff-index']);

            Route::get('staff/summary', [StaffAttendanceDashboardController::class, 'summary'])
                ->name('staff.summary')
                ->middleware(['ability:super-admin,attendance-dashboard-staff-summary']);
        });

        /* ===== Reports: Monthly ===== */
        Route::prefix('reports')->name('reports.')->group(function () {
            // Students monthly
            Route::get('students/monthly',      [StudentMonthlyReportController::class, 'index'])
                ->name('students.monthly')
                ->middleware(['ability:super-admin,attendance-reports-students-monthly']);

            Route::get('students/monthly/data', [StudentMonthlyReportController::class, 'data'])
                ->name('students.monthly.data')
                ->middleware(['ability:super-admin,attendance-reports-students-monthly-data']);

            // Staff monthly
            Route::get('staff/monthly',      [StaffMonthlyReportController::class, 'index'])
                ->name('staff.monthly')
                ->middleware(['ability:super-admin,attendance-reports-staff-monthly']);

            Route::get('staff/monthly/data', [StaffMonthlyReportController::class, 'data'])
                ->name('staff.monthly.data')
                ->middleware(['ability:super-admin,attendance-reports-staff-monthly-data']);
        });

        /* ===== Reports: Individual ===== */
        Route::prefix('reports/individual')->name('reports.individual.')->group(function () {
            Route::get('students', [IndividualAttendanceReportController::class, 'studentIndex'])
                ->name('students')
                ->middleware(['ability:super-admin,attendance-reports-individual-students']);

            Route::get('staff',    [IndividualAttendanceReportController::class, 'staffIndex'])
                ->name('staff')
                ->middleware(['ability:super-admin,attendance-reports-individual-staff']);

            Route::get('search',   [IndividualAttendanceReportController::class, 'search'])
                ->name('search')
                ->middleware(['ability:super-admin,attendance-reports-individual-search']);

            Route::get('data',     [IndividualAttendanceReportController::class, 'data'])
                ->name('data')
                ->middleware(['ability:super-admin,attendance-reports-individual-data']);

            // Subject list for dropdown
            Route::get('subjects', [IndividualAttendanceReportController::class, 'subjects'])
                ->name('subjects')
                ->middleware(['ability:super-admin,attendance-reports-individual-subjects']);
        });
    });




/*Exam group */
Route::group(['prefix' => 'exam/',                                      'as' => 'exam',                                         'namespace' => 'Examination\\'], function () {

    /*Exam Types Routes*/
    Route::get('',                                  ['as' => '',                  'middleware' => ['ability:super-admin,exam-index'],           'uses' => 'ExamController@index']);
    Route::post('store',                            ['as' => '.store',            'middleware' => ['ability:super-admin,exam-add'],             'uses' => 'ExamController@store']);
    Route::get('{id}/edit',                         ['as' => '.edit',             'middleware' => ['ability:super-admin,exam-edit'],            'uses' => 'ExamController@edit']);
    Route::post('{id}/update',                      ['as' => '.update',           'middleware' => ['ability:super-admin,exam-edit'],            'uses' => 'ExamController@update']);
    Route::get('{id}/delete',                       ['as' => '.delete',           'middleware' => ['ability:super-admin,exam-delete'],          'uses' => 'ExamController@delete']);
    Route::get('{id}/active',                       ['as' => '.active',           'middleware' => ['ability:super-admin,exam-active'],          'uses' => 'ExamController@Active']);
    Route::get('{id}/in-active',                    ['as' => '.in-active',        'middleware' => ['ability:super-admin,exam-in-active'],       'uses' => 'ExamController@inActive']);
    Route::post('bulk-action',                      ['as' => '.bulk-action',      'middleware' => ['ability:super-admin,exam-bulk-action'],     'uses' => 'ExamController@bulkAction']);

    /*Exam Print Routes*/
    Route::get('admit-card',                        ['as' => '.admit-card',         'middleware' => ['ability:super-admin,exam-admit-card'],        'uses' => 'ExamController@admitCard']);
    Route::get('routine',                           ['as' => '.routine',            'middleware' => ['ability:super-admin,exam-exam-routine'],      'uses' => 'ExamController@examRoutine']);
    Route::get('mark-sheet',                        ['as' => '.mark-sheet',         'middleware' => ['ability:super-admin,exam-mark-ledger'],        'uses' => 'ExamController@markSheet']);

    //result publish status
    Route::get('schedule/{year}/{month}/{exam}/{faculty}/{semester}/result-publish',    ['as' => '.schedule.result-publish',                  'middleware' => ['ability:super-admin,exam-result-publish'],                'uses' => 'ExamScheduleController@publish']);
    Route::get('schedule/{year}/{month}/{exam}/{faculty}/{semester}/result-un-publish', ['as' => '.schedule.result-un-publish',               'middleware' => ['ability:super-admin,exam-result-un-publish'],             'uses' => 'ExamScheduleController@unPublish']);

    /*Exam Schedule Routes*/
    Route::get('schedule',                                                      ['as' => '.schedule',                  'middleware' => ['ability:super-admin,exam-schedule-index'],                 'uses' => 'ExamScheduleController@index']);
    Route::get('schedule/add',                                                  ['as' => '.schedule.add',              'middleware' => ['ability:super-admin,exam-schedule-add'],                   'uses' => 'ExamScheduleController@add']);
    Route::post('schedule/store',                                               ['as' => '.schedule.store',            'middleware' => ['ability:super-admin,exam-schedule-add'],                   'uses' => 'ExamScheduleController@store']);
    Route::get('schedule/{id}/edit',                                            ['as' => '.schedule.edit',             'middleware' => ['ability:super-admin,exam-schedule-edit'],                  'uses' => 'ExamScheduleController@edit']);
    Route::post('schedule/{id}/update',                                         ['as' => '.schedule.update',           'middleware' => ['ability:super-admin,exam-schedule-edit'],                  'uses' => 'ExamScheduleController@update']);
    Route::get('schedule/{year}/{month}/{exam}/{faculty}/{semester}/delete',    ['as' => '.schedule.delete',           'middleware' => ['ability:super-admin,exam-schedule-delete'],                'uses' => 'ExamScheduleController@delete']);
    Route::get('schedule/{year}/{month}/{exam}/{faculty}/{semester}/active',    ['as' => '.schedule.active',           'middleware' => ['ability:super-admin,exam-schedule-active'],                'uses' => 'ExamScheduleController@active']);
    Route::get('schedule/{year}/{month}/{exam}/{faculty}/{semester}/in-active', ['as' => '.schedule.in-active',        'middleware' => ['ability:super-admin,exam-schedule-in-active'],             'uses' => 'ExamScheduleController@inActive']);
    Route::post('schedule/bulk-action',                                          ['as' => '.schedule.bulk-action',      'middleware' => ['ability:super-admin,exam-schedule-index'],                 'uses' => 'ExamScheduleController@bulkAction']);
    Route::post('schedule/subject-html',                                        ['as' => '.schedule.subject-html',                                                                                  'uses' => 'ExamScheduleController@subjectHtmlRow']);

    /*Exam Schedule Routes*/
    Route::get('mark-ledger',                                                      ['as' => '.mark-ledger',                     'middleware' => ['ability:super-admin,exam-mark-ledger-index'],                 'uses' => 'ExamMarkLedgerController@index']);
    Route::get('mark-ledger/add',                                                  ['as' => '.mark-ledger.add',                 'middleware' => ['ability:super-admin,exam-mark-ledger-add'],                   'uses' => 'ExamMarkLedgerController@add']);
    Route::post('mark-ledger/store',                                               ['as' => '.mark-ledger.store',               'middleware' => ['ability:super-admin,exam-mark-ledger-add'],                   'uses' => 'ExamMarkLedgerController@store']);
    Route::get('mark-ledger/{id}/edit',                                            ['as' => '.mark-ledger.edit',                'middleware' => ['ability:super-admin,exam-mark-ledger-edit'],                  'uses' => 'ExamMarkLedgerController@edit']);
    Route::post('mark-ledger/{id}/update',                                         ['as' => '.mark-ledger.update',              'middleware' => ['ability:super-admin,exam-mark-ledger-edit'],                  'uses' => 'ExamMarkLedgerController@update']);
    Route::get('mark-ledger/{exam}/{student}/delete',                              ['as' => '.mark-ledger.delete',              'middleware' => ['ability:super-admin,exam-mark-ledger-delete'],                'uses' => 'ExamMarkLedgerController@delete']);
    Route::get('mark-ledger/{exam}/{student}/active',                              ['as' => '.mark-ledger.active',              'middleware' => ['ability:super-admin,exam-mark-ledger-active'],                'uses' => 'ExamMarkLedgerController@active']);
    Route::get('mark-ledger/{exam}/{student}/in-active',                           ['as' => '.mark-ledger.in-active',           'middleware' => ['ability:super-admin,exam-mark-ledger-in-acctive'],            'uses' => 'ExamMarkLedgerController@inActive']);
    Route::post('mark-ledger/find-subject',                                        ['as' => '.mark-ledger.find-subject',                                                                                        'uses' => 'ExamMarkLedgerController@findSubject']);
    Route::post('mark-ledger/student-html',                                        ['as' => '.mark-ledger.student-html',                                                                                        'uses' => 'ExamMarkLedgerController@studentHtmlRow']);

});

Route::group(['prefix' => 'mcq/',                                      'as' => 'mcq.',                                         'namespace' => 'McqExam\\'], function () {

    Route::group(['prefix' => 'question/',                                      'as' => 'question.',                                         'namespace' => 'Question\\'], function () {

        Route::get('index',                     ['as' => 'index',                  'middleware' => ['ability:super-admin,question-index'],                 'uses' => 'QuestionController@index']);
        Route::get('add',                       ['as' => 'add',                    'middleware' => ['ability:super-admin,question-add'],                    'uses' => 'QuestionController@add']);
        Route::post('store',                    ['as' => 'store',                  'middleware' => ['ability:super-admin,question-add'],                    'uses' => 'QuestionController@store']);
        Route::get('{id}/view',                 ['as' => 'view',                    'middleware' => ['ability:super-admin,question-view'],                   'uses' => 'QuestionController@view']);
        Route::get('{id}/edit',                 ['as' => 'edit',                    'middleware' => ['ability:super-admin,question-edit'],                   'uses' => 'QuestionController@edit']);
        Route::post('{id}/update',              ['as' => 'update',                  'middleware' => ['ability:super-admin,question-edit'],                   'uses' => 'QuestionController@update']);
        Route::get('{id}/delete',               ['as' => 'delete',                  'middleware' => ['ability:super-admin,question-delete'],                 'uses' => 'QuestionController@delete']);
        Route::post('bulk-action',              ['as' => 'bulk-action',             'middleware' => ['ability:super-admin,question-bulk-action'],            'uses' => 'QuestionController@bulkAction']);
        Route::get('{id}/active',               ['as' => 'active',                  'middleware' => ['ability:super-admin,question-active'],                 'uses' => 'QuestionController@Active']);
        Route::get('{id}/in-active',            ['as' => 'in-active',               'middleware' => ['ability:super-admin,question-in-active'],              'uses' => 'QuestionController@inActive']);

        Route::get('import',                      ['as' => 'import',               'middleware' => ['ability:super-admin,question-add'],                   'uses' => 'QuestionController@importQuestion']);
        Route::post('import',                     ['as' => 'bulk.import',          'middleware' => ['ability:super-admin,question-add'],                    'uses' => 'QuestionController@handleImportQuestion']);

        //Custom options
        Route::post('option-html',              ['as' => 'option-html',             'middleware' => ['ability:super-admin,option-add'],               'uses' => 'QuestionController@optionHtml']);
        Route::get('option/{id}/delete',        ['as' => 'option.delete',           'middleware' => ['ability:super-admin,option-delete'],            'uses' => 'QuestionController@optionDelete']);
        Route::get('option/{id}/active',        ['as' => 'option.active',           'middleware' => ['ability:super-admin,option-active'],            'uses' => 'QuestionController@optionActive']);
        Route::get('option/{id}/in-active',     ['as' => 'option.in-active',        'middleware' => ['ability:super-admin,option-in-active'],         'uses' => 'QuestionController@optionInActive']);

        /*Route::post('find-category',            ['as' => '.find-category',                                                                                   'uses' => 'QuestionController@findCategory']);


        Route::post('product-detail-html',         ['as' => '.product-detail-html',                                                                         'uses' => 'QuestionController@productInfo']);
        Route::get('product-name-autocomplete',    ['as' => '.product-name-autocomplete',                                                                   'uses' => 'QuestionController@productNameAutocomplete']);
        */


        /*Question Level Routes*/
        Route::get('question-level',                    ['as' => 'question-level',                  'middleware' => ['ability:super-admin,question-level-index'],               'uses' => 'QuestionLevelController@index']);
        Route::post('question-level/store',             ['as' => 'question-level.store',            'middleware' => ['ability:super-admin,question-level-add'],                 'uses' => 'QuestionLevelController@store']);
        Route::get('question-level/{id}/edit',          ['as' => 'question-level.edit',             'middleware' => ['ability:super-admin,question-level-edit'],                'uses' => 'QuestionLevelController@edit']);
        Route::post('question-level/{id}/update',       ['as' => 'question-level.update',           'middleware' => ['ability:super-admin,question-level-edit'],                'uses' => 'QuestionLevelController@update']);
        Route::get('question-level/{id}/delete',        ['as' => 'question-level.delete',           'middleware' => ['ability:super-admin,question-level-delete'],              'uses' => 'QuestionLevelController@delete']);
        Route::get('question-level/{id}/active',        ['as' => 'question-level.active',           'middleware' => ['ability:super-admin,question-level-active'],              'uses' => 'QuestionLevelController@Active']);
        Route::get('question-level/{id}/in-active',     ['as' => 'question-level.in-active',        'middleware' => ['ability:super-admin,question-level-in-active'],           'uses' => 'QuestionLevelController@inActive']);
        Route::post('question-level/bulk-action',       ['as' => 'question-level.bulk-action',      'middleware' => ['ability:super-admin,question-level-bulk-action'],         'uses' => 'QuestionLevelController@bulkAction']);

        /*Question Groups Routes*/
        Route::get('question-group',                    ['as' => 'question-group',                  'middleware' => ['ability:super-admin,question-group-index'],               'uses' => 'QuestionGroupController@index']);
        Route::post('question-group/store',             ['as' => 'question-group.store',            'middleware' => ['ability:super-admin,question-group-add'],                 'uses' => 'QuestionGroupController@store']);
        Route::get('question-group/{id}/edit',          ['as' => 'question-group.edit',             'middleware' => ['ability:super-admin,question-group-edit'],                'uses' => 'QuestionGroupController@edit']);
        Route::post('question-group/{id}/update',       ['as' => 'question-group.update',           'middleware' => ['ability:super-admin,question-group-edit'],                'uses' => 'QuestionGroupController@update']);
        Route::get('question-group/{id}/delete',        ['as' => 'question-group.delete',           'middleware' => ['ability:super-admin,question-group-delete'],              'uses' => 'QuestionGroupController@delete']);
        Route::get('question-group/{id}/active',        ['as' => 'question-group.active',           'middleware' => ['ability:super-admin,question-group-active'],              'uses' => 'QuestionGroupController@Active']);
        Route::get('question-group/{id}/in-active',     ['as' => 'question-group.in-active',        'middleware' => ['ability:super-admin,question-group-in-active'],           'uses' => 'QuestionGroupController@inActive']);
        Route::post('question-group/bulk-action',       ['as' => 'question-group.bulk-action',      'middleware' => ['ability:super-admin,question-group-bulk-action'],         'uses' => 'QuestionGroupController@bulkAction']);

    });

    Route::group(['prefix' => 'exam/',                                      'as' => 'exam.',                                         'namespace' => 'Exam\\'], function () {

        Route::get('index',                     ['as' => 'index',                  'middleware' => ['ability:super-admin,exam-index'],                 'uses' => 'ExamController@index']);
        Route::get('add',                       ['as' => 'add',                    'middleware' => ['ability:super-admin,exam-add'],                    'uses' => 'ExamController@add']);
        Route::post('store',                    ['as' => 'store',                  'middleware' => ['ability:super-admin,exam-add'],                    'uses' => 'ExamController@store']);
        Route::get('{id}/view',                 ['as' => 'view',                    'middleware' => ['ability:super-admin,exam-view'],                   'uses' => 'ExamController@view']);
        Route::get('{id}/edit',                 ['as' => 'edit',                    'middleware' => ['ability:super-admin,exam-edit'],                   'uses' => 'ExamController@edit']);
        Route::post('{id}/update',              ['as' => 'update',                  'middleware' => ['ability:super-admin,exam-edit'],                   'uses' => 'ExamController@update']);
        Route::get('{id}/delete',               ['as' => 'delete',                  'middleware' => ['ability:super-admin,exam-delete'],                 'uses' => 'ExamController@delete']);
        Route::post('bulk-action',              ['as' => 'bulk-action',             'middleware' => ['ability:super-admin,exam-bulk-action'],            'uses' => 'ExamController@bulkAction']);
        Route::get('{id}/active',               ['as' => 'active',                  'middleware' => ['ability:super-admin,exam-active'],                 'uses' => 'ExamController@Active']);
        Route::get('{id}/in-actie',            ['as' => 'in-active',               'middleware' => ['ability:super-admin,exam-in-active'],              'uses' => 'ExamController@inActive']);

        Route::post('find-subject',         ['as' => 'find-subject',                                                               'uses' => 'ExamController@findSubject']);



        /*Question Level Routes*/
        Route::get('exam-instruction',                    ['as' => 'exam-instruction',                  'middleware' => ['ability:super-admin,exam-instruction-index'],               'uses' => 'ExamInstructionController@index']);
        Route::post('exam-instruction/store',             ['as' => 'exam-instruction.store',            'middleware' => ['ability:super-admin,exam-instruction-add'],                 'uses' => 'ExamInstructionController@store']);
        Route::get('exam-instruction/{id}/edit',          ['as' => 'exam-instruction.edit',             'middleware' => ['ability:super-admin,exam-instruction-edit'],                'uses' => 'ExamInstructionController@edit']);
        Route::post('exam-instruction/{id}/update',       ['as' => 'exam-instruction.update',           'middleware' => ['ability:super-admin,exam-instruction-edit'],                'uses' => 'ExamInstructionController@update']);
        Route::get('exam-instruction/{id}/delete',        ['as' => 'exam-instruction.delete',           'middleware' => ['ability:super-admin,exam-instruction-delete'],              'uses' => 'ExamInstructionController@delete']);
        Route::get('exam-instruction/{id}/active',        ['as' => 'exam-instruction.active',           'middleware' => ['ability:super-admin,exam-instruction-active'],              'uses' => 'ExamInstructionController@Active']);
        Route::get('exam-instruction/{id}/in-active',     ['as' => 'exam-instruction.in-active',        'middleware' => ['ability:super-admin,exam-instruction-in-active'],           'uses' => 'ExamInstructionController@inActive']);
        Route::post('exam-instruction/bulk-action',       ['as' => 'exam-instruction.bulk-action',      'middleware' => ['ability:super-admin,exam-instruction-bulk-action'],         'uses' => 'ExamInstructionController@bulkAction']);


    });


    /*Exam Types Routes*/
   /* Route::get('',                                  ['as' => '',                  'middleware' => ['ability:super-admin,exam-index'],           'uses' => 'ExamController@index']);
    Route::post('store',                            ['as' => '.store',            'middleware' => ['ability:super-admin,exam-add'],             'uses' => 'ExamController@store']);
    Route::get('{id}/edit',                         ['as' => '.edit',             'middleware' => ['ability:super-admin,exam-edit'],            'uses' => 'ExamController@edit']);
    Route::post('{id}/update',                      ['as' => '.update',           'middleware' => ['ability:super-admin,exam-edit'],            'uses' => 'ExamController@update']);
    Route::get('{id}/delete',                       ['as' => '.delete',           'middleware' => ['ability:super-admin,exam-delete'],          'uses' => 'ExamController@delete']);
    Route::get('{id}/active',                       ['as' => '.active',           'middleware' => ['ability:super-admin,exam-active'],          'uses' => 'ExamController@Active']);
    Route::get('{id}/in-active',                    ['as' => '.in-active',        'middleware' => ['ability:super-admin,exam-in-active'],       'uses' => 'ExamController@inActive']);
    Route::post('bulk-action',                      ['as' => '.bulk-action',      'middleware' => ['ability:super-admin,exam-bulk-action'],     'uses' => 'ExamController@bulkAction']);*/



});

/*Certificate Grouping*/
Route::group(['prefix' => 'certificate/',                                   'as' => 'certificate.',                                    'namespace' => 'Certificate\\'], function () {

    //Issue Certificate
    Route::get('issue',                                 ['as' => 'issue',                  'middleware' => ['ability:super-admin,issue-certificate'],           'uses' => 'CertificateController@issue']);
    Route::post('student-detail-html',                  ['as' => 'student-detail-html',                                                                         'uses' => 'CertificateController@studentDetail']);

    Route::get('issue-history',            ['as' => 'issue-history',             'middleware' => ['ability:super-admin,certificate-history'],                              'uses' => 'CertificateController@history']);

    //Id Card
    // Route::get('id-card',                          ['as' => 'id-card',                'middleware' => ['ability:super-admin,id-card-index'],           'uses' => 'IdCardController@index']);
    // Route::get('id-card/{id}/print',               ['as' => 'id-card.print',          'middleware' => ['ability:super-admin,id-card-print'],           'uses' => 'IdCardController@print']);
    // Route::post('id-card/bulk-print',              ['as' => 'id-card.bulk-print',     'middleware' => ['ability:super-admin,id-card-bulk-print'],      'uses' => 'IdCardController@bulkPrint']);

    //attendance certificate
    Route::get('attendance',                          ['as' => 'attendance',                'middleware' => ['ability:super-admin,attendance-certificate-index'],           'uses' => 'AttendanceCertificateController@index']);
    Route::get('attendance/{id}/add',                 ['as' => 'attendance.add',            'middleware' => ['ability:super-admin,attendance-certificate-add'],             'uses' => 'AttendanceCertificateController@add']);
    Route::post('attendance/store',                   ['as' => 'attendance.store',          'middleware' => ['ability:super-admin,attendance-certificate-add'],             'uses' => 'AttendanceCertificateController@store']);
    Route::get('attendance/{id}/edit',                ['as' => 'attendance.edit',           'middleware' => ['ability:super-admin,attendance-certificate-edit'],            'uses' => 'AttendanceCertificateController@edit']);
    Route::post('attendance/{id}/update',             ['as' => 'attendance.update',         'middleware' => ['ability:super-admin,attendance-certificate-edit'],            'uses' => 'AttendanceCertificateController@update']);
    Route::get('attendance/{id}/view',                ['as' => 'attendance.view',           'middleware' => ['ability:super-admin,attendance-certificate-view'],            'uses' => 'AttendanceCertificateController@view']);
    Route::get('attendance/{id}/delete',              ['as' => 'attendance.delete',         'middleware' => ['ability:super-admin,attendance-certificate-delete'],          'uses' => 'AttendanceCertificateController@delete']);
    Route::post('attendance/bulk-action',             ['as' => 'attendance.bulk-action',    'middleware' => ['ability:super-admin,attendance-certificate-bulk-action'],     'uses' => 'AttendanceCertificateController@bulkAction']);
    Route::get('attendance/{id}/print',               ['as' => 'attendance.print',          'middleware' => ['ability:super-admin,attendance-certificate-print'],            'uses' => 'AttendanceCertificateController@print']);
    Route::post('attendance/bulk-print',              ['as' => 'attendance.bulk-print',     'middleware' => ['ability:super-admin,attendance-certificate-bulk-print'],        'uses' => 'AttendanceCertificateController@bulkPrint']);

    //transfer certificate
    Route::get('transfer',                          ['as' => 'transfer',                'middleware' => ['ability:super-admin,transfer-certificate-index'],           'uses' => 'TransferCertificateController@index']);
    Route::get('transfer/{id}/add',                 ['as' => 'transfer.add',            'middleware' => ['ability:super-admin,transfer-certificate-add'],             'uses' => 'TransferCertificateController@add']);
    Route::post('transfer/store',                   ['as' => 'transfer.store',          'middleware' => ['ability:super-admin,transfer-certificate-add'],             'uses' => 'TransferCertificateController@store']);
    Route::get('transfer/{id}/edit',                ['as' => 'transfer.edit',           'middleware' => ['ability:super-admin,transfer-certificate-edit'],            'uses' => 'TransferCertificateController@edit']);
    Route::post('transfer/{id}/update',             ['as' => 'transfer.update',         'middleware' => ['ability:super-admin,transfer-certificate-edit'],            'uses' => 'TransferCertificateController@update']);
    Route::get('transfer/{id}/view',                ['as' => 'transfer.view',           'middleware' => ['ability:super-admin,transfer-certificate-view'],            'uses' => 'TransferCertificateController@view']);
    Route::get('transfer/{id}/delete',              ['as' => 'transfer.delete',         'middleware' => ['ability:super-admin,transfer-certificate-delete'],          'uses' => 'TransferCertificateController@delete']);
    Route::post('transfer/bulk-action',             ['as' => 'transfer.bulk-action',    'middleware' => ['ability:super-admin,transfer-certificate-bulk-action'],     'uses' => 'TransferCertificateController@bulkAction']);
    Route::get('transfer/{id}/{template}/print',               ['as' => 'transfer.print',          'middleware' => ['ability:super-admin,transfer-certificate-print'],            'uses' => 'TransferCertificateController@print']);
    Route::post('transfer/bulk-print',              ['as' => 'transfer.bulk-print',     'middleware' => ['ability:super-admin,transfer-certificate-bulk-print'],        'uses' => 'TransferCertificateController@bulkPrint']);

    //Character certificate
    Route::get('character',                          ['as' => 'character',                'middleware' => ['ability:super-admin,character-certificate-index'],           'uses' => 'CharacterCertificateController@index']);
    Route::get('character/{id}/add',                 ['as' => 'character.add',            'middleware' => ['ability:super-admin,character-certificate-add'],             'uses' => 'CharacterCertificateController@add']);
    Route::post('character/store',                   ['as' => 'character.store',          'middleware' => ['ability:super-admin,character-certificate-add'],             'uses' => 'CharacterCertificateController@store']);
    Route::get('character/{id}/edit',                ['as' => 'character.edit',           'middleware' => ['ability:super-admin,character-certificate-edit'],            'uses' => 'CharacterCertificateController@edit']);
    Route::post('character/{id}/update',             ['as' => 'character.update',         'middleware' => ['ability:super-admin,character-certificate-edit'],            'uses' => 'CharacterCertificateController@update']);
    Route::get('character/{id}/view',                ['as' => 'character.view',           'middleware' => ['ability:super-admin,character-certificate-view'],            'uses' => 'CharacterCertificateController@view']);
    Route::get('character/{id}/delete',              ['as' => 'character.delete',         'middleware' => ['ability:super-admin,character-certificate-delete'],          'uses' => 'CharacterCertificateController@delete']);
    Route::post('character/bulk-action',             ['as' => 'character.bulk-action',    'middleware' => ['ability:super-admin,character-certificate-bulk-action'],     'uses' => 'CharacterCertificateController@bulkAction']);
    Route::get('character/{id}/print',               ['as' => 'character.print',          'middleware' => ['ability:super-admin,character-certificate-print'],            'uses' => 'CharacterCertificateController@print']);
    Route::post('character/bulk-print',              ['as' => 'character.bulk-print',     'middleware' => ['ability:super-admin,character-certificate-bulk-print'],        'uses' => 'CharacterCertificateController@bulkPrint']);

    //Provisional certificate
    Route::get('provisional',                          ['as' => 'provisional',                'middleware' => ['ability:super-admin,provisional-certificate-index'],           'uses' => 'ProvisionalCertificateController@index']);
    Route::get('provisional/{id}/add',                 ['as' => 'provisional.add',            'middleware' => ['ability:super-admin,provisional-certificate-add'],             'uses' => 'ProvisionalCertificateController@add']);
    Route::post('provisional/store',                   ['as' => 'provisional.store',          'middleware' => ['ability:super-admin,provisional-certificate-add'],             'uses' => 'ProvisionalCertificateController@store']);
    Route::get('provisional/{id}/edit',                ['as' => 'provisional.edit',           'middleware' => ['ability:super-admin,provisional-certificate-edit'],            'uses' => 'ProvisionalCertificateController@edit']);
    Route::post('provisional/{id}/update',             ['as' => 'provisional.update',         'middleware' => ['ability:super-admin,provisional-certificate-edit'],            'uses' => 'ProvisionalCertificateController@update']);
    Route::get('provisional/{id}/view',                ['as' => 'provisional.view',           'middleware' => ['ability:super-admin,provisional-certificate-view'],            'uses' => 'ProvisionalCertificateController@view']);
    Route::get('provisional/{id}/delete',              ['as' => 'provisional.delete',         'middleware' => ['ability:super-admin,provisional-certificate-delete'],          'uses' => 'ProvisionalCertificateController@delete']);
    Route::post('provisional/bulk-action',             ['as' => 'provisional.bulk-action',    'middleware' => ['ability:super-admin,provisional-certificate-bulk-action'],     'uses' => 'ProvisionalCertificateController@bulkAction']);
    Route::get('provisional/{id}/print',               ['as' => 'provisional.print',          'middleware' => ['ability:super-admin,provisional-certificate-print'],            'uses' => 'ProvisionalCertificateController@print']);
    Route::post('provisional/bulk-print',              ['as' => 'provisional.bulk-print',     'middleware' => ['ability:super-admin,provisional-certificate-bulk-print'],        'uses' => 'ProvisionalCertificateController@bulkPrint']);

    //Testimonial certificate
    Route::get('testimonial',                          ['as' => 'testimonial',                'middleware' => ['ability:super-admin,testimonial-certificate-index'],           'uses' => 'TestimonialCertificateController@index']);
    Route::get('testimonial/{id}/add',                 ['as' => 'testimonial.add',            'middleware' => ['ability:super-admin,testimonial-certificate-add'],             'uses' => 'TestimonialCertificateController@add']);
    Route::post('testimonial/store',                   ['as' => 'testimonial.store',          'middleware' => ['ability:super-admin,testimonial-certificate-add'],             'uses' => 'TestimonialCertificateController@store']);
    Route::get('testimonial/{id}/edit',                ['as' => 'testimonial.edit',           'middleware' => ['ability:super-admin,testimonial-certificate-edit'],            'uses' => 'TestimonialCertificateController@edit']);
    Route::post('testimonial/{id}/update',             ['as' => 'testimonial.update',         'middleware' => ['ability:super-admin,testimonial-certificate-edit'],            'uses' => 'TestimonialCertificateController@update']);
    Route::get('testimonial/{id}/view',                ['as' => 'testimonial.view',           'middleware' => ['ability:super-admin,testimonial-certificate-view'],            'uses' => 'TestimonialCertificateController@view']);
    Route::get('testimonial/{id}/delete',              ['as' => 'testimonial.delete',         'middleware' => ['ability:super-admin,testimonial-certificate-delete'],          'uses' => 'TestimonialCertificateController@delete']);
    Route::post('testimonial/bulk-action',             ['as' => 'testimonial.bulk-action',    'middleware' => ['ability:super-admin,testimonial-certificate-bulk-action'],     'uses' => 'TestimonialCertificateController@bulkAction']);
    Route::get('testimonial/{id}/print',               ['as' => 'testimonial.print',          'middleware' => ['ability:super-admin,testimonial-certificate-print'],            'uses' => 'TestimonialCertificateController@print']);
    Route::post('testimonial/bulk-print',              ['as' => 'testimonial.bulk-print',     'middleware' => ['ability:super-admin,testimonial-certificate-bulk-print'],        'uses' => 'TestimonialCertificateController@bulkPrint']);

    //Medium of Instruction certificate
    Route::get('moi',                          ['as' => 'moi',                'middleware' => ['ability:super-admin,moi-certificate-index'],           'uses' => 'MOICertificateController@index']);
    Route::get('moi/{id}/add',                 ['as' => 'moi.add',            'middleware' => ['ability:super-admin,moi-certificate-add'],             'uses' => 'MOICertificateController@add']);
    Route::post('moi/store',                   ['as' => 'moi.store',          'middleware' => ['ability:super-admin,moi-certificate-add'],             'uses' => 'MOICertificateController@store']);
    Route::get('moi/{id}/edit',                ['as' => 'moi.edit',           'middleware' => ['ability:super-admin,moi-certificate-edit'],            'uses' => 'MOICertificateController@edit']);
    Route::post('moi/{id}/update',             ['as' => 'moi.update',         'middleware' => ['ability:super-admin,moi-certificate-edit'],            'uses' => 'MOICertificateController@update']);
    Route::get('moi/{id}/view',                ['as' => 'moi.view',           'middleware' => ['ability:super-admin,moi-certificate-view'],            'uses' => 'MOICertificateController@view']);
    Route::get('moi/{id}/delete',              ['as' => 'moi.delete',         'middleware' => ['ability:super-admin,moi-certificate-delete'],          'uses' => 'MOICertificateController@delete']);
    Route::post('moi/bulk-action',             ['as' => 'moi.bulk-action',    'middleware' => ['ability:super-admin,moi-certificate-bulk-action'],     'uses' => 'MOICertificateController@bulkAction']);
    Route::get('moi/{id}/print',               ['as' => 'moi.print',          'middleware' => ['ability:super-admin,moi-certificate-print'],            'uses' => 'MOICertificateController@print']);
    Route::post('moi/bulk-print',              ['as' => 'moi.bulk-print',     'middleware' => ['ability:super-admin,moi-certificate-bulk-print'],        'uses' => 'MOICertificateController@bulkPrint']);

    //Transcript certificate
    Route::get('transcript',                          ['as' => 'transcript',                'middleware' => ['ability:super-admin,transcript-certificate-index'],           'uses' => 'TranscriptCertificateController@index']);
    Route::get('transcript/{id}/add',                 ['as' => 'transcript.add',            'middleware' => ['ability:super-admin,transcript-certificate-add'],             'uses' => 'TranscriptCertificateController@add']);
    Route::post('transcript/store',                   ['as' => 'transcript.store',          'middleware' => ['ability:super-admin,transcript-certificate-add'],             'uses' => 'TranscriptCertificateController@store']);
    Route::get('transcript/{id}/edit',                ['as' => 'transcript.edit',           'middleware' => ['ability:super-admin,transcript-certificate-edit'],            'uses' => 'TranscriptCertificateController@edit']);
    Route::post('transcript/{id}/update',             ['as' => 'transcript.update',         'middleware' => ['ability:super-admin,transcript-certificate-edit'],            'uses' => 'TranscriptCertificateController@update']);
    Route::get('transcript/{id}/view',                ['as' => 'transcript.view',           'middleware' => ['ability:super-admin,transcript-certificate-view'],            'uses' => 'TranscriptCertificateController@view']);
    Route::get('transcript/{id}/delete',              ['as' => 'transcript.delete',         'middleware' => ['ability:super-admin,transcript-certificate-delete'],          'uses' => 'TranscriptCertificateController@delete']);
    Route::post('transcript/bulk-action',             ['as' => 'transcript.bulk-action',    'middleware' => ['ability:super-admin,transcript-certificate-bulk-action'],     'uses' => 'TranscriptCertificateController@bulkAction']);
    //Route::get('transcript/{id}/print',               ['as' => 'transcript.print',          'middleware' => ['ability:super-admin,transcript-certificate-print'],            'uses' => 'TranscriptCertificateController@print']);
    Route::get('transcript/{id}/{template}/print',     ['as' => 'transcript.print',          'middleware' => ['ability:super-admin,transcript-certificate-print'],            'uses' => 'TranscriptCertificateController@print']);
    Route::post('transcript/bulk-print',              ['as' => 'transcript.bulk-print',     'middleware' => ['ability:super-admin,transcript-certificate-bulk-print'],        'uses' => 'TranscriptCertificateController@bulkPrint']);


    //bonafied certificate
    Route::get('bonafide',                          ['as' => 'bonafide',                'middleware' => ['ability:super-admin,bonafide-certificate-index'],           'uses' => 'BonafideCertificateController@index']);
    Route::get('bonafide/{id}/add',                 ['as' => 'bonafide.add',            'middleware' => ['ability:super-admin,bonafide-certificate-add'],             'uses' => 'BonafideCertificateController@add']);
    Route::post('bonafide/store',                   ['as' => 'bonafide.store',          'middleware' => ['ability:super-admin,bonafide-certificate-add'],             'uses' => 'BonafideCertificateController@store']);
    Route::get('bonafide/{id}/edit',                ['as' => 'bonafide.edit',           'middleware' => ['ability:super-admin,bonafide-certificate-edit'],            'uses' => 'BonafideCertificateController@edit']);
    Route::post('bonafide/{id}/update',             ['as' => 'bonafide.update',         'middleware' => ['ability:super-admin,bonafide-certificate-edit'],            'uses' => 'BonafideCertificateController@update']);
    Route::get('bonafide/{id}/view',                ['as' => 'bonafide.view',           'middleware' => ['ability:super-admin,bonafide-certificate-view'],            'uses' => 'BonafideCertificateController@view']);
    Route::get('bonafide/{id}/delete',              ['as' => 'bonafide.delete',         'middleware' => ['ability:super-admin,bonafide-certificate-delete'],          'uses' => 'BonafideCertificateController@delete']);
    Route::post('bonafide/bulk-action',             ['as' => 'bonafide.bulk-action',    'middleware' => ['ability:super-admin,bonafide-certificate-bulk-action'],     'uses' => 'BonafideCertificateController@bulkAction']);
    Route::get('bonafide/{id}/print',               ['as' => 'bonafide.print',          'middleware' => ['ability:super-admin,bonafide-certificate-print'],             'uses' => 'BonafideCertificateController@print']);
    Route::post('bonafide/bulk-print',              ['as' => 'bonafide.bulk-print',     'middleware' => ['ability:super-admin,bonafide-certificate-bulk-print'],         'uses' => 'BonafideCertificateController@bulkPrint']);

    //transfer certificate
    Route::get('course-completion',                          ['as' => 'course-completion',                'middleware' => ['ability:super-admin,course-completion-certificate-index'],           'uses' => 'CourseCompletionCertificateController@index']);
    Route::get('course-completion/{id}/add',                 ['as' => 'course-completion.add',            'middleware' => ['ability:super-admin,course-completion-certificate-add'],             'uses' => 'CourseCompletionCertificateController@add']);
    Route::post('course-completion/store',                   ['as' => 'course-completion.store',          'middleware' => ['ability:super-admin,course-completion-certificate-add'],             'uses' => 'CourseCompletionCertificateController@store']);
    Route::get('course-completion/{id}/edit',                ['as' => 'course-completion.edit',           'middleware' => ['ability:super-admin,course-completion-certificate-edit'],            'uses' => 'CourseCompletionCertificateController@edit']);
    Route::post('course-completion/{id}/update',             ['as' => 'course-completion.update',         'middleware' => ['ability:super-admin,course-completion-certificate-edit'],            'uses' => 'CourseCompletionCertificateController@update']);
    Route::get('course-completion/{id}/view',                ['as' => 'course-completion.view',           'middleware' => ['ability:super-admin,course-completion-certificate-view'],            'uses' => 'CourseCompletionCertificateController@view']);
    Route::get('course-completion/{id}/delete',              ['as' => 'course-completion.delete',         'middleware' => ['ability:super-admin,course-completion-certificate-delete'],          'uses' => 'CourseCompletionCertificateController@delete']);
    Route::post('course-completion/bulk-action',             ['as' => 'course-completion.bulk-action',    'middleware' => ['ability:super-admin,course-completion-certificate-bulk-action'],     'uses' => 'CourseCompletionCertificateController@bulkAction']);
    Route::get('course-completion/{id}/print',               ['as' => 'course-completion.print',          'middleware' => ['ability:super-admin,course-completion-certificate-print'],           'uses' => 'CourseCompletionCertificateController@print']);
    Route::post('course-completion/bulk-print',              ['as' => 'course-completion.bulk-print',     'middleware' => ['ability:super-admin,course-completion-certificate-bulk-print'],      'uses' => 'CourseCompletionCertificateController@bulkPrint']);


    //Nirgam Utara certificate
    Route::get('nirgam-utara',                          ['as' => 'nirgam-utara',                'middleware' => ['ability:super-admin,nirgam-utara-certificate-index'],           'uses' => 'NirgamUtaraCertificateController@index']);
    Route::get('nirgam-utara/{id}/add',                 ['as' => 'nirgam-utara.add',            'middleware' => ['ability:super-admin,nirgam-utara-certificate-add'],             'uses' => 'NirgamUtaraCertificateController@add']);
    Route::post('nirgam-utara/store',                   ['as' => 'nirgam-utara.store',          'middleware' => ['ability:super-admin,nirgam-utara-certificate-add'],             'uses' => 'NirgamUtaraCertificateController@store']);
    Route::get('nirgam-utara/{id}/edit',                ['as' => 'nirgam-utara.edit',           'middleware' => ['ability:super-admin,nirgam-utara-certificate-edit'],            'uses' => 'NirgamUtaraCertificateController@edit']);
    Route::post('nirgam-utara/{id}/update',             ['as' => 'nirgam-utara.update',         'middleware' => ['ability:super-admin,nirgam-utara-certificate-edit'],            'uses' => 'NirgamUtaraCertificateController@update']);
    Route::get('nirgam-utara/{id}/view',                ['as' => 'nirgam-utara.view',           'middleware' => ['ability:super-admin,nirgam-utara-certificate-view'],            'uses' => 'NirgamUtaraCertificateController@view']);
    Route::get('nirgam-utara/{id}/delete',              ['as' => 'nirgam-utara.delete',         'middleware' => ['ability:super-admin,nirgam-utara-certificate-delete'],          'uses' => 'NirgamUtaraCertificateController@delete']);
    Route::post('nirgam-utara/bulk-action',             ['as' => 'nirgam-utara.bulk-action',    'middleware' => ['ability:super-admin,nirgam-utara-certificate-bulk-action'],     'uses' => 'NirgamUtaraCertificateController@bulkAction']);
    Route::get('nirgam-utara/{id}/{template}/print',               ['as' => 'nirgam-utara.print',          'middleware' => ['ability:super-admin,nirgam-utara-certificate-print'], 'uses' => 'NirgamUtaraCertificateController@print']);
    Route::post('nirgam-utara/bulk-print',              ['as' => 'nirgam-utara.bulk-print',     'middleware' => ['ability:super-admin,nirgam-utara-certificate-bulk-print'],        'uses' => 'NirgamUtaraCertificateController@bulkPrint']);


    //gemerate custom certificate
    Route::get('generate',                          ['as' => 'generate',                  'middleware' => ['ability:super-admin,certificate-generate'],             'uses' => 'CertificateController@generate']);

    /*Custom Certificate Master*/
    Route::get('template',                          ['as' => 'template',                'middleware' => ['ability:super-admin,certificate-template-index'],         'uses' => 'TemplateController@index']);
    Route::get('template/add',                      ['as' => 'template.add',            'middleware' => ['ability:super-admin,certificate-template-add'],           'uses' => 'TemplateController@add']);
    Route::post('template/store',                   ['as' => 'template.store',          'middleware' => ['ability:super-admin,certificate-template-add'],           'uses' => 'TemplateController@store']);
    Route::get('template/{id}/edit',                ['as' => 'template.edit',           'middleware' => ['ability:super-admin,certificate-template-edit'],          'uses' => 'TemplateController@edit']);
    Route::post('template/{id}/update',             ['as' => 'template.update',         'middleware' => ['ability:super-admin,certificate-template-edit'],          'uses' => 'TemplateController@update']);
    Route::get('template/{id}/view',                ['as' => 'template.view',          'middleware' => ['ability:super-admin,certificate-template-view'],           'uses' => 'TemplateController@view']);
    Route::get('template/{id}/delete',              ['as' => 'template.delete',          'middleware' => ['ability:super-admin,certificate-template-delete'],          'uses' => 'TemplateController@delete']);
    Route::get('template/{id}/active',              ['as' => 'template.active',          'middleware' => ['ability:super-admin,certificate-template-active'],          'uses' => 'TemplateController@Active']);
    Route::get('template/{id}/in-active',           ['as' => 'template.in-active',       'middleware' => ['ability:super-admin,certificate-template-in-active'],       'uses' => 'TemplateController@inActive']);
    Route::post('template/bulk-action',                      ['as' => 'template.bulk-action',     'middleware' => ['ability:super-admin,certificate-template-bulk-action'],     'uses' => 'TemplateController@bulkAction']);

});

/*Hostel Grouping */
Route::group(['prefix' => 'hostel/',                                    'as' => 'hostel',                                       'namespace' => 'Hostel\\'], function () {

    /*Hostel Routes*/
    Route::get('',                   ['as' => '',                  'middleware' => ['ability:super-admin,hostel-index'],                'uses' => 'HostelController@index']);
    Route::get('add',                ['as' => '.add',              'middleware' => ['ability:super-admin,hostel-add'],                  'uses' => 'HostelController@add']);
    Route::post('store',             ['as' => '.store',            'middleware' => ['ability:super-admin,hostel-add'],                  'uses' => 'HostelController@store']);
    Route::get('{id}/edit',          ['as' => '.edit',             'middleware' => ['ability:super-admin,hostel-edit'],                 'uses' => 'HostelController@edit']);
    Route::post('{id}/update',       ['as' => '.update',           'middleware' => ['ability:super-admin,hostel-edit'],                 'uses' => 'HostelController@update']);
    Route::get('{id}/view',          ['as' => '.view',             'middleware' => ['ability:super-admin,hostel-view'],                 'uses' => 'HostelController@view']);
    Route::get('{id}/delete',        ['as' => '.delete',           'middleware' => ['ability:super-admin,hostel-delete'],               'uses' => 'HostelController@delete']);
    Route::get('{id}/active',        ['as' => '.active',           'middleware' => ['ability:super-admin,hostel-active'],               'uses' => 'HostelController@Active']);
    Route::get('{id}/in-active',     ['as' => '.in-active',        'middleware' => ['ability:super-admin,hostel-in-active'],            'uses' => 'HostelController@inActive']);
    Route::post('bulk-action',       ['as' => '.bulk-action',      'middleware' => ['ability:super-admin,hostel-bulk-action'],          'uses' => 'HostelController@bulkAction']);
    Route::post('find-rooms',        ['as' => '.find-rooms',                                                                            'uses' => 'HostelController@findRooms']);
    Route::post('find-beds',         ['as' => '.find-beds',                                                                              'uses' => 'HostelController@findBeds']);

    /*Rooms Level*/
    Route::post('room/add',                         ['as' => '.room.add',                   'middleware' => ['ability:super-admin,room-add'],               'uses' => 'RoomController@add']);
    Route::get('room/{id}/view',                    ['as' => '.room.view',                  'middleware' => ['ability:super-admin,room-view'],              'uses' => 'RoomController@view']);
    Route::post('room/{id}/update',                 ['as' => '.room.update',                'middleware' => ['ability:super-admin,room-edit'],              'uses' => 'RoomController@update']);
    Route::get('room/{id}/delete',                  ['as' => '.room.delete',                'middleware' => ['ability:super-admin,room-delete'],            'uses' => 'RoomController@delete']);
    Route::get('room/{id}/active',                  ['as' => '.room.active',                'middleware' => ['ability:super-admin,room-active'],            'uses' => 'RoomController@Active']);
    Route::get('room/{id}/in-active',               ['as' => '.room.in-active',             'middleware' => ['ability:super-admin,room-in-active'],         'uses' => 'RoomController@InActive']);
    Route::post('room/bulk-rooms',                  ['as' => '.room.bulk-rooms',            'middleware' => ['ability:super-admin,room-bulk-action'],       'uses' => 'RoomController@bulkAction']);

    /*Bed*/
    Route::post('bed/add',                          ['as' => '.bed.add',                'middleware' => ['ability:super-admin,bed-add'],                'uses' => 'BedController@addBeds']);
    Route::get('bed/{id}/bed-status/{status}',      ['as' => '.bed.bed-status',         'middleware' => ['ability:super-admin,bed-status'],             'uses' => 'BedController@bedStatus']);
    Route::get('bed/{id}/delete',                   ['as' => '.bed.delete',             'middleware' => ['ability:super-admin,bed-delete'],             'uses' => 'BedController@delete']);
    Route::get('bed/{id}/active',                   ['as' => '.bed.active',             'middleware' => ['ability:super-admin,bed-active'],             'uses' => 'BedController@Active']);
    Route::get('bed/{id}/in-active',                ['as' => '.bed.in-active',          'middleware' => ['ability:super-admin,bed-in-active'],          'uses' => 'BedController@InActive']);
    Route::post('bed/bulk-beds',                    ['as' => '.bed.bulk-beds',          'middleware' => ['ability:super-admin,bed-bulk-action'],        'uses' => 'BedController@bulkAction']);

    /*Room Types Routes*/
    Route::get('room-type',                    ['as' => '.room-type',                  'middleware' => ['ability:super-admin,room-type-index'],             'uses' => 'RoomTypeController@index']);
    Route::post('room-type/store',             ['as' => '.room-type.store',            'middleware' => ['ability:super-admin,room-type-add'],               'uses' => 'RoomTypeController@store']);
    Route::get('room-type/{id}/edit',          ['as' => '.room-type.edit',             'middleware' => ['ability:super-admin,room-type-edit'],              'uses' => 'RoomTypeController@edit']);
    Route::post('room-type/{id}/update',       ['as' => '.room-type.update',           'middleware' => ['ability:super-admin,room-type-edit'],              'uses' => 'RoomTypeController@update']);
    Route::get('room-type/{id}/delete',        ['as' => '.room-type.delete',           'middleware' => ['ability:super-admin,room-type-delete'],            'uses' => 'RoomTypeController@delete']);
    Route::get('room-type/{id}/active',        ['as' => '.room-type.active',           'middleware' => ['ability:super-admin,room-type-active'],            'uses' => 'RoomTypeController@Active']);
    Route::get('room-type/{id}/in-active',     ['as' => '.room-type.in-active',        'middleware' => ['ability:super-admin,room-type-in-active'],         'uses' => 'RoomTypeController@inActive']);
    Route::post('room-type/bulk-action',       ['as' => '.room-type.bulk-action',      'middleware' => ['ability:super-admin,room-type-bulk-action'],       'uses' => 'RoomTypeController@bulkAction']);

    /*Hostel Resident Routes*/
    Route::get('resident',                    ['as' => '.resident',                     'middleware' => ['ability:super-admin,resident-index'],                'uses' => 'ResidentController@index']);
    Route::get('resident/add',                ['as' => '.resident.add',                 'middleware' => ['ability:super-admin,resident-add'],                  'uses' => 'ResidentController@add']);
    Route::post('resident/store',             ['as' => '.resident.store',               'middleware' => ['ability:super-admin,resident-add'],                  'uses' => 'ResidentController@store']);
    Route::get('resident/quick-resident',     ['as' => '.resident.quick-resident',         'middleware' => ['ability:super-admin,resident-add'],               'uses' => 'ResidentController@quickResident']);
    Route::get('resident/{id}/edit',          ['as' => '.resident.edit',                'middleware' => ['ability:super-admin,resident-edit'],                 'uses' => 'ResidentController@edit']);
    Route::post('resident/{id}/update',       ['as' => '.resident.update',              'middleware' => ['ability:super-admin,resident-edit'],                 'uses' => 'ResidentController@update']);
    Route::get('resident/{id}/delete',        ['as' => '.resident.delete',              'middleware' => ['ability:super-admin,resident-delete'],               'uses' => 'ResidentController@delete']);
    Route::post('resident/bulk-action',       ['as' => '.resident.bulk-action',         'middleware' => ['ability:super-admin,resident-bulk-action'],          'uses' => 'ResidentController@bulkAction']);
    Route::post('resident/renew',             ['as' => '.resident.renew',               'middleware' => ['ability:super-admin,resident-renew'],                 'uses' => 'ResidentController@renew']);
    Route::get('resident/{id}/leave',         ['as' => '.resident.leave',               'middleware' => ['ability:super-admin,resident-leave'],                 'uses' => 'ResidentController@leave']);
    Route::post('resident/shift',             ['as' => '.resident.shift',               'middleware' => ['ability:super-admin,resident-shift'],                 'uses' => 'ResidentController@shift']);
    Route::get('resident/history',            ['as' => '.resident.history',             'middleware' => ['ability:super-admin,resident-history'],               'uses' => 'ResidentController@history']);
    /*For Search and Listing Room & Bed*/
    Route::post('resident/find-rooms',        ['as' => '.resident.find-rooms',          'middleware' => ['ability:super-admin,resident'],                       'uses' => 'ResidentController@findRooms']);
    Route::post('resident/find-beds',         ['as' => '.resident.find-beds',           'middleware' => ['ability:super-admin,resident'],                       'uses' => 'ResidentController@findBeds']);

/*Food & Meal*/
    /*Food Schedule Routes*/
    Route::get('food',                          ['as' => '.food',                       'middleware' => ['ability:super-admin,food-index'],                 'uses' => 'FoodController@index']);
    Route::post('food/store',                   ['as' => '.food.store',                 'middleware' => ['ability:super-admin,food-add'],                   'uses' => 'FoodController@store']);
    Route::get('food/{id}/edit',                ['as' => '.food.edit',                  'middleware' => ['ability:super-admin,food-edit'],                  'uses' => 'FoodController@edit']);
    Route::post('food/{id}/update',             ['as' => '.food.update',                'middleware' => ['ability:super-admin,food-edit'],                  'uses' => 'FoodController@update']);
    Route::get('food/{id}/delete',              ['as' => '.food.delete',                'middleware' => ['ability:super-admin,food-delete'],                'uses' => 'FoodController@delete']);
    Route::get('food/{id}/active',              ['as' => '.food.active',                'middleware' => ['ability:super-admin,food-active'],                'uses' => 'FoodController@Active']);
    Route::get('food/{id}/in-active',           ['as' => '.food.in-active',             'middleware' => ['ability:super-admin,food-in-active'],             'uses' => 'FoodController@inActive']);
    Route::post('food/bulk-action',             ['as' => '.food.bulk-action',           'middleware' => ['ability:super-admin,food-bulk-action'],           'uses' => 'FoodController@bulkAction']);
    Route::post('food/food-html',               ['as' => '.food.food-html',                                                                                 'uses' => 'FoodController@foodHtmlRow']);
    Route::get('food-name-autocomplete',        ['as' => '.food-name-autocomplete',                                                                         'uses' => 'FoodController@foodNameAutocomplete']);

    /*Food Category Routes*/
    Route::get('food/category',                    ['as' => '.food.category',                  'middleware' => ['ability:super-admin,food-category-index'],             'uses' => 'FoodCategoryController@index']);
    Route::post('food/category/store',             ['as' => '.food.category.store',            'middleware' => ['ability:super-admin,food-category-add'],               'uses' => 'FoodCategoryController@store']);
    Route::get('food/category/{id}/edit',          ['as' => '.food.category.edit',             'middleware' => ['ability:super-admin,food-category-edit'],              'uses' => 'FoodCategoryController@edit']);
    Route::post('food/category/{id}/update',       ['as' => '.food.category.update',           'middleware' => ['ability:super-admin,food-category-edit'],              'uses' => 'FoodCategoryController@update']);
    Route::get('food/category/{id}/delete',        ['as' => '.food.category.delete',           'middleware' => ['ability:super-admin,food-category-delete'],            'uses' => 'FoodCategoryController@delete']);
    Route::get('food/category/{id}/active',        ['as' => '.food.category.active',           'middleware' => ['ability:super-admin,food-category-active'],            'uses' => 'FoodCategoryController@Active']);
    Route::get('food/category/{id}/in-active',     ['as' => '.food.category.in-active',        'middleware' => ['ability:super-admin,food-category-in-active'],         'uses' => 'FoodCategoryController@inActive']);
    Route::post('food/category/bulk-action',       ['as' => '.food.category.bulk-action',      'middleware' => ['ability:super-admin,food-category-bulk-action'],       'uses' => 'FoodCategoryController@bulkAction']);

    /*Food Item Routes*/
    Route::get('food/item',                    ['as' => '.food.item',                          'middleware' => ['ability:super-admin,food-item-index'],             'uses' => 'FoodItemController@index']);
    Route::post('food/item/store',             ['as' => '.food.item.store',                    'middleware' => ['ability:super-admin,food-item-add'],               'uses' => 'FoodItemController@store']);
    Route::get('food/item/{id}/edit',          ['as' => '.food.item.edit',                     'middleware' => ['ability:super-admin,food-item-edit'],              'uses' => 'FoodItemController@edit']);
    Route::post('food/item/{id}/update',       ['as' => '.food.item.update',                   'middleware' => ['ability:super-admin,food-item-edit'],              'uses' => 'FoodItemController@update']);
    Route::get('food/item/{id}/delete',        ['as' => '.food.item.delete',                   'middleware' => ['ability:super-admin,food-item-delete'],            'uses' => 'FoodItemController@delete']);
    Route::get('food/item/{id}/active',        ['as' => '.food.item.active',                   'middleware' => ['ability:super-admin,food-item-active'],            'uses' => 'FoodItemController@Active']);
    Route::get('food/item/{id}/in-active',     ['as' => '.food.item.in-active',                'middleware' => ['ability:super-admin,food-item-in-active'],         'uses' => 'FoodItemController@inActive']);
    Route::post('food/item/bulk-action',       ['as' => '.food.item.bulk-action',              'middleware' => ['ability:super-admin,food-item-bulk-action'],       'uses' => 'FoodItemController@bulkAction']);

    /*Food Eating Time Routes*/
    Route::get('food/eating-time',                    ['as' => '.food.eating-time',                  'middleware' => ['ability:super-admin,eating-time-index'],             'uses' => 'EatingTimeController@index']);
    Route::post('food/eating-time/store',             ['as' => '.food.eating-time.store',            'middleware' => ['ability:super-admin,eating-time-add'],               'uses' => 'EatingTimeController@store']);
    Route::get('food/eating-time/{id}/edit',          ['as' => '.food.eating-time.edit',             'middleware' => ['ability:super-admin,eating-time-edit'],              'uses' => 'EatingTimeController@edit']);
    Route::post('food/eating-time/{id}/update',       ['as' => '.food.eating-time.update',           'middleware' => ['ability:super-admin,eating-time-edit'],              'uses' => 'EatingTimeController@update']);
    Route::get('food/eating-time/{id}/delete',        ['as' => '.food.eating-time.delete',           'middleware' => ['ability:super-admin,eating-time-delete'],            'uses' => 'EatingTimeController@delete']);
    Route::get('food/eating-time/{id}/active',        ['as' => '.food.eating-time.active',           'middleware' => ['ability:super-admin,eating-time-active'],            'uses' => 'EatingTimeController@Active']);
    Route::get('food/eating-time/{id}/in-active',     ['as' => '.food.eating-time.in-active',        'middleware' => ['ability:super-admin,eating-time-in-acctive'],        'uses' => 'EatingTimeController@inActive']);
    Route::post('food/eating-time/bulk-action',       ['as' => '.food.eating-time.bulk-action',      'middleware' => ['ability:super-admin,eating-time-bulk-action'],       'uses' => 'EatingTimeController@bulkAction']);
});

/*Transport Grouping */
Route::group(['prefix' => 'transport/',                                 'as' => 'transport',                                    'namespace' => 'Transport\\'], function () {

    /*Room Types Routes*/
    Route::get('route',                         ['as' => '.route',                      'middleware' => ['ability:super-admin,transport-route-index'],              'uses' => 'RouteController@index']);
    Route::post('route/store',                  ['as' => '.route.store',                'middleware' => ['ability:super-admin,transport-route-add'],                'uses' => 'RouteController@store']);
    Route::get('route/{id}/edit',               ['as' => '.route.edit',                 'middleware' => ['ability:super-admin,transport-route-edit'],               'uses' => 'RouteController@edit']);
    Route::post('route/{id}/update',            ['as' => '.route.update',               'middleware' => ['ability:super-admin,transport-route-edit'],               'uses' => 'RouteController@update']);
    Route::get('route/{id}/delete',             ['as' => '.route.delete',               'middleware' => ['ability:super-admin,transport-route-delete'],             'uses' => 'RouteController@delete']);
    Route::get('route/{id}/active',             ['as' => '.route.active',               'middleware' => ['ability:super-admin,transport-route-active'],             'uses' => 'RouteController@Active']);
    Route::get('route/{id}/in-active',          ['as' => '.route.in-active',            'middleware' => ['ability:super-admin,transport-route-in-active'],          'uses' => 'RouteController@inActive']);
    Route::post('route/bulk-action',            ['as' => '.route.bulk-action',          'middleware' => ['ability:super-admin,transport-route-bulk-action'],        'uses' => 'RouteController@bulkAction']);
    Route::post('route/vehicle-html',           ['as' => '.route.vehicle-html',                                                                                     'uses' => 'RouteController@vehicleHtmlRow']);
    Route::get('vehicle-autocomplete',          ['as' => '.vehicle-autocomplete',                                                                                   'uses' => 'RouteController@vehicleAutocomplete']);

    /*Room Types Routes*/
    Route::get('vehicle',                       ['as' => '.vehicle',                    'middleware' => ['ability:super-admin,vehicle-index'],                  'uses' => 'VehicleController@index']);
    Route::post('vehicle/store',                ['as' => '.vehicle.store',              'middleware' => ['ability:super-admin,vehicle-add'],                    'uses' => 'VehicleController@store']);
    Route::get('vehicle/{id}/edit',             ['as' => '.vehicle.edit',               'middleware' => ['ability:super-admin,vehicle-edit'],                   'uses' => 'VehicleController@edit']);
    Route::post('vehicle/{id}/update',          ['as' => '.vehicle.update',             'middleware' => ['ability:super-admin,vehicle-edit'],                   'uses' => 'VehicleController@update']);
    Route::get('vehicle/{id}/delete',           ['as' => '.vehicle.delete',             'middleware' => ['ability:super-admin,vehicle-delete'],                 'uses' => 'VehicleController@delete']);
    Route::get('vehicle/{id}/active',           ['as' => '.vehicle.active',             'middleware' => ['ability:super-admin,vehicle-active'],                 'uses' => 'VehicleController@Active']);
    Route::get('vehicle/{id}/in-active',        ['as' => '.vehicle.in-active',          'middleware' => ['ability:super-admin,vehicle-in-active'],              'uses' => 'VehicleController@inActive']);
    Route::post('vehicle/bulk-action',          ['as' => '.vehicle.bulk-action',        'middleware' => ['ability:super-admin,vehicle-bulk-action'],            'uses' => 'VehicleController@bulkAction']);
    Route::post('vehicle/staff-html',           ['as' => '.vehicle.staff-html',                                                                                 'uses' => 'VehicleController@staffHtmlRow']);
    Route::get('staff-autocomplete',            ['as' => '.staff-autocomplete',                                                                                 'uses' => 'VehicleController@staffAutocomplete']);

    /*Travellers Routes*/
    Route::get('user',                    ['as' => '.user',                     'middleware' => ['ability:super-admin,transport-user-index'],                'uses' => 'TransportUserController@index']);
    Route::get('user/add',                ['as' => '.user.add',                 'middleware' => ['ability:super-admin,transport-user-add'],                  'uses' => 'TransportUserController@add']);
    Route::post('user/store',             ['as' => '.user.store',               'middleware' => ['ability:super-admin,transport-user-add'],                  'uses' => 'TransportUserController@store']);
    Route::get('user/quick-register',     ['as' => '.user.quick-register',      'middleware' => ['ability:super-admin,transport-user-add'],                  'uses' => 'TransportUserController@quickRegister']);
    Route::get('user/{id}/edit',          ['as' => '.user.edit',                'middleware' => ['ability:super-admin,transport-user-edit'],                 'uses' => 'TransportUserController@edit']);
    Route::post('user/{id}/update',       ['as' => '.user.update',              'middleware' => ['ability:super-admin,transport-user-edit'],                 'uses' => 'TransportUserController@update']);
    Route::get('user/{id}/delete',        ['as' => '.user.delete',              'middleware' => ['ability:super-admin,transport-user-delete'],               'uses' => 'TransportUserController@delete']);
    Route::post('user/bulk-action',       ['as' => '.user.bulk-action',         'middleware' => ['ability:super-admin,transport-user-bulk-action'],          'uses' => 'TransportUserController@bulkAction']);
    Route::post('user/renew',             ['as' => '.user.renew',               'middleware' => ['ability:super-admin,transport-user-renew'],                 'uses' => 'TransportUserController@renew']);
    Route::get('user/{id}/leave',         ['as' => '.user.leave',               'middleware' => ['ability:super-admin,transport-user-leave'],                 'uses' => 'TransportUserController@leave']);
    Route::post('user/shift',             ['as' => '.user.shift',               'middleware' => ['ability:super-admin,transport-user-shift'],                 'uses' => 'TransportUserController@shift']);
    Route::get('user/history',            ['as' => '.user.history',             'middleware' => ['ability:super-admin,transport-user-history'],               'uses' => 'TransportUserController@history']);
    Route::post('find-vehicles',            ['as' => '.find-vehicles',                                                                            'uses' => 'TransportUserController@findVehicles']);

});

/*Report Grouping*/
Route::group(['prefix' => 'report/',                                    'as' => 'report',                                       'namespace' => 'Report\\'], function () {

    Route::get('student',           ['as' => '.student',            'middleware' => ['ability:super-admin,student-report'],      'uses' => 'StudentReportController@index']);
    Route::get('staff',             ['as' => '.staff',              'middleware' => ['ability:super-admin,staff-report'],        'uses' => 'StaffReportController@index']);
});

/*Info Center Grouping*/
Route::group(['prefix' => 'info/',                                      'as' => 'info.',                                        'namespace' => 'Info\\'], function () {

    /*Notice Board Routes*/
    Route::get('notice',                            ['as' => 'notice',                                  'middleware' => ['ability:super-admin,notice-index'],           'uses' => 'NoticeBoardController@index']);
    Route::get('notice/add',                        ['as' => 'notice.add',                              'middleware' => ['ability:super-admin,notice-add'],             'uses' => 'NoticeBoardController@add']);
    Route::post('notice/store',                     ['as' => 'notice.store',                            'middleware' => ['ability:super-admin,notice-add'],             'uses' => 'NoticeBoardController@store']);
    Route::get('notice/{id}/edit',                  ['as' => 'notice.edit',                             'middleware' => ['ability:super-admin,notice-edit'],            'uses' => 'NoticeBoardController@edit']);
    Route::post('notice/{id}/update',               ['as' => 'notice.update',                           'middleware' => ['ability:super-admin,notice-edit'],            'uses' => 'NoticeBoardController@update']);
    Route::get('notice/{id}/delete',                ['as' => 'notice.delete',                           'middleware' => ['ability:super-admin,notice-delete'],          'uses' => 'NoticeBoardController@delete']);

    /*SMS Email Routes*/
    Route::get('smsemail/birthday-wish',           ['as' => 'smsemail.send',                           /*'middleware' => ['ability:super-admin,sms-email-send'], */                    'uses' => 'SmsEmailController@birthdayWish']);

    Route::get('smsemail',                          ['as' => 'smsemail',                                'middleware' => ['ability:super-admin,sms-email-index'],                     'uses' => 'SmsEmailController@index']);
    Route::get('smsemail/{id}/delete',              ['as' => 'smsemail.delete',                         'middleware' => ['ability:super-admin,sms-email-delete'],                    'uses' => 'SmsEmailController@delete']);
    Route::post('smsemail/bulk-action',             ['as' => 'smsemail.bulk-action',                    'middleware' => ['ability:super-admin,sms-email-bulk-action'],               'uses' => 'SmsEmailController@bulkAction']);

    /*Group*/
    Route::get('smsemail/create',                   ['as' => 'smsemail.create',                         'middleware' => ['ability:super-admin,sms-email-create'],                   'uses' => 'SmsEmailController@create']);
    Route::post('smsemail/send',                    ['as' => 'smsemail.send',                           'middleware' => ['ability:super-admin,sms-email-send'],                     'uses' => 'SmsEmailController@send']);

    /*StudentGuardian*/
    Route::get('smsemail/student-guardian',         ['as' => 'smsemail.student-guardian',              'middleware' => ['ability:super-admin,sms-email-create'],                     'uses' => 'SmsEmailController@studentGuardian']);
    Route::post('smsemail/student-guardian/send',   ['as' => 'smsemail.student-guardian.send',         'middleware' => ['ability:super-admin,sms-email-student-guardian-send'],      'uses' => 'SmsEmailController@studentGuardianSend']);

    /*StudentGuardian*/
    Route::get('smsemail/staff',                    ['as' => 'smsemail.staff',                          'middleware' => ['ability:super-admin,sms-email-create'],                   'uses' => 'SmsEmailController@staff']);
    Route::post('smsemail/staff/send',              ['as' => 'smsemail.staff.send',                     'middleware' => ['ability:super-admin,sms-email-staff-send'],                'uses' => 'SmsEmailController@staffSend']);

    /*Individual*/
    Route::get('smsemail/individual',               ['as' => 'smsemail.individual',                     'middleware' => ['ability:super-admin,sms-email-create'],                   'uses' => 'SmsEmailController@individual']);
    Route::post('smsemail/individual/send',         ['as' => 'smsemail.individual.send',                'middleware' => ['ability:super-admin,sms-email-individual-send'],           'uses' => 'SmsEmailController@individualSend']);

    /*Reminder Alert*/
    Route::get('smsemail/{id}/fees-receipt',        ['as' => 'smsemail.fees-receipt',                   'middleware' => ['ability:super-admin,sms-email-fee-receipt'],               'uses' => 'SmsEmailController@feeReceipt']);
    Route::post('smsemail/dueReminder',             ['as' => 'smsemail.dueReminder',                    'middleware' => ['ability:super-admin,sms-email-due-reminder'],              'uses' => 'SmsEmailController@dueReminder']);
    Route::post('smsemail/bookReturnReminder',      ['as' => 'smsemail.bookReturnReminder',             'middleware' => ['ability:super-admin,sms-email-book-return-reminder'],      'uses' => 'SmsEmailController@bookReturnReminder']);

});

/*Print Grouping*/
Route::group(['prefix' => 'print-out/',                                 'as' => 'print-out.',                                   'namespace' => 'PrintOut\\'], function () {
    
    //Route::get('fees/online-payment-receipt/{id}', ['as' => 'fees.online-payment-receipt', 'uses' => 'FeesPrintController@printOnlinePaymentReceipt']);
    //Route::get('fees/online-payment-receipt/{id}',  ['as' => 'fees.online-payment-receipt',                  'uses' => 'FeesPrintController@printOnlinePaymentReceipt']);

    
    /*Print Fees*/
    Route::get('fees/online-payment-receipt/{id}',              ['as' => 'fees.online-payment-receipt',           'uses' => 'FeesPrintController@printOnlinePaymentReceipt']);
    Route::get('fees/master-receipt/{id}',              ['as' => 'fees.master-receipt',                     'middleware' => ['ability:super-admin,fee-print-master'],                   'uses' => 'FeesPrintController@printMaster']);
    Route::get('fees/selected-master-receipt',          ['as' => 'fees.selected-master-receipt',            'middleware' => ['ability:super-admin,fee-print-master'],                   'uses' => 'FeesPrintController@printSelectedMaster']);

    Route::get('fees/collection/{id}',                  ['as' => 'fees.collection',                         'middleware' => ['ability:super-admin,fee-print-collection'],               'uses' => 'FeesPrintController@printCollection']);
    //student today receipt
    Route::get('fees/today-receipt/{id}',               ['as' => 'fees.today-receipt',                      'middleware' => ['ability:super-admin,fee-print-today-receipt'],            'uses' => 'FeesPrintController@todayReceiptAmount']);
    Route::get('fees/today-receipt-detail/{id}',        ['as' => 'fees.today-receipt-detail',               'middleware' => ['ability:super-admin,fee-print-today-detail-receipt'],     'uses' => 'FeesPrintController@todayReceiptDetail']);

    //student today receipt
    Route::get('fees/date-receipt/{id}/{date}',               ['as' => 'fees.date-receipt',                      'middleware' => ['ability:super-admin,fee-print-today-receipt'],            'uses' => 'FeesPrintController@dateReceiptAmount']);
    Route::get('fees/date-receipt-detail/{id}/{date}',        ['as' => 'fees.date-receipt-detail',               'middleware' => ['ability:super-admin,fee-print-today-detail-receipt'],     'uses' => 'FeesPrintController@dateReceiptDetail']);

    Route::get('fees/student-ledger/{id}',              ['as' => 'fees.student-ledger',                     'middleware' => ['ability:super-admin,fee-print-student-ledger'],           'uses' => 'FeesPrintController@studentLedger']);

    Route::get('fees/student-due/{id}',                 ['as' => 'fees.student-due',                        'middleware' => ['ability:super-admin,fee-print-student-due'],              'uses' => 'FeesPrintController@studentDue']);
    Route::get('fees/student-due-detail/{id}',          ['as' => 'fees.student-due-detail',                 'middleware' => ['ability:super-admin,fee-print-student-due-detail'],       'uses' => 'FeesPrintController@studentDueDetail']);

    Route::post('fees/bulk-due-slip',                   ['as' => 'fees.bulk-due-slip',                       'middleware' => ['ability:super-admin,fee-print-bulk-due-slip'],           'uses' => 'FeesPrintController@bulkDueSlip']);
    Route::post('fees/bulk-due-detail-slip',            ['as' => 'fees.bulk-due-detail-slip',                'middleware' => ['ability:super-admin,fee-print-bulk-due-detail-slip'],    'uses' => 'FeesPrintController@bulkDueDetailSlip']);
    Route::post('fees/create-fee-voucher',                     ['as' => 'fees.create-fee-voucher',                                  'middleware' => ['ability:super-admin,fee-print-bulk-due-detail-slip'],    'uses' => 'FeesPrintController@feeVoucher']);

    //payroll print
    Route::get('payroll/staff-ledger/{id}',              ['as' => 'payroll.staff-ledger',                     'middleware' => ['ability:super-admin,payroll-print-staff-ledger'],           'uses' => 'PayrollPrintController@staffLedger']);

    //exam print
    Route::post('exam/admit-card',                      ['as' => 'exam.admit-card',                         'middleware' => ['ability:super-admin,exam-print-admitcard'],                     'uses' => 'ExamPrintController@admitCard']);
    Route::post('exam/routine',                         ['as' => 'exam.routine',                            'middleware' => ['ability:super-admin,exam-print-routine'],                       'uses' => 'ExamPrintController@examRoutine']);
    Route::post('exam/mark-sheet/',                     ['as' => 'exam.mark-sheet',                         'middleware' => ['ability:super-admin,exam-print-mark-sheet'],                    'uses' => 'ExamPrintController@examMarkSheet']);
    Route::post('exam/grade-sheet/',                     ['as' => 'exam.grade-sheet',                         'middleware' => ['ability:super-admin,exam-print-mark-sheet'],                    'uses' => 'ExamPrintController@examGradeSheet']);
    Route::post('exam/mark-ledger/',                     ['as' => 'exam.mark-ledger',                         'middleware' => ['ability:super-admin,exam-print-mark-ledger'],                    'uses' => 'ExamPrintController@examMarkLedger']);

    //certificates
    Route::post('general-certificate',                                   ['as' => 'general-certificate',                         'middleware' => ['ability:super-admin,general-certificate-print'],      'uses' => 'CertificatePrintController@generalCertificate']);
    //Route::get('certificate/bonafide/{id}',                      ['as' => 'certificate.bonafide',                'middleware' => ['ability:super-admin,certificate-print-bonafide'],          'uses' => 'CertificatePrintController@bonafideCertificate']);
});

// /*Academic Grouping */
// Route::group(['prefix' => '/',                                          'as' => '',                                             'namespace' => 'Academic\\'], function () {
//     // Custom AJAX routes
//    /* Academic AJAX Routes */
// Route::get('get-departments', ['as' => 'get-departments', 'uses' => 'AcademicController@getDepartments']);
// Route::get('get-faculties', ['as' => 'get-faculties', 'uses' => 'AcademicController@getFaculties']);
// Route::get('get-semesters', ['as' => 'get-semesters', 'uses' => 'AcademicController@getSemesters']);
// Route::get('get-batches', ['as' => 'get-batches', 'uses' => 'AcademicController@getBatches']);
// Route::get('get-subjects', ['as' => 'get-subjects', 'uses' => 'AcademicController@getSubjects']);

//     /*Payment Method Routes*/
//     Route::get('payment-method',                    ['as' => 'payment-method',                  'middleware' => ['ability:super-admin,payment-method-index'],               'uses' => 'PaymentMethodController@index']);
//     Route::post('payment-method/store',             ['as' => 'payment-method.store',            'middleware' => ['ability:super-admin,payment-method-add'],                 'uses' => 'PaymentMethodController@store']);
//     Route::get('payment-method/{id}/edit',          ['as' => 'payment-method.edit',             'middleware' => ['ability:super-admin,payment-method-edit'],                'uses' => 'PaymentMethodController@edit']);
//     Route::post('payment-method/{id}/update',       ['as' => 'payment-method.update',           'middleware' => ['ability:super-admin,payment-method-edit'],                'uses' => 'PaymentMethodController@update']);
//     Route::get('payment-method/{id}/delete',        ['as' => 'payment-method.delete',           'middleware' => ['ability:super-admin,payment-method-delete'],              'uses' => 'PaymentMethodController@delete']);
//     Route::get('payment-method/{id}/active',        ['as' => 'payment-method.active',           'middleware' => ['ability:super-admin,payment-method-active'],              'uses' => 'PaymentMethodController@Active']);
//     Route::get('payment-method/{id}/in-active',     ['as' => 'payment-method.in-active',        'middleware' => ['ability:super-admin,payment-method-in-active'],           'uses' => 'PaymentMethodController@inActive']);
//     Route::post('payment-method/bulk-action',       ['as' => 'payment-method.bulk-action',      'middleware' => ['ability:super-admin,payment-method-bulk-action'],         'uses' => 'PaymentMethodController@bulkAction']);

//     /*Department Head Routes*/
//     Route::get('department-head',                    ['as' => 'department-head',                  'middleware' => ['ability:super-admin,department-head-index'],            'uses' => 'DepartmentHeadController@index']);
//     Route::post('department-head/store',             ['as' => 'department-head.store',            'middleware' => ['ability:super-admin,department-head-add'],              'uses' => 'DepartmentHeadController@store']);
//     Route::get('department-head/{id}/edit',          ['as' => 'department-head.edit',             'middleware' => ['ability:super-admin,department-head-edit'],             'uses' => 'DepartmentHeadController@edit']);
//     Route::post('department-head/{id}/update',       ['as' => 'department-head.update',           'middleware' => ['ability:super-admin,department-head-edit'],             'uses' => 'DepartmentHeadController@update']);
//     Route::get('department-head/{id}/delete',        ['as' => 'department-head.delete',           'middleware' => ['ability:super-admin,department-head-delete'],           'uses' => 'DepartmentHeadController@delete']);
//     Route::get('department-head/{id}/active',        ['as' => 'department-head.active',           'middleware' => ['ability:super-admin,department-head-active'],           'uses' => 'DepartmentHeadController@Active']);
//     Route::get('department-head/{id}/in-active',     ['as' => 'department-head.in-active',        'middleware' => ['ability:super-admin,department-head-in-active'],        'uses' => 'DepartmentHeadController@inActive']);
//     Route::post('department-head/bulk-action',       ['as' => 'department-head.bulk-action',      'middleware' => ['ability:super-admin,department-head-bulk-action'],      'uses' => 'DepartmentHeadController@bulkAction']);

//     /*Department Routes*/
//     Route::get('department',                    ['as' => 'department',                  'middleware' => ['ability:super-admin,department-index'],            'uses' => 'DepartmentController@index']);
//     Route::post('department/store',             ['as' => 'department.store',            'middleware' => ['ability:super-admin,department-add'],              'uses' => 'DepartmentController@store']);
//     Route::get('department/{id}/edit',          ['as' => 'department.edit',             'middleware' => ['ability:super-admin,department-edit'],             'uses' => 'DepartmentController@edit']);
//     Route::post('department/{id}/update',       ['as' => 'department.update',           'middleware' => ['ability:super-admin,department-edit'],             'uses' => 'DepartmentController@update']);
//     Route::get('department/{id}/delete',        ['as' => 'department.delete',           'middleware' => ['ability:super-admin,department-delete'],           'uses' => 'DepartmentController@delete']);
//     Route::get('department/{id}/active',        ['as' => 'department.active',           'middleware' => ['ability:super-admin,department-active'],           'uses' => 'DepartmentController@Active']);
//     Route::get('department/{id}/in-active',     ['as' => 'department.in-active',        'middleware' => ['ability:super-admin,department-in-active'],        'uses' => 'DepartmentController@inActive']);
//     Route::post('department/bulk-action',       ['as' => 'department.bulk-action',      'middleware' => ['ability:super-admin,department-bulk-action'],      'uses' => 'DepartmentController@bulkAction']);



//     /*faculty Routes*/
//     Route::get('faculty',                    ['as' => 'faculty',                  'middleware' => ['ability:super-admin,faculty-index'],            'uses' => 'FacultyController@index']);
//     Route::post('faculty/store',             ['as' => 'faculty.store',            'middleware' => ['ability:super-admin,faculty-add'],              'uses' => 'FacultyController@store']);
//     Route::get('faculty/{id}/edit',          ['as' => 'faculty.edit',             'middleware' => ['ability:super-admin,faculty-edit'],             'uses' => 'FacultyController@edit']);
//     Route::post('faculty/{id}/update',       ['as' => 'faculty.update',           'middleware' => ['ability:super-admin,faculty-edit'],             'uses' => 'FacultyController@update']);
//     Route::get('faculty/{id}/delete',        ['as' => 'faculty.delete',           'middleware' => ['ability:super-admin,faculty-delete'],           'uses' => 'FacultyController@delete']);
//     Route::get('faculty/{id}/active',        ['as' => 'faculty.active',           'middleware' => ['ability:super-admin,faculty-active'],           'uses' => 'FacultyController@Active']);
//     Route::get('faculty/{id}/in-active',     ['as' => 'faculty.in-active',        'middleware' => ['ability:super-admin,faculty-in-active'],        'uses' => 'FacultyController@inActive']);
//     Route::post('faculty/bulk-action',       ['as' => 'faculty.bulk-action',      'middleware' => ['ability:super-admin,faculty-bulk-action'],      'uses' => 'FacultyController@bulkAction']);

//     /*semester Routes*/
//     Route::get('semester',                    ['as' => 'semester',                  'middleware' => ['ability:super-admin,semester-index'],             'uses' => 'SemesterController@index']);
//     Route::post('semester/store',             ['as' => 'semester.store',            'middleware' => ['ability:super-admin,semester-add'],               'uses' => 'SemesterController@store']);
//     Route::get('semester/{id}/edit',          ['as' => 'semester.edit',             'middleware' => ['ability:super-admin,semester-edit'],              'uses' => 'SemesterController@edit']);
//     Route::post('semester/{id}/update',       ['as' => 'semester.update',           'middleware' => ['ability:super-admin,semester-edit'],              'uses' => 'SemesterController@update']);
//     Route::get('semester/{id}/delete',        ['as' => 'semester.delete',           'middleware' => ['ability:super-admin,semester-delete'],            'uses' => 'SemesterController@delete']);
//     Route::get('semester/{id}/active',        ['as' => 'semester.active',           'middleware' => ['ability:super-admin,semester-active'],            'uses' => 'SemesterController@Active']);
//     Route::get('semester/{id}/in-active',     ['as' => 'semester.in-active',        'middleware' => ['ability:super-admin,semester-in-active'],         'uses' => 'SemesterController@inActive']);
//     Route::post('semester/bulk-action',       ['as' => 'semester.bulk-action',      'middleware' => ['ability:super-admin,semester-bulk-action'],       'uses' => 'SemesterController@bulkAction']);
//     Route::post('semester/subject-html',        ['as' => 'semester.subject-html',                                                                       'uses' => 'SemesterController@subjectHtmlRow']);

//     /*Student Batch */
//     Route::get('student-batch',                    ['as' => 'student-batch',                  'middleware' => ['ability:super-admin,student-batch-index'],               'uses' => 'StudentBatchController@index']);
//     Route::post('student-batch/store',             ['as' => 'student-batch.store',            'middleware' => ['ability:super-admin,student-batch-add'],                 'uses' => 'StudentBatchController@store']);
//     Route::get('student-batch/{id}/edit',          ['as' => 'student-batch.edit',             'middleware' => ['ability:super-admin,student-batch-edit'],                'uses' => 'StudentBatchController@edit']);
//     Route::post('student-batch/{id}/update',       ['as' => 'student-batch.update',           'middleware' => ['ability:super-admin,student-batch-edit'],                'uses' => 'StudentBatchController@update']);
//     Route::get('student-batch/{id}/delete',        ['as' => 'student-batch.delete',           'middleware' => ['ability:super-admin,student-batch-delete'],              'uses' => 'StudentBatchController@delete']);
//     Route::get('student-batch/{id}/active',        ['as' => 'student-batch.active',           'middleware' => ['ability:super-admin,student-batch-active'],              'uses' => 'StudentBatchController@Active']);
//     Route::get('student-batch/{id}/in-active',     ['as' => 'student-batch.in-active',        'middleware' => ['ability:super-admin,student-batch-in-active'],           'uses' => 'StudentBatchController@inActive']);
//     Route::post('student-batch/bulk-action',       ['as' => 'student-batch.bulk-action',      'middleware' => ['ability:super-admin,student-batch-bulk-action'],         'uses' => 'StudentBatchController@bulkAction']);
//     Route::get('student-batch/{id}/active-status',['as' => 'student-batch.active-status',      'middleware' => ['ability:super-admin,student-batch-active'],         'uses' => 'StudentBatchController@activeStatus']);


//     /*Grading Type & Scale*/
//     Route::get('grading',                    ['as' => 'grading',                  'middleware' => ['ability:super-admin,grading-index'],                'uses' => 'GradingController@index']);
//     Route::post('grading/store',             ['as' => 'grading.store',            'middleware' => ['ability:super-admin,grading-add'],                  'uses' => 'GradingController@store']);
//     Route::get('grading/{id}/edit',          ['as' => 'grading.edit',             'middleware' => ['ability:super-admin,grading-edit'],                 'uses' => 'GradingController@edit']);
//     Route::post('grading/{id}/update',       ['as' => 'grading.update',           'middleware' => ['ability:super-admin,grading-edit'],                 'uses' => 'GradingController@update']);
//     Route::get('grading/{id}/delete',        ['as' => 'grading.delete',           'middleware' => ['ability:super-admin,grading-delete'],               'uses' => 'GradingController@delete']);
//     Route::get('grading/{id}/active',        ['as' => 'grading.active',           'middleware' => ['ability:super-admin,grading-active'],               'uses' => 'GradingController@Active']);
//     Route::get('grading/{id}/in-active',     ['as' => 'grading.in-active',        'middleware' => ['ability:super-admin,grading-in-active'],            'uses' => 'GradingController@inActive']);
//     Route::post('grading/bulk-action',       ['as' => 'grading.bulk-action',      'middleware' => ['ability:super-admin,grading-bulk-action'],          'uses' => 'GradingController@bulkAction']);
//     Route::post('grading/grade-html',        ['as' => 'grading.grade-html',                                                                                 'uses' => 'GradingController@gradeHtmlRow']);

//     /*Subject*/
//     Route::get('subject',                    ['as' => 'subject',                  'middleware' => ['ability:super-admin,subject-index'],                'uses' => 'SubjectController@index']);
//     Route::post('subject/store',             ['as' => 'subject.store',            'middleware' => ['ability:super-admin,subject-add'],                  'uses' => 'SubjectController@store']);
//     Route::get('subject/{id}/edit',          ['as' => 'subject.edit',             'middleware' => ['ability:super-admin,subject-edit'],                 'uses' => 'SubjectController@edit']);
//     Route::post('subject/{id}/update',       ['as' => 'subject.update',           'middleware' => ['ability:super-admin,subject-edit'],                 'uses' => 'SubjectController@update']);
//     Route::get('subject/{id}/delete',        ['as' => 'subject.delete',           'middleware' => ['ability:super-admin,subject-delete'],               'uses' => 'SubjectController@delete']);
//     Route::get('subject/{id}/active',        ['as' => 'subject.active',           'middleware' => ['ability:super-admin,subject-active'],               'uses' => 'SubjectController@Active']);
//     Route::get('subject/{id}/in-active',     ['as' => 'subject.in-active',        'middleware' => ['ability:super-admin,subject-in-active'],            'uses' => 'SubjectController@inActive']);
//     Route::post('subject/bulk-action',       ['as' => 'subject.bulk-action',      'middleware' => ['ability:super-admin,subject-bulk-action'],          'uses' => 'SubjectController@bulkAction']);
//     Route::get('subject-name-autocomplete',  ['as' => 'subject-name-autocomplete',                                                                      'uses' => 'SubjectController@subjectNameAutocomplete']);

//     /*Student Status Routes*/
//     Route::get('student-status',                    ['as' => 'student-status',                  'middleware' => ['ability:super-admin,student-status-index'],               'uses' => 'StudentStatusController@index']);
//     Route::post('student-status/store',             ['as' => 'student-status.store',            'middleware' => ['ability:super-admin,student-status-add'],                 'uses' => 'StudentStatusController@store']);
//     Route::get('student-status/{id}/edit',          ['as' => 'student-status.edit',             'middleware' => ['ability:super-admin,student-status-edit'],                'uses' => 'StudentStatusController@edit']);
//     Route::post('student-status/{id}/update',       ['as' => 'student-status.update',           'middleware' => ['ability:super-admin,student-status-edit'],                'uses' => 'StudentStatusController@update']);
//     Route::get('student-status/{id}/delete',        ['as' => 'student-status.delete',           'middleware' => ['ability:super-admin,student-status-delete'],              'uses' => 'StudentStatusController@delete']);
//     Route::get('student-status/{id}/active',        ['as' => 'student-status.active',           'middleware' => ['ability:super-admin,student-status-active'],              'uses' => 'StudentStatusController@Active']);
//     Route::get('student-status/{id}/in-active',     ['as' => 'student-status.in-active',        'middleware' => ['ability:super-admin,student-status-in-active'],           'uses' => 'StudentStatusController@inActive']);
//     Route::post('student-status/bulk-action',       ['as' => 'student-status.bulk-action',      'middleware' => ['ability:super-admin,student-status-bulk-action'],         'uses' => 'StudentStatusController@bulkAction']);

//     /*attendance Status Routes*/
//     Route::get('attendance-status',                    ['as' => 'attendance-status',                  'middleware' => ['ability:super-admin,attendance-status-index'],            'uses' => 'AttendanceStatusController@index']);
//     Route::post('attendance-status/store',             ['as' => 'attendance-status.store',            'middleware' => ['ability:super-admin,attendance-status-add'],              'uses' => 'AttendanceStatusController@store']);
//     Route::get('attendance-status/{id}/edit',          ['as' => 'attendance-status.edit',             'middleware' => ['ability:super-admin,attendance-status-edit'],             'uses' => 'AttendanceStatusController@edit']);
//     Route::post('attendance-status/{id}/update',       ['as' => 'attendance-status.update',           'middleware' => ['ability:super-admin,attendance-status-edit'],             'uses' => 'AttendanceStatusController@update']);
//     Route::get('attendance-status/{id}/delete',        ['as' => 'attendance-status.delete',           'middleware' => ['ability:super-admin,attendance-status-delete'],           'uses' => 'AttendanceStatusController@delete']);
//     Route::get('attendance-status/{id}/active',        ['as' => 'attendance-status.active',           'middleware' => ['ability:super-admin,attendance-status-active'],           'uses' => 'AttendanceStatusController@Active']);
//     Route::get('attendance-status/{id}/in-active',     ['as' => 'attendance-status.in-active',        'middleware' => ['ability:super-admin,attendance-status-in-active'],        'uses' => 'AttendanceStatusController@inActive']);
//     Route::post('attendance-status/bulk-action',       ['as' => 'attendance-status.bulk-action',      'middleware' => ['ability:super-admin,attendance-status-bulk-action'],      'uses' => 'AttendanceStatusController@bulkAction']);

//     /*Book Status Routes*/
//     Route::get('book-status',                    ['as' => 'book-status',                  'middleware' => ['ability:super-admin,book-status-index'],            'uses' => 'BookStatusController@index']);
//     Route::post('book-status/store',             ['as' => 'book-status.store',            'middleware' => ['ability:super-admin,book-status-add'],              'uses' => 'BookStatusController@store']);
//     Route::get('book-status/{id}/edit',          ['as' => 'book-status.edit',             'middleware' => ['ability:super-admin,book-status-edit'],             'uses' => 'BookStatusController@edit']);
//     Route::post('book-status/{id}/update',       ['as' => 'book-status.update',           'middleware' => ['ability:super-admin,book-status-edit'],             'uses' => 'BookStatusController@update']);
//     Route::get('book-status/{id}/delete',        ['as' => 'book-status.delete',           'middleware' => ['ability:super-admin,book-status-delete'],           'uses' => 'BookStatusController@delete']);
//     Route::get('book-status/{id}/active',        ['as' => 'book-status.active',           'middleware' => ['ability:super-admin,book-status-active'],           'uses' => 'BookStatusController@Active']);
//     Route::get('book-status/{id}/in-active',     ['as' => 'book-status.in-active',        'middleware' => ['ability:super-admin,book-status-in-active'],        'uses' => 'BookStatusController@inActive']);
//     Route::post('book-status/bulk-action',       ['as' => 'book-status.bulk-action',      'middleware' => ['ability:super-admin,book-status-bulk-action'],      'uses' => 'BookStatusController@bulkAction']);

//     /*Hostel Room Beds Status Routes*/
//     Route::get('bed-status',                    ['as' => 'bed-status',                  'middleware' => ['ability:super-admin,bed-status-index'],               'uses' => 'BedStatusController@index']);
//     Route::post('bed-status/store',             ['as' => 'bed-status.store',            'middleware' => ['ability:super-admin,bed-status-add'],                 'uses' => 'BedStatusController@store']);
//     Route::get('bed-status/{id}/edit',          ['as' => 'bed-status.edit',             'middleware' => ['ability:super-admin,bed-status-edit'],                'uses' => 'BedStatusController@edit']);
//     Route::post('bed-status/{id}/update',       ['as' => 'bed-status.update',           'middleware' => ['ability:super-admin,bed-status-edit'],                'uses' => 'BedStatusController@update']);
//     Route::get('bed-status/{id}/delete',        ['as' => 'bed-status.delete',           'middleware' => ['ability:super-admin,bed-status-delete'],              'uses' => 'BedStatusController@delete']);
//     Route::get('bed-status/{id}/active',        ['as' => 'bed-status.active',           'middleware' => ['ability:super-admin,bed-status-active'],              'uses' => 'BedStatusController@Active']);
//     Route::get('bed-status/{id}/in-active',     ['as' => 'bed-status.in-active',        'middleware' => ['ability:super-admin,bed-status-in-active'],           'uses' => 'BedStatusController@inActive']);
//     Route::post('bed-status/bulk-action',       ['as' => 'bed-status.bulk-action',      'middleware' => ['ability:super-admin,bed-status-bulk-action'],          'uses' => 'BedStatusController@bulkAction']);

//     /*Customer Status Routes*/
//     Route::get('customer-status',                    ['as' => 'customer-status',                  'middleware' => ['ability:super-admin,customer-status-index'],               'uses' => 'CustomerStatusController@index']);
//     Route::post('customer-status/store',             ['as' => 'customer-status.store',            'middleware' => ['ability:super-admin,customer-status-add'],                 'uses' => 'CustomerStatusController@store']);
//     Route::get('customer-status/{id}/edit',          ['as' => 'customer-status.edit',             'middleware' => ['ability:super-admin,customer-status-edit'],                'uses' => 'CustomerStatusController@edit']);
//     Route::post('customer-status/{id}/update',       ['as' => 'customer-status.update',           'middleware' => ['ability:super-admin,customer-status-edit'],                'uses' => 'CustomerStatusController@update']);
//     Route::get('customer-status/{id}/delete',        ['as' => 'customer-status.delete',           'middleware' => ['ability:super-admin,customer-status-delete'],              'uses' => 'CustomerStatusController@delete']);
//     Route::get('customer-status/{id}/active',        ['as' => 'customer-status.active',           'middleware' => ['ability:super-admin,customer-status-active'],              'uses' => 'CustomerStatusController@Active']);
//     Route::get('customer-status/{id}/in-active',     ['as' => 'customer-status.in-active',        'middleware' => ['ability:super-admin,customer-status-in-active'],           'uses' => 'CustomerStatusController@inActive']);
//     Route::post('customer-status/bulk-action',       ['as' => 'customer-status.bulk-action',      'middleware' => ['ability:super-admin,customer-status-bulk-action'],         'uses' => 'CustomerStatusController@bulkAction']);

//     /*Year Routes*/
//     Route::get('year',                    ['as' => 'year',                  'middleware' => ['ability:super-admin,year-index'],            'uses' => 'YearsController@index']);
//     Route::post('year/store',             ['as' => 'year.store',            'middleware' => ['ability:super-admin,year-add'],              'uses' => 'YearsController@store']);
//     Route::get('year/{id}/edit',          ['as' => 'year.edit',             'middleware' => ['ability:super-admin,year-edit'],             'uses' => 'YearsController@edit']);
//     Route::post('year/{id}/update',       ['as' => 'year.update',           'middleware' => ['ability:super-admin,year-edit'],             'uses' => 'YearsController@update']);
//     Route::get('year/{id}/delete',        ['as' => 'year.delete',           'middleware' => ['ability:super-admin,year-delete'],           'uses' => 'YearsController@delete']);
//     Route::get('year/{id}/active',        ['as' => 'year.active',           'middleware' => ['ability:super-admin,year-active'],           'uses' => 'YearsController@Active']);
//     Route::get('year/{id}/in-active',     ['as' => 'year.in-active',        'middleware' => ['ability:super-admin,year-in-active'],        'uses' => 'YearsController@inActive']);
//     Route::post('year/bulk-action',       ['as' => 'year.bulk-action',      'middleware' => ['ability:super-admin,year-bulk-action'],      'uses' => 'YearsController@bulkAction']);
//     Route::get('year/{id}/active-status', ['as' => 'year.active-status',    'middleware' => ['ability:super-admin,year-active-status'],    'uses' => 'YearsController@activeStatus']);

//     /*Months Routes*/
//     Route::get('month',                    ['as' => 'month',                  'middleware' => ['ability:super-admin,month-index'],          'uses' => 'MonthsController@index']);
//     Route::post('month/store',             ['as' => 'month.store',            'middleware' => ['ability:super-admin,month-add'],            'uses' => 'MonthsController@store']);
//     Route::get('month/{id}/edit',          ['as' => 'month.edit',             'middleware' => ['ability:super-admin,month-edit'],           'uses' => 'MonthsController@edit']);
//     Route::post('month/{id}/update',       ['as' => 'month.update',           'middleware' => ['ability:super-admin,month-edit'],           'uses' => 'MonthsController@update']);
//     Route::get('month/{id}/delete',        ['as' => 'month.delete',           'middleware' => ['ability:super-admin,month-delete'],         'uses' => 'MonthsController@delete']);
//     Route::get('month/{id}/active',        ['as' => 'month.active',           'middleware' => ['ability:super-admin,month-active'],         'uses' => 'MonthsController@Active']);
//     Route::get('month/{id}/in-active',     ['as' => 'month.in-active',        'middleware' => ['ability:super-admin,month-in-active'],      'uses' => 'MonthsController@inActive']);
//     Route::post('month/bulk-action',       ['as' => 'month.bulk-action',      'middleware' => ['ability:super-admin,month-bulk-action'],    'uses' => 'MonthsController@bulkAction']);

//     /*Day Routes*/
//     Route::get('day',                    ['as' => 'day',                  'middleware' => ['ability:super-admin,day-index'],            'uses' => 'DayController@index']);
//     Route::post('day/store',             ['as' => 'day.store',            'middleware' => ['ability:super-admin,day-add'],              'uses' => 'DayController@store']);
//     Route::get('day/{id}/edit',          ['as' => 'day.edit',             'middleware' => ['ability:super-admin,day-edit'],             'uses' => 'DayController@edit']);
//     Route::post('day/{id}/update',       ['as' => 'day.update',           'middleware' => ['ability:super-admin,day-edit'],             'uses' => 'DayController@update']);
//     Route::get('day/{id}/delete',        ['as' => 'day.delete',           'middleware' => ['ability:super-admin,day-delete'],           'uses' => 'DayController@delete']);
//     Route::get('day/{id}/active',        ['as' => 'day.active',           'middleware' => ['ability:super-admin,day-active'],           'uses' => 'DayController@Active']);
//     Route::get('day/{id}/in-active',     ['as' => 'day.in-active',        'middleware' => ['ability:super-admin,day-in-active'],        'uses' => 'DayController@inActive']);
//     Route::post('day/bulk-action',       ['as' => 'day.bulk-action',      'middleware' => ['ability:super-admin,day-bulk-action'],      'uses' => 'DayController@bulkAction']);

//     /*Dynamic Degree Routes*/
//     Route::get('dynamic/degree',                    ['as' => 'dynamic.degree',                  'middleware' => ['ability:super-admin,dynamic-degree-index'],               'uses' => 'Dynamic\DegreeController@index']);
//     Route::post('dynamic/degree/store',             ['as' => 'dynamic.degree.store',            'middleware' => ['ability:super-admin,dynamic-degree-add'],                 'uses' => 'Dynamic\DegreeController@store']);
//     Route::get('dynamic/degree/{id}/edit',          ['as' => 'dynamic.degree.edit',             'middleware' => ['ability:super-admin,dynamic-degree-edit'],                'uses' => 'Dynamic\DegreeController@edit']);
//     Route::post('dynamic/degree/{id}/update',       ['as' => 'dynamic.degree.update',           'middleware' => ['ability:super-admin,dynamic-degree-edit'],                'uses' => 'Dynamic\DegreeController@update']);
//     Route::get('dynamic/degree/{id}/delete',        ['as' => 'dynamic.degree.delete',           'middleware' => ['ability:super-admin,dynamic-degree-delete'],              'uses' => 'Dynamic\DegreeController@delete']);
//     Route::get('dynamic/degree/{id}/active',        ['as' => 'dynamic.degree.active',           'middleware' => ['ability:super-admin,dynamic-degree-active'],              'uses' => 'Dynamic\DegreeController@Active']);
//     Route::get('dynamic/degree/{id}/in-active',     ['as' => 'dynamic.degree.in-active',        'middleware' => ['ability:super-admin,dynamic-degree-in-active'],           'uses' => 'Dynamic\DegreeController@inActive']);
//     Route::post('dynamic/degree/bulk-action',       ['as' => 'dynamic.degree.bulk-action',      'middleware' => ['ability:super-admin,degree-bulk-action'],                 'uses' => 'Dynamic\DegreeController@bulkAction']);

//     /*Dynamic Scholarship Routes*/
//     Route::get('dynamic/scholarship',                    ['as' => 'dynamic.scholarship',                  'middleware' => ['ability:super-admin,dynamic-scholarship-index'],               'uses' => 'Dynamic\ScholarshipController@index']);
//     Route::post('dynamic/scholarship/store',             ['as' => 'dynamic.scholarship.store',            'middleware' => ['ability:super-admin,dynamic-scholarship-add'],                 'uses' => 'Dynamic\ScholarshipController@store']);
//     Route::get('dynamic/scholarship/{id}/edit',          ['as' => 'dynamic.scholarship.edit',             'middleware' => ['ability:super-admin,dynamic-scholarship-edit'],                'uses' => 'Dynamic\ScholarshipController@edit']);
//     Route::post('dynamic/scholarship/{id}/update',       ['as' => 'dynamic.scholarship.update',           'middleware' => ['ability:super-admin,dynamic-scholarship-edit'],                'uses' => 'Dynamic\ScholarshipController@update']);
//     Route::get('dynamic/scholarship/{id}/delete',        ['as' => 'dynamic.scholarship.delete',           'middleware' => ['ability:super-admin,dynamic-scholarship-delete'],              'uses' => 'Dynamic\ScholarshipController@delete']);
//     Route::get('dynamic/scholarship/{id}/active',        ['as' => 'dynamic.scholarship.active',           'middleware' => ['ability:super-admin,dynamic-scholarship-active'],              'uses' => 'Dynamic\ScholarshipController@Active']);
//     Route::get('dynamic/scholarship/{id}/in-active',     ['as' => 'dynamic.scholarship.in-active',        'middleware' => ['ability:super-admin,dynamic-scholarship-in-active'],           'uses' => 'Dynamic\ScholarshipController@inActive']);
//     Route::post('dynamic/scholarship/bulk-action',       ['as' => 'dynamic.scholarship.bulk-action',      'middleware' => ['ability:super-admin,scholarship-bulk-action'],                  'uses' => 'Dynamic\ScholarshipController@bulkAction']);

//     /*Dynamic Placement Routes*/
//     Route::get('dynamic/placement',                    ['as' => 'dynamic.placement',                  'middleware' => ['ability:super-admin,dynamic-placement-index'],               'uses' => 'Dynamic\PlacementController@index']);
//     Route::post('dynamic/placement/store',             ['as' => 'dynamic.placement.store',            'middleware' => ['ability:super-admin,dynamic-placement-add'],                 'uses' => 'Dynamic\PlacementController@store']);
//     Route::get('dynamic/placement/{id}/edit',          ['as' => 'dynamic.placement.edit',             'middleware' => ['ability:super-admin,dynamic-placement-edit'],                'uses' => 'Dynamic\PlacementController@edit']);
//     Route::post('dynamic/placement/{id}/update',       ['as' => 'dynamic.placement.update',           'middleware' => ['ability:super-admin,dynamic-placement-edit'],                'uses' => 'Dynamic\PlacementController@update']);
//     Route::get('dynamic/placement/{id}/delete',        ['as' => 'dynamic.placement.delete',           'middleware' => ['ability:super-admin,dynamic-placement-delete'],              'uses' => 'Dynamic\PlacementController@delete']);
//     Route::get('dynamic/placement/{id}/active',        ['as' => 'dynamic.placement.active',           'middleware' => ['ability:super-admin,dynamic-placement-active'],              'uses' => 'Dynamic\PlacementController@Active']);
//     Route::get('dynamic/placement/{id}/in-active',     ['as' => 'dynamic.placement.in-active',        'middleware' => ['ability:super-admin,dynamic-placement-in-active'],           'uses' => 'Dynamic\PlacementController@inActive']);
//     Route::post('dynamic/placement/bulk-action',       ['as' => 'dynamic.placement.bulk-action',      'middleware' => ['ability:super-admin,placement-bulk-action'],                  'uses' => 'Dynamic\PlacementController@bulkAction']);

//     /*Dynamic Annexure Routes*/
//     Route::get('dynamic/annexure',                    ['as' => 'dynamic.annexure',                  'middleware' => ['ability:super-admin,dynamic-annexure-index'],               'uses' => 'Dynamic\AnnexureController@index']);
//     Route::post('dynamic/annexure/store',             ['as' => 'dynamic.annexure.store',            'middleware' => ['ability:super-admin,dynamic-annexure-add'],                 'uses' => 'Dynamic\AnnexureController@store']);
//     Route::get('dynamic/annexure/{id}/edit',          ['as' => 'dynamic.annexure.edit',             'middleware' => ['ability:super-admin,dynamic-annexure-edit'],                'uses' => 'Dynamic\AnnexureController@edit']);
//     Route::post('dynamic/annexure/{id}/update',       ['as' => 'dynamic.annexure.update',           'middleware' => ['ability:super-admin,dynamic-annexure-edit'],                'uses' => 'Dynamic\AnnexureController@update']);
//     Route::get('dynamic/annexure/{id}/delete',        ['as' => 'dynamic.annexure.delete',           'middleware' => ['ability:super-admin,dynamic-annexure-delete'],              'uses' => 'Dynamic\AnnexureController@delete']);
//     Route::get('dynamic/annexure/{id}/active',        ['as' => 'dynamic.annexure.active',           'middleware' => ['ability:super-admin,dynamic-annexure-active'],              'uses' => 'Dynamic\AnnexureController@Active']);
//     Route::get('dynamic/annexure/{id}/in-active',     ['as' => 'dynamic.annexure.in-active',        'middleware' => ['ability:super-admin,dynamic-annexure-in-active'],           'uses' => 'Dynamic\AnnexureController@inActive']);
//     Route::post('dynamic/annexure/bulk-action',       ['as' => 'dynamic.annexure.bulk-action',      'middleware' => ['ability:super-admin,annexure-bulk-action'],                  'uses' => 'Dynamic\AnnexureController@bulkAction']);

//     /*Dynamic AcademicInfo Routes*/
//     Route::get('dynamic/academic-info-level',                    ['as' => 'dynamic.academic-info-level',                  'middleware' => ['ability:super-admin,dynamic-academic-info-level-index'],               'uses' => 'Dynamic\AcademicLevelController@index']);
//     Route::post('dynamic/academic-info-level/store',             ['as' => 'dynamic.academic-info-level.store',            'middleware' => ['ability:super-admin,dynamic-academic-info-level-add'],                 'uses' => 'Dynamic\AcademicLevelController@store']);
//     Route::get('dynamic/academic-info-level/{id}/edit',          ['as' => 'dynamic.academic-info-level.edit',             'middleware' => ['ability:super-admin,dynamic-academic-info-level-edit'],                'uses' => 'Dynamic\AcademicLevelController@edit']);
//     Route::post('dynamic/academic-info-level/{id}/update',       ['as' => 'dynamic.academic-info-level.update',           'middleware' => ['ability:super-admin,dynamic-academic-info-level-edit'],                'uses' => 'Dynamic\AcademicLevelController@update']);
//     Route::get('dynamic/academic-info-level/{id}/delete',        ['as' => 'dynamic.academic-info-level.delete',           'middleware' => ['ability:super-admin,dynamic-academic-info-level-delete'],              'uses' => 'Dynamic\AcademicLevelController@delete']);
//     Route::get('dynamic/academic-info-level/{id}/active',        ['as' => 'dynamic.academic-info-level.active',           'middleware' => ['ability:super-admin,dynamic-academic-info-level-active'],              'uses' => 'Dynamic\AcademicLevelController@Active']);
//     Route::get('dynamic/academic-info-level/{id}/in-active',     ['as' => 'dynamic.academic-info-level.in-active',        'middleware' => ['ability:super-admin,dynamic-academic-info-level-in-active'],           'uses' => 'Dynamic\AcademicLevelController@inActive']);
//     Route::post('dynamic/academic-info-level/bulk-action',       ['as' => 'dynamic.academic-info-level.bulk-action',      'middleware' => ['ability:super-admin,academic-info-level-bulk-action'],                  'uses' => 'Dynamic\AcademicLevelController@bulkAction']);

//     /*Dynamic State Routes*/
//     Route::get('dynamic/state',                    ['as' => 'dynamic.state',                  'middleware' => ['ability:super-admin,dynamic-state-index'],               'uses' => 'Dynamic\StateController@index']);
//     Route::post('dynamic/state/store',             ['as' => 'dynamic.state.store',            'middleware' => ['ability:super-admin,dynamic-state-add'],                 'uses' => 'Dynamic\StateController@store']);
//     Route::get('dynamic/state/{id}/edit',          ['as' => 'dynamic.state.edit',             'middleware' => ['ability:super-admin,dynamic-state-edit'],                'uses' => 'Dynamic\StateController@edit']);
//     Route::post('dynamic/state/{id}/update',       ['as' => 'dynamic.state.update',           'middleware' => ['ability:super-admin,dynamic-state-edit'],                'uses' => 'Dynamic\StateController@update']);
//     Route::get('dynamic/state/{id}/delete',        ['as' => 'dynamic.state.delete',           'middleware' => ['ability:super-admin,dynamic-state-delete'],              'uses' => 'Dynamic\StateController@delete']);
//     Route::get('dynamic/state/{id}/active',        ['as' => 'dynamic.state.active',           'middleware' => ['ability:super-admin,dynamic-state-active'],              'uses' => 'Dynamic\StateController@Active']);
//     Route::get('dynamic/state/{id}/in-active',     ['as' => 'dynamic.state.in-active',        'middleware' => ['ability:super-admin,dynamic-state-in-active'],           'uses' => 'Dynamic\StateController@inActive']);
//     Route::post('dynamic/state/bulk-action',       ['as' => 'dynamic.state.bulk-action',      'middleware' => ['ability:super-admin,state-bulk-action'],                  'uses' => 'Dynamic\StateController@bulkAction']);

//     /*Dynamic Applications Routes*/
//     Route::get('dynamic/application-type',                    ['as' => 'dynamic.application-type',                  'middleware' => ['ability:super-admin,dynamic-application-type-index'],               'uses' => 'Dynamic\ApplicationTypeController@index']);
//     Route::post('dynamic/application-type/store',             ['as' => 'dynamic.application-type.store',            'middleware' => ['ability:super-admin,dynamic-application-type-add'],                 'uses' => 'Dynamic\ApplicationTypeController@store']);
//     Route::get('dynamic/application-type/{id}/edit',          ['as' => 'dynamic.application-type.edit',             'middleware' => ['ability:super-admin,dynamic-application-type-edit'],                'uses' => 'Dynamic\ApplicationTypeController@edit']);
//     Route::post('dynamic/application-type/{id}/update',       ['as' => 'dynamic.application-type.update',           'middleware' => ['ability:super-admin,dynamic-application-type-edit'],                'uses' => 'Dynamic\ApplicationTypeController@update']);
//     Route::get('dynamic/application-type/{id}/delete',        ['as' => 'dynamic.application-type.delete',           'middleware' => ['ability:super-admin,dynamic-application-type-delete'],              'uses' => 'Dynamic\ApplicationTypeController@delete']);
//     Route::get('dynamic/application-type/{id}/active',        ['as' => 'dynamic.application-type.active',           'middleware' => ['ability:super-admin,dynamic-application-type-active'],              'uses' => 'Dynamic\ApplicationTypeController@Active']);
//     Route::get('dynamic/application-type/{id}/in-active',     ['as' => 'dynamic.application-type.in-active',        'middleware' => ['ability:super-admin,dynamic-application-type-in-active'],           'uses' => 'Dynamic\ApplicationTypeController@inActive']);
//     Route::get('dynamic/application-type/{id}/need-approve-active',        ['as' => 'dynamic.application-type.need-approve-active',           'middleware' => ['ability:super-admin,dynamic-application-type-need-approve-active'],  'uses' => 'Dynamic\ApplicationTypeController@ApproveActive']);
//     Route::get('dynamic/application-type/{id}/need-approve-in-active',     ['as' => 'dynamic.application-type.need-approve-in-active',        'middleware' => ['ability:super-admin,dynamic-application-type-need-approve-in-active'],           'uses' => 'Dynamic\ApplicationTypeController@ApproveinActive']);
//     Route::post('dynamic/application-type/bulk-action',       ['as' => 'dynamic.application-type.bulk-action',      'middleware' => ['ability:super-admin,application-type-bulk-action'],                  'uses' => 'Dynamic\ApplicationTypeController@bulkAction']);

// });

/* Academic Grouping */
Route::group([
    'prefix'    => '/',
    'as'        => '',
    'namespace' => 'Academic\\',
], function () {

    // =======================
    // CLASS ROUTINE (ALL ROUTES)
    // =======================
    Route::prefix('routine')->name('routine.')->group(function () {
        // Manage (composite editor) + AJAX
        Route::get('manage',    ['as' => 'manage',    'middleware' => ['ability:super-admin,routine-manage'], 'uses' => 'ClassRoutineController@manage']);
        Route::get('schedules', ['as' => 'schedules', 'middleware' => ['ability:super-admin,routine-manage'], 'uses' => 'ClassRoutineController@getExistingSchedules']);
        Route::get('existing',  ['as' => 'existing',  'middleware' => ['ability:super-admin,routine-manage'], 'uses' => 'ClassRoutineController@getExistingSchedules']);
        Route::post('save',     ['as' => 'save',      'middleware' => ['ability:super-admin,routine-manage'], 'uses' => 'ClassRoutineController@save']);

        // Import / Export
        Route::get('import',           ['as' => 'import.form',     'middleware' => ['ability:super-admin,routine-import'], 'uses' => 'ClassRoutineController@importForm']);
        Route::post('import',          ['as' => 'import.store',    'middleware' => ['ability:super-admin,routine-import'], 'uses' => 'ClassRoutineController@importStore']);

        Route::get('export',           ['as' => 'export.form',     'middleware' => ['ability:super-admin,routine-export'], 'uses' => 'ClassRoutineController@exportForm']);
        Route::get('export/download',  ['as' => 'export.download', 'middleware' => ['ability:super-admin,routine-export'], 'uses' => 'ClassRoutineController@exportDownload']);
        Route::get('export/template',  ['as' => 'export.template', 'middleware' => ['ability:super-admin,routine-export'], 'uses' => 'ClassRoutineController@exportTemplate']);

        // Printing
        Route::prefix('print')->name('print.')->group(function () {
            Route::get('department/{departmentId}', ['as' => 'department', 'middleware' => ['ability:super-admin,routine-print'], 'uses' => 'ClassRoutineController@printDepartment'])
                ->where(['departmentId' => '[0-9]+']);

            Route::get('faculty/{departmentId}/{facultyId}', ['as' => 'faculty', 'middleware' => ['ability:super-admin,routine-print'], 'uses' => 'ClassRoutineController@printFaculty'])
                ->where(['departmentId' => '[0-9]+', 'facultyId' => '[0-9]+']);

            Route::get('semester/{departmentId}/{facultyId}/{semesterId}', ['as' => 'semester', 'middleware' => ['ability:super-admin,routine-print'], 'uses' => 'ClassRoutineController@printSemester'])
                ->where(['departmentId' => '[0-9]+', 'facultyId' => '[0-9]+', 'semesterId' => '[0-9]+']);

            Route::get('batch/{departmentId}/{batchId}', ['as' => 'batch', 'middleware' => ['ability:super-admin,routine-print'], 'uses' => 'ClassRoutineController@printBatch'])
                ->where(['departmentId' => '[0-9]+', 'batchId' => '[0-9]+']);

            Route::get('subject/{departmentId}/{facultyId}/{semesterId}/{subjectId}', ['as' => 'subject.all', 'middleware' => ['ability:super-admin,routine-print'], 'uses' => 'ClassRoutineController@printSubjectAllBatches'])
                ->where(['departmentId' => '[0-9]+', 'facultyId' => '[0-9]+', 'semesterId' => '[0-9]+', 'subjectId' => '[0-9]+']);

            Route::get('subject/{departmentId}/{facultyId}/{semesterId}/{subjectId}/batch/{batchId}', ['as' => 'subject.batch', 'middleware' => ['ability:super-admin,routine-print'], 'uses' => 'ClassRoutineController@printSubjectForBatch'])
                ->where(['departmentId' => '[0-9]+', 'facultyId' => '[0-9]+', 'semesterId' => '[0-9]+', 'subjectId' => '[0-9]+', 'batchId' => '[0-9]+']);
        });

        // Resource-like routes (explicit)
        Route::get('/',         ['as' => 'index',  'middleware' => ['ability:super-admin,routine-index'], 'uses' => 'ClassRoutineController@index']);
        Route::get('create',    ['as' => 'create', 'middleware' => ['ability:super-admin,routine-add'],   'uses' => 'ClassRoutineController@create']);
        Route::post('/',        ['as' => 'store',  'middleware' => ['ability:super-admin,routine-add'],   'uses' => 'ClassRoutineController@store']);

        Route::get('{id}',        ['as' => 'show',   'middleware' => ['ability:super-admin,routine-view'],  'uses' => 'ClassRoutineController@show'])->where(['id' => '[0-9]+']);
        Route::get('{id}/edit',   ['as' => 'edit',   'middleware' => ['ability:super-admin,routine-edit'],  'uses' => 'ClassRoutineController@edit'])->where(['id' => '[0-9]+']);
        Route::match(['put','patch'], '{id}', ['as' => 'update', 'middleware' => ['ability:super-admin,routine-edit'], 'uses' => 'ClassRoutineController@update'])->where(['id' => '[0-9]+']);
        Route::delete('{id}',     ['as' => 'destroy','middleware' => ['ability:super-admin,routine-delete'],'uses' => 'ClassRoutineController@destroy'])->where(['id' => '[0-9]+']);
    });

    // =======================
    // Academic AJAX Routes
    // =======================
    Route::get('get-departments', ['as' => 'get-departments', 'uses' => 'AcademicController@getDepartments']);
    Route::get('get-faculties',   ['as' => 'get-faculties',   'uses' => 'AcademicController@getFaculties']);
    Route::get('get-semesters',   ['as' => 'get-semesters',   'uses' => 'AcademicController@getSemesters']);
    Route::get('get-batches',     ['as' => 'get-batches',     'uses' => 'AcademicController@getBatches']);
    Route::get('get-subjects',    ['as' => 'get-subjects',    'uses' => 'AcademicController@getSubjects']);

    // =======================
    // Payment Method Routes
    // =======================
    Route::get('payment-method',                    ['as' => 'payment-method',                  'middleware' => ['ability:super-admin,payment-method-index'],               'uses' => 'PaymentMethodController@index']);
    Route::post('payment-method/store',             ['as' => 'payment-method.store',            'middleware' => ['ability:super-admin,payment-method-add'],                 'uses' => 'PaymentMethodController@store']);
    Route::get('payment-method/{id}/edit',          ['as' => 'payment-method.edit',             'middleware' => ['ability:super-admin,payment-method-edit'],                'uses' => 'PaymentMethodController@edit']);
    Route::post('payment-method/{id}/update',       ['as' => 'payment-method.update',           'middleware' => ['ability:super-admin,payment-method-edit'],                'uses' => 'PaymentMethodController@update']);
    Route::get('payment-method/{id}/delete',        ['as' => 'payment-method.delete',           'middleware' => ['ability:super-admin,payment-method-delete'],              'uses' => 'PaymentMethodController@delete']);
    Route::get('payment-method/{id}/active',        ['as' => 'payment-method.active',           'middleware' => ['ability:super-admin,payment-method-active'],              'uses' => 'PaymentMethodController@Active']);
    Route::get('payment-method/{id}/in-active',     ['as' => 'payment-method.in-active',        'middleware' => ['ability:super-admin,payment-method-in-active'],           'uses' => 'PaymentMethodController@inActive']);
    Route::post('payment-method/bulk-action',       ['as' => 'payment-method.bulk-action',      'middleware' => ['ability:super-admin,payment-method-bulk-action'],         'uses' => 'PaymentMethodController@bulkAction']);

    // =======================
    // Department Head Routes
    // =======================
    Route::get('department-head',                    ['as' => 'department-head',                  'middleware' => ['ability:super-admin,department-head-index'],            'uses' => 'DepartmentHeadController@index']);
    Route::post('department-head/store',             ['as' => 'department-head.store',            'middleware' => ['ability:super-admin,department-head-add'],              'uses' => 'DepartmentHeadController@store']);
    Route::get('department-head/{id}/edit',          ['as' => 'department-head.edit',             'middleware' => ['ability:super-admin,department-head-edit'],             'uses' => 'DepartmentHeadController@edit']);
    Route::post('department-head/{id}/update',       ['as' => 'department-head.update',           'middleware' => ['ability:super-admin,department-head-edit'],             'uses' => 'DepartmentHeadController@update']);
    Route::get('department-head/{id}/delete',        ['as' => 'department-head.delete',           'middleware' => ['ability:super-admin,department-head-delete'],           'uses' => 'DepartmentHeadController@delete']);
    Route::get('department-head/{id}/active',        ['as' => 'department-head.active',           'middleware' => ['ability:super-admin,department-head-active'],           'uses' => 'DepartmentHeadController@Active']);
    Route::get('department-head/{id}/in-active',     ['as' => 'department-head.in-active',        'middleware' => ['ability:super-admin,department-head-in-active'],        'uses' => 'DepartmentHeadController@inActive']);
    Route::post('department-head/bulk-action',       ['as' => 'department-head.bulk-action',      'middleware' => ['ability:super-admin,department-head-bulk-action'],      'uses' => 'DepartmentHeadController@bulkAction']);

    // =======================
    // Department Routes
    // =======================
    Route::get('department',                    ['as' => 'department',                  'middleware' => ['ability:super-admin,department-index'],            'uses' => 'DepartmentController@index']);
    Route::post('department/store',             ['as' => 'department.store',            'middleware' => ['ability:super-admin,department-add'],              'uses' => 'DepartmentController@store']);
    Route::get('department/{id}/edit',          ['as' => 'department.edit',             'middleware' => ['ability:super-admin,department-edit'],             'uses' => 'DepartmentController@edit']);
    Route::post('department/{id}/update',       ['as' => 'department.update',           'middleware' => ['ability:super-admin,department-edit'],             'uses' => 'DepartmentController@update']);
    Route::get('department/{id}/delete',        ['as' => 'department.delete',           'middleware' => ['ability:super-admin,department-delete'],           'uses' => 'DepartmentController@delete']);
    Route::get('department/{id}/active',        ['as' => 'department.active',           'middleware' => ['ability:super-admin,department-active'],           'uses' => 'DepartmentController@Active']);
    Route::get('department/{id}/in-active',     ['as' => 'department.in-active',        'middleware' => ['ability:super-admin,department-in-active'],        'uses' => 'DepartmentController@inActive']);
    Route::post('department/bulk-action',       ['as' => 'department.bulk-action',      'middleware' => ['ability:super-admin,department-bulk-action'],      'uses' => 'DepartmentController@bulkAction']);

    // =======================
    // Faculty Routes
    // =======================
    Route::get('faculty',                    ['as' => 'faculty',                  'middleware' => ['ability:super-admin,faculty-index'],            'uses' => 'FacultyController@index']);
    Route::post('faculty/store',             ['as' => 'faculty.store',            'middleware' => ['ability:super-admin,faculty-add'],              'uses' => 'FacultyController@store']);
    Route::get('faculty/{id}/edit',          ['as' => 'faculty.edit',             'middleware' => ['ability:super-admin,faculty-edit'],             'uses' => 'FacultyController@edit']);
    Route::post('faculty/{id}/update',       ['as' => 'faculty.update',           'middleware' => ['ability:super-admin,faculty-edit'],             'uses' => 'FacultyController@update']);
    Route::get('faculty/{id}/delete',        ['as' => 'faculty.delete',           'middleware' => ['ability:super-admin,faculty-delete'],           'uses' => 'FacultyController@delete']);
    Route::get('faculty/{id}/active',        ['as' => 'faculty.active',           'middleware' => ['ability:super-admin,faculty-active'],           'uses' => 'FacultyController@Active']);
    Route::get('faculty/{id}/in-active',     ['as' => 'faculty.in-active',        'middleware' => ['ability:super-admin,faculty-in-active'],        'uses' => 'FacultyController@inActive']);
    Route::post('faculty/bulk-action',       ['as' => 'faculty.bulk-action',      'middleware' => ['ability:super-admin,faculty-bulk-action'],      'uses' => 'FacultyController@bulkAction']);

    // =======================
    // Semester Routes
    // =======================
    Route::get('semester',                    ['as' => 'semester',                  'middleware' => ['ability:super-admin,semester-index'],             'uses' => 'SemesterController@index']);
    Route::post('semester/store',             ['as' => 'semester.store',            'middleware' => ['ability:super-admin,semester-add'],               'uses' => 'SemesterController@store']);
    Route::get('semester/{id}/edit',          ['as' => 'semester.edit',             'middleware' => ['ability:super-admin,semester-edit'],              'uses' => 'SemesterController@edit']);
    Route::post('semester/{id}/update',       ['as' => 'semester.update',           'middleware' => ['ability:super-admin,semester-edit'],              'uses' => 'SemesterController@update']);
    Route::get('semester/{id}/delete',        ['as' => 'semester.delete',           'middleware' => ['ability:super-admin,semester-delete'],            'uses' => 'SemesterController@delete']);
    Route::get('semester/{id}/active',        ['as' => 'semester.active',           'middleware' => ['ability:super-admin,semester-active'],            'uses' => 'SemesterController@Active']);
    Route::get('semester/{id}/in-active',     ['as' => 'semester.in-active',        'middleware' => ['ability:super-admin,semester-in-active'],         'uses' => 'SemesterController@inActive']);
    Route::post('semester/bulk-action',       ['as' => 'semester.bulk-action',      'middleware' => ['ability:super-admin,semester-bulk-action'],       'uses' => 'SemesterController@bulkAction']);
    Route::post('semester/subject-html',      ['as' => 'semester.subject-html',                                                                       'uses' => 'SemesterController@subjectHtmlRow']);

    // =======================
    // Student Batch Routes
    // =======================
    Route::get('student-batch',                    ['as' => 'student-batch',                  'middleware' => ['ability:super-admin,student-batch-index'],               'uses' => 'StudentBatchController@index']);
    Route::post('student-batch/store',             ['as' => 'student-batch.store',            'middleware' => ['ability:super-admin,student-batch-add'],                 'uses' => 'StudentBatchController@store']);
    Route::get('student-batch/{id}/edit',          ['as' => 'student-batch.edit',             'middleware' => ['ability:super-admin,student-batch-edit'],                'uses' => 'StudentBatchController@edit']);
    Route::post('student-batch/{id}/update',       ['as' => 'student-batch.update',           'middleware' => ['ability:super-admin,student-batch-edit'],                'uses' => 'StudentBatchController@update']);
    Route::get('student-batch/{id}/delete',        ['as' => 'student-batch.delete',           'middleware' => ['ability:super-admin,student-batch-delete'],              'uses' => 'StudentBatchController@delete']);
    Route::get('student-batch/{id}/active',        ['as' => 'student-batch.active',           'middleware' => ['ability:super-admin,student-batch-active'],              'uses' => 'StudentBatchController@Active']);
    Route::get('student-batch/{id}/in-active',     ['as' => 'student-batch.in-active',        'middleware' => ['ability:super-admin,student-batch-in-active'],           'uses' => 'StudentBatchController@inActive']);
    Route::post('student-batch/bulk-action',       ['as' => 'student-batch.bulk-action',      'middleware' => ['ability:super-admin,student-batch-bulk-action'],         'uses' => 'StudentBatchController@bulkAction']);
    Route::get('student-batch/{id}/active-status', ['as' => 'student-batch.active-status',    'middleware' => ['ability:super-admin,student-batch-active'],              'uses' => 'StudentBatchController@activeStatus']);

    // =======================
    // Grading Type & Scale
    // =======================
    Route::get('grading',                    ['as' => 'grading',                  'middleware' => ['ability:super-admin,grading-index'],                'uses' => 'GradingController@index']);
    Route::post('grading/store',             ['as' => 'grading.store',            'middleware' => ['ability:super-admin,grading-add'],                  'uses' => 'GradingController@store']);
    Route::get('grading/{id}/edit',          ['as' => 'grading.edit',             'middleware' => ['ability:super-admin,grading-edit'],                 'uses' => 'GradingController@edit']);
    Route::post('grading/{id}/update',       ['as' => 'grading.update',           'middleware' => ['ability:super-admin,grading-edit'],                 'uses' => 'GradingController@update']);
    Route::get('grading/{id}/delete',        ['as' => 'grading.delete',           'middleware' => ['ability:super-admin,grading-delete'],               'uses' => 'GradingController@delete']);
    Route::get('grading/{id}/active',        ['as' => 'grading.active',           'middleware' => ['ability:super-admin,grading-active'],               'uses' => 'GradingController@Active']);
    Route::get('grading/{id}/in-active',     ['as' => 'grading.in-active',        'middleware' => ['ability:super-admin,grading-in-active'],            'uses' => 'GradingController@inActive']);
    Route::post('grading/bulk-action',       ['as' => 'grading.bulk-action',      'middleware' => ['ability:super-admin,grading-bulk-action'],          'uses' => 'GradingController@bulkAction']);
    Route::post('grading/grade-html',        ['as' => 'grading.grade-html',                                                                         'uses' => 'GradingController@gradeHtmlRow']);

    // =======================
    // Subject
    // =======================
    Route::get('subject',                    ['as' => 'subject',                  'middleware' => ['ability:super-admin,subject-index'],                'uses' => 'SubjectController@index']);
    Route::post('subject/store',             ['as' => 'subject.store',            'middleware' => ['ability:super-admin,subject-add'],                  'uses' => 'SubjectController@store']);
    Route::get('subject/{id}/edit',          ['as' => 'subject.edit',             'middleware' => ['ability:super-admin,subject-edit'],                 'uses' => 'SubjectController@edit']);
    Route::post('subject/{id}/update',       ['as' => 'subject.update',           'middleware' => ['ability:super-admin,subject-edit'],                 'uses' => 'SubjectController@update']);
    Route::get('subject/{id}/delete',        ['as' => 'subject.delete',           'middleware' => ['ability:super-admin,subject-delete'],               'uses' => 'SubjectController@delete']);
    Route::get('subject/{id}/active',        ['as' => 'subject.active',           'middleware' => ['ability:super-admin,subject-active'],               'uses' => 'SubjectController@Active']);
    Route::get('subject/{id}/in-active',     ['as' => 'subject.in-active',        'middleware' => ['ability:super-admin,subject-in-active'],            'uses' => 'SubjectController@inActive']);
    Route::post('subject/bulk-action',       ['as' => 'subject.bulk-action',      'middleware' => ['ability:super-admin,subject-bulk-action'],          'uses' => 'SubjectController@bulkAction']);
    Route::get('subject-name-autocomplete',  ['as' => 'subject-name-autocomplete',                                                                      'uses' => 'SubjectController@subjectNameAutocomplete']);

    // =======================
    // Student Status Routes
    // =======================
    Route::get('student-status',                    ['as' => 'student-status',                  'middleware' => ['ability:super-admin,student-status-index'],               'uses' => 'StudentStatusController@index']);
    Route::post('student-status/store',             ['as' => 'student-status.store',            'middleware' => ['ability:super-admin,student-status-add'],                 'uses' => 'StudentStatusController@store']);
    Route::get('student-status/{id}/edit',          ['as' => 'student-status.edit',             'middleware' => ['ability:super-admin,student-status-edit'],                'uses' => 'StudentStatusController@edit']);
    Route::post('student-status/{id}/update',       ['as' => 'student-status.update',           'middleware' => ['ability:super-admin,student-status-edit'],                'uses' => 'StudentStatusController@update']);
    Route::get('student-status/{id}/delete',        ['as' => 'student-status.delete',           'middleware' => ['ability:super-admin,student-status-delete'],              'uses' => 'StudentStatusController@delete']);
    Route::get('student-status/{id}/active',        ['as' => 'student-status.active',           'middleware' => ['ability:super-admin,student-status-active'],              'uses' => 'StudentStatusController@Active']);
    Route::get('student-status/{id}/in-active',     ['as' => 'student-status.in-active',        'middleware' => ['ability:super-admin,student-status-in-active'],           'uses' => 'StudentStatusController@inActive']);
    Route::post('student-status/bulk-action',       ['as' => 'student-status.bulk-action',      'middleware' => ['ability:super-admin,student-status-bulk-action'],         'uses' => 'StudentStatusController@bulkAction']);

    // =======================
    // Attendance Status Routes
    // =======================
    Route::get('attendance-status',                    ['as' => 'attendance-status',                  'middleware' => ['ability:super-admin,attendance-status-index'],            'uses' => 'AttendanceStatusController@index']);
    Route::post('attendance-status/store',             ['as' => 'attendance-status.store',            'middleware' => ['ability:super-admin,attendance-status-add'],              'uses' => 'AttendanceStatusController@store']);
    Route::get('attendance-status/{id}/edit',          ['as' => 'attendance-status.edit',             'middleware' => ['ability:super-admin,attendance-status-edit'],             'uses' => 'AttendanceStatusController@edit']);
    Route::post('attendance-status/{id}/update',       ['as' => 'attendance-status.update',           'middleware' => ['ability:super-admin,attendance-status-edit'],             'uses' => 'AttendanceStatusController@update']);
    Route::get('attendance-status/{id}/delete',        ['as' => 'attendance-status.delete',           'middleware' => ['ability:super-admin,attendance-status-delete'],           'uses' => 'AttendanceStatusController@delete']);
    Route::get('attendance-status/{id}/active',        ['as' => 'attendance-status.active',           'middleware' => ['ability:super-admin,attendance-status-active'],           'uses' => 'AttendanceStatusController@Active']);
    Route::get('attendance-status/{id}/in-active',     ['as' => 'attendance-status.in-active',        'middleware' => ['ability:super-admin,attendance-status-in-active'],        'uses' => 'AttendanceStatusController@inActive']);
    Route::post('attendance-status/bulk-action',       ['as' => 'attendance-status.bulk-action',      'middleware' => ['ability:super-admin,attendance-status-bulk-action'],      'uses' => 'AttendanceStatusController@bulkAction']);

    // =======================
    // Book Status Routes
    // =======================
    Route::get('book-status',                    ['as' => 'book-status',                  'middleware' => ['ability:super-admin,book-status-index'],            'uses' => 'BookStatusController@index']);
    Route::post('book-status/store',             ['as' => 'book-status.store',            'middleware' => ['ability:super-admin,book-status-add'],              'uses' => 'BookStatusController@store']);
    Route::get('book-status/{id}/edit',          ['as' => 'book-status.edit',             'middleware' => ['ability:super-admin,book-status-edit'],             'uses' => 'BookStatusController@edit']);
    Route::post('book-status/{id}/update',       ['as' => 'book-status.update',           'middleware' => ['ability:super-admin,book-status-edit'],             'uses' => 'BookStatusController@update']);
    Route::get('book-status/{id}/delete',        ['as' => 'book-status.delete',           'middleware' => ['ability:super-admin,book-status-delete'],           'uses' => 'BookStatusController@delete']);
    Route::get('book-status/{id}/active',        ['as' => 'book-status.active',           'middleware' => ['ability:super-admin,book-status-active'],           'uses' => 'BookStatusController@Active']);
    Route::get('book-status/{id}/in-active',     ['as' => 'book-status.in-active',        'middleware' => ['ability:super-admin,book-status-in-active'],        'uses' => 'BookStatusController@inActive']);
    Route::post('book-status/bulk-action',       ['as' => 'book-status.bulk-action',      'middleware' => ['ability:super-admin,book-status-bulk-action'],      'uses' => 'BookStatusController@bulkAction']);

    // =======================
    // Hostel Room Beds Status Routes
    // =======================
    Route::get('bed-status',                    ['as' => 'bed-status',                  'middleware' => ['ability:super-admin,bed-status-index'],               'uses' => 'BedStatusController@index']);
    Route::post('bed-status/store',             ['as' => 'bed-status.store',            'middleware' => ['ability:super-admin,bed-status-add'],                 'uses' => 'BedStatusController@store']);
    Route::get('bed-status/{id}/edit',          ['as' => 'bed-status.edit',             'middleware' => ['ability:super-admin,bed-status-edit'],                'uses' => 'BedStatusController@edit']);
    Route::post('bed-status/{id}/update',       ['as' => 'bed-status.update',           'middleware' => ['ability:super-admin,bed-status-edit'],                'uses' => 'BedStatusController@update']);
    Route::get('bed-status/{id}/delete',        ['as' => 'bed-status.delete',           'middleware' => ['ability:super-admin,bed-status-delete'],              'uses' => 'BedStatusController@delete']);
    Route::get('bed-status/{id}/active',        ['as' => 'bed-status.active',           'middleware' => ['ability:super-admin,bed-status-active'],              'uses' => 'BedStatusController@Active']);
    Route::get('bed-status/{id}/in-active',     ['as' => 'bed-status.in-active',        'middleware' => ['ability:super-admin,bed-status-in-active'],           'uses' => 'BedStatusController@inActive']);
    Route::post('bed-status/bulk-action',       ['as' => 'bed-status.bulk-action',      'middleware' => ['ability:super-admin,bed-status-bulk-action'],         'uses' => 'BedStatusController@bulkAction']);

    // =======================
    // Customer Status Routes
    // =======================
    Route::get('customer-status',                    ['as' => 'customer-status',                  'middleware' => ['ability:super-admin,customer-status-index'],               'uses' => 'CustomerStatusController@index']);
    Route::post('customer-status/store',             ['as' => 'customer-status.store',            'middleware' => ['ability:super-admin,customer-status-add'],                 'uses' => 'CustomerStatusController@store']);
    Route::get('customer-status/{id}/edit',          ['as' => 'customer-status.edit',             'middleware' => ['ability:super-admin,customer-status-edit'],                'uses' => 'CustomerStatusController@edit']);
    Route::post('customer-status/{id}/update',       ['as' => 'customer-status.update',           'middleware' => ['ability:super-admin,customer-status-edit'],                'uses' => 'CustomerStatusController@update']);
    Route::get('customer-status/{id}/delete',        ['as' => 'customer-status.delete',           'middleware' => ['ability:super-admin,customer-status-delete'],              'uses' => 'CustomerStatusController@delete']);
    Route::get('customer-status/{id}/active',        ['as' => 'customer-status.active',           'middleware' => ['ability:super-admin,customer-status-active'],              'uses' => 'CustomerStatusController@Active']);
    Route::get('customer-status/{id}/in-active',     ['as' => 'customer-status.in-active',        'middleware' => ['ability:super-admin,customer-status-in-active'],           'uses' => 'CustomerStatusController@inActive']);
    Route::post('customer-status/bulk-action',       ['as' => 'customer-status.bulk-action',      'middleware' => ['ability:super-admin,customer-status-bulk-action'],         'uses' => 'CustomerStatusController@bulkAction']);

    // =======================
    // Year Routes
    // =======================
    Route::get('year',                    ['as' => 'year',                  'middleware' => ['ability:super-admin,year-index'],            'uses' => 'YearsController@index']);
    Route::post('year/store',             ['as' => 'year.store',            'middleware' => ['ability:super-admin,year-add'],              'uses' => 'YearsController@store']);
    Route::get('year/{id}/edit',          ['as' => 'year.edit',             'middleware' => ['ability:super-admin,year-edit'],             'uses' => 'YearsController@edit']);
    Route::post('year/{id}/update',       ['as' => 'year.update',           'middleware' => ['ability:super-admin,year-edit'],             'uses' => 'YearsController@update']);
    Route::get('year/{id}/delete',        ['as' => 'year.delete',           'middleware' => ['ability:super-admin,year-delete'],           'uses' => 'YearsController@delete']);
    Route::get('year/{id}/active',        ['as' => 'year.active',           'middleware' => ['ability:super-admin,year-active'],           'uses' => 'YearsController@Active']);
    Route::get('year/{id}/in-active',     ['as' => 'year.in-active',        'middleware' => ['ability:super-admin,year-in-active'],        'uses' => 'YearsController@inActive']);
    Route::post('year/bulk-action',       ['as' => 'year.bulk-action',      'middleware' => ['ability:super-admin,year-bulk-action'],      'uses' => 'YearsController@bulkAction']);
    Route::get('year/{id}/active-status', ['as' => 'year.active-status',    'middleware' => ['ability:super-admin,year-active-status'],    'uses' => 'YearsController@activeStatus']);

    // =======================
    // Months Routes
    // =======================
    Route::get('month',                    ['as' => 'month',                  'middleware' => ['ability:super-admin,month-index'],          'uses' => 'MonthsController@index']);
    Route::post('month/store',             ['as' => 'month.store',            'middleware' => ['ability:super-admin,month-add'],            'uses' => 'MonthsController@store']);
    Route::get('month/{id}/edit',          ['as' => 'month.edit',             'middleware' => ['ability:super-admin,month-edit'],           'uses' => 'MonthsController@edit']);
    Route::post('month/{id}/update',       ['as' => 'month.update',           'middleware' => ['ability:super-admin,month-edit'],           'uses' => 'MonthsController@update']);
    Route::get('month/{id}/delete',        ['as' => 'month.delete',           'middleware' => ['ability:super-admin,month-delete'],         'uses' => 'MonthsController@delete']);
    Route::get('month/{id}/active',        ['as' => 'month.active',           'middleware' => ['ability:super-admin,month-active'],         'uses' => 'MonthsController@Active']);
    Route::get('month/{id}/in-active',     ['as' => 'month.in-active',        'middleware' => ['ability:super-admin,month-in-active'],      'uses' => 'MonthsController@inActive']);
    Route::post('month/bulk-action',       ['as' => 'month.bulk-action',      'middleware' => ['ability:super-admin,month-bulk-action'],    'uses' => 'MonthsController@bulkAction']);

    // =======================
    // Day Routes
    // =======================
    Route::get('day',                    ['as' => 'day',                  'middleware' => ['ability:super-admin,day-index'],            'uses' => 'DayController@index']);
    Route::post('day/store',             ['as' => 'day.store',            'middleware' => ['ability:super-admin,day-add'],              'uses' => 'DayController@store']);
    Route::get('day/{id}/edit',          ['as' => 'day.edit',             'middleware' => ['ability:super-admin,day-edit'],             'uses' => 'DayController@edit']);
    Route::post('day/{id}/update',       ['as' => 'day.update',           'middleware' => ['ability:super-admin,day-edit'],             'uses' => 'DayController@update']);
    Route::get('day/{id}/delete',        ['as' => 'day.delete',           'middleware' => ['ability:super-admin,day-delete'],           'uses' => 'DayController@delete']);
    Route::get('day/{id}/active',        ['as' => 'day.active',           'middleware' => ['ability:super-admin,day-active'],           'uses' => 'DayController@Active']);
    Route::get('day/{id}/in-active',     ['as' => 'day.in-active',        'middleware' => ['ability:super-admin,day-in-active'],        'uses' => 'DayController@inActive']);
    Route::post('day/bulk-action',       ['as' => 'day.bulk-action',      'middleware' => ['ability:super-admin,day-bulk-action'],      'uses' => 'DayController@bulkAction']);

    // =======================
    // Dynamic Degree Routes
    // =======================
    Route::get('dynamic/degree',                    ['as' => 'dynamic.degree',                  'middleware' => ['ability:super-admin,dynamic-degree-index'],               'uses' => 'Dynamic\DegreeController@index']);
    Route::post('dynamic/degree/store',             ['as' => 'dynamic.degree.store',            'middleware' => ['ability:super-admin,dynamic-degree-add'],                 'uses' => 'Dynamic\DegreeController@store']);
    Route::get('dynamic/degree/{id}/edit',          ['as' => 'dynamic.degree.edit',             'middleware' => ['ability:super-admin,dynamic-degree-edit'],                'uses' => 'Dynamic\DegreeController@edit']);
    Route::post('dynamic/degree/{id}/update',       ['as' => 'dynamic.degree.update',           'middleware' => ['ability:super-admin,dynamic-degree-edit'],                'uses' => 'Dynamic\DegreeController@update']);
    Route::get('dynamic/degree/{id}/delete',        ['as' => 'dynamic.degree.delete',           'middleware' => ['ability:super-admin,dynamic-degree-delete'],              'uses' => 'Dynamic\DegreeController@delete']);
    Route::get('dynamic/degree/{id}/active',        ['as' => 'dynamic.degree.active',           'middleware' => ['ability:super-admin,dynamic-degree-active'],              'uses' => 'Dynamic\DegreeController@Active']);
    Route::get('dynamic/degree/{id}/in-active',     ['as' => 'dynamic.degree.in-active',        'middleware' => ['ability:super-admin,dynamic-degree-in-active'],           'uses' => 'Dynamic\DegreeController@inActive']);
    Route::post('dynamic/degree/bulk-action',       ['as' => 'dynamic.degree.bulk-action',      'middleware' => ['ability:super-admin,degree-bulk-action'],                 'uses' => 'Dynamic\DegreeController@bulkAction']);

    // =======================
    // Dynamic Scholarship Routes
    // =======================
    Route::get('dynamic/scholarship',                    ['as' => 'dynamic.scholarship',                  'middleware' => ['ability:super-admin,dynamic-scholarship-index'],               'uses' => 'Dynamic\ScholarshipController@index']);
    Route::post('dynamic/scholarship/store',             ['as' => 'dynamic.scholarship.store',            'middleware' => ['ability:super-admin,dynamic-scholarship-add'],                 'uses' => 'Dynamic\ScholarshipController@store']);
    Route::get('dynamic/scholarship/{id}/edit',          ['as' => 'dynamic.scholarship.edit',             'middleware' => ['ability:super-admin,dynamic-scholarship-edit'],                'uses' => 'Dynamic\ScholarshipController@edit']);
    Route::post('dynamic/scholarship/{id}/update',       ['as' => 'dynamic.scholarship.update',           'middleware' => ['ability:super-admin,dynamic-scholarship-edit'],                'uses' => 'Dynamic\ScholarshipController@update']);
    Route::get('dynamic/scholarship/{id}/delete',        ['as' => 'dynamic.scholarship.delete',           'middleware' => ['ability:super-admin,dynamic-scholarship-delete'],              'uses' => 'Dynamic\ScholarshipController@delete']);
    Route::get('dynamic/scholarship/{id}/active',        ['as' => 'dynamic.scholarship.active',           'middleware' => ['ability:super-admin,dynamic-scholarship-active'],              'uses' => 'Dynamic\ScholarshipController@Active']);
    Route::get('dynamic/scholarship/{id}/in-active',     ['as' => 'dynamic.scholarship.in-active',        'middleware' => ['ability:super-admin,dynamic-scholarship-in-active'],           'uses' => 'Dynamic\ScholarshipController@inActive']);
    Route::post('dynamic/scholarship/bulk-action',       ['as' => 'dynamic.scholarship.bulk-action',      'middleware' => ['ability:super-admin,scholarship-bulk-action'],                  'uses' => 'Dynamic\ScholarshipController@bulkAction']);

    // =======================
    // Dynamic Placement Routes
    // =======================
    Route::get('dynamic/placement',                    ['as' => 'dynamic.placement',                  'middleware' => ['ability:super-admin,dynamic-placement-index'],               'uses' => 'Dynamic\PlacementController@index']);
    Route::post('dynamic/placement/store',             ['as' => 'dynamic.placement.store',            'middleware' => ['ability:super-admin,dynamic-placement-add'],                 'uses' => 'Dynamic\PlacementController@store']);
    Route::get('dynamic/placement/{id}/edit',          ['as' => 'dynamic.placement.edit',             'middleware' => ['ability:super-admin,dynamic-placement-edit'],                'uses' => 'Dynamic\PlacementController@edit']);
    Route::post('dynamic/placement/{id}/update',       ['as' => 'dynamic.placement.update',           'middleware' => ['ability:super-admin,dynamic-placement-edit'],                'uses' => 'Dynamic\PlacementController@update']);
    Route::get('dynamic/placement/{id}/delete',        ['as' => 'dynamic.placement.delete',           'middleware' => ['ability:super-admin,dynamic-placement-delete'],              'uses' => 'Dynamic\PlacementController@delete']);
    Route::get('dynamic/placement/{id}/active',        ['as' => 'dynamic.placement.active',           'middleware' => ['ability:super-admin,dynamic-placement-active'],              'uses' => 'Dynamic\PlacementController@Active']);
    Route::get('dynamic/placement/{id}/in-active',     ['as' => 'dynamic.placement.in-active',        'middleware' => ['ability:super-admin,dynamic-placement-in-active'],           'uses' => 'Dynamic\PlacementController@inActive']);
    Route::post('dynamic/placement/bulk-action',       ['as' => 'dynamic.placement.bulk-action',      'middleware' => ['ability:super-admin,placement-bulk-action'],                  'uses' => 'Dynamic\PlacementController@bulkAction']);

    // =======================
    // Dynamic Annexure Routes
    // =======================
    Route::get('dynamic/annexure',                    ['as' => 'dynamic.annexure',                  'middleware' => ['ability:super-admin,dynamic-annexure-index'],               'uses' => 'Dynamic\AnnexureController@index']);
    Route::post('dynamic/annexure/store',             ['as' => 'dynamic.annexure.store',            'middleware' => ['ability:super-admin,dynamic-annexure-add'],                 'uses' => 'Dynamic\AnnexureController@store']);
    Route::get('dynamic/annexure/{id}/edit',          ['as' => 'dynamic.annexure.edit',             'middleware' => ['ability:super-admin,dynamic-annexure-edit'],                'uses' => 'Dynamic\AnnexureController@edit']);
    Route::post('dynamic/annexure/{id}/update',       ['as' => 'dynamic.annexure.update',           'middleware' => ['ability:super-admin,dynamic-annexure-edit'],                'uses' => 'Dynamic\AnnexureController@update']);
    Route::get('dynamic/annexure/{id}/delete',        ['as' => 'dynamic.annexure.delete',           'middleware' => ['ability:super-admin,dynamic-annexure-delete'],              'uses' => 'Dynamic\AnnexureController@delete']);
    Route::get('dynamic/annexure/{id}/active',        ['as' => 'dynamic.annexure.active',           'middleware' => ['ability:super-admin,dynamic-annexure-active'],              'uses' => 'Dynamic\AnnexureController@Active']);
    Route::get('dynamic/annexure/{id}/in-active',     ['as' => 'dynamic.annexure.in-active',        'middleware' => ['ability:super-admin,dynamic-annexure-in-active'],           'uses' => 'Dynamic\AnnexureController@inActive']);
    Route::post('dynamic/annexure/bulk-action',       ['as' => 'dynamic.annexure.bulk-action',      'middleware' => ['ability:super-admin,annexure-bulk-action'],                  'uses' => 'Dynamic\AnnexureController@bulkAction']);

    // =======================
    // Dynamic AcademicInfo Routes
    // =======================
    Route::get('dynamic/academic-info-level',                    ['as' => 'dynamic.academic-info-level',                  'middleware' => ['ability:super-admin,dynamic-academic-info-level-index'],               'uses' => 'Dynamic\AcademicLevelController@index']);
    Route::post('dynamic/academic-info-level/store',             ['as' => 'dynamic.academic-info-level.store',            'middleware' => ['ability:super-admin,dynamic-academic-info-level-add'],                 'uses' => 'Dynamic\AcademicLevelController@store']);
    Route::get('dynamic/academic-info-level/{id}/edit',          ['as' => 'dynamic.academic-info-level.edit',             'middleware' => ['ability:super-admin,dynamic-academic-info-level-edit'],                'uses' => 'Dynamic\AcademicLevelController@edit']);
    Route::post('dynamic/academic-info-level/{id}/update',       ['as' => 'dynamic.academic-info-level.update',           'middleware' => ['ability:super-admin,dynamic-academic-info-level-edit'],                'uses' => 'Dynamic\AcademicLevelController@update']);
    Route::get('dynamic/academic-info-level/{id}/delete',        ['as' => 'dynamic.academic-info-level.delete',           'middleware' => ['ability:super-admin,dynamic-academic-info-level-delete'],              'uses' => 'Dynamic\AcademicLevelController@delete']);
    Route::get('dynamic/academic-info-level/{id}/active',        ['as' => 'dynamic.academic-info-level.active',           'middleware' => ['ability:super-admin,dynamic-academic-info-level-active'],              'uses' => 'Dynamic\AcademicLevelController@Active']);
    Route::get('dynamic/academic-info-level/{id}/in-active',     ['as' => 'dynamic.academic-info-level.in-active',        'middleware' => ['ability:super-admin,dynamic-academic-info-level-in-active'],           'uses' => 'Dynamic\AcademicLevelController@inActive']);
    Route::post('dynamic/academic-info-level/bulk-action',       ['as' => 'dynamic.academic-info-level.bulk-action',      'middleware' => ['ability:super-admin,academic-info-level-bulk-action'],                  'uses' => 'Dynamic\AcademicLevelController@bulkAction']);

    // =======================
    // Dynamic State Routes
    // =======================
    Route::get('dynamic/state',                    ['as' => 'dynamic.state',                  'middleware' => ['ability:super-admin,dynamic-state-index'],               'uses' => 'Dynamic\StateController@index']);
    Route::post('dynamic/state/store',             ['as' => 'dynamic.state.store',            'middleware' => ['ability:super-admin,dynamic-state-add'],                 'uses' => 'Dynamic\StateController@store']);
    Route::get('dynamic/state/{id}/edit',          ['as' => 'dynamic.state.edit',             'middleware' => ['ability:super-admin,dynamic-state-edit'],                'uses' => 'Dynamic\StateController@edit']);
    Route::post('dynamic/state/{id}/update',       ['as' => 'dynamic.state.update',           'middleware' => ['ability:super-admin,dynamic-state-edit'],                'uses' => 'Dynamic\StateController@update']);
    Route::get('dynamic/state/{id}/delete',        ['as' => 'dynamic.state.delete',           'middleware' => ['ability:super-admin,dynamic-state-delete'],              'uses' => 'Dynamic\StateController@delete']);
    Route::get('dynamic/state/{id}/active',        ['as' => 'dynamic.state.active',           'middleware' => ['ability:super-admin,dynamic-state-active'],              'uses' => 'Dynamic\StateController@Active']);
    Route::get('dynamic/state/{id}/in-active',     ['as' => 'dynamic.state.in-active',        'middleware' => ['ability:super-admin,dynamic-state-in-active'],           'uses' => 'Dynamic\StateController@inActive']);
    Route::post('dynamic/state/bulk-action',       ['as' => 'dynamic.state.bulk-action',      'middleware' => ['ability:super-admin,state-bulk-action'],                  'uses' => 'Dynamic\StateController@bulkAction']);

    // =======================
    // Dynamic Applications Routes
    // =======================
    Route::get('dynamic/application-type',                    ['as' => 'dynamic.application-type',                  'middleware' => ['ability:super-admin,dynamic-application-type-index'],               'uses' => 'Dynamic\ApplicationTypeController@index']);
    Route::post('dynamic/application-type/store',             ['as' => 'dynamic.application-type.store',            'middleware' => ['ability:super-admin,dynamic-application-type-add'],                 'uses' => 'Dynamic\ApplicationTypeController@store']);
    Route::get('dynamic/application-type/{id}/edit',          ['as' => 'dynamic.application-type.edit',             'middleware' => ['ability:super-admin,dynamic-application-type-edit'],                'uses' => 'Dynamic\ApplicationTypeController@edit']);
    Route::post('dynamic/application-type/{id}/update',       ['as' => 'dynamic.application-type.update',           'middleware' => ['ability:super-admin,dynamic-application-type-edit'],                'uses' => 'Dynamic\ApplicationTypeController@update']);
    Route::get('dynamic/application-type/{id}/delete',        ['as' => 'dynamic.application-type.delete',           'middleware' => ['ability:super-admin,dynamic-application-type-delete'],              'uses' => 'Dynamic\ApplicationTypeController@delete']);
    Route::get('dynamic/application-type/{id}/active',        ['as' => 'dynamic.application-type.active',           'middleware' => ['ability:super-admin,dynamic-application-type-active'],              'uses' => 'Dynamic\ApplicationTypeController@Active']);
    Route::get('dynamic/application-type/{id}/in-active',     ['as' => 'dynamic.application-type.in-active',        'middleware' => ['ability:super-admin,dynamic-application-type-in-active'],           'uses' => 'Dynamic\ApplicationTypeController@inActive']);
    Route::get('dynamic/application-type/{id}/need-approve-active',    ['as' => 'dynamic.application-type.need-approve-active',    'middleware' => ['ability:super-admin,dynamic-application-type-need-approve-active'],    'uses' => 'Dynamic\ApplicationTypeController@ApproveActive']);
    Route::get('dynamic/application-type/{id}/need-approve-in-active', ['as' => 'dynamic.application-type.need-approve-in-active', 'middleware' => ['ability:super-admin,dynamic-application-type-need-approve-in-active'], 'uses' => 'Dynamic\ApplicationTypeController@ApproveinActive']);
    Route::post('dynamic/application-type/bulk-action',       ['as' => 'dynamic.application-type.bulk-action',      'middleware' => ['ability:super-admin,application-type-bulk-action'],                  'uses' => 'Dynamic\ApplicationTypeController@bulkAction']);
});


/*Setting Grouping */
Route::group(['prefix' => 'setting/',                                   'as' => 'setting',                                      'namespace' => 'Setting\\'], function () {
    /* General Setting Routes */
    Route::get('general',                    ['as' => '.general',                  'middleware' => ['ability:super-admin,general-setting-index'],           'uses' => 'GeneralSettingController@index']);
    Route::get('general/add',                ['as' => '.general.add',              'middleware' => ['ability:super-admin,general-setting-add'],             'uses' => 'GeneralSettingController@add']);
    Route::post('general/store',             ['as' => '.general.store',            'middleware' => ['ability:super-admin,general-setting-add'],             'uses' => 'GeneralSettingController@store']);
    Route::get('general/{id}/edit',          ['as' => '.general.edit',             'middleware' => ['ability:super-admin,general-setting-edit'],            'uses' => 'GeneralSettingController@edit']);
    Route::post('general/{id}/update',       ['as' => '.general.update',           'middleware' => ['ability:super-admin,general-setting-edit'],            'uses' => 'GeneralSettingController@update']);

    /*Registration Section*/
    Route::get('online-registration',                       ['as' => '.online-registration',                     'middleware' => ['ability:super-admin|admin,web-setting-registration-index'],            'uses' => 'OnlineRegistrationSettingController@index']);
    Route::get('online-registration/add',                   ['as' => '.online-registration.add',                 'middleware' => ['ability:super-admin|admin,web-setting-registration-add'],              'uses' => 'OnlineRegistrationSettingController@add']);
    Route::post('online-registration/store',                ['as' => '.online-registration.store',               'middleware' => ['ability:super-admin|admin,web-setting-registration-add'],              'uses' => 'OnlineRegistrationSettingController@store']);
    Route::get('online-registration/{id}/edit',             ['as' => '.online-registration.edit',                'middleware' => ['ability:super-admin|admin,web-setting-registration-edit'],             'uses' => 'OnlineRegistrationSettingController@edit']);
    Route::post('online-registration/{id}/update',          ['as' => '.online-registration.update',              'middleware' => ['ability:super-admin|admin,web-setting-registration-edit'],             'uses' => 'OnlineRegistrationSettingController@update']);

    Route::post('online-registration/program-html',        ['as' => '.online-registration.program-html',                                                                         'uses' => 'OnlineRegistrationSettingController@programHtml']);
    Route::get('online-registration/remove-program',        ['as' => '.online-registration.remove-program',       'middleware' => ['ability:super-admin|admin,web-setting-registration-edit'],     'uses' => 'OnlineRegistrationSettingController@removeProgram']);
    Route::post('online-registration/find-semester',        ['as' => '.online-registration.find-semester',                                                                       'uses' => 'OnlineRegistrationSettingController@findSemester']);

    /* Online Registration Student Management Routes */
    Route::get('online-registration-student',              ['as' => '.online-registration-student',                         'middleware' => ['ability:super-admin|admin,web-setting-registration-index'],            'uses' => 'OnlineRegistrationStudentController@index']);
    Route::get('online-registration-student/{id}',         ['as' => '.online-registration-student.show',                    'middleware' => ['ability:super-admin|admin,web-setting-registration-index'],            'uses' => 'OnlineRegistrationStudentController@show']);
    Route::post('online-registration-student/{id}/payment', ['as' => '.online-registration-student.payment',                'middleware' => ['ability:super-admin|admin,web-setting-registration-index'],            'uses' => 'OnlineRegistrationStudentController@initiatePayment']);

    /* Alert Setting Routes */
    Route::get('alert',                    ['as' => '.alert',                   'middleware' => ['ability:super-admin,alert-setting-index'],        'uses' => 'AlertSettingController@index']);
    Route::get('alert/add',                ['as' => '.alert.add',               'middleware' => ['ability:super-admin,alert-setting-add'],          'uses' => 'AlertSettingController@add']);
    Route::post('alert/store',             ['as' => '.alert.store',             'middleware' => ['ability:super-admin,alert-setting-add'],          'uses' => 'AlertSettingController@store']);
    Route::get('alert/{id}/edit',          ['as' => '.alert.edit',              'middleware' => ['ability:super-admin,alert-setting-edit'],         'uses' => 'AlertSettingController@edit']);
    Route::post('alert/{id}/update',       ['as' => '.alert.update',            'middleware' => ['ability:super-admin,alert-setting-edit'],         'uses' => 'AlertSettingController@update']);

    /* SMS Setting Routes */
    Route::get('sms',                    ['as' => '.sms',                           'middleware' => ['ability:super-admin,sms-setting-index'],              'uses' => 'SmsSettingController@index']);
    Route::get('sms/add',                ['as' => '.sms.add',                       'middleware' => ['ability:super-admin,sms-setting-add'],                'uses' => 'SmsSettingController@add']);
    Route::post('sms/store',             ['as' => '.sms.store',                     'middleware' => ['ability:super-admin,sms-setting-add'],                'uses' => 'SmsSettingController@store']);
    Route::get('sms/{id}/edit',          ['as' => '.sms.edit',                      'middleware' => ['ability:super-admin,sms-setting-edit'],               'uses' => 'SmsSettingController@edit']);
    Route::post('sms/{id}/update',       ['as' => '.sms.update',                    'middleware' => ['ability:super-admin,sms-setting-edit'],               'uses' => 'SmsSettingController@update']);
    Route::get('sms/{id}/active',        ['as' => '.sms.active',                    'middleware' => ['ability:super-admin,sms-setting-active'],                      'uses' => 'SmsSettingController@Active']);
    Route::get('sms/{id}/in-active',     ['as' => '.sms.in-active',                 'middleware' => ['ability:super-admin,sms-setting-in-active'],                  'uses' => 'SmsSettingController@inActive']);

    /* Email Setting Routes */
    Route::get('email',                    ['as' => '.email',                       'middleware' => ['ability:super-admin,email-setting-index'],                'uses' => 'EmailSettingController@index']);
    Route::get('email/add',                ['as' => '.email.add',                   'middleware' => ['ability:super-admin,email-setting-add'],                  'uses' => 'EmailSettingController@add']);
    Route::post('email/store',             ['as' => '.email.store',                 'middleware' => ['ability:super-admin,email-setting-add'],                  'uses' => 'EmailSettingController@store']);
    Route::get('email/{id}/edit',          ['as' => '.email.edit',                  'middleware' => ['ability:super-admin,email-setting-edit'],                 'uses' => 'EmailSettingController@edit']);
    Route::post('email/{id}/update',       ['as' => '.email.update',                'middleware' => ['ability:super-admin,email-setting-edit'],                 'uses' => 'EmailSettingController@update']);
    Route::post('email/change-status',     ['as' => '.email.change-status',         'middleware' => ['ability:super-admin,email-setting-status-change'],        'uses' => 'EmailSettingController@statusChange']);

    /* Payment Gateway Setting Routes */
    Route::get('payment-gateway',                    ['as' => '.payment-gateway',                       'middleware' => ['ability:super-admin,payment-gateway-setting-index'],                'uses' => 'PaymentSettingController@index']);
    Route::get('payment-gateway/add',                ['as' => '.payment-gateway.add',                   'middleware' => ['ability:super-admin,payment-gateway-setting-add'],                  'uses' => 'PaymentSettingController@add']);
    Route::post('payment-gateway/store',             ['as' => '.payment-gateway.store',                 'middleware' => ['ability:super-admin,payment-gateway-setting-add'],                  'uses' => 'PaymentSettingController@store']);
    Route::get('payment-gateway/{id}/edit',          ['as' => '.payment-gateway.edit',                  'middleware' => ['ability:super-admin,payment-gateway-setting-edit'],                 'uses' => 'PaymentSettingController@edit']);
    Route::post('payment-gateway/{id}/update',       ['as' => '.payment-gateway.update',                'middleware' => ['ability:super-admin,payment-gateway-setting-edit'],                 'uses' => 'PaymentSettingController@update']);
    Route::get('payment-gateway/{id}/active',        ['as' => '.payment-gateway.active',                 'middleware' => ['ability:super-admin,payment-gateway-active'],                       'uses' => 'PaymentSettingController@Active']);
    Route::get('payment-gateway/{id}/in-active',     ['as' => '.payment-gateway.in-active',              'middleware' => ['ability:super-admin,payment-gateway-in-active'],                     'uses' => 'PaymentSettingController@inActive']);

    /* SMS Setting Routes */
    Route::get('meeting',                    ['as' => '.meeting',                           'middleware' => ['ability:super-admin,meeting-setting-index'],              'uses' => 'MeetingSettingController@index']);
    Route::get('meeting/add',                ['as' => '.meeting.add',                       'middleware' => ['ability:super-admin,meeting-setting-add'],                'uses' => 'MeetingSettingController@add']);
    Route::post('meeting/store',             ['as' => '.meeting.store',                     'middleware' => ['ability:super-admin,meeting-setting-add'],                'uses' => 'MeetingSettingController@store']);
    Route::get('meeting/{id}/edit',          ['as' => '.meeting.edit',                      'middleware' => ['ability:super-admin,meeting-setting-edit'],               'uses' => 'MeetingSettingController@edit']);
    Route::post('meeting/{id}/update',       ['as' => '.meeting.update',                    'middleware' => ['ability:super-admin,meeting-setting-edit'],               'uses' => 'MeetingSettingController@update']);
    Route::get('meeting/{id}/active',        ['as' => '.meeting.active',                    'middleware' => ['ability:super-admin,meeting-setting-active'],              'uses' => 'MeetingSettingController@Active']);
    Route::get('meeting/{id}/in-active',     ['as' => '.meeting.in-active',                 'middleware' => ['ability:super-admin,meeting-setting-in-active'],           'uses' => 'MeetingSettingController@inActive']);

});



// Callback URLs from SSLCommerz - Umesh
// use App\Http\Controllers\Account\Fees\Payment\SslCommerzPaymentController;



// // Callback/Redirect Routes from SSLCommerz
// Route::middleware(['web'])->group(function () {
//     // Main Payment Form Routes
//     Route::prefix('fees/sslcommerz')->name('fees.sslcommerz.')->group(function () {
//         Route::get('/', [SslCommerzPaymentController::class, 'index'])->name('index');        // Show payment form
//         Route::post('/', [SslCommerzPaymentController::class, 'pay'])->name('initiate');      // Initiate payment
//     });

//     // IPN Endpoint (Instant Payment Notification from SSLCommerz)
//     Route::post('/sslcommerz-ipn', [SslCommerzPaymentController::class, 'ipn'])->name('fees.sslcommerz.ipn');
//     // Success, Fail, and Cancel Callbacks
//     // These routes are called by SSLCommerz after payment processing
//     Route::post('/sslcommerz-success', [SslCommerzPaymentController::class, 'success'])->name('fees.sslcommerz.success');
//     Route::post('/sslcommerz-fail', [SslCommerzPaymentController::class, 'fail'])->name('fees.sslcommerz.fail');
//     Route::post('/sslcommerz-cancel', [SslCommerzPaymentController::class, 'cancel'])->name('fees.sslcommerz.cancel');
// });

 
    

// Route::get('/ucb/payment-form', 'Account\Fees\Payment\UcbPaymentController@paymentForm')->name('ucb.payment.form');
// Route::post('/ucb/payment-confirmation', 'Account\Fees\Payment\UcbPaymentController@paymentConfirmation')->name('ucb.payment.confirmation');
// Route::post('/ucb/payment-receipt', 'Account\Fees\Payment\UcbPaymentController@paymentReceipt')->name('ucb.payment.receipt');
use App\Http\Controllers\Account\Fees\Payment\UcbPaymentController;

// 1. Show payment form to user (GET)
Route::get('/ucb/payment-form', [UcbPaymentController::class, 'paymentForm'])
    ->name('ucb.payment.form');

// 2. Callback/receipt from CyberSource (POST) – must support POST
// Route::match(['get', 'post'], '/ucb/payment-confirmation', [UcbPaymentController::class, 'paymentConfirmation'])
//     ->name('ucb.payment.confirmation');
    //Route::match(['get', 'post'], '/ucb/payment-confirmation', 'Account\Fees\Payment\UcbPaymentController@paymentConfirmation')->name('ucb.payment.confirmation');
    Route::match(['get', 'post'], '/ucb/payment-confirmation', [UcbPaymentController::class, 'paymentConfirmation'])
    ->name('ucb.payment.confirmation')
    ->middleware(['web']);


// 3. Optional: if you manually redirect to a receipt view (GET or POST)
Route::match(['get', 'post'], '/ucb/payment-receipt', [UcbPaymentController::class, 'paymentReceipt'])
    ->name('ucb.payment.receipt');



// Route::post('/ucbl-pay/success', ['as' => 'account.fees.pay-with-ucbl.success', 'uses' => 'Account\Fees\Payment\UcbPaymentController@success']);
// Route::post('/ucbl-pay/fail', ['as' => 'account.fees.pay-with-ucbl.fail', 'uses' => 'Account\Fees\Payment\UcbPaymentController@fail']);
// Route::post('/ucbl-pay/cancel', ['as' => 'account.fees.pay-with-ucbl.cancel', 'uses' => 'Account\Fees\Payment\UcbPaymentController@cancel']);
// Route::post('/ucbl-pay/ipn', ['as' => 'account.fees.pay-with-ucbl.ipn', 'uses' => 'Account\Fees\Payment\UcbPaymentController@ipn']);

 // Upay Payment Routes
    Route::get('fees/upay-pay', ['as' => 'fees.upay.index', 'uses' => 'Account\Fees\Payment\UpayPaymentController@index']);
    Route::post('fees/upay-pay', ['as' => 'fees.upay.pay', 'uses' => 'Account\Fees\Payment\UpayPaymentController@pay']);
    Route::get('fees/upay-callback', ['as' => 'fees.upay.callback', 'uses' => 'Account\Fees\Payment\UpayPaymentController@callback']);











/*EduFirm Super Suit Grouping - user activity, database backup, cronjob, Developer Guide*/
Route::group(['prefix' => 'super-suit/',                                   'as' => 'super-suit',                                      'namespace' => 'SuperSuit\\'], function () {
    /* User Activity */
    Route::get('user-activity',                         ['as' => '.user-activity',                      'middleware' => ['ability:super-admin,user-activity-index'],                'uses' => 'UserActivityController@index']);
    Route::get('user-activity/{id}/delete',             ['as' => '.user-activity.delete',               'middleware' => ['ability:super-admin,user-activity-delete'],               'uses' => 'UserActivityController@delete']);
    Route::post('user-activity/bulk-action',            ['as' => '.user-activity.bulk-action',          'middleware' => ['ability:super-admin,user-activity-bulk-action'],               'uses' => 'UserActivityController@bulkAction']);

//    On Click Action
    Route::get('cleaner',                               ['as' => '.cleaner',                                'middleware' => ['role:super-admin'],        'uses' => 'OnClickActionController@cleaner']);
    Route::get('clear-all-cache',                      ['as' => '.clear-all-cache',                         'middleware' => ['role:super-admin'],        'uses' => 'OnClickActionController@clearAllCache']);
    Route::get('clear-cache',                         ['as' => '.clear-cache',                              'middleware' => ['role:super-admin'],        'uses' => 'OnClickActionController@cacheClear']);
    Route::get('clear-route',                         ['as' => '.clear-route',                              'middleware' => ['role:super-admin'],        'uses' => 'OnClickActionController@routeClear']);
    Route::get('clear-config',                         ['as' => '.clear-config',                            'middleware' => ['role:super-admin'],        'uses' => 'OnClickActionController@configClear']);
    Route::get('clear-view',                         ['as' => '.clear-view',                                'middleware' => ['role:super-admin'],        'uses' => 'OnClickActionController@viewClear']);
    /*table clean*/
    Route::get('clear-user-activity',                         ['as' => '.clear-user-activity',               'middleware' => ['role:super-admin'],        'uses' => 'OnClickActionController@clearUserActivity']);
    Route::get('clear-student-operational-data',              ['as' => '.clear-student-operational-data',    'middleware' => ['role:super-admin'],        'uses' => 'OnClickActionController@clearStudentOperationalData']);

    /*Database Sync */



});

Route::group(['prefix' => 'developer-info/',                                                    'as' => 'developer-info',                                      'namespace' => 'DeveloperInfo\\'], function () {

    Route::get('index',                               ['as' => '',                              'middleware' => ['role:super-admin'],        'uses' => 'DeveloperInfoController@index']);
    Route::get('routes',                               ['as' => '.routes',                      'middleware' => ['role:super-admin'],        'uses' => 'DeveloperInfoController@routesInfo']);
    Route::get('tables',                               ['as' => '.tables',                      'middleware' => ['role:super-admin'],        'uses' => 'DeveloperInfoController@tablesInfo']);


});

/*Super Suit Exit Here*/



/*Extra Features Grouping */
/*Assignment Grouping */
Route::group(['prefix' => 'assignment/',                                    'as' => 'assignment',                                       'namespace' => 'Assignment\\'], function () {

    /*Assignment Routes*/
    Route::get('',                   ['as' => '',                  'middleware' => ['ability:super-admin,assignment-index'],                'uses' => 'AssignmentController@index']);
    Route::get('add',                ['as' => '.add',              'middleware' => ['ability:super-admin,assignment-add'],                  'uses' => 'AssignmentController@add']);
    Route::post('store',             ['as' => '.store',            'middleware' => ['ability:super-admin,assignment-add'],                  'uses' => 'AssignmentController@store']);
    Route::get('{id}/edit',          ['as' => '.edit',             'middleware' => ['ability:super-admin,assignment-edit'],                 'uses' => 'AssignmentController@edit']);
    Route::post('{id}/update',       ['as' => '.update',           'middleware' => ['ability:super-admin,assignment-edit'],                 'uses' => 'AssignmentController@update']);
    Route::get('{id}/view',          ['as' => '.view',             'middleware' => ['ability:super-admin,assignment-view'],                 'uses' => 'AssignmentController@view']);
    Route::get('{id}/delete',        ['as' => '.delete',           'middleware' => ['ability:super-admin,assignment-delete'],               'uses' => 'AssignmentController@delete']);
    Route::get('{id}/active',        ['as' => '.active',           'middleware' => ['ability:super-admin,assignment-active'],               'uses' => 'AssignmentController@Active']);
    Route::get('{id}/in-active',     ['as' => '.in-active',        'middleware' => ['ability:super-admin,assignment-in-active'],            'uses' => 'AssignmentController@inActive']);
    Route::post('bulk-action',       ['as' => '.bulk-action',      'middleware' => ['ability:super-admin,assignment-bulk-action'],          'uses' => 'AssignmentController@bulkAction']);

    //Route::post('find-semester',      ['as' => '.find-semester',                                                              'uses' => 'AssignmentController@findSemester']);
    Route::post('find-subject',         ['as' => '.find-subject',                                                               'uses' => 'AssignmentController@findSubject']);

    /*Answer Routes*/
    Route::get('answer/{id}/{answer}/view',     ['as' => '.answer.view',                'middleware' => ['ability:super-admin,assignment-answer-view'],                 'uses' => 'AssignmentController@viewAnswer']);
    Route::get('answer/{id}/approve',           ['as' => '.answer.approve',             'middleware' => ['ability:super-admin,assignment-answer-approve'],              'uses' => 'AssignmentController@approveAnswer']);
    Route::get('answer/{id}/reject',            ['as' => '.answer.reject',              'middleware' => ['ability:super-admin,assignment-answer-reject'],               'uses' => 'AssignmentController@rejectAnswer']);
    Route::get('answer/{id}/delete',            ['as' => '.answer.delete',              'middleware' => ['ability:super-admin,assignment-answer-delete'],               'uses' => 'AssignmentController@deleteAnswer']);

    Route::post('answer/bulk-action',           ['as' => '.answer.bulk-action',      'middleware' => ['ability:super-admin,assignment-answer-bulk-action'],             'uses' => 'AssignmentController@bulkActionAnswer']);
});

/*Application Grouping */
Route::group(['prefix' => 'application/',                                    'as' => 'application',                                       'namespace' => 'Application\\'], function () {

    /*Application Routes*/
    Route::get('index',              ['as' => '.index',                  'middleware' => ['ability:super-admin,application-index'],                'uses' => 'ApplicationController@index']);
    Route::get('add',                ['as' => '.add',              'middleware' => ['ability:super-admin,application-add'],                  'uses' => 'ApplicationController@add']);
    Route::post('store',             ['as' => '.store',            'middleware' => ['ability:super-admin,application-add'],                  'uses' => 'ApplicationController@store']);
    Route::get('{id}/edit',          ['as' => '.edit',             'middleware' => ['ability:super-admin,application-edit'],                 'uses' => 'ApplicationController@edit']);
    Route::post('{id}/update',       ['as' => '.update',           'middleware' => ['ability:super-admin,application-edit'],                 'uses' => 'ApplicationController@update']);
    Route::get('{id}/view',          ['as' => '.view',             'middleware' => ['ability:super-admin,application-view'],                 'uses' => 'ApplicationController@view']);
    Route::get('{id}/delete',        ['as' => '.delete',           'middleware' => ['ability:super-admin,application-delete'],               'uses' => 'ApplicationController@delete']);
    Route::get('{id}/active',        ['as' => '.active',           'middleware' => ['ability:super-admin,application-active'],               'uses' => 'ApplicationController@Active']);
    Route::get('{id}/in-active',     ['as' => '.in-active',        'middleware' => ['ability:super-admin,application-in-active'],            'uses' => 'ApplicationController@inActive']);
    Route::post('bulk-action',       ['as' => '.bulk-action',      'middleware' => ['ability:super-admin,application-bulk-action'],          'uses' => 'ApplicationController@bulkAction']);

    Route::get('{id}/approve',        ['as' => '.approve',           'middleware' => ['ability:super-admin,application-approve'],               'uses' => 'ApplicationController@Approve']);
    Route::get('{id}/un-approve',     ['as' => '.un-approve',        'middleware' => ['ability:super-admin,application-un-approve'],            'uses' => 'ApplicationController@UnApprove']);

});

/*Download Grouping */
Route::group(['prefix' => 'download/',                                    'as' => 'download',                                       'namespace' => 'Download\\'], function () {
    /*Download Routes*/
    Route::get('',                   ['as' => '',                  'middleware' => ['ability:super-admin,download-index'],                'uses' => 'DownloadController@index']);
    Route::get('add',             ['as' => '.add',                 'middleware' => ['ability:super-admin,download-add'],                        'uses' => 'DownloadController@add']);
    Route::post('store',             ['as' => '.store',            'middleware' => ['ability:super-admin,download-add'],                  'uses' => 'DownloadController@store']);
    Route::get('{id}/edit',          ['as' => '.edit',             'middleware' => ['ability:super-admin,download-edit'],                 'uses' => 'DownloadController@edit']);
    Route::post('{id}/update',       ['as' => '.update',           'middleware' => ['ability:super-admin,download-edit'],                 'uses' => 'DownloadController@update']);
    Route::get('{id}/delete',        ['as' => '.delete',           'middleware' => ['ability:super-admin,download-delete'],               'uses' => 'DownloadController@delete']);
    Route::get('{id}/active',        ['as' => '.active',           'middleware' => ['ability:super-admin,download-active'],               'uses' => 'DownloadController@Active']);
    Route::get('{id}/in-active',     ['as' => '.in-active',        'middleware' => ['ability:super-admin,download-in-active'],            'uses' => 'DownloadController@inActive']);
    Route::post('bulk-action',       ['as' => '.bulk-action',      'middleware' => ['ability:super-admin,download-bulk-action'],          'uses' => 'DownloadController@bulkAction']);

    Route::post('find-subject',         ['as' => '.find-subject',                                                              'uses' => 'DownloadController@findSubject']);

});

//Zoom Meeting
Route::group(['prefix' => 'meeting/',                                    'as' => 'meeting',                                       'namespace' => 'Meeting\\'], function () {

    Route::get('zoom-index',                   ['as' => '.zoom-index',                  'middleware' => ['ability:super-admin,meeting-zoom-index'],                'uses' => 'ZoomMeetingController@zoomIndex']);

    Route::get('',                   ['as' => '',                   'middleware' => ['ability:super-admin,meeting-index'],                'uses' => 'ZoomMeetingController@index']);
    Route::get('add',                ['as' => '.add',               'middleware' => ['ability:super-admin,meeting-add'],                'uses' => 'ZoomMeetingController@add']);
    Route::post('store',             ['as' => '.store',             'middleware' => ['ability:super-admin,meeting-add'],                  'uses' => 'ZoomMeetingController@store']);
    Route::get('{id}/delete',        ['as' => '.delete',            'middleware' => ['ability:super-admin,meeting-delete'],               'uses' => 'ZoomMeetingController@delete']);
    Route::get('{id}/complete',      ['as' => '.complete',          'middleware' => ['ability:super-admin,meeting-complete'],               'uses' => 'ZoomMeetingController@complete']);
    Route::get('{id}/start',         ['as' => '.start',             'middleware' => ['ability:super-admin,meeting-start'],               'uses' => 'ZoomMeetingController@start']);
    Route::get('{id}/pending',       ['as' => '.pending',           'middleware' => ['ability:super-admin,meeting-pending'],            'uses' => 'ZoomMeetingController@pending']);
    Route::post('bulk-action',       ['as' => '.bulk-action',       'middleware' => ['ability:super-admin,meeting-bulk-action'],          'uses' => 'ZoomMeetingController@bulkAction']);

    //Route::post('find-semester',                    ['as' => '.find-semester',                                                              'uses' => 'ZoomMeetingController@findSemester']);
    Route::post('mark-ledger/find-subject',         ['as' => '.find-subject',                                                               'uses' => 'ZoomMeetingController@findSubject']);

    //send alert
    Route::get('send-alert/{event}/{id}',      ['as' => '.send-alert',             'middleware' => ['ability:super-admin,sms-email-meeting'],     'uses' => 'ZoomMeetingController@meetingAlert']);
});

// Route::get('qr-code', function () {
//     //return QrCode::size(500)->generate('Welcome to http://unlimitededufirm.com');
// });

/*Inventory Grouping*/
Route::group(['prefix' => 'inventory/',                                   'as' => 'inventory.',                                    'namespace' => 'Inventory\\'], function () {

    /*Asset Routes*/
    Route::get('assets',                    ['as' => 'assets',                  'middleware' => ['ability:super-admin,assets-index'],            'uses' => 'AssetsController@index']);
    Route::post('assets/store',             ['as' => 'assets.store',            'middleware' => ['ability:super-admin,assets-add'],              'uses' => 'AssetsController@store']);
    Route::get('assets/{id}/edit',          ['as' => 'assets.edit',             'middleware' => ['ability:super-admin,assets-edit'],             'uses' => 'AssetsController@edit']);
    Route::post('assets/{id}/update',       ['as' => 'assets.update',           'middleware' => ['ability:super-admin,assets-edit'],             'uses' => 'AssetsController@update']);
    Route::get('assets/{id}/delete',        ['as' => 'assets.delete',           'middleware' => ['ability:super-admin,assets-delete'],           'uses' => 'AssetsController@delete']);
    Route::get('assets/{id}/active',        ['as' => 'assets.active',           'middleware' => ['ability:super-admin,assets-active'],           'uses' => 'AssetsController@Active']);
    Route::get('assets/{id}/in-active',     ['as' => 'assets.in-active',        'middleware' => ['ability:super-admin,assets-in-active'],        'uses' => 'AssetsController@inActive']);
    Route::post('assets/bulk-action',       ['as' => 'assets.bulk-action',      'middleware' => ['ability:super-admin,assets-bulk-action'],      'uses' => 'AssetsController@bulkAction']);

    /*SemAsset Routes*/
    Route::get('sem-assets',                    ['as' => 'sem-assets',                  'middleware' => ['ability:super-admin,sem-assets-index'],            'uses' => 'SemesterAssetsController@index']);
    Route::get('sem-assets/add',                ['as' => 'sem-assets.add',              'middleware' => ['ability:super-admin,sem-assets-add'],              'uses' => 'SemesterAssetsController@add']);
    Route::post('sem-assets/store',             ['as' => 'sem-assets.store',            'middleware' => ['ability:super-admin,sem-assets-add'],              'uses' => 'SemesterAssetsController@store']);
    Route::get('sem-assets/{id}/delete',        ['as' => 'sem-assets.delete',           'middleware' => ['ability:super-admin,sem-assets-delete'],           'uses' => 'SemesterAssetsController@delete']);

    /*Customers Grouping*/
    Route::group(['prefix' => 'customer/',                                   'as' => 'customer',                                     'namespace' => 'Customer\\'], function () {

        Route::get('',                          ['as' => '',                         'middleware' => ['ability:super-admin,customer-index'],                  'uses' => 'CustomerController@index']);
        Route::get('registration',              ['as' => '.registration',            'middleware' => ['ability:super-admin,customer-registration'],           'uses' => 'CustomerController@registration']);
        Route::post('register',                 ['as' => '.register',                'middleware' => ['ability:super-admin,customer-registration'],               'uses' => 'CustomerController@register']);
        Route::get('{id}/view',                 ['as' => '.view',                    'middleware' => ['ability:super-admin,customer-view'],                   'uses' => 'CustomerController@view']);
        Route::get('{id}/edit',                 ['as' => '.edit',                    'middleware' => ['ability:super-admin,customer-edit'],                   'uses' => 'CustomerController@edit']);
        Route::post('{id}/update',              ['as' => '.update',                  'middleware' => ['ability:super-admin,customer-edit'],                    'uses' => 'CustomerController@update']);
        Route::get('{id}/delete',               ['as' => '.delete',                  'middleware' => ['ability:super-admin,customer-delete'],                 'uses' => 'CustomerController@delete']);
        Route::post('bulk-action',              ['as' => '.bulk-action',             'middleware' => ['ability:super-admin,customer-bulk-action'],            'uses' => 'CustomerController@bulkAction']);
        Route::get('{id}/active',               ['as' => '.active',                  'middleware' => ['ability:super-admin,customer-active'],                 'uses' => 'CustomerController@Active']);
        Route::get('{id}/in-active',            ['as' => '.in-active',               'middleware' => ['ability:super-admin,customer-in-active'],              'uses' => 'CustomerController@inActive']);

        Route::get('import',                      ['as' => '.import',             'middleware' => ['ability:super-admin,customer-bulk-registration'],           'uses' => 'CustomerController@importCustomer']);
        Route::post('import',                     ['as' => '.bulk.import',        'middleware' => ['ability:super-admin,customer-bulk-registration'],             'uses' => 'CustomerController@handleImportCustomer']);

        /*Customer login access*/
        Route::post('user/create',             ['as' => '.user.create',                  'middleware' => ['ability:super-admin,user-add'],                    'uses' => 'CustomerController@createUser']);
        Route::post('{id}/user/update',        ['as' => '.user.update',                  'middleware' => ['ability:super-admin,user-edit'],                   'uses' => 'CustomerController@updateUser']);
        Route::get('{id}/user/active',         ['as' => '.user.active',                  'middleware' => ['ability:super-admin,user-active'],                 'uses' => 'CustomerController@activeUser']);
        Route::get('{id}/user/in-active',      ['as' => '.user.in-active',               'middleware' => ['ability:super-admin,user-in-active'],              'uses' => 'CustomerController@inActiveUser']);
        Route::get('{id}/user/delete',         ['as' => '.user.delete',                  'middleware' => ['ability:super-admin,user-delete'],                 'uses' => 'CustomerController@deleteUser']);

        /*Customer Document Upload*/
        Route::get('document',                      ['as' => '.document',                'middleware' => ['ability:super-admin,customer-document-index'],      'uses' => 'DocumentController@index']);
        Route::post('document/store',               ['as' => '.document.store',          'middleware' => ['ability:super-admin,customer-document-add'],      'uses' => 'DocumentController@store']);
        Route::get('document/{id}/edit',            ['as' => '.document.edit',           'middleware' => ['ability:super-admin,customer-document-edit'],      'uses' => 'DocumentController@edit']);
        Route::post('document/{id}/update',         ['as' => '.document.update',         'middleware' => ['ability:super-admin,customer-document-edit'],      'uses' => 'DocumentController@update']);
        Route::get('document/{id}/delete',          ['as' => '.document.delete',         'middleware' => ['ability:super-admin,customer-document-delete'],      'uses' => 'DocumentController@delete']);
        Route::get('document/{id}/active',          ['as' => '.document.active',         'middleware' => ['ability:super-admin,customer-document-active'],      'uses' => 'DocumentController@Active']);
        Route::get('document/{id}/in-active',       ['as' => '.document.in-active',      'middleware' => ['ability:super-admin,customer-document-in-active'],      'uses' => 'DocumentController@inActive']);
        Route::post('document/bulk-action',         ['as' => '.document.bulk-action',    'middleware' => ['ability:super-admin,customer-document-bulk-action'],      'uses' => 'DocumentController@bulkAction']);

        /*Customer Notes Creating*/
        Route::get('note',                          ['as' => '.note',                'middleware' => ['ability:super-admin,customer-note-index'],      'uses' => 'NoteController@index']);
        Route::post('note/store',                   ['as' => '.note.store',          'middleware' => ['ability:super-admin,customer-note-add'],      'uses' => 'NoteController@store']);
        Route::get('note/{id}/edit',                ['as' => '.note.edit',           'middleware' => ['ability:super-admin,customer-note-edit'],      'uses' => 'NoteController@edit']);
        Route::post('note/{id}/update',             ['as' => '.note.update',         'middleware' => ['ability:super-admin,customer-note-edit'],      'uses' => 'NoteController@update']);
        Route::get('note/{id}/delete',              ['as' => '.note.delete',         'middleware' => ['ability:super-admin,customer-note-delete'],      'uses' => 'NoteController@delete']);
        Route::get('note/{id}/active',              ['as' => '.note.active',         'middleware' => ['ability:super-admin,customer-note-active'],      'uses' => 'NoteController@Active']);
        Route::get('note/{id}/in-active',           ['as' => '.note.in-active',      'middleware' => ['ability:super-admin,customer-note-in-active'],      'uses' => 'NoteController@inActive']);
        Route::post('note/bulk-action',             ['as' => '.note.bulk-action',    'middleware' => ['ability:super-admin,customer-note-bulk-action'],      'uses' => 'NoteController@bulkAction']);
    });

    /*Vendor Grouping*/
    Route::group(['prefix' => 'vendor/',                                   'as' => 'vendor',                                     'namespace' => 'Vendor\\'], function () {

        Route::get('',                          ['as' => '',                         'middleware' => ['ability:super-admin,vendor-index'],                  'uses' => 'VendorController@index']);
        Route::get('registration',              ['as' => '.registration',            'middleware' => ['ability:super-admin,vendor-registration'],           'uses' => 'VendorController@registration']);
        Route::post('register',                 ['as' => '.register',                'middleware' => ['ability:super-admin,vendor-registration'],               'uses' => 'VendorController@register']);
        Route::get('{id}/view',                 ['as' => '.view',                    'middleware' => ['ability:super-admin,vendor-view'],                   'uses' => 'VendorController@view']);
        Route::get('{id}/edit',                 ['as' => '.edit',                    'middleware' => ['ability:super-admin,vendor-edit'],                   'uses' => 'VendorController@edit']);
        Route::post('{id}/update',              ['as' => '.update',                  'middleware' => ['ability:super-admin,vendor-edit'],                    'uses' => 'VendorController@update']);
        Route::get('{id}/delete',               ['as' => '.delete',                  'middleware' => ['ability:super-admin,vendor-delete'],                 'uses' => 'VendorController@delete']);
        Route::post('bulk-action',              ['as' => '.bulk-action',             'middleware' => ['ability:super-admin,vendor-bulk-action'],            'uses' => 'VendorController@bulkAction']);
        Route::get('{id}/active',               ['as' => '.active',                  'middleware' => ['ability:super-admin,vendor-active'],                 'uses' => 'VendorController@Active']);
        Route::get('{id}/in-active',            ['as' => '.in-active',               'middleware' => ['ability:super-admin,vendor-in-active'],              'uses' => 'VendorController@inActive']);

        Route::get('import',                    ['as' => '.import',             'middleware' => ['ability:super-admin,vendor-bulk-registration'],           'uses' => 'VendorController@importVendor']);
        Route::post('import',                   ['as' => '.bulk.import',        'middleware' => ['ability:super-admin,vendor-bulk-registration'],             'uses' => 'VendorController@handleImportVendor']);

        /*Customer login access*/
        Route::post('user/create',             ['as' => '.user.create',                  'middleware' => ['ability:super-admin,user-add'],                    'uses' => 'VendorController@createUser']);
        Route::post('{id}/user/update',        ['as' => '.user.update',                  'middleware' => ['ability:super-admin,user-edit'],                   'uses' => 'VendorController@updateUser']);
        Route::get('{id}/user/active',         ['as' => '.user.active',                  'middleware' => ['ability:super-admin,user-active'],                 'uses' => 'VendorController@activeUser']);
        Route::get('{id}/user/in-active',      ['as' => '.user.in-active',               'middleware' => ['ability:super-admin,user-in-active'],              'uses' => 'VendorController@inActiveUser']);
        Route::get('{id}/user/delete',         ['as' => '.user.delete',                  'middleware' => ['ability:super-admin,user-delete'],                 'uses' => 'VendorController@deleteUser']);

        /*Vendor Document Upload*/
        Route::get('document',                      ['as' => '.document',                'middleware' => ['ability:super-admin,vendor-document-index'],      'uses' => 'DocumentController@index']);
        Route::post('document/store',               ['as' => '.document.store',          'middleware' => ['ability:super-admin,vendor-document-add'],      'uses' => 'DocumentController@store']);
        Route::get('document/{id}/edit',            ['as' => '.document.edit',           'middleware' => ['ability:super-admin,vendor-document-edit'],      'uses' => 'DocumentController@edit']);
        Route::post('document/{id}/update',         ['as' => '.document.update',         'middleware' => ['ability:super-admin,vendor-document-edit'],      'uses' => 'DocumentController@update']);
        Route::get('document/{id}/delete',          ['as' => '.document.delete',         'middleware' => ['ability:super-admin,vendor-document-delete'],      'uses' => 'DocumentController@delete']);
        Route::get('document/{id}/active',          ['as' => '.document.active',         'middleware' => ['ability:super-admin,vendor-document-active'],      'uses' => 'DocumentController@Active']);
        Route::get('document/{id}/in-active',       ['as' => '.document.in-active',      'middleware' => ['ability:super-admin,vendor-document-in-active'],      'uses' => 'DocumentController@inActive']);
        Route::post('document/bulk-action',         ['as' => '.document.bulk-action',    'middleware' => ['ability:super-admin,vendor-document-bulk-action'],      'uses' => 'DocumentController@bulkAction']);

        /*Vendor Notes Creating*/
        Route::get('note',                          ['as' => '.note',                'middleware' => ['ability:super-admin,vendor-note-index'],      'uses' => 'NoteController@index']);
        Route::post('note/store',                   ['as' => '.note.store',          'middleware' => ['ability:super-admin,vendor-note-add'],      'uses' => 'NoteController@store']);
        Route::get('note/{id}/edit',                ['as' => '.note.edit',           'middleware' => ['ability:super-admin,vendor-note-edit'],      'uses' => 'NoteController@edit']);
        Route::post('note/{id}/update',             ['as' => '.note.update',         'middleware' => ['ability:super-admin,vendor-note-edit'],      'uses' => 'NoteController@update']);
        Route::get('note/{id}/delete',              ['as' => '.note.delete',         'middleware' => ['ability:super-admin,vendor-note-delete'],      'uses' => 'NoteController@delete']);
        Route::get('note/{id}/active',              ['as' => '.note.active',         'middleware' => ['ability:super-admin,vendor-note-active'],      'uses' => 'NoteController@Active']);
        Route::get('note/{id}/in-active',           ['as' => '.note.in-active',      'middleware' => ['ability:super-admin,vendor-note-in-active'],      'uses' => 'NoteController@inActive']);
        Route::post('note/bulk-action',             ['as' => '.note.bulk-action',    'middleware' => ['ability:super-admin,vendor-note-bulk-action'],      'uses' => 'NoteController@bulkAction']);
    });

    /*Product Grouping */
    Route::group(['prefix' => 'product/',                                   'as' => 'product',                                      'namespace' => 'Product\\'], function () {

        Route::get('',                          ['as' => '',                         'middleware' => ['ability:super-admin,product-index'],                  'uses' => 'ProductController@index']);
        Route::get('registration',              ['as' => '.registration',            'middleware' => ['ability:super-admin,product-add'],                    'uses' => 'ProductController@registration']);
        Route::post('register',                 ['as' => '.register',                'middleware' => ['ability:super-admin,product-add'],                    'uses' => 'ProductController@register']);
        Route::get('{id}/view',                 ['as' => '.view',                    'middleware' => ['ability:super-admin,product-view'],                   'uses' => 'ProductController@view']);
        Route::get('{id}/edit',                 ['as' => '.edit',                    'middleware' => ['ability:super-admin,product-edit'],                   'uses' => 'ProductController@edit']);
        Route::post('{id}/update',              ['as' => '.update',                  'middleware' => ['ability:super-admin,product-edit'],                   'uses' => 'ProductController@update']);
        Route::get('{id}/delete',               ['as' => '.delete',                  'middleware' => ['ability:super-admin,product-delete'],                 'uses' => 'ProductController@delete']);
        Route::post('bulk-action',              ['as' => '.bulk-action',             'middleware' => ['ability:super-admin,product-bulk-action'],            'uses' => 'ProductController@bulkAction']);
        Route::get('{id}/active',               ['as' => '.active',                  'middleware' => ['ability:super-admin,product-active'],                 'uses' => 'ProductController@Active']);
        Route::get('{id}/in-active',            ['as' => '.in-active',               'middleware' => ['ability:super-admin,product-in-active'],              'uses' => 'ProductController@inActive']);
        Route::post('find-category',            ['as' => '.find-category',                                                                                   'uses' => 'ProductController@findCategory']);

        Route::get('import',                      ['as' => '.import',             'middleware' => ['ability:super-admin,product-add'],           'uses' => 'ProductController@importProduct']);
        Route::post('import',                     ['as' => '.bulk.import',        'middleware' => ['ability:super-admin,product-add'],             'uses' => 'ProductController@handleImportProduct']);

        Route::post('product-detail-html',         ['as' => '.product-detail-html',                                                                         'uses' => 'ProductController@productInfo']);
        Route::get('product-name-autocomplete',    ['as' => '.product-name-autocomplete',                                                                   'uses' => 'ProductController@productNameAutocomplete']);

        /* Category Routes */
        Route::get('category',                    ['as' => '.category',                  'middleware' => ['ability:super-admin,product-category-index'],             'uses' => 'CategoryController@index']);
        Route::get('category/add',                ['as' => '.category.add',              'middleware' => ['ability:super-admin,product-category-add'],               'uses' => 'CategoryController@add']);
        Route::post('category/store',             ['as' => '.category.store',            'middleware' => ['ability:super-admin,product-category-add'],               'uses' => 'CategoryController@store']);
        Route::get('category/{id}/edit',          ['as' => '.category.edit',             'middleware' => ['ability:super-admin,product-category-edit'],              'uses' => 'CategoryController@edit']);
        Route::post('category/{id}/update',       ['as' => '.category.update',           'middleware' => ['ability:super-admin,product-category-edit'],              'uses' => 'CategoryController@update']);
        Route::get('category/{id}/delete',        ['as' => '.category.delete',           'middleware' => ['ability:super-admin,product-category-delete'],            'uses' => 'CategoryController@delete']);
        Route::get('category/{id}/active',        ['as' => '.category.active',           'middleware' => ['ability:super-admin,product-category-active'],            'uses' => 'CategoryController@Active']);
        Route::get('category/{id}/in-active',     ['as' => '.category.in-active',        'middleware' => ['ability:super-admin,product-category-in-active'],         'uses' => 'CategoryController@inActive']);
        Route::post('category/bulk-action',       ['as' => '.category.bulk-action',      'middleware' => ['ability:super-admin,product-category-bulk-action'],       'uses' => 'CategoryController@bulkAction']);
        Route::post('category/grade-html',        ['as' => '.category.subCat-html',                                                                                  'uses' => 'CategoryController@subCatHtmlRow']);



    });

    /*Purchase Grouping*/
    /*Product Grouping */
    Route::group(['prefix' => 'purchase/',                                   'as' => 'purchase',                                      'namespace' => 'Purchase\\'], function () {

        Route::get('',                          ['as' => '',                         'middleware' => ['ability:super-admin,purchase-index'],                  'uses' => 'PurchaseController@index']);
        Route::get('registration',              ['as' => '.registration',            'middleware' => ['ability:super-admin,purchase-add'],                    'uses' => 'PurchaseController@registration']);
        Route::post('register',                 ['as' => '.register',                'middleware' => ['ability:super-admin,purchase-add'],                    'uses' => 'PurchaseController@register']);
        Route::get('{id}/view',                 ['as' => '.view',                    'middleware' => ['ability:super-admin,purchase-view'],                   'uses' => 'PurchaseController@view']);
        Route::get('{id}/edit',                 ['as' => '.edit',                    'middleware' => ['ability:super-admin,purchase-edit'],                   'uses' => 'PurchaseController@edit']);
        Route::post('{id}/update',              ['as' => '.update',                  'middleware' => ['ability:super-admin,purchase-edit'],                   'uses' => 'PurchaseController@update']);
        Route::get('{id}/delete',               ['as' => '.delete',                  'middleware' => ['ability:super-admin,purchase-delete'],                 'uses' => 'PurchaseController@delete']);
        Route::post('bulk-action',              ['as' => '.bulk-action',             'middleware' => ['ability:super-admin,purchase-bulk-action'],            'uses' => 'PurchaseController@bulkAction']);
        Route::get('{id}/active',               ['as' => '.active',                  'middleware' => ['ability:super-admin,purchase-active'],                 'uses' => 'PurchaseController@Active']);
        Route::get('{id}/in-active',            ['as' => '.in-active',               'middleware' => ['ability:super-admin,purchase-in-active'],              'uses' => 'PurchaseController@inActive']);
        //Route::post('find-category',            ['as' => '.find-category',                                                                                   'uses' => 'ProductController@findCategory']);

//        Route::get('import',                      ['as' => '.import',             'middleware' => ['ability:super-admin,purchase-add'],           'uses' => 'ProductController@importProduct']);
//        Route::post('import',                     ['as' => '.bulk.import',        'middleware' => ['ability:super-admin,purchase-add'],             'uses' => 'ProductController@handleImportProduct']);
    });

    /*Sales Grouping*/
});


//commands
/*Route::get('/smsemail', function () {
    $exitCode = \Illuminate\Support\Facades\Artisan::call('command:smsemail');
});*/


//Verify License
Route::get('/license-info',                 ['as' => 'license-info',   'uses' => 'LicenseInfoController@index']);


//Public Web Portal
/*Web Protal Admin*/
Route::group(['prefix' => 'webportal',                                   'as' => 'web.',                                    'namespace' => 'Web\\'], function () {

    Route::group(['prefix' => '/admin/', 'as' => 'admin.', 'namespace' => 'Admin\\', 'middleware' => 'auth'], function () {

        /*Setting Grouping */
        Route::group(['prefix' => 'settings/',                                   'as' => 'settings',                                      'namespace' => 'Setting\\'], function () {
            /* General Setting Routes */
            Route::get('general',                    ['as' => '.general',                  'middleware' => ['ability:super-admin,web-settings-general-index'],           'uses' => 'GeneralSettingController@index']);
            Route::get('general/add',                ['as' => '.general.add',              'middleware' => ['ability:super-admin,web-settings-general-add'],             'uses' => 'GeneralSettingController@add']);
            Route::post('general/store',             ['as' => '.general.store',            'middleware' => ['ability:super-admin,web-settings-general-add'],             'uses' => 'GeneralSettingController@store']);
            Route::get('general/{id}/edit',          ['as' => '.general.edit',             'middleware' => ['ability:super-admin,web-settings-general-edit'],            'uses' => 'GeneralSettingController@edit']);
            Route::post('general/{id}/update',       ['as' => '.general.update',           'middleware' => ['ability:super-admin,web-settings-general-edit'],            'uses' => 'GeneralSettingController@update']);

            /*Home Page Setting*/
            Route::get('home',                       ['as' => '.home',                     'middleware' => ['ability:super-admin,web-settings-home-index'],            'uses' => 'HomeSettingController@index']);
            Route::get('home/add',                   ['as' => '.home.add',                 'middleware' => ['ability:super-admin,web-settings-home-add'],              'uses' => 'HomeSettingController@add']);
            Route::post('home/store',                ['as' => '.home.store',               'middleware' => ['ability:super-admin,web-settings-home-add'],              'uses' => 'HomeSettingController@store']);
            Route::get('home/{id}/edit',             ['as' => '.home.edit',                'middleware' => ['ability:super-admin,web-settings-home-edit'],             'uses' => 'HomeSettingController@edit']);
            Route::post('home/{id}/update',          ['as' => '.home.update',              'middleware' => ['ability:super-admin,web-settings-home-edit'],             'uses' => 'HomeSettingController@update']);

            /*About Us Page*/
            Route::get('about-us',                    ['as' => '.about-us',                  'middleware' => ['ability:super-admin,web-settings-about-us-index'],          'uses' => 'AboutUsController@index']);
            Route::get('about-us/add',                ['as' => '.about-us.add',              'middleware' => ['ability:super-admin,web-settings-about-us-add'],            'uses' => 'AboutUsController@add']);
            Route::post('about-us/store',             ['as' => '.about-us.store',            'middleware' => ['ability:super-admin,web-settings-about-us-add'],            'uses' => 'AboutUsController@store']);
            Route::get('about-us/{id}/edit',          ['as' => '.about-us.edit',             'middleware' => ['ability:super-admin,web-settings-about-us-edit'],           'uses' => 'AboutUsController@edit']);
            Route::post('about-us/{id}/update',       ['as' => '.about-us.update',           'middleware' => ['ability:super-admin,web-settings-about-us-edit'],           'uses' => 'AboutUsController@update']);

            /*Contact Us Page*/
            Route::get('contact-us',                    ['as' => '.contact-us',               'middleware' => ['ability:super-admin,web-settings-contact-us-index'],       'uses' => 'ContactUsController@index']);
            Route::get('contact-us/add',                ['as' => '.contact-us.add',           'middleware' => ['ability:super-admin,web-settings-contact-us-add'],         'uses' => 'ContactUsController@add']);
            Route::post('contact-us/store',             ['as' => '.contact-us.store',         'middleware' => ['ability:super-admin,web-settings-contact-us-add'],         'uses' => 'ContactUsController@store']);
            Route::get('contact-us/{id}/edit',          ['as' => '.contact-us.edit',          'middleware' => ['ability:super-admin,web-settings-contact-us-edit'],        'uses' => 'ContactUsController@edit']);
            Route::post('contact-us/{id}/update',       ['as' => '.contact-us.update',        'middleware' => ['ability:super-admin,web-settings-contact-us-edit'],        'uses' => 'ContactUsController@update']);

            /*Registration Section*/
            Route::get('registration',                       ['as' => '.registration',                     'middleware' => ['ability:super-admin,web-settings-registration-index'],            'uses' => 'RegistrationSettingController@index']);
            Route::get('registration/add',                   ['as' => '.registration.add',                 'middleware' => ['ability:super-admin,web-settings-registration-add'],              'uses' => 'RegistrationSettingController@add']);
            Route::post('registration/store',                ['as' => '.registration.store',               'middleware' => ['ability:super-admin,web-settings-registration-add'],              'uses' => 'RegistrationSettingController@store']);
            Route::get('registration/{id}/edit',             ['as' => '.registration.edit',                'middleware' => ['ability:super-admin,web-settings-registration-edit'],             'uses' => 'RegistrationSettingController@edit']);
            Route::post('registration/{id}/update',          ['as' => '.registration.update',              'middleware' => ['ability:super-admin,web-settings-registration-edit'],             'uses' => 'RegistrationSettingController@update']);

        });

        Route::get('dashboard',              ['as' => 'dashboard',      'middleware' => ['ability:super-admin,web-dashboard-index'],                'uses' => 'DashboardController@index']);
        Route::get('analytics',              ['as' => 'analytics',      'middleware' => ['ability:super-admin,web-view-analytics'],                 'uses' => 'DashboardController@analytics']);

        /* Page Routes */
        Route::get('page',                    ['as' => 'page',                  'middleware' => ['ability:super-admin,web-page-index'],             'uses' => 'PageController@index']);
        Route::get('page/add',                ['as' => 'page.add',              'middleware' => ['ability:super-admin,web-page-add'],               'uses' => 'PageController@add']);
        Route::post('page/store',             ['as' => 'page.store',            'middleware' => ['ability:super-admin,web-page-add'],               'uses' => 'PageController@store']);
        Route::get('page/{id}/edit',          ['as' => 'page.edit',             'middleware' => ['ability:super-admin,web-page-edit'],              'uses' => 'PageController@edit']);
        Route::post('page/{id}/update',       ['as' => 'page.update',           'middleware' => ['ability:super-admin,web-page-edit'],              'uses' => 'PageController@update']);
        Route::get('page/{id}/view',          ['as' => 'page.view',             'middleware' => ['ability:super-admin,web-page-view'],              'uses' => 'PageController@view']);
        Route::get('page/{id}/delete',        ['as' => 'page.delete',           'middleware' => ['ability:super-admin,web-page-delete'],            'uses' => 'PageController@delete']);
        Route::get('page/{id}/active',        ['as' => 'page.active',           'middleware' => ['ability:super-admin,web-page-active'],            'uses'  => 'PageController@active']);
        Route::get('page/{id}/in-active',     ['as' => 'page.in-active',        'middleware' => ['ability:super-admin,web-page-in-active'],         'uses'  => 'PageController@inActive']);
        Route::post('page/bulk-action',       ['as' => 'page.bulk-action',      'middleware' => ['ability:super-admin,web-page-bulk-action'],       'uses' => 'PageController@bulkAction']);

        /* Menu Routes */
        Route::get('menu',                    ['as' => 'menu',                  'middleware' => ['ability:super-admin,web-menu-index'],             'uses' => 'MenuController@index']);
        Route::get('menu/{id}/edit',          ['as' => 'menu.edit',             'middleware' => ['ability:super-admin,web-menu-edit'],              'uses' => 'MenuController@edit']);
        Route::post('menu/{id}/update',       ['as' => 'menu.update',           'middleware' => ['ability:super-admin,web-menu-edit'],              'uses' => 'MenuController@update']);
        Route::post('menu/menu-pages-html',   ['as' => 'menu.menu-pages-html',                                                                  'uses' => 'MenuController@menuPageHtml']);


        /*Blog Category*/
        Route::get('category',                    ['as' => 'category',                  'middleware' => ['ability:super-admin,web-category-index'],             'uses' => 'CategoryController@index']);
        Route::get('category/add',                ['as' => 'category.add',              'middleware' => ['ability:super-admin,web-category-add'],               'uses' => 'CategoryController@add']);
        Route::post('category/store',             ['as' => 'category.store',            'middleware' => ['ability:super-admin,web-category-add'],               'uses' => 'CategoryController@store']);
        Route::get('category/{id}/edit',          ['as' => 'category.edit',             'middleware' => ['ability:super-admin,web-category-edit'],              'uses' => 'CategoryController@edit']);
        Route::post('category/{id}/update',       ['as' => 'category.update',           'middleware' => ['ability:super-admin,web-category-edit'],              'uses' => 'CategoryController@update']);
        Route::get('category/{id}/view',          ['as' => 'category.view',             'middleware' => ['ability:super-admin,web-category-view'],              'uses' => 'CategoryController@view']);
        Route::get('category/{id}/delete',        ['as' => 'category.delete',           'middleware' => ['ability:super-admin,web-category-delete'],            'uses' => 'CategoryController@delete']);
        Route::get('category/{id}/active',        ['as' => 'category.active',           'middleware' => ['ability:super-admin,web-category-active'],            'uses' => 'CategoryController@active']);
        Route::get('category/{id}/in-active',     ['as' => 'category.in-active',        'middleware' => ['ability:super-admin,web-category-in-active'],         'uses' => 'CategoryController@inActive']);
        Route::post('category/bulk-action',       ['as' => 'category.bulk-action',      'middleware' => ['ability:super-admin,web-category-bulk-action'],       'uses' => 'CategoryController@bulkAction']);

        /* Blog Post Routes */
        Route::get('blog',                    ['as' => 'blog',                  'middleware' => ['ability:super-admin,web-blog-index'],                         'uses' => 'BlogController@index']);
        Route::get('blog/add',                ['as' => 'blog.add',              'middleware' => ['ability:super-admin,web-blog-add'],                           'uses' => 'BlogController@add']);
        Route::post('blog/store',             ['as' => 'blog.store',            'middleware' => ['ability:super-admin,web-blog-add'],                           'uses' => 'BlogController@store']);
        Route::get('blog/{id}/edit',          ['as' => 'blog.edit',             'middleware' => ['ability:super-admin,web-blog-edit'],                          'uses' => 'BlogController@edit']);
        Route::post('blog/{id}/update',       ['as' => 'blog.update',           'middleware' => ['ability:super-admin,web-blog-edit'],                          'uses' => 'BlogController@update']);
        Route::get('blog/{id}/view',          ['as' => 'blog.view',             'middleware' => ['ability:super-admin,web-blog-view'],                          'uses' => 'BlogController@view']);
        Route::get('blog/{id}/delete',        ['as' => 'blog.delete',           'middleware' => ['ability:super-admin,web-blog-delete'],                        'uses' => 'BlogController@delete']);
        Route::get('blog/{id}/active',        ['as' => 'blog.active',           'middleware' => ['ability:super-admin,web-blog-active'],                        'uses' => 'BlogController@active']);
        Route::get('blog/{id}/in-active',     ['as' => 'blog.in-active',        'middleware' => ['ability:super-admin,web-blog-in-active'],                     'uses' => 'BlogController@inActive']);
        Route::post('blog/bulk-action',       ['as' => 'blog.bulk-action',      'middleware' => ['ability:super-admin,web-blog-bulk-action'],                   'uses' => 'BlogController@bulkAction']);

        /* Notice Routes */
        Route::get('notice',                    ['as' => 'notice',                  'middleware' => ['ability:super-admin,web-notice-index'],               'uses' => 'NoticeController@index']);
        Route::get('notice/add',                ['as' => 'notice.add',              'middleware' => ['ability:super-admin,web-notice-add'],                 'uses' => 'NoticeController@add']);
        Route::post('notice/store',             ['as' => 'notice.store',            'middleware' => ['ability:super-admin,web-notice-add'],                 'uses' => 'NoticeController@store']);
        Route::get('notice/{id}/edit',          ['as' => 'notice.edit',             'middleware' => ['ability:super-admin,web-notice-edit'],                'uses' => 'NoticeController@edit']);
        Route::post('notice/{id}/update',       ['as' => 'notice.update',           'middleware' => ['ability:super-admin,web-notice-edit'],                'uses' => 'NoticeController@update']);
        Route::get('notice/{id}/view',          ['as' => 'notice.view',             'middleware' => ['ability:super-admin,web-notice-view'],                'uses' => 'NoticeController@view']);
        Route::get('notice/{id}/delete',        ['as' => 'notice.delete',           'middleware' => ['ability:super-admin,web-notice-delete'],              'uses' => 'NoticeController@delete']);
        Route::get('notice/{id}/active',        ['as' => 'notice.active',           'middleware' => ['ability:super-admin,web-notice-active'],              'uses' => 'NoticeController@active']);
        Route::get('notice/{id}/in-active',     ['as' => 'notice.in-active',        'middleware' => ['ability:super-admin,web-notice-in-active'],           'uses' => 'NoticeController@inActive']);
        Route::post('notice/bulk-action',       ['as' => 'notice.bulk-action',      'middleware' => ['ability:super-admin,web-notice-bulk-action'],         'uses' => 'NoticeController@bulkAction']);

        /* Event Routes */
        Route::get('events',                    ['as' => 'events',                  'middleware' => ['ability:super-admin,web-events-index'],               'uses' => 'EventsController@index']);
        Route::get('events/add',                ['as' => 'events.add',              'middleware' => ['ability:super-admin,web-events-add'],                 'uses' => 'EventsController@add']);
        Route::post('events/store',             ['as' => 'events.store',            'middleware' => ['ability:super-admin,web-events-add'],                 'uses' => 'EventsController@store']);
        Route::get('events/{id}/edit',          ['as' => 'events.edit',             'middleware' => ['ability:super-admin,web-events-edit'],                'uses' => 'EventsController@edit']);
        Route::post('events/{id}/update',       ['as' => 'events.update',           'middleware' => ['ability:super-admin,web-events-edit'],                'uses' => 'EventsController@update']);
        Route::get('events/{id}/view',          ['as' => 'events.view',             'middleware' => ['ability:super-admin,web-events-view'],                'uses' => 'EventsController@view']);
        Route::get('events/{id}/delete',        ['as' => 'events.delete',           'middleware' => ['ability:super-admin,web-events-delete'],              'uses' => 'EventsController@delete']);
        Route::get('events/{id}/active',        ['as' => 'events.active',           'middleware' => ['ability:super-admin,web-events-active'],              'uses' => 'EventsController@active']);
        Route::get('events/{id}/in-active',     ['as' => 'events.in-active',        'middleware' => ['ability:super-admin,web-events-in-active'],           'uses' => 'EventsController@inActive']);
        Route::post('events/bulk-action',       ['as' => 'events.bulk-action',      'middleware' => ['ability:super-admin,web-events-bulk-action'],         'uses' => 'EventsController@bulkAction']);

        /* Services Category Routes */
        Route::get('services',                    ['as' => 'services',                  'middleware' => ['ability:super-admin,web-services-index'],               'uses' => 'ServiceController@index']);
        Route::get('services/add',                ['as' => 'services.add',              'middleware' => ['ability:super-admin,web-services-add'],                 'uses' => 'ServiceController@add']);
        Route::post('services/store',             ['as' => 'services.store',            'middleware' => ['ability:super-admin,web-services-add'],                 'uses' => 'ServiceController@store']);
        Route::get('services/{id}/edit',          ['as' => 'services.edit',             'middleware' => ['ability:super-admin,web-services-edit'],                'uses' => 'ServiceController@edit']);
        Route::post('services/{id}/update',       ['as' => 'services.update',           'middleware' => ['ability:super-admin,web-services-edit'],                'uses' => 'ServiceController@update']);
        Route::get('services/{id}/view',          ['as' => 'services.view',             'middleware' => ['ability:super-admin,web-services-view'],                'uses' => 'ServiceController@view']);
        Route::get('services/{id}/delete',        ['as' => 'services.delete',           'middleware' => ['ability:super-admin,web-services-delete'],              'uses' => 'ServiceController@delete']);
        Route::get('services/{id}/active',        ['as' => 'services.active',           'middleware' => ['ability:super-admin,web-services-active'],              'uses' => 'ServiceController@active']);
        Route::get('services/{id}/in-active',     ['as' => 'services.in-active',        'middleware' => ['ability:super-admin,web-services-in-active'],           'uses' => 'ServiceController@inActive']);
        Route::post('services/bulk-action',       ['as' => 'services.bulk-action',      'middleware' => ['ability:super-admin,web-services-bulk-action'],         'uses' => 'ServiceController@bulkAction']);


        /* Slider Category Routes */
        Route::get('slider',                    ['as' => 'slider',                  'middleware' => ['ability:super-admin,web-slider-index'],               'uses' => 'SliderController@index']);
        Route::get('slider/add',                ['as' => 'slider.add',              'middleware' => ['ability:super-admin,web-slider-add'],                 'uses' => 'SliderController@add']);
        Route::post('slider/store',             ['as' => 'slider.store',            'middleware' => ['ability:super-admin,web-slider-add'],                 'uses' => 'SliderController@store']);
        Route::get('slider/{id}/edit',          ['as' => 'slider.edit',             'middleware' => ['ability:super-admin,web-slider-edit'],                'uses' => 'SliderController@edit']);
        Route::post('slider/{id}/update',       ['as' => 'slider.update',           'middleware' => ['ability:super-admin,web-slider-edit'],                'uses' => 'SliderController@update']);
        Route::get('slider/{id}/view',          ['as' => 'slider.view',             'middleware' => ['ability:super-admin,web-slider-view'],                'uses' => 'SliderController@view']);
        Route::get('slider/{id}/delete',        ['as' => 'slider.delete',           'middleware' => ['ability:super-admin,web-slider-delete'],              'uses' => 'SliderController@delete']);
        Route::get('slider/{id}/active',        ['as' => 'slider.active',           'middleware' => ['ability:super-admin,web-slider-active'],              'uses' => 'SliderController@active']);
        Route::get('slider/{id}/in-active',     ['as' => 'slider.in-active',        'middleware' => ['ability:super-admin,web-slider-in-active'],           'uses' => 'SliderController@inActive']);
        Route::post('slider/bulk-action',       ['as' => 'slider.bulk-action',      'middleware' => ['ability:super-admin,web-slider-bulk-action'],         'uses' => 'SliderController@bulkAction']);

        /* Client Category Routes */
        Route::get('client',                    ['as' => 'client',                  'middleware' => ['ability:super-admin,web-clint-index'],               'uses' => 'ClientController@index']);
        Route::get('client/add',                ['as' => 'client.add',              'middleware' => ['ability:super-admin,web-clint-add'],                 'uses' => 'ClientController@add']);
        Route::post('client/store',             ['as' => 'client.store',            'middleware' => ['ability:super-admin,web-clint-add'],                 'uses' => 'ClientController@store']);
        Route::get('client/{id}/edit',          ['as' => 'client.edit',             'middleware' => ['ability:super-admin,web-clint-edit'],                'uses' => 'ClientController@edit']);
        Route::post('client/{id}/update',       ['as' => 'client.update',           'middleware' => ['ability:super-admin,web-clint-edit'],                'uses' => 'ClientController@update']);
        Route::get('client/{id}/view',          ['as' => 'client.view',             'middleware' => ['ability:super-admin,web-clint-view'],                'uses' => 'ClientController@view']);
        Route::get('client/{id}/delete',        ['as' => 'client.delete',           'middleware' => ['ability:super-admin,web-clint-delete'],              'uses' => 'ClientController@delete']);
        Route::get('client/{id}/active',        ['as' => 'client.active',           'middleware' => ['ability:super-admin,web-clint-active'],              'uses' => 'ClientController@active']);
        Route::get('client/{id}/in-active',     ['as' => 'client.in-active',        'middleware' => ['ability:super-admin,web-clint-in-active'],           'uses' => 'ClientController@inActive']);
        Route::post('client/bulk-action',       ['as' => 'client.bulk-action',      'middleware' => ['ability:super-admin,web-clint-bulk-action'],         'uses' => 'ClientController@bulkAction']);

        /* Gallery Routes */
        Route::get('gallery',                    ['as' => 'gallery',                  'middleware' => ['ability:super-admin,web-gallery-index'],            'uses' => 'GalleryController@index']);
        Route::get('gallery/add',                ['as' => 'gallery.add',              'middleware' => ['ability:super-admin,web-gallery-add'],              'uses' => 'GalleryController@add']);
        Route::post('gallery/store',             ['as' => 'gallery.store',            'middleware' => ['ability:super-admin,web-gallery-add'],              'uses' => 'GalleryController@store']);
        Route::get('gallery/{id}/edit',          ['as' => 'gallery.edit',             'middleware' => ['ability:super-admin,web-gallery-edit'],             'uses' => 'GalleryController@edit']);
        Route::post('gallery/{id}/update',       ['as' => 'gallery.update',           'middleware' => ['ability:super-admin,web-gallery-edit'],             'uses' => 'GalleryController@update']);
        Route::get('gallery/{id}/view',          ['as' => 'gallery.view',             'middleware' => ['ability:super-admin,web-gallery-view'],             'uses' => 'GalleryController@view']);
        Route::get('gallery/{id}/delete',        ['as' => 'gallery.delete',           'middleware' => ['ability:super-admin,web-gallery-delete'],           'uses' => 'GalleryController@delete']);
        Route::get('gallery/{id}/active',        ['as' => 'gallery.active',           'middleware' => ['ability:super-admin,web-gallery-active'],           'uses' => 'GalleryController@active']);
        Route::get('gallery/{id}/in-active',     ['as' => 'gallery.in-active',        'middleware' => ['ability:super-admin,web-gallery-in-active'],        'uses' => 'GalleryController@inActive']);
        Route::post('gallery/bulk-action',       ['as' => 'gallery.bulk-action',      'middleware' => ['ability:super-admin,web-gallery-bulk-action'],      'uses' => 'GalleryController@bulkAction']);

        Route::post('gallery/image-html',        ['as' => 'gallery.image-html',                                                                         'uses' => 'GalleryController@imageHtml']);
        Route::post('gallery/remove-image',      ['as' => 'gallery.remove-image',                                                                       'uses' => 'GalleryController@removeImage']);


        /* Download Routes */
        Route::get('download',                    ['as' => 'download',                  'middleware' => ['ability:super-admin,web-download-index'],             'uses' => 'DownloadController@index']);
        Route::get('download/add',                ['as' => 'download.add',              'middleware' => ['ability:super-admin,web-download-add'],               'uses' => 'DownloadController@add']);
        Route::post('download/store',             ['as' => 'download.store',            'middleware' => ['ability:super-admin,web-download-add'],               'uses' => 'DownloadController@store']);
        Route::get('download/{id}/edit',          ['as' => 'download.edit',             'middleware' => ['ability:super-admin,web-download-edit'],              'uses' => 'DownloadController@edit']);
        Route::post('download/{id}/update',       ['as' => 'download.update',           'middleware' => ['ability:super-admin,web-download-edit'],              'uses' => 'DownloadController@update']);
        Route::get('download/{id}/view',          ['as' => 'download.view',             'middleware' => ['ability:super-admin,web-download-view'],              'uses' => 'DownloadController@view']);
        Route::get('download/{id}/delete',        ['as' => 'download.delete',           'middleware' => ['ability:super-admin,web-download-delete'],            'uses' => 'DownloadController@delete']);
        Route::get('download/{id}/active',        ['as' => 'download.active',           'middleware' => ['ability:super-admin,web-download-active'],            'uses' => 'DownloadController@active']);
        Route::get('download/{id}/in-active',     ['as' => 'download.in-active',        'middleware' => ['ability:super-admin,web-download-in-active'],         'uses' => 'DownloadController@inActive']);
        Route::post('download/bulk-action',       ['as' => 'download.bulk-action',      'middleware' => ['ability:super-admin,web-download-bulk-action'],       'uses' => 'DownloadController@bulkAction']);

        /* Teacher Detail Routes */
        Route::get('staff',                    ['as' => 'staff',                  'middleware' => ['ability:super-admin,web-staff-index'],                'uses' => 'StaffController@index']);
        Route::get('staff/add',                ['as' => 'staff.add',              'middleware' => ['ability:super-admin,web-staff-add'],                  'uses' => 'StaffController@add']);
        Route::post('staff/store',             ['as' => 'staff.store',            'middleware' => ['ability:super-admin,web-staff-add'],                  'uses' => 'StaffController@store']);
        Route::get('staff/{id}/edit',          ['as' => 'staff.edit',             'middleware' => ['ability:super-admin,web-staff-edit'],                 'uses' => 'StaffController@edit']);
        Route::post('staff/{id}/update',       ['as' => 'staff.update',           'middleware' => ['ability:super-admin,web-staff-edit'],                 'uses' => 'StaffController@update']);
        Route::get('staff/{id}/view',          ['as' => 'staff.view',             'middleware' => ['ability:super-admin,web-staff-view'],                 'uses' => 'StaffController@view']);
        Route::get('staff/{id}/delete',        ['as' => 'staff.delete',           'middleware' => ['ability:super-admin,web-staff-delete'],               'uses' => 'StaffController@delete']);
        Route::get('staff/{id}/active',        ['as' => 'staff.active',           'middleware' => ['ability:super-admin,web-staff-active'],               'uses' => 'StaffController@active']);
        Route::get('staff/{id}/in-active',     ['as' => 'staff.in-active',        'middleware' => ['ability:super-admin,web-staff-in-active'],            'uses' => 'StaffController@inActive']);
        Route::post('staff/bulk-action',       ['as' => 'staff.bulk-action',      'middleware' => ['ability:super-admin,web-staff-bulk-action'],          'uses' => 'StaffController@bulkAction']);


        /* Testimonial Category Routes */
        Route::get('testimonial',                    ['as' => 'testimonial',                  'middleware' => ['ability:super-admin,web-testimonial-index'],            'uses' => 'TestimonialController@index']);
        Route::get('testimonial/add',                ['as' => 'testimonial.add',              'middleware' => ['ability:super-admin,web-testimonial-add'],              'uses' => 'TestimonialController@add']);
        Route::post('testimonial/store',             ['as' => 'testimonial.store',            'middleware' => ['ability:super-admin,web-testimonial-add'],              'uses' => 'TestimonialController@store']);
        Route::get('testimonial/{id}/edit',          ['as' => 'testimonial.edit',             'middleware' => ['ability:super-admin,web-testimonial-edit'],             'uses' => 'TestimonialController@edit']);
        Route::post('testimonial/{id}/update',       ['as' => 'testimonial.update',           'middleware' => ['ability:super-admin,web-testimonial-edit'],             'uses' => 'TestimonialController@update']);
        Route::get('testimonial/{id}/view',          ['as' => 'testimonial.view',             'middleware' => ['ability:super-admin,web-testimonial-view'],             'uses' => 'TestimonialController@view']);
        Route::get('testimonial/{id}/delete',        ['as' => 'testimonial.delete',           'middleware' => ['ability:super-admin,web-testimonial-delete'],           'uses' => 'TestimonialController@delete']);
        Route::get('testimonial/{id}/active',        ['as' => 'testimonial.active',           'middleware' => ['ability:super-admin,web-testimonial-active'],           'uses' => 'TestimonialController@active']);
        Route::get('testimonial/{id}/in-active',     ['as' => 'testimonial.in-active',        'middleware' => ['ability:super-admin,web-testimonial-in-active'],        'uses' => 'TestimonialController@inActive']);
        Route::post('testimonial/bulk-action',       ['as' => 'testimonial.bulk-action',      'middleware' => ['ability:super-admin,web-testimonial-bulk-action'],      'uses' => 'TestimonialController@bulkAction']);

        /* Faq Routes */
        Route::get('faq',                    ['as' => 'faq',                  'middleware' => ['ability:super-admin,web-faq-index'],                'uses' => 'FaqController@index']);
        Route::get('faq/add',                ['as' => 'faq.add',              'middleware' => ['ability:super-admin,web-faq-add'],                  'uses' => 'FaqController@add']);
        Route::post('faq/store',             ['as' => 'faq.store',            'middleware' => ['ability:super-admin,web-faq-add'],                  'uses' => 'FaqController@store']);
        Route::get('faq/{id}/edit',          ['as' => 'faq.edit',             'middleware' => ['ability:super-admin,web-faq-edit'],                 'uses' => 'FaqController@edit']);
        Route::post('faq/{id}/update',       ['as' => 'faq.update',           'middleware' => ['ability:super-admin,web-faq-edit'],                 'uses' => 'FaqController@update']);
        Route::get('faq/{id}/view',          ['as' => 'faq.view',             'middleware' => ['ability:super-admin,web-faq-view'],                 'uses' => 'FaqController@view']);
        Route::get('faq/{id}/delete',        ['as' => 'faq.delete',           'middleware' => ['ability:super-admin,web-faq-delete'],               'uses' => 'FaqController@delete']);
        Route::get('faq/{id}/active',        ['as' => 'faq.active',           'middleware' => ['ability:super-admin,web-faq-active'],               'uses' => 'FaqController@active']);
        Route::get('faq/{id}/in-active',     ['as' => 'faq.in-active',        'middleware' => ['ability:super-admin,web-faq-in-active'],            'uses' => 'FaqController@inActive']);
        Route::post('faq/bulk-action',       ['as' => 'faq.bulk-action',      'middleware' => ['ability:super-admin,web-faq-bulk-action'],          'uses' => 'FaqController@bulkAction']);

        /* Counter Routes */
        Route::get('counter',                    ['as' => 'counter',                  'middleware' => ['ability:super-admin,web-counter-index'],            'uses' => 'CounterController@index']);
        Route::get('counter/add',                ['as' => 'counter.add',              'middleware' => ['ability:super-admin,web-counter-add'],              'uses' => 'CounterController@add']);
        Route::post('counter/store',             ['as' => 'counter.store',            'middleware' => ['ability:super-admin,web-counter-add'],              'uses' => 'CounterController@store']);
        Route::get('counter/{id}/edit',          ['as' => 'counter.edit',             'middleware' => ['ability:super-admin,web-counter-edit'],             'uses' => 'CounterController@edit']);
        Route::post('counter/{id}/update',       ['as' => 'counter.update',           'middleware' => ['ability:super-admin,web-counter-edit'],             'uses' => 'CounterController@update']);
        Route::get('counter/{id}/view',          ['as' => 'counter.view',             'middleware' => ['ability:super-admin,web-counter-view'],             'uses' => 'CounterController@view']);
        Route::get('counter/{id}/delete',        ['as' => 'counter.delete',           'middleware' => ['ability:super-admin,web-counter-delete'],           'uses' => 'CounterController@delete']);
        Route::get('counter/{id}/active',        ['as' => 'counter.active',           'middleware' => ['ability:super-admin,web-counter-active'],           'uses' => 'CounterController@active']);
        Route::get('counter/{id}/in-active',     ['as' => 'counter.in-active',        'middleware' => ['ability:super-admin,web-counter-in-active'],        'uses' => 'CounterController@inActive']);
        Route::post('counter/bulk-action',       ['as' => 'counter.bulk-action',      'middleware' => ['ability:super-admin,web-counter-bulk-action'],      'uses' => 'CounterController@bulkAction']);

        /*Blog Category*/
        Route::get('pricing',                    ['as' => 'pricing',                  'middleware' => ['ability:super-admin,web-pricing-index'],             'uses' => 'PricingController@index']);
        Route::get('pricing/add',                ['as' => 'pricing.add',              'middleware' => ['ability:super-admin,web-pricing-add'],               'uses' => 'PricingController@add']);
        Route::post('pricing/store',             ['as' => 'pricing.store',            'middleware' => ['ability:super-admin,web-pricing-add'],               'uses' => 'PricingController@store']);
        Route::get('pricing/{id}/edit',          ['as' => 'pricing.edit',             'middleware' => ['ability:super-admin,web-pricing-edit'],              'uses' => 'PricingController@edit']);
        Route::post('pricing/{id}/update',       ['as' => 'pricing.update',           'middleware' => ['ability:super-admin,web-pricing-edit'],              'uses' => 'PricingController@update']);
        Route::get('pricing/{id}/view',          ['as' => 'pricing.view',             'middleware' => ['ability:super-admin,web-pricing-view'],              'uses' => 'PricingController@view']);
        Route::get('pricing/{id}/delete',        ['as' => 'pricing.delete',           'middleware' => ['ability:super-admin,web-pricing-delete'],            'uses' => 'PricingController@delete']);
        Route::get('pricing/{id}/active',        ['as' => 'pricing.active',           'middleware' => ['ability:super-admin,web-pricing-active'],            'uses' => 'PricingController@active']);
        Route::get('pricing/{id}/in-active',     ['as' => 'pricing.in-active',        'middleware' => ['ability:super-admin,web-pricing-in-active'],         'uses' => 'PricingController@inActive']);
        Route::post('pricing/bulk-action',       ['as' => 'pricing.bulk-action',      'middleware' => ['ability:super-admin,web-pricing-bulk-action'],       'uses' => 'PricingController@bulkAction']);


        Route::get('/contact-us',                            ['as' => 'contact-us',                         'middleware' => ['ability:super-admin,web-countact-us-index'],              'uses' => 'ContactUsController@contactList']);
        Route::get('/contact-us/{id}/view',                  ['as' => 'contact-us.view',                    'middleware' => ['ability:super-admin,web-countact-us-view'],               'uses' => 'ContactUsController@viewMessage']);
        Route::get('/contact-us/{id}/delete',                ['as' => 'contact-us.delete',                  'middleware' => ['ability:super-admin,web-countact-us-delete'],              'uses' => 'ContactUsController@deleteMessage']);

        /* Subscriber Routes */
        Route::get('/subscribers',                      ['as' => 'subscribers',                     'middleware' => ['ability:super-admin,web-subscribers-index'],                      'uses' => 'SubscribersController@index']);
        Route::get('/subscribers/{id}/active',          ['as' => 'subscribers.active',              'middleware' => ['ability:super-admin,web-subscribers-active'],                     'uses' => 'SubscribersController@active']);
        Route::get('/subscribers/{id}/in-active',       ['as' => 'subscribers.in-active',           'middleware' => ['ability:super-admin,web-subscribers-in-active'],                  'uses' => 'SubscribersController@inActive']);
        Route::get('/subscribers/{id}/delete',          ['as' => 'subscribers.delete',              'middleware' => ['ability:super-admin,web-subscribers-delete'],                     'uses' => 'SubscribersController@delete']);
        Route::get('subscribers/import',                ['as' => 'subscribers.import',              'middleware' => ['ability:super-admin,web-subscribers-import'],                     'uses' => 'SubscribersController@importSubscriber']);
        Route::post('subscribers/import',               ['as' => 'subscribers.bulk.import',         'middleware' => ['ability:super-admin,web-subscribers-import'],                     'uses' => 'SubscribersController@handleImportSubscriber']);
        Route::post('subscribers/bulk-action',          ['as' => 'subscribers.bulk-action',         'middleware' => ['ability:super-admin,web-subscribers-bulk-action'],                'uses' => 'SubscribersController@bulkAction']);

        /* Send Email Routes */
        Route::get('emailMessage',                    ['as' => 'emailMessage',                  'middleware' => ['ability:super-admin,web-emailMessage-index'],          'uses' => 'EmailMessageController@index']);
        Route::get('emailMessage/add',                ['as' => 'emailMessage.add',              'middleware' => ['ability:super-admin,web-emailMessage-add'],          'uses' => 'EmailMessageController@add']);
        Route::post('emailMessage/store',             ['as' => 'emailMessage.store',            'middleware' => ['ability:super-admin,web-emailMessage-add'],          'uses' => 'EmailMessageController@store']);
        Route::get('emailMessage/{id}/view',          ['as' => 'emailMessage.view',             'middleware' => ['ability:super-admin,web-emailMessage-view'],          'uses' => 'EmailMessageController@view']);
        Route::get('emailMessage/{id}/delete',        ['as' => 'emailMessage.delete',           'middleware' => ['ability:super-admin,web-emailMessage-delete'],          'uses' => 'EmailMessageController@delete']);
        Route::post('emailMessage/bulk-action',       ['as' => 'emailMessage.bulk-action',      'middleware' => ['ability:super-admin,web-emailMessage-bulk-action'],          'uses' => 'EmailMessageController@bulkAction']);

        Route::get('help',                            ['as' => 'help',                          'uses' => 'HelpController@index']);

        //Registration Routes */
        Route::get('registration',                    ['as' => 'registration',                  'middleware' => ['ability:super-admin,web-registration-index'],               'uses' => 'RegistrationController@index']);
        Route::get('registration/add',                ['as' => 'registration.add',              'middleware' => ['ability:super-admin,web-registration-add'],                 'uses' => 'RegistrationController@add']);
        Route::post('registration/store',             ['as' => 'registration.store',            'middleware' => ['ability:super-admin,web-registration-add'],                 'uses' => 'RegistrationController@store']);
        Route::get('registration/{id}/edit',          ['as' => 'registration.edit',             'middleware' => ['ability:super-admin,web-registration-edit'],                'uses' => 'RegistrationController@edit']);
        Route::post('registration/{id}/update',       ['as' => 'registration.update',           'middleware' => ['ability:super-admin,web-registration-edit'],                'uses' => 'RegistrationController@update']);
        Route::get('registration/{id}/view',          ['as' => 'registration.view',             'middleware' => ['ability:super-admin,web-registration-view'],                'uses' => 'RegistrationController@view']);
        Route::get('registration/{id}/delete',        ['as' => 'registration.delete',           'middleware' => ['ability:super-admin,web-registration-delete'],              'uses' => 'RegistrationController@delete']);
        Route::get('registration/{id}/active',        ['as' => 'registration.active',           'middleware' => ['ability:super-admin,web-registration-active'],              'uses' => 'RegistrationController@active']);
        Route::get('registration/{id}/in-active',     ['as' => 'registration.in-active',        'middleware' => ['ability:super-admin,web-registration-in-active'],           'uses' => 'RegistrationController@inActive']);
        Route::post('registration/bulk-action',       ['as' => 'registration.bulk-action',      'middleware' => ['ability:super-admin,web-registration-bulk-action'],         'uses' => 'RegistrationController@bulkAction']);
        Route::post('registration/academicInfo-html', ['as' => 'registration.academicInfo-html',                                                                          'uses' => 'RegistrationController@academicInfoHtml']);
        Route::get('registration/{id}/delete-academicInfo',  ['as' => 'registration.delete-academicInfo', 'middleware' => ['ability:super-admin,web-registration-delete-academic-info'], 'uses' => 'RegistrationController@deleteAcademicInfo']);

        Route::post('registration/workExperience-html', ['as' => 'registration.workExperience-html',                                                                          'uses' => 'RegistrationController@workExperienceHtml']);
        Route::get('registration/{id}/delete-workExperience',  ['as' => 'registration.delete-workExperience', 'middleware' => ['ability:super-admin,web-registration-delete-work-experience'], 'uses' => 'RegistrationController@deleteWorkExperience']);


        /* Programme Routes */
        Route::get('registration/programme',                    ['as' => 'registration.programme',                  'middleware' => ['ability:super-admin,web-programme-index'],               'uses' => 'ProgrammeController@index']);
        Route::get('registration/programme/add',                ['as' => 'registration.programme.add',              'middleware' => ['ability:super-admin,web-programme-add'],                 'uses' => 'ProgrammeController@add']);
        Route::post('registration/programme/store',             ['as' => 'registration.programme.store',            'middleware' => ['ability:super-admin,web-programme-add'],                 'uses' => 'ProgrammeController@store']);
        Route::get('registration/programme/{id}/edit',          ['as' => 'registration.programme.edit',             'middleware' => ['ability:super-admin,web-programme-edit'],                'uses' => 'ProgrammeController@edit']);
        Route::post('registration/programme/{id}/update',       ['as' => 'registration.programme.update',           'middleware' => ['ability:super-admin,web-programme-edit'],                'uses' => 'ProgrammeController@update']);
        Route::get('registration/programme/{id}/view',          ['as' => 'registration.programme.view',             'middleware' => ['ability:super-admin,web-programme-view'],                'uses' => 'ProgrammeController@view']);
        Route::get('registration/programme/{id}/delete',        ['as' => 'registration.programme.delete',           'middleware' => ['ability:super-admin,web-programme-delete'],              'uses' => 'ProgrammeController@delete']);
        Route::get('registration/programme/{id}/active',        ['as' => 'registration.programme.active',           'middleware' => ['ability:super-admin,web-programme-active'],              'uses' => 'ProgrammeController@active']);
        Route::get('registration/programme/{id}/in-active',     ['as' => 'registration.programme.in-active',        'middleware' => ['ability:super-admin,web-programme-in-active'],           'uses' => 'ProgrammeController@inActive']);
        Route::post('registration/programme/bulk-action',       ['as' => 'registration.programme.bulk-action',      'middleware' => ['ability:super-admin,web-programme-bulk-action'],         'uses' => 'ProgrammeController@bulkAction']);


    //Contact Manager Routes
        //Registration Routes */
        Route::get('contact',                    ['as' => 'contact',                  'middleware' => ['ability:super-admin,web-contact-index'],               'uses' => 'ContactDetailController@index']);
        Route::get('contact/add',                ['as' => 'contact.add',              'middleware' => ['ability:super-admin,web-contact-add'],                 'uses' => 'ContactDetailController@add']);
        Route::post('contact/store',             ['as' => 'contact.store',            'middleware' => ['ability:super-admin,web-contact-add'],                 'uses' => 'ContactDetailController@store']);
        Route::get('contact/{id}/edit',          ['as' => 'contact.edit',             'middleware' => ['ability:super-admin,web-contact-edit'],                'uses' => 'ContactDetailController@edit']);
        Route::post('contact/{id}/update',       ['as' => 'contact.update',           'middleware' => ['ability:super-admin,web-contact-edit'],                'uses' => 'ContactDetailController@update']);
        Route::get('contact/{id}/view',          ['as' => 'contact.view',             'middleware' => ['ability:super-admin,web-contact-view'],                'uses' => 'ContactDetailController@view']);
        Route::get('contact/{id}/delete',        ['as' => 'contact.delete',           'middleware' => ['ability:super-admin,web-contact-delete'],              'uses' => 'ContactDetailController@delete']);
        Route::get('contact/{id}/active',        ['as' => 'contact.active',           'middleware' => ['ability:super-admin,web-contact-active'],              'uses' => 'ContactDetailController@active']);
        Route::get('contact/{id}/in-active',     ['as' => 'contact.in-active',        'middleware' => ['ability:super-admin,web-contact-in-active'],           'uses' => 'ContactDetailController@inActive']);
        Route::post('contact/bulk-action',       ['as' => 'contact.bulk-action',      'middleware' => ['ability:super-admin,web-contact-bulk-action'],         'uses' => 'ContactDetailController@bulkAction']);
        Route::get('contact/import',             ['as' => 'contact.import',           'middleware' => ['ability:super-admin,web-contact-import'],              'uses' => 'ContactDetailController@importContactDetail']);
        Route::post('contact/import',            ['as' => 'contact.bulk.import',      'middleware' => ['ability:super-admin,web-contact-import'],              'uses' => 'ContactDetailController@handleImportContactDetail']);

        /* Contact Group Routes */
        Route::get('contact/group',                    ['as' => 'contact.group',                  'middleware' => ['ability:super-admin,web-contact-group-index'],               'uses' => 'ContactGroupController@index']);
        Route::get('contact/group/add',                ['as' => 'contact.group.add',              'middleware' => ['ability:super-admin,web-contact-group-add'],                 'uses' => 'ContactGroupController@add']);
        Route::post('contact/group/store',             ['as' => 'contact.group.store',            'middleware' => ['ability:super-admin,web-contact-group-add'],                 'uses' => 'ContactGroupController@store']);
        Route::get('contact/group/{id}/edit',          ['as' => 'contact.group.edit',             'middleware' => ['ability:super-admin,web-contact-group-edit'],                'uses' => 'ContactGroupController@edit']);
        Route::post('contact/group/{id}/update',       ['as' => 'contact.group.update',           'middleware' => ['ability:super-admin,web-contact-group-edit'],                'uses' => 'ContactGroupController@update']);
        Route::get('contact/group/{id}/view',          ['as' => 'contact.group.view',             'middleware' => ['ability:super-admin,web-contact-group-view'],                'uses' => 'ContactGroupController@view']);
        Route::get('contact/group/{id}/delete',        ['as' => 'contact.group.delete',           'middleware' => ['ability:super-admin,web-contact-group-delete'],              'uses' => 'ContactGroupController@delete']);
        Route::get('contact/group/{id}/active',        ['as' => 'contact.group.active',           'middleware' => ['ability:super-admin,web-contact-group-active'],              'uses' => 'ContactGroupController@active']);
        Route::get('contact/group/{id}/in-active',     ['as' => 'contact.group.in-active',        'middleware' => ['ability:super-admin,web-contact-group-in-active'],           'uses' => 'ContactGroupController@inActive']);
        Route::post('contact/group/bulk-action',       ['as' => 'contact.group.bulk-action',      'middleware' => ['ability:super-admin,web-contact-group-bulk-action'],         'uses' => 'ContactGroupController@bulkAction']);

        /*Info Center Grouping*/
        Route::group(['prefix' => 'info/',                                      'as' => 'info.',                                        'namespace' => 'Info\\'], function () {

            /*SMS Email Routes*/
            Route::get('smsemail',                          ['as' => 'smsemail',                                'middleware' => ['ability:super-admin,web-sms-email-index'],                     'uses' => 'SmsEmailController@index']);
            Route::get('smsemail/{id}/delete',              ['as' => 'smsemail.delete',                         'middleware' => ['ability:super-admin,web-sms-email-delete'],                    'uses' => 'SmsEmailController@delete']);
            Route::post('smsemail/bulk-action',             ['as' => 'smsemail.bulk-action',                    'middleware' => ['ability:super-admin,web-sms-email-bulk-action'],               'uses' => 'SmsEmailController@bulkAction']);

            /*Subscriber Mail*/
            Route::get('smsemail/subscriber',                       ['as' => 'smsemail.subscriber',                                 'middleware' => ['ability:super-admin,web-sms-email-subscriber'],               'uses' => 'SmsEmailController@subscriberMail']);
            Route::post('smsemail/subscriber/send',                 ['as' => 'smsemail.subscriber.send',                            'middleware' => ['ability:super-admin,web-sms-email-subscriber'],               'uses' => 'SmsEmailController@subscriberMailSend']);

            Route::get('smsemail/{id}/blog-notification',            ['as' => 'smsemail.subscriber-blog-notification',              'middleware' => ['ability:super-admin,web-sms-email-subscriber'],              'uses' => 'SmsEmailController@mailBlogToSubscriber']);
            Route::get('smsemail/{id}/notice-notification',          ['as' => 'smsemail.subscriber-notice-notification',            'middleware' => ['ability:super-admin,web-sms-email-subscriber'],              'uses' => 'SmsEmailController@mailNoticeToSubscriber']);
            Route::get('smsemail/{id}/event-notification',           ['as' => 'smsemail.subscriber-event-notification',             'middleware' => ['ability:super-admin,web-sms-email-subscriber'],              'uses' => 'SmsEmailController@mailEventToSubscriber']);

            /*Group*/
            Route::get('smsemail/create',                   ['as' => 'smsemail.create',                         'middleware' => ['ability:super-admin,web-sms-email-create'],                   'uses' => 'SmsEmailController@create']);
            Route::post('smsemail/send',                    ['as' => 'smsemail.send',                           'middleware' => ['ability:super-admin,web-sms-email-send'],                     'uses' => 'SmsEmailController@send']);

            /*StudentGuardian*/
            Route::get('smsemail/staff',                    ['as' => 'smsemail.staff',                          'middleware' => ['ability:super-admin,web-sms-email-create'],                   'uses' => 'SmsEmailController@staff']);
            Route::post('smsemail/staff/send',              ['as' => 'smsemail.staff.send',                     'middleware' => ['ability:super-admin,web-sms-email-send'],                'uses' => 'SmsEmailController@staffSend']);

            /*Individual*/
            Route::get('smsemail/individual',               ['as' => 'smsemail.individual',                     'middleware' => ['ability:super-admin,web-sms-email-create'],                   'uses' => 'SmsEmailController@individual']);
            Route::post('smsemail/individual/send',         ['as' => 'smsemail.individual.send',                'middleware' => ['ability:super-admin,web-sms-email-send'],           'uses' => 'SmsEmailController@individualSend']);
        });
    });

    //Public Website Routes
    Route::get('',                                  ['as' => 'home',                            'uses' => 'Website\HomeController@index']);
    Route::get('/about-us',                         ['as' => 'about-us',                        'uses' => 'Website\AboutUsController@index']);
    Route::get('/contact-us',                       ['as' => 'contact-us',                      'uses' => 'Website\ContactUsController@index']);
    Route::post('/contact-us/store',                ['as' => 'contact-us.store',                'uses' => 'Website\ContactUsController@store']);

    Route::get('/subscribers',                      ['as' => 'subscribers.index',               'uses' => 'Website\SubscribersController@index']);
    Route::post('/subscribers',                     ['as' => 'subscribers.store',               'uses' => 'Website\SubscribersController@store']);
    Route::get('/subscribers/{id}/active',          ['as' => 'subscribers.active',              'uses' => 'Website\SubscribersController@active']);
    Route::get('/subscribers/{id}/in-active',       ['as' => 'subscribers.in-active',           'uses' => 'Website\SubscribersController@inActive']);

    Route::get('/staffs',                           ['as' => 'staffs',                          'uses' => 'Website\StaffController@index']);
    Route::get('/staffs/{id}/detail',               ['as' => 'staffs.detail',                   'uses' => 'Website\StaffController@detail']);
    Route::post('/staffs/message',                  ['as' => 'staffs.message',                  'uses' => 'Website\StaffController@message']);

    Route::get('/faqs',                             ['as' => 'faqs',                            'uses' => 'Website\FaqsController@index']);
    Route::get('/pricing',                          ['as' => 'pricing',                         'uses' => 'Website\PricingController@index']);
    Route::get('/clients',                          ['as' => 'clients',                         'uses' => 'Website\ClientController@index']);
    Route::get('/services',                         ['as' => 'services',                         'uses' => 'Website\ServiceController@index']);

    Route::get('/category',                         ['as' => 'category',                        'uses' => 'Website\CategoryController@index']);
    Route::get('/category/{slug}',                  ['as' => 'category.detail',                 'uses' => 'Website\CategoryController@CategoryPost']);

    Route::get('/blog',                             ['as' => 'blog',                            'uses' => 'Website\BlogController@index']);
    Route::get('/blog/{slug}',                      ['as' => 'blog.detail',                     'uses' => 'Website\BlogController@blogDetail']);


    Route::get('/notice',                           ['as' => 'notice',                          'uses' => 'Website\NoticeController@index']);
    Route::get('/notice/{slug}',                    ['as' => 'notice.detail',                   'uses' => 'Website\NoticeController@noticeDetail']);

    Route::get('/events',                           ['as' => 'events',                          'uses' => 'Website\EventsController@index']);
    Route::get('/events/{slug}',                    ['as' => 'events.detail',                   'uses' => 'Website\EventsController@eventDetail']);

    Route::get('/gallery',                          ['as' => 'gallery',                         'uses' => 'Website\GalleryController@index']);
    Route::get('/gallery/{slug}',                   ['as' => 'gallery.detail',                  'uses' => 'Website\GalleryController@galleryDetail']);

    Route::get('/page',                             ['as' => 'page',                            'uses' => 'Website\PageController@index']);
    Route::get('/page/{slug}',                      ['as' => 'page.detail',                     'uses' => 'Website\PageController@pageDetail']);

    Route::get('/downloads',                         ['as' => 'download',                        'uses' => 'Website\DownloadController@index']);

    Route::get('/online-registration',              ['as' => 'online-registration',             'uses' => 'Website\RegistrationController@index']);
    Route::post('/registration/store',              ['as' => 'registration.store',              'uses' => 'Website\RegistrationController@store']);

    Route::get('/find-registration',                ['as' => 'find-registration',               'uses' => 'Website\RegistrationController@findApplication']);
    Route::post('/registration-print',              ['as' => 'registration-print',              'uses' => 'Website\RegistrationController@printApplication']);


    Route::post('/registration/academicInfo-html',   ['as' => 'registration.academicInfo-html',   'uses' => 'Website\RegistrationController@academicInfoHtml']);
    Route::post('/registration/workExperience-html', ['as' => 'registration.workExperience-html', 'uses' => 'Website\RegistrationController@workExperienceHtml']);

    Route::get('/{page?}',         ['as' => '404',                'uses' => 'ErrController@pageNotFound'])->where('page','.*');
});

