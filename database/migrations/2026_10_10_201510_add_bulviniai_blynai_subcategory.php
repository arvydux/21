<?php

use Database\Seeders\BulviniaiBlynaiSubcategorySeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Move Bulvinukai and the two potato pancake dishes into a "Bulviniai
     * blynai" subcategory of "Bulviniai patiekalai", numbered 1-3, and remove
     * the then empty "Visi" subcategory. A fresh database is left to the
     * seeders.
     */
    public function up(): void
    {
        (new BulviniaiBlynaiSubcategorySeeder)->run();
    }

    public function down(): void
    {
        // Data move only.
    }
};
