<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmasUser;
use Illuminate\Support\Facades\Hash;

class EmasMarkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test marker account (Mathematics marker)
        EmasUser::updateOrCreate(
            ['username' => 'marker'],
            [
                'name' => 'Test Marker - Mathematics',
                'email' => 'marker@emas.com',
                'phone' => '0700000001',
                'role' => 'marker',
                'center_code' => 'CTR001',
                'district' => 'Dar es Salaam',
                'region' => 'Dar es Salaam',
                'assigned_subjects' => ['Mathematics', 'Physics'],
                'password' => Hash::make('marker'),
            ]
        );

        // Create additional marker accounts for different regions
        EmasUser::updateOrCreate(
            ['username' => 'marker_arusha'],
            [
                'name' => 'Marker - Arusha (English)',
                'email' => 'marker.arusha@emas.com',
                'phone' => '0700000002',
                'role' => 'marker',
                'center_code' => 'CTR002',
                'district' => 'Arusha Urban',
                'region' => 'Arusha',
                'assigned_subjects' => ['English', 'Kiswahili'],
                'password' => Hash::make('marker'),
            ]
        );

        EmasUser::updateOrCreate(
            ['username' => 'marker_mwanza'],
            [
                'name' => 'Marker - Mwanza (Science)',
                'email' => 'marker.mwanza@emas.com',
                'phone' => '0700000003',
                'role' => 'marker',
                'center_code' => 'CTR003',
                'district' => 'Mwanza City',
                'region' => 'Mwanza',
                'assigned_subjects' => ['Biology', 'Chemistry', 'Physics'],
                'password' => Hash::make('marker'),
            ]
        );

        $this->command->info('✓ EMAS Marker users created successfully!');
        $this->command->info('  Username: marker, Password: marker');
        $this->command->info('  Username: marker_arusha, Password: marker');
        $this->command->info('  Username: marker_mwanza, Password: marker');
    }
}
