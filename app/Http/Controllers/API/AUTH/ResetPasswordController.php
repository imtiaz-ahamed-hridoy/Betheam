<?php

namespace App\Http\Controllers\API\AUTH;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Repositories\{OtpRepository, UserRepository};
use App\Services\OtpService;

class ResetPasswordController extends Controller
{
    protected $otpService;
    protected $otpRepo;
    protected $userRepo;

    public function __construct(
        OtpService $otpService,
        OtpRepository $otpRepo,
        UserRepository $userRepo
    ) {
        $this->otpService = $otpService;
        $this->otpRepo    = $otpRepo;
        $this->userRepo   = $userRepo;
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = $this->otpRepo->findUsersByLogin($request->email);

        if (! $user) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'User not found.'
            ], 404);
        }

        // Verify reset token
        if ($user->reset_password_token !== $request->token ||
            !$user->reset_password_token_expires_at ||
            now()->greaterThan($user->reset_password_token_expires_at)) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'Invalid or expired reset token.'
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->remember_token = Str::random(60);
        $user->save();

        return response()->json([
            'status'  => 'Success',
            'message' => 'Password reset successfully.'
        ], 200);
    }
}
