<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KibinaiSeeder::class,
            DrinkSeeder::class,
            OtherProductSeeder::class,
            CategorySeeder::class,
            CeburekSeeder::class,
            ToppingSeeder::class,
            PotatoDishSeeder::class,
            PotatoToppingSeeder::class,
            KoldunaiDishSeeder::class,
            KoldunaiToppingSeeder::class,
            FreeNumbersSeeder::class,
            SubcategorySeeder::class,
            HotDrinkSeeder::class,
            ColdDrinkSeeder::class,
            DraftBeerSeeder::class,
            CiderSeeder::class,
            WineSeeder::class,
            SpiritsSeeder::class,
        ]);
    }
}
