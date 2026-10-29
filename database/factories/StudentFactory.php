<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_number' => strtoupper($this->faker->unique()->bothify('scs-###-####')),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('+2567########'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'date_of_birth' => $this->faker->dateTimeBetween('-27 years', '-18 years'),
            'program' => $this->faker->randomElement([
                'Bachelor of Science in Computer Science',
                'Bachelor of Business Administration',
                'Bachelor of Engineering',
                'Bachelor of Education',
            ]),
        ];
    }
}
