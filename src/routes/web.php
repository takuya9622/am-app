<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::middleware('guest')->group(function () {
    Route::get('admin/login', [AuthenticatedSessionController::class, 'create'])->name('admin.login');
    Route::post('admin/login', [AuthenticatedSessionController::class, 'store']);
});


Route::middleware(['auth', 'staff'])->group(function () {
    Route::resource('attendance', AttendanceController::class)
        ->only(['index', 'store', 'update']);
    Route::get('attendance/list', [AttendanceController::class, 'list'])
        ->name('attendance.list');
});