<?php

use App\Models\Ceburek;
use App\Models\Subcategory;
use Database\Seeders\SignatureCeburekSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Put Šefo, Gurmaniškas and Pachmo into their own "Firminis" subcategory,
     * numbered 1-3. Renames them from the original seeder names first, and
     * moves them out of "Su mėsa" where an earlier migration placed them.
     * A fresh database is left to the seeders.
     */
    public function up(): void
    {
        foreach (SignatureCeburekSeeder::PREVIOUS_NAMES as $name => $previousName) {
            if (Ceburek::where('name', $name)->exists()) {
                continue;
            }

            Ceburek::where('name', $previousName)->limit(1)->update(['name' => $name]);
        }

        $seeder = new SignatureCeburekSeeder;
        $seeder->run();

        $firminis = Subcategory::where('name', 'Firminis')
            ->whereHas('category', fn ($query) => $query->where('component_name', 'ceburekai'))
            ->first();

        if ($firminis === null) {
            return;
        }

        foreach ($seeder->products() as $product) {
            Ceburek::where('name', $product['name'])
                ->where('subcategory_id', '!=', $firminis->id)
                ->update(['subcategory_id' => $firminis->id, 'number' => $product['number']]);
        }
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
