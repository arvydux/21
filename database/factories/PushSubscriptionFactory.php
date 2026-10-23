<?php

namespace Database\Factories;

use App\Models\OrderNumbers;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PushSubscription>
 */
class PushSubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number_id' => fn (): int => OrderNumbers::create([
                'number' => fake()->numberBetween(1, 99),
                'is_ready' => false,
                'by_phone' => false,
                'is_taken' => false,
            ])->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/'.fake()->uuid(),
            'public_key' => fake()->sha256(),
            'auth_token' => fake()->md5(),
        ];
    }
}
