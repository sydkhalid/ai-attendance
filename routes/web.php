<?php

use Illuminate\Support\Facades\Route;

// ========== ADMIN CONTROLLERS ==========
use App\Http\Controllers\Admin\StudentAdminController;
use App\Http\Controllers\Admin\AttendanceAdminController;

// ================================================================
// ROOT REDIRECT → LOGIN
// ================================================================
Route::get('/', function () {
    return redirect()->route('login');
});
Route::prefix('admin/students')->group(function () {

    Route::get('/', [StudentAdminController::class, 'index'])->name('admin.students.index');
    Route::post('/store', [StudentAdminController::class, 'store'])->name('admin.students.store');

    Route::get('/show/{id}', [StudentAdminController::class, 'show']);
    Route::post('/update/{id}', [StudentAdminController::class, 'update']);

    Route::delete('/delete/{id}', [StudentAdminController::class, 'destroy']);

    // B2 FEATURE — DELETE SINGLE IMAGE
    Route::delete('/image/delete', [StudentAdminController::class, 'deleteImage']);
});

// ================================================================
// ADMIN ROUTES (AUTH REQUIRED)
// ================================================================
Route::middleware(['auth'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        // -------------------------
        // DASHBOARD
        // -------------------------
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');



        // ============================================================
        // ATTENDANCE MODULE
        // ============================================================

        Route::get('/attendance', [AttendanceAdminController::class, 'index'])
            ->name('attendance.index');

        Route::get('/attendance/export', [AttendanceAdminController::class, 'export'])
            ->name('attendance.export');
    });


// ================================================================
// AUTH ROUTES (LOGIN / LOGOUT / REGISTER / PASSWORD RESET)
// ================================================================
require __DIR__ . '/auth.php';
