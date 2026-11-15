<?php

use App\Models\OtherProduct;
use App\Models\Subcategory;
use Database\Seeders\SnackSubcategorySeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the boiled cepelinai to "Užkandžiai" as
     * numbers 8 and 9 and move "Užkandžių rinkinys" from 8 to 10 after them,
     * as on the November menu. A rinkinys renumbered by hand is left alone. A
     * fresh database is left to the seeders.
     */
    public function up(): void
    {
        (new SnackSubcategorySeeder)->run();

        $snacks = Subcategory::where('name', 'Užkandžiai')->first();

        if ($snacks === null) {
            return;
        }

        OtherProduct::where('subcategory_id', $snacks->id)
            ->where('name', 'Užkandžių rinkinys')
            ->where('number', 8)
            ->update(['number' => 10, 'position' => OtherProduct::max('position') + 1]);
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
