<?php

use App\Http\Controllers\Api\V1\User\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum', 'ability:user'])->group(function () {
    // verified
    Route::middleware(['verified'])->group(function () {
        Route::get('user', function () {
            return 'aaa';
        });
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('email/verify/{id}/{hash}', 'verify')->middleware('signed')->name('verification.verify');
    Route::post('forgot-password', 'forgotPassword')->middleware('guest')->name('password.email');
    Route::get('reset-password/{token}', 'resetPassword')->middleware('guest')->name('password.reset');
    Route::post('reset-password', 'updatePassword')->middleware('guest')->name('password.update');
});
