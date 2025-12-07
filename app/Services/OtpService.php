<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\OtpRepository;
use App\Jobs\SendOtpEmailJob;

use function Illuminate\Support\now;

class OtpService
{

    protected OtpRepository $otpRepo;

    public function __construct(OtpRepository $otpRepo)
    {
        $this->otpRepo = $otpRepo;
    }


    public function generateOtp(User $user):string
    {

        $otp = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

        $this->otpRepo->otpUpdate($user, $otp);
        
        return $otp;
    }

    public function resendOtp(User $user){
        if($user->otp_expires_at && now()->lessThan($user->otp_expires_at)){
            
            return $user->otp_code;
            
        }

        return $this->generateOtp($user);

    }

    public function verifyOtp(User $user, string $otp )
    {
        if(!$user->otp_code || $user->otp_code !== $otp ||now()->greaterThan($user->otp_expires_at) ){
            return false;
        }

          $user->update([
            'otp_verified' => true,
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return true;


    }
}
