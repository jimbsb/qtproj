<?php

namespace Database\Factories;

use App\Models\user_postions;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<user_postions>
 */
class UserPostionsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'user_id' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'office_id' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'designation_id' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'is_main' => false,
            'is_actg' => false,
            'is_active' => true,
            'acting_start' => $this->faker->date(),
            'acting_end' => $this->faker->date(),
        ];
    }
}
