<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class HafizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);

        return [
            'user_id' => fake()->randomElement([1, 2]),
            'name' => fake()->firstName($gender),
            'birth_date' => fake()->dateTimeBetween('-15 years', '-4 years')->format('Y-m-d'),
            'gender' => $gender,
            'phone' => '',
            'address' => '',
            'active' => true,
            'notes' => '',
        ];
    }

}
