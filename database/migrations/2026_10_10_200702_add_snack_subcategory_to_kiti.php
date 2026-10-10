<?php

use Database\Seeders\SnackSubcategorySeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the numbered "Užkandžiai" subcategory to
     * "Kiti". A fresh database is left to the seeders.
     */
    public function up(): void
    {
        (new SnackSubcategorySeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
