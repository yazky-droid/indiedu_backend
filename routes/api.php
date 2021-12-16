<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\SociaLiteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
// */

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [UserController::class, 'store']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify')->middleware('auth:sanctum');
Route::post('email/verification-notification', [VerificationController::class, 'sendVerificationEmail'])->middleware('auth:sanctum');
Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('reset-password', [NewPasswordController::class, 'reset']);
Route::get('reset-password', [NewPasswordController::class, 'getTokenReset'])->name('password.reset');


Route::get('auth/{provider}', [SociaLiteController::class, 'redirectToProvider']);
Route::get('auth/{provider}/callback', [SociaLiteController::class, 'handleProvideCallback']);
