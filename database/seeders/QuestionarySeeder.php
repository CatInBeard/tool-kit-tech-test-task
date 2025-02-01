<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Questionary;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuestionarySeeder extends Seeder
{
    public function run()
    {
        Questionary::factory()->count(10)->create();
    }

}
