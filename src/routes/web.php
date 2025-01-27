<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;
Route::middleware(['auth', 'staff'])->group(function () {
    Route::resource('attendance', AttendanceController::class)
        ->only(['index', 'store', 'update']);
    Route::get('attendance/list', [AttendanceController::class, 'list'])
        ->name('attendance.list');
});