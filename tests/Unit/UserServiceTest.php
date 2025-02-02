<?php

namespace Tests\Unit;

use App\Exceptions\ErrorJsonException;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mockery;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new UserService();
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testCreateThrowsException()
    {
        $this->expectException(ErrorJsonException::class);

        $this->userService->create();
    }


    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testGetReturnsPaginatedUsers()
    {
        $user = Mockery::mock('alias:' . User::class);
        $user->shouldReceive('isAdmin')->andReturn(true);

        Auth::shouldReceive('user')->andReturn($user);

        $mockedUsers = collect([new User(['id' => 1, 'name' => 'Test User'])]);
        $mockedPaginator = new \Illuminate\Pagination\LengthAwarePaginator($mockedUsers, 1, 1, 1);

        $user->shouldReceive('query')->andReturnSelf();
        $user->shouldReceive('paginate')->with(10, ['*'], 'page', 1)->andReturn($mockedPaginator);

        $users = $this->userService->get(10, 1);

        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\LengthAwarePaginator::class, $users);
        $this->assertCount(1, $users);
    }


    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testGetByIdThrowsExceptionForUnauthorizedAccess()
    {
        $user = Mockery::mock('alias:' . User::class);
        $user->shouldReceive('isAdmin')->andReturn(false)->once();

        $user->id = 1;

        Auth::shouldReceive('user')->andReturn($user);

        $this->expectException(ErrorJsonException::class);

        $this->userService->getById(2);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testUpdateThrowsExceptionForUnauthorizedAccess()
    {
        $user = Mockery::mock('alias:' . User::class);
        $user->shouldReceive('isAdmin')->andReturn(false)->once();

        $user->id = 1;

        Auth::shouldReceive('user')->andReturn($user);

        $this->expectException(ErrorJsonException::class);

        $this->userService->update(2, ['name' => 'New Name']);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testUpdateSuccessfullyUpdatesUser()
    {
        $user = Mockery::mock('alias:' . User::class);
        $user->shouldReceive('isAdmin')->andReturn(false);
        $user->shouldReceive('id')->andReturn(1);
        $user->id = 1;

        Auth::shouldReceive('user')->andReturn($user);

        $user->shouldReceive('findOrFail')->with(1)->andReturn($user);
        $user->shouldReceive('update');

        $updatedUser = $this->userService->update(1, ['name' => 'Updated Name', 'password' => 'newpassword']);

        $this->assertEquals(1, $updatedUser->id);
    }


    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testDeleteSuccessfullyDeleteUser()
    {
        $admin = Mockery::mock('alias:' . User::class);
        $admin->shouldReceive('isAdmin')->andReturn(true)->once();
        $admin->id = 1;

        Auth::shouldReceive('user')->andReturn($admin)->once();

        $admin->shouldReceive('findOrFail')->andReturn($admin)->once();
        $admin->shouldReceive('delete')->once();

        $this->userService->delete(1);

    }
}
