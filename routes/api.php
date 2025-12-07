<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AUTH\{ForgotPasswordController, LoginController, LogoutController, OtpController, ResetPasswordController};
use App\Http\Controllers\API\Auth\RegisterController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');
Route::post('email/otp/verify',[OtpController::class,'otpVerify']);
Route::post('email/otp/resend',[OtpController::class,'otpResend']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetOtp']);
Route::post('/forgot-password/otp/resend', [ForgotPasswordController::class, 'resendResetOtp']);
Route::post('/forgot-password/otp/verify', [ForgotPasswordController::class, 'sendResetOtpVerify']);
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);





