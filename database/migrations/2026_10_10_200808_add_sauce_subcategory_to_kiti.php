<?php

use Database\Seeders\SauceSubcategorySeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the numbered "Padažai" subcategory to
     * "Kiti". A fresh database is left to the seeders.
     */
    public function up(): void
    {
        (new SauceSubcategorySeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
