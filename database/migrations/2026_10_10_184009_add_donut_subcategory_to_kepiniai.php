<?php

use App\Models\Kepiniai;
use Database\Seeders\DonutSubcategorySeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the "Spurgos" subcategory to "Kepiniai"
     * and move the existing donut into it at the new price (renaming the
     * original "Spurga" to "Varškės spurga"). A fresh database is left to
     * the seeders.
     */
    public function up(): void
    {
        foreach (DonutSubcategorySeeder::PREVIOUS_NAMES as $name => $previousName) {
            if (Kepiniai::where('name', $name)->exists()) {
                continue;
            }

            Kepiniai::where('name', $previousName)->limit(1)->update(['name' => $name]);
        }

        (new DonutSubcategorySeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
