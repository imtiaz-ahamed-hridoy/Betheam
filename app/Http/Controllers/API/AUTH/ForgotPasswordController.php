<?php

namespace App\Http\Controllers\API\AUTH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request){


        // Send reset link token
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Password reset link sent successfully.'
            ], 200);
        }

        return response()->json([
            'status' => 'Error',
            'message' => __($status)
        ], 400);
    }
}
