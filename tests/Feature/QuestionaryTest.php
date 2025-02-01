<?php

namespace Tests\Feature;

use App\Models\Questionary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class QuestionaryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->faker = \Faker\Factory::create();

        $user = User::factory()->create();
        $this->token = JWTAuth::fromUser($user);
        $this->userId = $user;

        $admin = User::factory()->create();
        $admin->role = 'admin';
        $admin->save();
        $this->tokenAdmin = JWTAuth::fromUser($admin);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function test_create_questionary_success()
    {
        $filePath = base_path('tests/Fixtures/cat_01.jpg');
        $data = [
            'name' => $this->faker->unique()->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'tg_name' => $this->faker->unique()->userName(),
            'catPhoto' => UploadedFile::fake()->createWithContent('cat_01.jpg', file_get_contents($filePath)),
        ];

        $response = $this->postJson(route('questionary.store'), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('questionaries', [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'tg_name' => $data['tg_name'],
        ]);

        $catPhotoPath = 'cat_photos/' . $data['catPhoto']->hashName();
        Storage::disk('public')->assertExists($catPhotoPath);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function test_create_questionary_without_cat_photo()
    {
        $data = [
            'name' => $this->faker->unique()->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this->faker->password(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'tg_name' => $this->faker->unique()->userName(),
        ];

        $response = $this->postJson(route('questionary.store'), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('questionaries', [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'tg_name' => $data['tg_name'],
        ]);
    }
    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function test_create_questionary_with_invalid_data()
    {
        $data = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'phone' => 'invalid-phone',
            'tg_name' => '',
        ];

        $response = $this->postJson(route('questionary.store'), $data);

        $response->assertStatus(422);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function test_throttle_on_questionary_store()
    {

        for ($i = 0; $i < 11; $i++) {

            $data = [
                'name' => $this->faker->unique()->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'password' => $this->faker->password(),
                'phone' => $this->faker->optional()->phoneNumber(),
                'tg_name' => $this->faker->optional()->userName(),
            ];

            $response = $this->postJson(route('questionary.store'), $data);

            if ($i < 10) {
                $response->assertStatus(201);
            } else {
                $response->assertStatus(429);
            }
        }
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function test_confirm_not_admin()
    {

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson(route('questionary.confirm', ['id' => 1]), []);

        $response->assertStatus(403);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function test_confirm_not_authorized()
    {

        $response = $this->postJson(route('questionary.confirm', ['id' => 1]), []);

        $response->assertStatus(401);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function test_confirm_success()
    {

        $questionary = Questionary::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->tokenAdmin,
        ])->postJson(route('questionary.confirm', ['id' => $questionary->id]), []);

        $response->assertStatus(201);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function test_confirm_not_found()
    {

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->tokenAdmin,
        ])->postJson(route('questionary.confirm', ['id' => 1000000]), []);

        $response->assertStatus(404);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function test_index_questionaries_as_admin()
    {
        $questionaries = Questionary::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->tokenAdmin,
        ])->getJson(route('questionary.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function test_index_questionaries_not_authorized()
    {
        $response = $this->getJson(route('questionary.index'));

        $response->assertStatus(401);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function test_delete_questionary_success()
    {
        $questionary = Questionary::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->tokenAdmin,
        ])->deleteJson(route('questionary.destroy', ['questionary' => $questionary->id]));

        $response->assertStatus(204);
        $this->assertSoftDeleted('questionaries', ['id' => $questionary->id]);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function test_delete_questionary_not_found()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->tokenAdmin,
        ])->deleteJson(route('questionary.destroy', ['questionary' => 1000000]));

        $response->assertStatus(404);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)] public function test_delete_questionary_not_admin()
    {
        $questionary = Questionary::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->delete(route('questionary.destroy', ['questionary' => $questionary->id]));

        $response->assertStatus(403);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_update_questionary_as_admin()
    {
        $questionary = Questionary::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->tokenAdmin,
        ])->putJson(route('questionary.update', $questionary->id), [
            'name' => 'New name',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('questionaries', [
            'id' => $questionary->id,
            'name' => 'New name',
        ]);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_update_questionary_as_user()
    {
        $questionary = Questionary::factory()->create(['user_id' => $this->userId]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson(route('questionary.update', $questionary->id), [
            'name' => 'New name',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('questionaries', [
            'id' => $questionary->id,
            'name' => 'New name',
        ]);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_update_questionary_as_non_authorized_user()
    {
        $questionary = Questionary::factory()->create();

        $response = $this->putJson(route('questionary.update', $questionary->id), [
            'name' => 'New name',
        ]);

        $response->assertStatus(401);
    }

    #[RunInSeparateProcess] #[PreserveGlobalState(false)]
    public function test_update_questionary_as_user_trying_to_change_others()
    {
        $otherQuestionary = Questionary::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson(route('questionary.update', $otherQuestionary->id), [
            'name' => 'New name',
        ]);

        $response->assertStatus(403);
    }


}
