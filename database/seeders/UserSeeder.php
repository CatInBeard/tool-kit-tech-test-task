<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin user',
            'email' => 'admin@example.com',
            'password' => bcrypt('123'),
        ]);

        $admin->role = 'admin';
        $admin->save();

        User::factory()->count(10)->create();

    }
}
