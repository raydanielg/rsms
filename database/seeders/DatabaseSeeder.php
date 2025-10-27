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

        User::factory()->create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone' => '255700000001',
            'region' => 'Dar es Salaam',
            'school_name' => 'Test Secondary School',
            'school_number' => 'S0001',
        ]);

        $this->call(StudentSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(EmasSeeder::class);
    }
}
