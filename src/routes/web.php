<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
<<<<<<< HEAD
=======
use App\Http\Controllers\CorrectionController;
>>>>>>> function
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::middleware('guest')->group(function () {
    Route::get('admin/login', [AuthenticatedSessionController::class, 'create'])->name('admin.login');
    Route::post('admin/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/attendance/list', [AdminController::class, 'list'])
        ->name('admin.attendance.list');
    Route::get('admin/staff/list', [AdminController::class, 'staff'])
        ->name('admin.staff.index');
<<<<<<< HEAD
=======
    Route::get('admin/attendance/staff/{staffId}', [AdminController::class, 'staffAttendance'])
        ->name('admin.attendance.staff');
    Route::get('stamp_correction_request/list', [CorrectionController::class, 'list'])
        ->name('correction.request.list');
    Route::get('admin/attendance/staff/{staffId}', [AdminController::class, 'staffAttendance'])
        ->name('admin.attendance.staff');
>>>>>>> function
    Route::get('stamp_correction_request/list', [CorrectionController::class, 'list'])
        ->name('correction.request.list');
});

Route::middleware(['auth', 'staff'])->group(function () {
    Route::resource('attendance', AttendanceController::class)
        ->only(['index', 'store', 'update']);
    Route::get('attendance/list', [AttendanceController::class, 'list'])
        ->name('attendance.list');
<<<<<<< HEAD
=======
    Route::get('attendance/detail/{attendanceId}', [AttendanceController::class, 'edit'])
        ->name('attendance.detail');
    Route::patch('attendance/detail/{attendanceId}', [AttendanceController::class, 'correct'])
        ->name('attendance.correct');
>>>>>>> function
});