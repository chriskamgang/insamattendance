<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CompanyDetailController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\LeaveTypesController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['as' => 'admin.', 'middleware' => ['web']], static function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::get('/login', [AuthController::class, 'getAuthenticate']);
    Route::get('/password/reset', [AuthController::class, 'passwordReset'])->name('passwordReset');
    Route::get('/password/otp-view', [AuthController::class, 'otpView'])->name('otpView');
    Route::get('/password/{unique_code}/password-reset', [AuthController::class, 'passwordResetView'])->name('passwordResetView');

    Route::post('/password/reset-link', [AuthController::class, 'sendPasswordResetLink'])->name('sendPasswordResetLink');
    Route::post('/password/otpVerification', [AuthController::class, 'otpVerification'])->name('otpVerification');
    Route::post('/password/{unique_code}/update-password', [AuthController::class, 'updatePassword'])->name('updatePassword');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
});
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['web','auth']], static function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('user', UserController::class);
    Route::get('/users/{id}/change-password', [UserController::class, 'changePassword'])->name('admin.changePassword');
    Route::post('/users/{id}/save-password', [UserController::class, 'savePassword'])->name('admin.savePassword');

    Route::get('/list', [UserController::class, 'listAdmin'])->name('admin.listAdmin');
    Route::get('/create', [UserController::class, 'createAdmin'])->name('admin.createAdmin');
    Route::post('/store', [UserController::class, 'saveAdmin'])->name('admin.saveAdmin');
    Route::get('/{user}/edit-user', [UserController::class, 'editAdmin'])->name('admin.editAdmin');
    Route::put('/{user}/update-user', [UserController::class, 'updateAdmin'])->name('admin.updateAdmin');

    Route::get('/users/{id}/change-app-password', [UserController::class, 'changeAppPassword'])->name('admin.changeAppPassword');
    Route::post('/users/{id}/save-app-password', [UserController::class, 'saveAppPassword'])->name('admin.saveAppPassword');

    Route::delete('/user/{id}/delete-faceIds', [UserController::class, 'deleteFaceIds'])->name('user.deleteFaceIds');

    Route::resource('department', DepartmentController::class);
    Route::post('/department/{id}/change-status', [DepartmentController::class, 'changeStatus'])->name('department.changeStatus');

    Route::resource('notice', NoticeController::class);
    Route::post('/notice/{id}/change-status', [NoticeController::class, 'changeStatus'])->name('notice.changeStatus');

    Route::resource('holiday', HolidayController::class);
    Route::post('/holiday/{id}/change-status', [HolidayController::class, 'changeStatus'])->name('holiday.changeStatus');

    Route::resource('leaveType', LeaveTypesController::class);
    Route::post('/leaveType/{id}/change-status', [LeaveTypesController::class, 'changeStatus'])->name('leaveType.changeStatus');

    Route::get('/leave', [LeaveController::class, 'index'])->name('leave.index');
    Route::get('/leave/create', [LeaveController::class, 'create'])->name('leave.create');
    Route::post('/leave', [LeaveController::class, 'store'])->name('leave.store');
    Route::get('/leave/{leave_group_code}/edit', [LeaveController::class, 'edit'])->name('leave.edit');
    Route::put('/leave/{leave_group_code}/update', [LeaveController::class, 'update'])->name('leave.update');
    Route::delete('/leave/{leave_group_code}/delete', [LeaveController::class, 'delete'])->name('leave.delete');
    Route::post('/leave/{id}/change-status', [LeaveController::class, 'changeStatus'])->name('leave.changeStatus');

    Route::resource('shift', ShiftController::class);
    Route::post('/shift/{id}/change-status', [ShiftController::class, 'changeStatus'])->name('shift.changeStatus');

    Route::get('/attendance', [AttendanceController::class, 'attendanceList'])->name('attendance.attendanceList');
    Route::get('/attendance/create', [AttendanceController::class, 'attendanceCreate'])->name('attendance.attendanceCreate');
    Route::post('/attendance/attendance-save-detail', [AttendanceController::class, 'attendanceSaveDetail'])->name('attendance.attendanceSaveDetail');

    Route::get('/attendance/{attendance_id}/attendance-edit', [AttendanceController::class, 'attendanceEdit'])->name('attendance.attendanceEdit');
    Route::put('/attendance/{attendance_id}/attendance-update-detail', [AttendanceController::class, 'updateAttendanceDetail'])->name('attendance.updateAttendanceDetail');
    Route::delete('/attendance/{attendance_id}/delete-attendance', [AttendanceController::class, 'deleteAttendance'])->name('attendance.deleteAttendance');

    Route::get('/attendance/{user_id}/check-in', [AttendanceController::class, 'checkInEmployee'])->name('attendance.checkInEmployee');
    Route::get('/attendance/{user_id}/check-out/{attendance_id}', [AttendanceController::class, 'checkOutEmployee'])->name('attendance.checkOutEmployee');
    Route::get('/attendance/{user_id}/lunch-check-in/{attendance_id}', [AttendanceController::class, 'lunchCheckInEmployee'])->name('attendance.lunchCheckInEmployee');
    Route::get('/attendance/{user_id}/lunch-check-out/{attendance_id}', [AttendanceController::class, 'lunchCheckOutEmployee'])->name('attendance.lunchCheckOutEmployee');


    Route::get('/attendance/{user_id}/monthly-attendance-detail', [AttendanceController::class, 'monthlyAttendanceDetail'])->name('attendance.monthlyAttendanceDetail');
    Route::get('/attendance/{user_id}/download-excel-attendance-detail', [AttendanceController::class, 'downloadExcelAttendanceDetail'])->name('attendance.downloadExcelAttendanceDetail');

    Route::get('/company-detail', [CompanyDetailController::class, 'companyDetail'])->name('companyDetail.companyDetail');
    Route::put('/company-detail', [CompanyDetailController::class, 'companyDetailUpdate'])->name('companyDetail.companyDetailUpdate');

    Route::get('/app-setting', [SettingController::class, 'appSetting'])->name('setting.appSetting');
    Route::post('/app-setting-save', [SettingController::class, 'appSettingSave'])->name('setting.appSettingSave');

    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::post('/payroll/justify', [PayrollController::class, 'justify'])->name('payroll.justify');
    Route::post('/payroll/apply-deduction', [PayrollController::class, 'applyDeduction'])->name('payroll.apply-deduction');

});
