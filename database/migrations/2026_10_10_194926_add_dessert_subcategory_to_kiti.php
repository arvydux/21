<?php

use Database\Seeders\DessertSubcategorySeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the numbered "Desertai" subcategory to
     * "Kiti". A fresh database is left to the seeders.
     */
    public function up(): void
    {
        (new DessertSubcategorySeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
