<?php

namespace App\Http\Controllers\API\AUTH;

use App\Http\Controllers\Controller;
use App\Http\Requests\{ResendOtpRequest, VerifyOtpRequest};
use App\Repositories\OtpRepository;
use App\Services\OtpService;

class OtpController extends Controller
{
    protected $otpRepo;

    protected $otpService;

    public function __construct(OtpRepository $otpRepo, OtpService $otpService)
    {
        $this->otpRepo = $otpRepo;
        $this->otpService = $otpService;
    }

    public function otpResend(ResendOtpRequest $resendOtpRequest)
    {

        $user = $this->otpRepo->findUsersByLogin($resendOtpRequest->identifier);

        if (! $user) {

            return response()->json(['status' => 'Error', 'message' => 'User not found.'], 404);
        }

        $otp = $this->otpService->resendOtp($user);
        $otpStillValid = $user->otp_expires_at && now()->lessThan($user->otp_expires_at);

      return response()->json([
    'status' => 'Success',
    'message' => $otpStillValid
        ? 'OTP is still active and has not expired.'
        : 'A new OTP has been generated and sent successfully.',
    'data' => [
        'otp_expires_at' => $user->otp_expires_at,
        'otp' => $otp,
    ],
]);

    }

    public function otpVerify(VerifyOtpRequest $verifyOtpRequest)
    {

        $user = $this->otpRepo->findUsersByLogin($verifyOtpRequest->identifier);

        if (! $user) {

            return response()->json(['status' => 'Error', 'message' => 'User not found.'], 404);
        }

        $isVerify = $this->otpService->verifyOtp($user, $verifyOtpRequest->otp);

        if (! $isVerify) {
            return response()->json(['status' => 'Error', 'message' => 'Invalid or expired OTP.'], 422);
        }

        $user->update(['email_verified_at' => now()]);

        return response()->json([
            'status' => 'Success',
            'message' => 'Email verified successfully. Redirect to dashboard.',
        ]);

    }
}
