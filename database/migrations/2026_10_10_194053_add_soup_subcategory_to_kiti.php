<?php

use Database\Seeders\SoupSubcategorySeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the "Sriubos" subcategory to "Kiti" and
     * move the existing "Dienos sriuba" into it instead of duplicating it.
     * A fresh database is left to the seeders.
     */
    public function up(): void
    {
        (new SoupSubcategorySeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
