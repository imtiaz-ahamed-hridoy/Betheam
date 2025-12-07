<?php

namespace App\Http\Controllers\API\AUTH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
   public function resetPassword(Request $request){
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

            }
        );

        if( $status === Password::PasswordReset ){

            return response()->json(['message' => 'Password reset successfully.'],200);
        }
         

        return response()->json([
            'message'=> __($status)
        ],400);

   }
}
