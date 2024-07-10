<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => fake()->text(50),
            'text' => fake()->realText(100),
            'user_id' => User::all()->random()->id,
            'status' => rand(0,1),
            'read_admin' => rand(0,1),
            'read_owner' => rand(0,1)
        ];
    }
}
