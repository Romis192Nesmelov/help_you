<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class PartnerFactory extends Factory
{
    private static $counter = 1;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'logo' => 'images/partners/logo'.(self::$counter++).'.svg',
            'name' => fake()->company(),
            'about' => fake()->text(400),
            'info' => '<p>'.fake()->text(900).'</p><p>'.fake()->text(900).'</p><p>'.fake()->text(900).'</p>',
            'active' => 1
        ];
    }
}
