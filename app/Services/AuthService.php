<?php

namespace App\Services;

use App\Models\User;

class AuthService
{


    public function register(array $data) : User
    {
        return User::create($data);
    }

    public function login(array $data)
    {
        $user = User::whereEmail($data['email']);

        if (!$user) {
            throw new ClientErrorException("Email is not associated with any " . $this->guard);
        }

        if (!password_verify($request['password'], $user->password)) {
            throw new ClientErrorException("Incorrect password!");
        }

        $token = User::createToken('BL', ['api'])->accessToken;

        if($token){
            return [
                'accessToken' => $token,
                'user' => $user
            ];
        }

        throw new ClientErrorException("Error logging in. Please try again!");


    }
}
