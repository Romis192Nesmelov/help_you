<?php

namespace Database\Factories;

use App\Models\OrderType;
use App\Models\User;
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
        $orderType = OrderType::all()->random()->id;
        if ($orderType == 1) {
            $subtypes = [rand(1,4)];
        } else $subtypes = null;
        return [
            'user_id' => User::all()->random()->id,
            'order_type_id' => $orderType,
            'city_id' => 1,
            'subtypes' => $subtypes,
            'need_performers' => rand(1,20),
            'address' => fake()->address(),
            'latitude' => fake()->randomFloat(6, 55, 56),
            'longitude' => fake()->randomFloat(6, 37, 38),
//            'estimated_start_time' => Carbon::now(),
//            'estimated_end_time' => Carbon::now()->addDays(rand(1,10)),
            'description' => fake()->text(300),
            'approved' => 1,
            'active' => 1
        ];
    }
}
