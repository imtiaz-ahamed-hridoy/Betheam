<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\OtpRepository;
use App\Jobs\SendOtpEmailJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class OtpService
{
    protected OtpRepository $otpRepo;

    public function __construct(OtpRepository $otpRepo)
    {
        $this->otpRepo = $otpRepo;
    }

    /**
     * Generate a new 4-digit OTP and update user
     */
    public function generateOtp(User $user): string
    {
        $otp = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

        $this->otpRepo->otpUpdate($user, $otp);

        return $otp;
    }

    /**
     * Resend OTP – reuse active OTP or regenerate
     */
    public function resendOtp(User $user): string
    {
        if ($user->otp_expires_at && now()->lessThan($user->otp_expires_at)) {
            return $user->otp_code;
        }

        return $this->generateOtp($user);
    }

    /**
     * Validate and mark OTP as verified
     */
    public function verifyOtp(User $user, string $otp): bool
    {
        if (
            !$user->otp_code ||
            $user->otp_code !== $otp ||
            now()->greaterThan($user->otp_expires_at)
        ) {
            return false;
        }

        $user->update([
            'otp_verified'    => true,
            'otp_code'        => null,
            'otp_expires_at'  => null,
        ]);

        return true;
    }
}
