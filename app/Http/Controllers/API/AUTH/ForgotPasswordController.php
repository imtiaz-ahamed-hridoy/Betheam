<?php

namespace App\Http\Controllers\API\AUTH;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Jobs\ForgotPasswordJob;
use App\Repositories\OtpRepository;
use App\Services\OtpService;
use App\Repositories\UserRepository;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    protected OtpService $otpService;
    protected OtpRepository $otpRepo;
    protected UserRepository $userRepo;

    public function __construct(
        OtpService $otpService,
        OtpRepository $otpRepo,
        UserRepository $userRepo
    ) {
        $this->otpService = $otpService;
        $this->otpRepo = $otpRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * Send OTP for Forgot Password
     */
    public function sendResetOtp(ForgotPasswordRequest $request)
    {
        $user = $this->otpRepo->findUsersByLogin($request->email);

        if (! $user) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User not found.'
            ], 404);
        }

        $otp = $this->otpService->generateOtp($user);

        // Dispatch email job
        ForgotPasswordJob::dispatch($user);

        return response()->json([
            'status' => 'Success',
            'message' => 'A password reset OTP has been sent to your email.',
            'data' => [
                'otp_code' => $otp,
                'otp_expires_at' => $user->otp_expires_at,
            ]
        ]);
    }

    /**
     * Resend Forgot Password OTP
     */
    public function resendResetOtp(ForgotPasswordRequest $request)
    {
        $user = $this->otpRepo->findUsersByLogin($request->email);

        if (! $user) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User not found.'
            ], 404);
        }

        // Resend OTP (no action_type required because your service signature is simple)
        $otp = $this->otpService->resendOtp($user);

        ForgotPasswordJob::dispatch($user);

        return response()->json([
            'status' => 'Success',
            'message' => 'A new password reset OTP has been sent to your email.',
            'data' => [
                'otp_code' => $otp,
                'otp_expires_at' => $user->otp_expires_at,
            ]
        ]);
    }

    /**
     * Verify Forgot Password OTP + Generate Reset Token
     */
    public function sendResetOtpVerify(ForgotPasswordRequest $request)
    {
        $user = $this->otpRepo->findUsersByLogin($request->email);

        if (! $user) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User not found.'
            ], 404);
        }

        // Verify OTP
        $isVerify = $this->otpService->verifyOtp($user, $request->otp_code);

        if (! $isVerify) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Invalid or expired OTP.'
            ], 422);
        }

        // Generate reset token
        $resetToken = Str::random(60);
        $tokenExpiresAt = now()->addMinutes(10);


        $this->userRepo->resetPasswordToken($user, $resetToken,$tokenExpiresAt);

        return response()->json([
            'status' => 'Success',
            'message' => 'OTP verified successfully. Use the token to reset password.',
            'data' => [
                'reset_token' => $resetToken,
                'reset_token_expires_at' => $tokenExpiresAt,
            ]
        ]);
    }
}
