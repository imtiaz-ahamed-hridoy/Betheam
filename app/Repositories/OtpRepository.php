<?php

namespace App\Repositories;

use App\Models\User;

class OtpRepository
{
    public function findUsersByLogin(string $identifier)
    {

        return User::where('email', $identifier)->orWhere('phone', $identifier)->first();
    }

    public function otpUpdate(User $user, string $otp)
    {

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'otp_verified' => false,
        ]);

    }
}
