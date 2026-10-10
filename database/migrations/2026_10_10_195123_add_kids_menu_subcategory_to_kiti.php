<?php

use App\Models\OtherProduct;
use Database\Seeders\KidsMenuSubcategorySeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the numbered "Vaikų meniu" subcategory to
     * "Kiti", moving the original "Bulvių ir vištienos rutuliukai" into it
     * under its new name instead of duplicating it. A fresh database is left
     * to the seeders.
     */
    public function up(): void
    {
        foreach (KidsMenuSubcategorySeeder::PREVIOUS_NAMES as $name => $previousName) {
            if (OtherProduct::where('name', $name)->exists()) {
                continue;
            }

            OtherProduct::where('name', $previousName)->limit(1)->update(['name' => $name]);
        }

        (new KidsMenuSubcategorySeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
