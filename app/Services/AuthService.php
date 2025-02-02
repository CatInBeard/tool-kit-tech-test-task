<?php

namespace App\Services;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Exceptions\ErrorJsonException;

class AuthService
{
    /**
     * @throws ErrorJsonException
     */
    public function login(string $email, string $password): string
    {
        try {
            if (!$token = JWTAuth::attempt(compact('email', 'password'))) {
                throw new ErrorJsonException('invalid credentials', 401);
            }
        } catch (JWTException $e) {
            throw new ErrorJsonException('could not create token', 500);
        }

        return $token;
    }

    /**
     * @throws ErrorJsonException
     */
    public function logout(): void
    {
        try {
            // @phpstan-ignore-next-line
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            throw new ErrorJsonException('could not invalidate token', 500);
        }
    }
}
