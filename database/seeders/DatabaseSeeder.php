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
            KepiniaiSeeder::class,
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
            CheeseCeburekSeeder::class,
            MeatCeburekSeeder::class,
            SignatureCeburekSeeder::class,
            KibinaiSubcategorySeeder::class,
            DonutSubcategorySeeder::class,
            SoupSubcategorySeeder::class,
            DessertSubcategorySeeder::class,
            KidsMenuSubcategorySeeder::class,
            SnackSubcategorySeeder::class,
            SauceSubcategorySeeder::class,
            ComboSubcategorySeeder::class,
            ZemaiciuBlynaiSubcategorySeeder::class,
            BulviniaiBlynaiSubcategorySeeder::class,
            DefaultSubcategorySeeder::class,
        ]);
    }
}
