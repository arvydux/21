<?php

use App\Models\Ceburek;
use Database\Seeders\MeatCeburekSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, rename the meat Čeburekai from the original
     * seeder to their new menu names, then move them into "Su mėsa" at the new
     * prices (any missing ones are created). A fresh database is left to the
     * seeders.
     */
    public function up(): void
    {
        foreach (MeatCeburekSeeder::PREVIOUS_NAMES as $name => $previousName) {
            if (Ceburek::where('name', $name)->exists()) {
                continue;
            }

            Ceburek::where('name', $previousName)->limit(1)->update(['name' => $name]);
        }

        (new MeatCeburekSeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
