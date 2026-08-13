<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['username', 'firstname', 'lastname', 'email', 'password', 'token', 'salt', 'status_id', 'is_active', 'is_admin'])]
#[Hidden(['password', 'token','salt'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        // static::creating(function (self $user): void {
        //     if (empty($user->salt)) {
        //         $user->salt = self::generateSalt();
        //     }
        // });
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // private static function generateSalt(): string
    // {
    //     $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz%@#$!&';
    //     $salt = '';

    //     for ($i = 0; $i < env('AUTH_SALT_LENGTH', 8); $i++) {
    //         $salt .= $characters[random_int(0, strlen($characters) - 1)];
    //     }

    //     return $salt;
    // }
}
