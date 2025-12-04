<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'stats']);
    Route::post('/attendance/detect', [AttendanceController::class, 'detect']);
    Route::post('/attendance/submit', [AttendanceController::class, 'submit']);
});




