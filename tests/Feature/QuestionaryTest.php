<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

class QuestionaryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->faker = \Faker\Factory::create();
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

}
