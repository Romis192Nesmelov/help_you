<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class AnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'text' => fake()->realText(180),
            'ticket_id' => Ticket::all()->random()->id,
            'user_id' => User::query()->where('id','!=',1)->get()->random()->id,
            'read_admin' => rand(0,1),
            'read_owner' => rand(0,1),
        ];
    }
}
