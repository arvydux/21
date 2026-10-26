<?php

namespace Database\Factories;

use App\Models\OrderNumbers;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KitchenOrderItem>
 */
class KitchenOrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number_id' => fn () => OrderNumbers::create(['number' => fake()->numberBetween(1, 99)])->id,
            'name' => fake()->randomElement(['Kibinas su mėsa', 'Čeburekas su sūriu', 'Cepelinai']),
            'amount' => fake()->numberBetween(1, 3),
            'toppings' => [],
            'category' => 4,
            'takeaway' => false,
            'comments' => null,
        ];
    }
}
