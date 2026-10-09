<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KoldunaiTopping>
 */
class KoldunaiToppingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'price' => 0.40,
            'sign' => strtoupper(fake()->lexify('??')),
            'show' => true,
            'is_default' => false,
            'position' => 0,
        ];
    }

    /**
     * Topping that is pre-selected when a dish is added to the cart.
     */
    public function selectedByDefault(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
