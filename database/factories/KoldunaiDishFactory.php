<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KoldunaiDish>
 */
class KoldunaiDishFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'price' => 7.50,
            'sign' => strtoupper(fake()->lexify('??')),
            'show' => true,
            'position' => 0,
        ];
    }
}
