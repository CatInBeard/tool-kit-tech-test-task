<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use App\Services\AuthService;
use App\Exceptions\ErrorJsonException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthServiceTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    protected AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    public function test_login_success()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        JWTAuth::shouldReceive('attempt')
            ->once()
            ->with(['email' => 'test@example.com', 'password' => 'password123'])
            ->andReturn('mocked_token');

        $token = $this->authService->login('test@example.com', 'password123');

        $this->assertEquals('mocked_token', $token);
    }

    public function test_login_invalid_credentials()
    {

        JWTAuth::shouldReceive('attempt')
            ->once()
            ->with(['email' => 'test@example.com', 'password' => 'wrong_password'])
            ->andReturn(false);

        $this->expectException(ErrorJsonException::class);
        $this->expectExceptionCode(401);

        $this->authService->login('test@example.com', 'wrong_password');
    }

    public function test_login_jwt_exception()
    {

        JWTAuth::shouldReceive('attempt')
            ->once()
            ->andThrow(new JWTException());

        $this->expectException(ErrorJsonException::class);
        $this->expectExceptionCode(500);

        $this->authService->login('test@example.com', 'password123');
    }

    public function test_logout_success()
    {

        JWTAuth::shouldReceive('getToken')
            ->once()
            ->andReturn('mocked_token');

        JWTAuth::shouldReceive('invalidate')
            ->once()
            ->with('mocked_token');

        $this->authService->logout();

        $this->assertTrue(true);
    }

    public function test_logout_jwt_exception()
    {

        JWTAuth::shouldReceive('getToken')
            ->once()
            ->andReturn('mocked_token');

        JWTAuth::shouldReceive('invalidate')
            ->once()
            ->andThrow(new JWTException());

        $this->expectException(ErrorJsonException::class);
        $this->expectExceptionCode(500);

        $this->authService->logout();
    }
}
