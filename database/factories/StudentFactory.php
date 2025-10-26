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
        $f = $this->faker;
        if (!$f && class_exists(\Faker\Factory::class)) {
            $f = \Faker\Factory::create();
        }

        $sex = $f ? $f->randomElement(['M','F']) : 'M';
        $reg = $f ? $f->unique()->numerify('####') : str_pad((string)random_int(0,9999), 4, '0', STR_PAD_LEFT);
        $school = 'S'.($f ? $f->numerify('####') : str_pad((string)random_int(0,9999), 4, '0', STR_PAD_LEFT));
        $name = $f ? $f->name($sex === 'M' ? 'male' : 'female') : 'Student '.substr($reg, -4);
        $cls = $f ? $f->randomElement($classes) : $classes[0];

        return [
            'reg_no' => strtoupper($school.'-'.$reg),
            'name' => $name,
            'sex' => $sex,
            'class_name' => $cls,
        ];
    }
}
