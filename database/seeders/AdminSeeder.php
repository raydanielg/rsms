<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'System Administrator',
            'username' => 'admin',
            'email' => 'admin@rsms.com',
            'phone' => '255700000000',
            'region' => 'System',
            'school_name' => 'System Administration',
            'school_number' => 'ADMIN',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Username: admin');
        $this->command->info('Password: admin');
    }
}
