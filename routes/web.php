<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/securedocs/login');

Route::middleware('guest')->group(function () {
    Route::inertia('/securedocs/login', 'Auth/Login')->name('login.page');
    Route::inertia('/securedocs/register', 'Auth/Register')->name('register.page');

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    
    // Pages Rendering Endpoints
    Route::get('/project-files', [DocumentController::class, 'RenderProjectFiles'])->name('project_files.page');
    Route::inertia('/recycle-bin', 'RecycleBin')->name('recycle_bin.page');
    Route::inertia('/profile', 'Profile')->name('profile.page');
    Route::inertia('/apperance', 'Apperance')->name('apperance.page');


    // Documents Handling Endpoints
    Route::post('/upload', [DocumentController::class, 'uploadDocuments']);
    Route::delete('/delete/{documentName}', [DocumentController::class, 'deleteDocuments']);
    

    // Profile Handling Endpoints
    Route::post('/edit/profile', [AuthController::class, 'editProfile']);
    Route::post('/upload/profile/picture', [AuthController::class, 'uploadProfilePicture']);


    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});



