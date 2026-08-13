<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Authentication extends Controller
{
    //

    public static function generateSalt(): string
    {
        $characters = env('AUTH_SALT_CHARSET');
        $salt = '';

        for ($i = 0; $i < env('AUTH_SALT_LENGTH', 8); $i++) {
            $salt .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $salt;
    }
}
