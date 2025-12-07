<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository{

public function create(array $data)
{

        return User::create($data);

}

public function update(array $data)
{

        return User::update($data);

}
public function resetPasswordToken(User $user, $token)
{
    $user->reset_password_token = $token;
    $user->save();
    
}

public function updateResetPassword(User $user, $newPassword)
{
    $user->password = Hash::make($newPassword);
    $user->setRememberToken(Str::random(60));
    $user->save();

}


}