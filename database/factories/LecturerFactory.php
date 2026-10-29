<?php

namespace Database\Factories;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lecturer>
 */
class LecturerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'staff_number' => strtoupper($this->faker->unique()->bothify('stf-###')),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('+2567########'),
            'department' => $this->faker->randomElement([
                'Computer Science',
                'Business Administration',
                'Engineering',
                'Education',
            ]),
        ];
    }
}
