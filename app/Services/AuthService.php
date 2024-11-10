<?php

namespace App\Services;

use App\Exceptions\ClientErrorException;
use App\Models\User;

class AuthService
{


    public function register(array $data) : User
    {
        return User::create($data);
    }

    public function login(array $data)
    {
        $user = User::whereEmail($data['email'])->first();

        if (!$user) {
            throw new ClientErrorException("Email is not associated with any user");
        }

        if (!password_verify($data['password'], $user->password)) {
            throw new ClientErrorException("Incorrect password!");
        }

        $token = $user->createToken('BL', ['user'])->accessToken;

        if($token){
            return [
                'accessToken' => $token,
                'user' => $user
            ];
        }

        throw new ClientErrorException("Error logging in. Please try again!");


    }
}
