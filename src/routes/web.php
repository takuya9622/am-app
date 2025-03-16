<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CorrectionController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::middleware('guest')->group(function () {
    Route::get('admin/login', [AuthenticatedSessionController::class, 'create'])->name('admin.login');
    Route::post('admin/login', [AuthenticatedSessionController::class, 'store']);
});

Route::get('stamp_correction_request/list', function () {
    })->middleware(['auth', 'role.redirect'])->name('correction.request.list');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/attendance/list', [AdminController::class, 'list'])
        ->name('admin.attendance.list');
    Route::get('admin/staff/list', [AdminController::class, 'staff'])
        ->name('admin.staff.index');
    Route::get('admin/attendance/staff/{staffId}', [AdminController::class, 'staffAttendance'])
        ->name('admin.attendance.staff');
    Route::get('admin/correction_request/list', [CorrectionController::class, 'list'])
        ->name('admin.correction.list');
    Route::patch('attendance/detail/{attendanceId}', [CorrectionController::class, 'correctionApprove'])
        ->name('admin.approve');
    Route::patch('attendance/raw_correction/{attendanceId}', [CorrectionController::class, 'applyRawCorrection'])
        ->name('admin.raw.correction');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('attendance', AttendanceController::class)
        ->only(['index', 'store', 'update']);
    Route::get('attendance/list', [AttendanceController::class, 'list'])
        ->name('attendance.list');
    Route::get('attendance/detail/{attendanceId}', [AttendanceController::class, 'edit'])
        ->name('attendance.detail');
    Route::post('attendance/detail/{attendanceId}', [AttendanceController::class, 'correctionRequest'])
        ->name('attendance.correct');
    Route::get('attendance/correction_request/list', [CorrectionController::class, 'list'])
        ->name('attendance.correction.list');
    Route::get('approved/detail/{correctionId}', [CorrectionController::class, 'approvedDetail'])
        ->name('approved.detail');
});