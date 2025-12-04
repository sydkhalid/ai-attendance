<?php

use Illuminate\Support\Facades\Route;

// ADMIN CONTROLLERS
use App\Http\Controllers\Admin\StudentAdminController;
use App\Http\Controllers\Admin\AttendanceAdminController;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';
// ADMIN ROUTES (AUTH REQUIRED)
Route::middleware(['auth'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        //STUDENTS MODULE
        Route::prefix('students')->as('students.')->group(function () {
            Route::get('/', [StudentAdminController::class, 'index'])->name('index');
            Route::get('/list', [StudentAdminController::class, 'list'])->name('list');
            Route::post('/store', [StudentAdminController::class, 'store'])->name('store');
            Route::get('/show/{id}', [StudentAdminController::class, 'show'])->name('show');
            Route::post('/update/{id}', [StudentAdminController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [StudentAdminController::class, 'destroy'])->name('delete');
            Route::delete('/image/delete', [StudentAdminController::class, 'deleteImage'])->name('image.delete');
        });
        // ATTENDANCE MODULE
        Route::prefix('attendance')->as('attendance.')->group(function () {
            Route::get('/', [AttendanceAdminController::class, 'index'])->name('index');
            Route::get('/ajax', [AttendanceAdminController::class, 'ajax'])->name('ajax');
            Route::get('/export', [AttendanceAdminController::class, 'export'])->name('export');
        });
    });

