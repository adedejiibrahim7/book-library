<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\LoginRequest;
use App\Http\Requests\API\V1\RegisterRequest;
use App\Http\Resources\API\V1\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{

    public function __construct(private readonly AuthService $authService)
    {
    }


    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        return response()->json([
            'message' => 'Registration successful',
            'data' => UserResource::make($this->authService->register($data))
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $loginData = $this->authService->login($data);

        return response()->json([
           'accessToken' => $loginData['accessToken'],
           'user' => UserResource::make($loginData['user'])
        ]);
    }

    public function logout()
    {
        auth('api')->user()->token()->revoke();

        return response()->json([
            'message' => 'Logout successful',
            'data' => []
        ]);
    }
}
