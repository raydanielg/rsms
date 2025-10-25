<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $f = $this->faker;
        if (!$f && class_exists(\Faker\Factory::class)) {
            $f = \Faker\Factory::create();
        }

        // Fallback values when Faker is unavailable (e.g., production without require-dev)
        $fallbackName = 'Admin User';
        $fallbackUser = 'admin'.uniqid();
        $fallbackEmail = 'admin+'.uniqid().'@example.com';
        $fallbackPhone = '2557'.substr(str_shuffle('0123456789'), 0, 8);
        $regions = ['Dar es Salaam','Arusha','Mwanza','Dodoma','Mbeya'];

        return [
            'name' => $f ? $f->name() : $fallbackName,
            'username' => $f ? $f->unique()->userName() : $fallbackUser,
            'email' => $f ? $f->unique()->safeEmail() : $fallbackEmail,
            'phone' => $f ? $f->unique()->numerify('2557########') : $fallbackPhone,
            'region' => $f ? $f->randomElement($regions) : $regions[0],
            'school_name' => $f ? ($f->unique()->company().' Secondary School') : ('Sample Secondary School'),
            'school_number' => $f ? $f->unique()->bothify('S####') : ('S'.mt_rand(1000,9999)),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
