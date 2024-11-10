<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\LoginRequest;
use App\Http\Requests\API\V1\RegisterRequest;
use App\Http\Resources\API\V1\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{

    public function __construct(private readonly AuthService $authService)
    {
    }


    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     summary="Register a new user",
     *     description="Creates a new user with name, email, and password.",
     *     operationId="registerUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe", maxLength=255),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com", maxLength=255),
     *             @OA\Property(property="password", type="string", format="password", example="securePassword123", minLength=8),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="securePassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email has already been taken.")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password confirmation does not match."))
     *             )
     *         )
     *     )
     * )
     */

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        return response()->json([
            'message' => 'Registration successful',
            'data' => UserResource::make($this->authService->register($data))
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     summary="User login",
     *     description="Authenticate a user and return an access token.",
     *     operationId="loginUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="user@example.com", description="User's email address"),
     *             @OA\Property(property="password", type="string", example="password123", description="User's password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged in",
     *         @OA\JsonContent(
     *             @OA\Property(property="accessToken", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="data", type="array",
*                        @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example="b75bd679-00ec-432c-816a-f2ec8fee8178"),
     *                      @OA\Property(property="name", type="string", example="Item Name"),
     *                      @OA\Property(property="email", type="string", example="mail@sampel.com"),
     *                  )
     *              ),

     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array",
     *                     @OA\Items(type="string", example="The email field is required.")
     *                 ),
     *                 @OA\Property(property="password", type="array",
     *                     @OA\Items(type="string", example="The password field is required.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $loginData = $this->authService->login($data);

        return response()->json([
           'accessToken' => $loginData['accessToken'],
           'data' => UserResource::make($loginData['user'])
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
