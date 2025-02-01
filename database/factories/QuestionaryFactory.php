<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Questionary>
 */
class QuestionaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->optional()->phoneNumber,
            'password' => Hash::make($this->faker->password),
            'tg_name' => $this->faker->optional()->userName,
            'cat_photo' => $this->getRandomCatPhoto(),
        ];
    }

    private function getRandomCatPhoto()
    {

        if (rand(0, 100) < 30) {
            return null;
        }

        $catPhotos = [
            'cat_01.jpg',
            'cat_02.jpg',
            'cat_03.jpg',
            'cat_04.jpg',
            'cat_05.jpg',
        ];

        $randomPhoto = $catPhotos[array_rand($catPhotos)];
        $sourcePath = base_path('tests/Fixtures/' . $randomPhoto);
        $destinationPath = Str::uuid() . '_' . $randomPhoto;

        Storage::disk('public')->putFileAs('cat_photos', new \Illuminate\Http\File($sourcePath), $destinationPath);

        return $destinationPath;
    }
}
