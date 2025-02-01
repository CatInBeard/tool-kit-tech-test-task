<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CustomJsonException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function store(LoginRequest $request)
    {
        try{
            $token = $this->authService->login(
                $request->email,
                $request->password
            );
        }
        catch(CustomJsonException $e){
            return $e->render();
        }

        return response()->json(compact('token'));

    }

    public function delete(Request $request)
    {
        try{
            $this->authService->logout();
        }
        catch(CustomJsonException $e){
            return $e->render();
        }
        return response()->json(['message' => 'User logged out successfully']);
    }
}
