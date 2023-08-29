<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Point>
 */
class PointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->text(50),
            'latitude' => fake()->randomFloat(6, 55, 56),
            'longitude' => fake()->randomFloat(6, 37, 38),
            'description' => fake()->text(300),
            'city_id' => 1
        ];
    }
}
