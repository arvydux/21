<?php

namespace Database\Seeders;

use App\Models\Topping;
use Illuminate\Database\Seeder;

class ToppingSeeder extends Seeder
{
    /**
     * Čeburekai toppings as listed on the paper menu.
     *
     * @var list<array{name: string, price: float, sign: string}>
     */
    public const TOPPINGS = [
        ['name' => 'Grybai', 'price' => 0.80, 'sign' => 'G'],
        ['name' => 'Jelapenai', 'price' => 0.80, 'sign' => 'J'],
        ['name' => 'Saliamis', 'price' => 0.80, 'sign' => 'SA'],
        ['name' => 'Alyvuogės', 'price' => 0.80, 'sign' => 'AL'],
        ['name' => 'Mar. agurkai', 'price' => 0.80, 'sign' => 'MA'],
        ['name' => 'Sūris', 'price' => 1.00, 'sign' => 'S'],
        ['name' => 'Daugiau mėsos', 'price' => 1.00, 'sign' => 'DM'],
        ['name' => '2-jų rūšių sūris', 'price' => 1.20, 'sign' => '2S'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::TOPPINGS as $position => $topping) {
            Topping::create([...$topping, 'position' => $position]);
        }
    }
}
