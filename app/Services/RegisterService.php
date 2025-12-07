<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Repositories\UserRepository;
use App\Services\OtpService;

class RegisterService
{
    protected UserRepository $userRepo;
    protected OtpService $otpService;

    public function __construct(UserRepository $userRepo, OtpService $otpService)
    {
        $this->userRepo = $userRepo;
        $this->otpService = $otpService;
    }

    /**
     * Register a new user and generate OTP for email verification
     *
     * @param array $data
     * @return User
     */
    public function registerUser(array $data): User
    {
        // Create the user
        $user = $this->userRepo->create([
            'name' => $data['name'],
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'country' => $data['country'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'settings' => $data['settings'] ?? null,
            'otp_verified' => false, // ensure OTP is not verified on creation
        ]);

        // Generate OTP and send to user's email
        $this->otpService->generateOtp($user);

        return $user;
    }
}
