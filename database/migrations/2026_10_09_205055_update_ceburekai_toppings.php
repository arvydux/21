<?php

use App\Models\Category;
use App\Models\Topping;
use Database\Seeders\ToppingSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Old topping names from the original seeder, keyed by their new name.
     *
     * @var array<string, string>
     */
    private array $previousNames = [
        'Sūris' => 'Sūriu',
    ];

    /**
     * On an existing deployment, bring the Čeburekai toppings in line with the
     * paper menu: update price, sign and order of the ones that exist (renaming
     * "Sūriu" to "Sūris"), add the missing ones and delete toppings that are
     * not on the menu. A fresh database is left to the seeders.
     */
    public function up(): void
    {
        if (Category::count() === 0) {
            return;
        }

        foreach (ToppingSeeder::TOPPINGS as $position => $topping) {
            $existing = Topping::where('name', $topping['name'])->first();

            if ($existing === null && isset($this->previousNames[$topping['name']])) {
                $existing = Topping::where('name', $this->previousNames[$topping['name']])->first();
            }

            if ($existing === null) {
                Topping::create([...$topping, 'position' => $position]);

                continue;
            }

            $existing->update([...$topping, 'position' => $position]);
        }

        Topping::whereNotIn('name', array_column(ToppingSeeder::TOPPINGS, 'name'))->delete();
    }

    public function down(): void
    {
        // Data update only.
    }
};
