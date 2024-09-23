<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/securedocs/login');

Route::middleware('guest')->group(function () {
    Route::inertia('/securedocs/login', 'Auth/Login')->name('login.page');
    Route::inertia('/securedocs/register', 'Auth/Register')->name('register.page');

    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth')->group(function () {
    Route::inertia('/project-files', 'ProjectFiles')->name('project_files.page');
    Route::inertia('/recycle-bin', 'RecycleBin')->name('recycle_bin.page');
    Route::inertia('/profile', 'Profile')->name('profile.page');
    Route::inertia('/apperance', 'Apperance')->name('apperance.page');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});



