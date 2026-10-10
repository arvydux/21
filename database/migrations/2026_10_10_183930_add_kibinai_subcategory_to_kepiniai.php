<?php

use App\Models\Kepiniai;
use Database\Seeders\KibinaiSubcategorySeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the "Kibinai" subcategory to "Kepiniai"
     * and move the existing kibinai into it at the new prices (renaming the
     * originally misspelled "Kibinas su vistiena"). A fresh database is left to
     * the seeders.
     */
    public function up(): void
    {
        foreach (KibinaiSubcategorySeeder::PREVIOUS_NAMES as $name => $previousName) {
            if (Kepiniai::where('name', $name)->exists()) {
                continue;
            }

            Kepiniai::where('name', $previousName)->limit(1)->update(['name' => $name]);
        }

        (new KibinaiSubcategorySeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
