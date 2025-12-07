<?php

use App\Http\Controllers\API\AUTH\ForgotPasswordController;
use App\Http\Controllers\API\AUTH\ResetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AUTH\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\AUTH\LogoutController;
use App\Http\Controllers\API\AUTH\OtpController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetOtp']);
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);

Route::post('/otp/verify',[OtpController::class,'otpVerify']);
Route::post('/otp/resend',[OtpController::class,'otpResend']);



