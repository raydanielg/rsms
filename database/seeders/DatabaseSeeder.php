<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\EmasUser;
use App\Models\EmasSubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\StudentSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear all tables first
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Student::truncate();
        EmasUser::truncate();
        EmasSubject::truncate();
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Tables cleared. Starting fresh seed...');

        // Create test user
        User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone' => '255700000001',
            'region' => 'Dar es Salaam',
            'school_name' => 'Test Secondary School',
            'school_number' => 'S0001',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $this->call(StudentSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(EmasSeeder::class);
        $this->call(EmasSubjectSeeder::class);
        $this->call(EmasMarkerSeeder::class);
    }
}
