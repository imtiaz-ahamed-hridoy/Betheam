<?php

namespace App\Http\Controllers\API\AUTH;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\RegisterService;

class RegisterController extends Controller
{
    protected RegisterService $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    public function store(RegisterRequest $registerRequest)
    {
        // Retrieve validated data
        $validatedData = $registerRequest->validated();

        // Pass to service
        $user = $this->registerService->registerUser($validatedData);

        return response()->json([
            'status' => 'Success',
            'message' => 'User registered successfully. OTP has been sent to your email.',
            'data' => $user,
        ], 201);
    }
}
