<?php

use Illuminate\Support\Facades\Route;

// ADMIN CONTROLLERS
use App\Http\Controllers\Admin\StudentAdminController;
use App\Http\Controllers\Admin\AttendanceAdminController;

/*
|--------------------------------------------------------------------------
| ROOT REDIRECT → LOGIN
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| AUTH ROUTES (LOGIN / LOGOUT / REGISTER / PASSWORD RESET)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | STUDENTS MODULE
        |--------------------------------------------------------------------------
        */
        Route::prefix('students')->as('students.')->group(function () {

            Route::get('/', [StudentAdminController::class, 'index'])->name('index');
            Route::post('/store', [StudentAdminController::class, 'store'])->name('store');

            Route::get('/show/{id}', [StudentAdminController::class, 'show'])->name('show');
            Route::post('/update/{id}', [StudentAdminController::class, 'update'])->name('update');

            Route::delete('/delete/{id}', [StudentAdminController::class, 'destroy'])->name('delete');

            // Delete a single image from gallery
            Route::delete('/image/delete', [StudentAdminController::class, 'deleteImage'])->name('image.delete');
        });


        /*
        |--------------------------------------------------------------------------
        | ATTENDANCE MODULE
        |--------------------------------------------------------------------------
        */
        Route::prefix('attendance')->as('attendance.')->group(function () {

            Route::get('/', [AttendanceAdminController::class, 'index'])->name('index');

            Route::get('/export', [AttendanceAdminController::class, 'export'])->name('export');
        });

    });
