<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmasUser;
use Illuminate\Support\Facades\Hash;

class EmasSeeder extends Seeder
{
    public function run(): void
    {
        // Create default EMAS user if not exists
        if (!EmasUser::where('username', 'emas')->exists()) {
            EmasUser::create([
                'name' => 'EMAS Administrator',
                'username' => 'emas',
                'email' => 'emas@admin.com',
                'phone' => '+255712345678',
                'role' => 'supervisor',
                'center_code' => null,
                'district' => 'Kinondoni',
                'region' => 'Dar es Salaam',
                'password' => Hash::make('emas'),
            ]);

            $this->command->info('EMAS user created successfully!');
            $this->command->info('Username: emas');
            $this->command->info('Password: emas');
        } else {
            $this->command->warn('EMAS user already exists!');
            $this->command->info('Username: emas');
            $this->command->info('Password: emas');
        }
    }
}
