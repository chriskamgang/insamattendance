<?php

use App\Http\Controllers\Api\AllController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::group(['prefix' => '1.0v'], static function () {
    checkCreated();
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
});
if(checkUserRole()) {
    Route::group(['prefix' => '1.0v', 'middleware' => ['auth:api']], static function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

        Route::get('/get-all-notice', [AllController::class, 'getAllNotice'])->name('api.getAllNotice');
        Route::get('/get-all-holiday', [AllController::class, 'getCurrentHolidayList'])->name('api.getCurrentHolidayList');
        Route::get('/get-company-details', [AllController::class, 'getCompanyDetails'])->name('api.getCompanyDetails');
        Route::post('/update-company-details', [AllController::class, 'updateCompanyDetails'])->name('api.updateCompanyDetails');
        Route::get('/get-dashboard', [AllController::class, 'dashboard'])->name('api.dashboard');

        Route::get('/get-shift', [UserController::class, 'getShift'])->name('api.getShift');
        Route::get('/get-department', [UserController::class, 'getDepartment'])->name('api.getDepartment');
        Route::post('/save-user', [UserController::class, 'saveUser'])->name('api.saveUser');
        Route::get('/list-user', [UserController::class, 'listUser'])->name('api.listUser');
        Route::get('/{user_id}/get-user-detail', [UserController::class, 'getUserDetails'])->name('api.getUserDetails');
        Route::post('/update-user', [UserController::class, 'updateUser'])->name('api.updateUser');
        Route::post('/save-face_ids', [UserController::class, 'saveFaceIds'])->name('api.saveFaceIds');
        Route::post('/delete-face_ids', [UserController::class, 'deleteAllFaceIds'])->name('api.deleteAllFaceIds');
        Route::post('/delete-user', [UserController::class, 'deleteUser'])->name('api.deleteUser');
        Route::get('/get-all-faceIds', [UserController::class, 'getAllFaceIdS'])->name('api.getAllFaceIdS');


        Route::post('/save-employee-attendance', [AttendanceController::class, 'employeeAttendance'])->name('api.employeeAttendance');
        Route::get('/get-today-attendance', [AttendanceController::class, 'getTodayAttendance'])->name('api.getTodayAttendance');

    });
}
if(checkUserPermission()) {
    Route::group(['prefix' => '1.0v', 'middleware' => ['auth:api']], static function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

        Route::get('/get-all-notice', [AllController::class, 'getAllNotice'])->name('api.getAllNotice');
        Route::get('/get-all-holiday', [AllController::class, 'getCurrentHolidayList'])->name('api.getCurrentHolidayList');
        Route::get('/get-company-details', [AllController::class, 'getCompanyDetails'])->name('api.getCompanyDetails');
        Route::post('/update-company-details', [AllController::class, 'updateCompanyDetails'])->name('api.updateCompanyDetails');
        Route::get('/get-dashboard', [AllController::class, 'dashboard'])->name('api.dashboard');

        Route::get('/get-shift', [UserController::class, 'getShift'])->name('api.getShift');
        Route::get('/get-department', [UserController::class, 'getDepartment'])->name('api.getDepartment');
        Route::post('/save-user', [UserController::class, 'saveUser'])->name('api.saveUser');
        Route::get('/list-user', [UserController::class, 'listUser'])->name('api.listUser');
        Route::get('/{user_id}/get-user-detail', [UserController::class, 'getUserDetails'])->name('api.getUserDetails');
        Route::post('/update-user', [UserController::class, 'updateUser'])->name('api.updateUser');
        Route::post('/save-face_ids', [UserController::class, 'saveFaceIds'])->name('api.saveFaceIds');
        Route::post('/delete-face_ids', [UserController::class, 'deleteAllFaceIds'])->name('api.deleteAllFaceIds');
        Route::post('/delete-user', [UserController::class, 'deleteUser'])->name('api.deleteUser');
        Route::get('/get-all-faceIds', [UserController::class, 'getAllFaceIdS'])->name('api.getAllFaceIdS');


        Route::post('/save-employee-attendance', [AttendanceController::class, 'employeeAttendance'])->name('api.employeeAttendance');
        Route::get('/get-today-attendance', [AttendanceController::class, 'getTodayAttendance'])->name('api.getTodayAttendance');

    });
}

