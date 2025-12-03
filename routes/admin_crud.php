<?php

use Illuminate\Support\Facades\Route;

Route::crud('students', \App\Http\Controllers\Admin\StudentAdminController::class);

Route::crud('users', \App\Http\Controllers\Admin\UserController::class);
