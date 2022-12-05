<?php

use App\Http\Controllers\Api\V1\User\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
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
        Route::get('/profile', function () {
            return 'aaa';
        });
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register')->middleware('throttle:1,60');
    Route::post('/login', 'login');
    Route::get('/email/verify/{id}/{hash}', 'verify')->middleware('signed')->name('verification.verify');
    Route::get('/email/verification-notification', 'send')->middleware('throttle:6,1')->name('verification.send');
});

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return response()->json(['status' => $status], 200);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function ($token) {
    return redirect()->to(env('APP_URL_FE') . config('const.uri_fe.reset-password') . '/' . $token);
})->middleware('guest')->name('password.reset');
