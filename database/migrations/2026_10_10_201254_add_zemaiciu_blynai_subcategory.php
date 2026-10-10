<?php

use Database\Seeders\ZemaiciuBlynaiSubcategorySeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Move the three Žemaičių blynai dishes into their own subcategory of
     * "Bulviniai patiekalai". A fresh database is left to the seeders.
     */
    public function up(): void
    {
        (new ZemaiciuBlynaiSubcategorySeeder)->run();
    }

    public function down(): void
    {
        // Data move only.
    }
};
