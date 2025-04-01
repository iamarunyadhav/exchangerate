<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'auth_token',
        'auth_token_expires_at',
        'otp',
        'otp_expires_at'
    ];

    protected $hidden = [
        'auth_token',
        'otp'
    ];

    protected $casts = [
        'auth_token_expires_at' => 'datetime',
        'otp_expires_at' => 'datetime',
    ];
}
