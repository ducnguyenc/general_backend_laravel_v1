<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    const ABILITY_ADMIN = 'admin';
    const ABILITY_USER = 'user';

    const ROLE_SUPPER_ADMIN = 0;
    const ROLE_ADMIN = 1;
    const ROLE_USER_V0 = 2;
    const ROLE_USER_V1 = 3;

    const ARR_ROLE_ADMIN = [
        self::ROLE_SUPPER_ADMIN,
        self::ROLE_ADMIN,
    ];

    const ARR_ROLE_USER = [
        self::ROLE_USER_V0,
        self::ROLE_USER_V1,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
