<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\VerifyOTPRequest;
use App\Http\Requests\AuthenticateRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $this->authService->register($request->validated());
        return response()->json(['message' => 'Registered successfully'], 201);
    }

    public function login(LoginRequest $request)
    {
        try {
            $response = $this->authService->login($request->email);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send OTP email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOTP(VerifyOTPRequest $request)
    {
        try {
            $response = $this->authService->verifyOTP($request->email, $request->otp);
            return response()->json([
                'token' => $response['token'],
                'user' => new UserResource($response['user'])
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function authenticate(AuthenticateRequest $request)
    {
        try {
            $token = $this->authService->authenticate($request->token);
            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->authService->logout($request->user());
            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Logout successful'], 200);
        }
    }

    public function getAuthenticatedUser(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated'
            ], 401);
        }

        return response()->json([
            'user' => new UserResource($user)
        ]);
    }

}
