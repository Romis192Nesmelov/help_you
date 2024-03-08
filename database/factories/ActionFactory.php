<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Action>
 */
class ActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $html = '';
        for ($i=0;$i<5;$i++) {
            $html .= '<div class="col-md-4 col-sm-12 image '.(rand(0,1) ? 'float-start me-3' : 'float-end ms-3').'">';
            $html .= '<img src="/images/placeholder.jpg" />';
            $html .= '</div>';
            $html .= '<p>'.fake()->text(rand(1000,5000)).'</p>';
        }

        return [
            'name' => fake()->text(50),
            'html' =>  $html,
            'start' => Carbon::now(),
            'end' => Carbon::now()->addDays(30),
            'rating' => rand(1,2)
        ];
    }
}
