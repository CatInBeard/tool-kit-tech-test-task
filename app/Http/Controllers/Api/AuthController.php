<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ErrorJsonException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @group Authorization
     *
     * APIs for jwt tokens
     */


    /**
     * Get jwt token
     *
     * @response 200 {
     *      "email": "john@example.com"
     *      "password": "mySecretPassword"
     * }
     *
     * @response 401 {
     *   "error": "Unauthenticated"
     * }
     *
     * @throws ErrorJsonException
     */
    public function store(LoginRequest $request): \Illuminate\Http\JsonResponse
    {

        $token = $this->authService->login(
            $request->input('email'),
            $request->input('password')
        );

        return response()->json(compact('token'));
    }

    /**
     * Invoke jwt token
     *
     * @header Authorization Bearer token
     *
     * @response 200 {
     *      "message": "User logged out successfully"
     * }
     *
     * @response 401 {
     *   "error": "Unauthenticated"
     * }
     *
     * @throws ErrorJsonException
     */
    public function delete(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authService->logout();

        return response()->json(['message' => 'User logged out successfully'], 204);
    }
}
