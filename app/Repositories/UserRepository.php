<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository{

public function create(array $data){

        return User::create($data);

}

public function update(array $data){

        return User::update($data);

}
}