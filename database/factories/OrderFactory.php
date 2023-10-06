<?php

namespace Database\Factories;

use App\Models\OrderType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::all()->random()->id,
            'order_type_id' => OrderType::all()->random()->id,
            'city_id' => 1,
            'performers' => rand(1,20),
            'latitude' => fake()->randomFloat(6, 55, 56),
            'longitude' => fake()->randomFloat(6, 37, 38),
            'start' => Carbon::now(),
            'end' => Carbon::now()->addDays(rand(1,10)),
            'description' => fake()->text(300),
            'approved' => 1,
            'active' => 1
        ];
    }
}
