<?php

namespace Database\Factories;

use App\Models\Statuses;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Statuses>
 */
class StatusesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'table' => fake()->word(),
            'bg_color' => fake()->hexColor(),
        ];
    }
}
