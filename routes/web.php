<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;


// 404 not found handler
Route::fallback(function () {
    return redirect()->route('login.page'); // Redirect to your custom login page
});

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
    Route::get('/recycle-bin', [DocumentController::class, 'RenderRecycleBin'])->name('recycle_bin.page');

    Route::get('/profile', [AuthController::class, 'RenderProfile'])->name('profile.page');
    Route::inertia('/apperance', 'Apperance')->name('apperance.page');


    // Documents Handling Endpoints
    Route::post('/upload', [DocumentController::class, 'uploadDocuments']);
    Route::put('/document/recycle/bin/{document_id}', [DocumentController::class, 'putToRecycleBin']);
    Route::put('/restore/{document_id}', [DocumentController::class, 'restoreDocument']);

    Route::delete('/delete/{documentID}/{documentName}', [DocumentController::class, 'deleteForeverDocuments']);
    

    // Profile Handling Endpoints
    Route::post('/edit/profile', [AuthController::class, 'editProfile']);
    Route::post('/upload/profile/picture', [AuthController::class, 'uploadProfilePicture']);


    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});



