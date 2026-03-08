<?php

namespace App\Models;

use App\Enums\DeleteFlag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'del_flag',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'del_flag' => DeleteFlag::class,
    ];

    /**
     * Implement JWTSubject methods
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get model by email
     */
    public function findByEmail(string $email): ?User
    {
        return self::where('email', $email)->where('del_flag', DeleteFlag::OFF)->first();
    }
}
