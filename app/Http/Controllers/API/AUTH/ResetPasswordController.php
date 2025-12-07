<?php

namespace App\Http\Controllers\API\AUTH;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\ResetPasswordRequest;
use App\Repositories\{OtpRepository, UserRepository};
use App\Services\OtpService;

class ResetPasswordController extends Controller
{
    protected $otpService;
    protected $otpRepo;
    protected $userRepo;

    public function __construct(OtpService $otpService, OtpRepository $otpRepo, UserRepository $userRepo)
    {
        $this->otpService = $otpService;
        $this->otpRepo = $otpRepo;
        $this->userRepo = $userRepo;
    }

    public function resetPasswordVerify(ResetPasswordRequest $resetPasswordRequest)
    {
        $user = $this->otpRepo->findUsersByLogin($resetPasswordRequest->email);
       
        if (!$user) {
            return response()->json(['status' => 'Error', 'message' => 'User not found.'], 404);
        }
        // Verify OTP
         $isVerify = $this->otpService->verifyOtp($user, $resetPasswordRequest->otp_code);

         $resetPasswordToken = Str::random(60);

         $this->userRepo->resetPasswordToken($user, $resetPasswordToken);

        if (!$isVerify) {
            return response()->json(['status' => 'Error', 'message' => 'Invalid or expired OTP.'], 422);
        }

        // Update password
        $this->userRepo->updateResetPassword($user, $resetPasswordRequest->password);

        return response()->json(['status' => 'Success', 'message' => 'OTP verified successfully.'], 200);

    }

    public functiontion resetPassword(ResetPasswordRequest $resetPasswordRequest)
    {
        $user = $this->otpRepo->findUsersByLogin($resetPasswordRequest->email);
       
        if (!$user) {
            return response()->json(['status' => 'Error', 'message' => 'User not found.'], 404);
        }

        // Update password
        $this->userRepo->updateResetPassword($user, $resetPasswordRequest->password);

        return response()->json(['status' => 'Success', 'message' => 'Password reset successfully.'], 200);

    }   

    
}
