<?php

use Database\Seeders\ComboSubcategorySeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the numbered "Komplektai" subcategory to
     * "Kiti". A fresh database is left to the seeders.
     */
    public function up(): void
    {
        (new ComboSubcategorySeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
