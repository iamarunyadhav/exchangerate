<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOTP;
use Illuminate\Support\Facades\Log;
use App\Traits\Auditable;

class AuthService
{
    use Auditable;

    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        self::logAction('user_register', "New user: {$user->email}");

        return $user;
    }

    public function login(string $email): array
    {
        $user = User::where('email', $email)->first();
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => $expiresAt
        ]);

        try {
            Mail::to($user->email)->send(new SendOTP($otp));

            Log::info("OTP sent to {$user->email}", [
                'otp' => $otp,
                'expires_at' => $expiresAt
            ]);

            self::logAction('otp_sent', "OTP sent to {$user->email}");

            return [
                'message' => 'OTP sent to your email',
                'expires_in' => 10
            ];
        } catch (\Exception $e) {
            Log::error("Failed to send OTP email", [
                'error' => $e->getMessage(),
                'email' => $user->email
            ]);

            throw $e;
        }
    }

    public function verifyOTP(string $email, string $otp): array
    {
        $user = User::where('email', $email)
            ->where('otp', $otp)
            ->where('otp_expires_at', '>', Carbon::now())
            ->first();

        if (!$user) {
            throw new \Exception('Invalid or expired OTP', 401);
        }

        $user->update([
            'otp' => null,
            'otp_expires_at' => null
        ]);

        $sanctumToken = $user->createToken('auth_token')->plainTextToken;
        self::logAction('login_success', "User logged in");

        return [
            'token' => $sanctumToken,
            'user' => $user
        ];
    }

    public function authenticate(string $token): string
    {
        $user = User::where('auth_token', $token)
            ->where('auth_token_expires_at', '>', Carbon::now())
            ->first();

        if (!$user) {
            throw new \Exception('Invalid or expired token', 401);
        }

        $sanctumToken = $user->createToken('auth_token')->plainTextToken;

        $user->update([
            'auth_token' => null,
            'auth_token_expires_at' => null
        ]);

        return $sanctumToken;
    }

    public function logout($user): void
    {
        if ($user) {
            $user->tokens()->delete();
        }
        self::logAction('logout', "User logged out");
    }
}
