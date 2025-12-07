<?php

namespace App\Http\Controllers\API\AUTH;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    //login user
    public function login(LoginRequest $loginRequest)
    {

        $user = User::where('email', $loginRequest->login)
                    ->orWhere('phone', $loginRequest->login)
                    ->first();

        if(!$user || !Hash::check($loginRequest->password, $user->password)){
            return response()->json([
                'status' => 'Error',
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'Success',
            'message' => 'User logged in successfully',
            'token' => $token,
            'user' => $user
        ]);

        

    }
}
