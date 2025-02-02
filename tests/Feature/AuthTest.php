<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Exception;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'testUser',
            'email' => 'testUser@test.com',
            'password' => Hash::make('password'),
        ]);
    }

    public function test_can_login_a_user()
    {
        $response = $this->post(route('auth.store'), [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $response->json());
    }

    public function test_cannot_login_with_invalid_credentials()
    {
        $response = $this->post(route('auth.store'), [
            'email' => $this->user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    public function test_can_logout_a_user()
    {
        $response = $this->post(route('auth.store'), [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $token = $response->json()['token'];

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->delete(route('auth.delete'));
        $response->assertStatus(204);
    }

    public function test_cannot_logout_without_token()
    {
        $response = $this->delete(route('auth.delete'));

        $response->assertStatus(401);
    }
}
