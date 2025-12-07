<?php

namespace App\Http\Controllers\API\AUTH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Safety check in case token is already revoked or missing
        if (!$request->user() || !$request->user()->currentAccessToken()) {
            return response()->json([
                'message' => 'No active session found.',
            ], 400);
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful.',
        ], 200);
    }
}
