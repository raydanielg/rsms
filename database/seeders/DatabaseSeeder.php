<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\StudentSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'username' => 'testuser',
                'phone' => '255700000001',
                'region' => 'Dar es Salaam',
                'school_name' => 'Test Secondary School',
                'school_number' => 'S0001',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->call(StudentSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(EmasSeeder::class);
    }
}
