<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;

Route::post('/attendance/detect', [AttendanceController::class, 'detect']);

