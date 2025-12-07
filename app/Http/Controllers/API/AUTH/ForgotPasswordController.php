<?php

namespace App\Http\Controllers\API\AUTH;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Jobs\ForgotPasswordJob;
use App\Repositories\OtpRepository;
use App\Services\OtpService;

class ForgotPasswordController extends Controller
{
    protected OtpRepository $otpRepo;
    protected OtpService $otpService;

    public function __construct(OtpRepository $otpRepo, OtpService $otpService)
    {
        $this->otpRepo = $otpRepo;
        $this->otpService = $otpService;
    }

    // Send OTP for password reset
    public function sendResetOtp(ForgotPasswordRequest $request)
    {
        $user = $this->otpRepo->findUsersByLogin($request->email);

        if (!$user) {
            return response()->json(['status' => 'Error', 'message' => 'User not found.'], 404);
        }

        // Generate OTP for forgot-password action
        $otp = $this->otpService->generateOtp($user);

        // Dispatch job using user ID for reliability
        ForgotPasswordJob::dispatch($user);

        return response()->json([
            'status' => 'Success',
            'message' => 'A password reset OTP has been sent to your email.',
            'data' => [
                'otp_code' => $otp,
                'otp_expires_at' => $user->otp_expires_at,
            ],
        ]);
    }

    // Resend OTP
    public function resendResetOtp(ForgotPasswordRequest $request)
    {
        $user = $this->otpRepo->findUsersByLogin($request->email);

        if (!$user) {
            return response()->json(['status' => 'Error', 'message' => 'User not found.'], 404);
        }

        // Resend OTP
        $otp = $this->otpService->resendOtp($user, 'forgot_password');

        // Dispatch job using user ID
        ForgotPasswordJob::dispatch($user);

        return response()->json([
            'status' => 'Success',
            'message' => 'A new password reset OTP has been sent to your email.',
            'data' => [
                'otp_code' => $otp,
                'otp_expires_at' => $user->otp_expires_at,
            ],
        ]);
    }
}
