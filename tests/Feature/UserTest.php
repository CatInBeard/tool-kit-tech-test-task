<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserTest extends TestCase
{
    protected $token;
    protected $tokenAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $user = User::factory()->create();
        $this->token = JWTAuth::fromUser($user);
        $this->userId = $user->id;

        $admin = User::factory()->create(['role' => 'admin']);
        $this->tokenAdmin = JWTAuth::fromUser($admin);

        $this->userToDelete = User::factory()->create();
        $this->userToUpdate = User::factory()->create();
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_index_accessible_to_admin()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->tokenAdmin,
        ])->getJson(route('users.index'));

        $response->assertStatus(200);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_index_not_accessible_to_regular_user()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson(route('users.index'));

        $response->assertStatus(403);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_index_not_accessible_without_authentication()
    {
        $response = $this->getJson(route('users.index'));

        $response->assertStatus(401);
    }


    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_store_not_accessible_to_admin()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->tokenAdmin,
        ])->postJson(route('users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(403);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_store_not_accessible_to_regular_user()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson(route('users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(403);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_store_not_accessible_without_authentication()
    {
        $response = $this->postJson(route('users.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(403);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_admin_can_delete_any_user()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->tokenAdmin,
        ])->deleteJson(route('users.destroy', ['user' => $this->userToDelete->id]));

        $response->assertStatus(200);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_user_can_delete_themselves()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson(route('users.destroy', ['user' => $this->userId]));

        $response->assertStatus(200);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_not_authorized_user_cannot_delete_anyone()
    {
        $response = $this->deleteJson(route('users.destroy', ['user' => $this->userToDelete->id]));

        $response->assertStatus(401);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_admin_can_update_any_user()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->tokenAdmin,
        ])->putJson(route('users.update', ['user' => $this->userToUpdate->id]), [
            'name' => 'Updated Name',
            'email' => $this->userToUpdate->email,
            'role' => 'client',
        ]);

        $response->assertStatus(200);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_user_can_update_themselves()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson(route('users.update', ['user' => $this->userId]), [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_not_authorized_user_cannot_update_anyone()
    {
        $response = $this->putJson(route('users.update', ['user' => $this->userToUpdate->id]), [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(401);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_user_cannot_change_their_own_role()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson(route('users.update', ['user' => $this->userId]), [
            'name' => 'Updated Name',
            'role' => 'admin',
        ]);

        $response->assertStatus(403);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_user_get_own_auth()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get(route('users.me'));

        $response->assertStatus(200);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_user_get_own_no_auth()
    {
        $response = $this->get(route('users.me'));

        $response->assertStatus(401);
    }
}
