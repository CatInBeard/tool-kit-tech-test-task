<?php

namespace Tests\Unit;

use App\Exceptions\ErrorJsonException;
use App\Models\Questionary;
use App\Models\User;
use App\Services\QuestionaryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mockery;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

class QuestionaryServiceTest extends TestCase
{
    protected QuestionaryService $questionaryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->questionaryService = new QuestionaryService();
        $this->faker = \Faker\Factory::create();
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testCreateQuestionary()
    {
        $data = [
            'name' => $this->faker->unique()->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password(),
        ];

        $questionaryMock = Mockery::mock('alias:' . Questionary::class);
        $questionaryMock->shouldReceive('create')
            ->once()
            ->andReturn(new Questionary());

        $questionary = $this->questionaryService->create(
            $data['name'],
            $data['email'],
            $data['password'],
        );

        $this->assertInstanceOf(Questionary::class, $questionary);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testGetQuestionaryByIdAsOwner()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAdmin')
            ->once()
            ->andReturn(false);
        Auth::shouldReceive('user')->andReturn($user);

        $questionaryMock = Mockery::mock('alias:' . Questionary::class);
        $questionaryMock->shouldReceive('findOrFail')
            ->once()
            ->andReturn($questionaryMock);
        $questionaryMock->shouldReceive('isOwner')
            ->with($user)
            ->once()->andReturn(true);
        $this->app->instance(Questionary::class, $questionaryMock);

        $result = $this->questionaryService->getById(1);

        $this->assertEquals($questionaryMock, $result);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testGetQuestionaryByIdAsAdmin()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAdmin')
            ->once()
            ->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);

        $questionaryMock = \Mockery::mock('alias:' . Questionary::class);
        $questionaryMock->shouldReceive('findOrFail')->andReturn($questionaryMock);
        $this->app->instance(Questionary::class, $questionaryMock);

        $result = $this->questionaryService->getById(1);

        $this->assertEquals($questionaryMock, $result);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testUpdateQuestionaryAsAdmin()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAdmin')
            ->once()
            ->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);

        $questionaryMock = \Mockery::mock('alias:' . Questionary::class);
        $questionaryMock->shouldReceive('findOrFail')->andReturn($questionaryMock);
        $questionaryMock->shouldReceive('update')->once();
        $this->app->instance(Questionary::class, $questionaryMock);

        $data = ['name' => 'Updated User'];

        $updatedQuestionary = $this->questionaryService->update(1, $data);

        $this->assertEquals($questionaryMock, $updatedQuestionary);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testDeleteQuestionaryNotAdmin()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAdmin')
            ->once()
            ->andReturn(false);
        Auth::shouldReceive('user')->andReturn($user);

        $this->expectException(ErrorJsonException::class);

        $this->questionaryService->delete(1);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testConfirmQuestionaryFailed()
    {
        $this->expectException(ErrorJsonException::class);


        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAdmin')
            ->once()
            ->andReturn(false);
        Auth::shouldReceive('user')->andReturn($user);

        $this->questionaryService->confirm(1);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function testConfirmQuestionarySuccess()
    {
        $questionaryMock = Mockery::mock('alias:' . Questionary::class);
        $questionaryMock->shouldReceive('findOrFail')
            ->once()
            ->andReturn($questionaryMock);

        $user = Mockery::mock('alias:' . User::class);
        $user->shouldReceive('isAdmin')
            ->once()
            ->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);

        $user->shouldReceive('createFromQuestionary')
            ->once()
            ->andReturn($user);

        $this->questionaryService->confirm(1);


    }
}
