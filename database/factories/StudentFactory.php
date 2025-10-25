<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $classes = ['Form 1','Form 2','Form 3','Form 4'];
        $sex = fake()->randomElement(['M','F']);
        return [
            'reg_no' => strtoupper('RSMS-'.fake()->unique()->numerify('####')),
            'name' => fake()->name($sex === 'M' ? 'male' : 'female'),
            'sex' => $sex,
            'class_name' => fake()->randomElement($classes),
        ];
    }
}
