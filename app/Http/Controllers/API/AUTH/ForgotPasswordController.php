<?php

namespace App\Http\Controllers\API\AUTH;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Jobs\ForgotPasswordJob;
use App\Repositories\OtpRepository;
use App\Services\OtpService;

class ForgotPasswordController extends Controller
{
    protected $otpRepo;

    protected $otpService;

    public function __construct(OtpRepository $otpRepo, OtpService $otpService)
    {
        $this->otpRepo = $otpRepo;
        $this->otpService = $otpService;
    }

    public function sendResetOtp(ForgotPasswordRequest $forgotPasswordRequest)
    {

        $user = $this->otpRepo->findUsersByLogin($forgotPasswordRequest->email);

        if (! $user) {

            return response()->json(['status' => 'Error', 'message' => 'User not found.'], 404);
        }

        $otp = $this->otpService->generateOtp($user);

        ForgotPasswordJob::dispatch($user);

        return response()->json([
            'status' => 'Success',
            'message' => 'A password reset OTP has been sent to your email.',
            'data' => [
                'otp_expires_at' => $user->otp_expires_at,
                'otp' => $otp, // Return the OTP for the client to use
            ],
        ]);

    }
}
