<?php

use Database\Seeders\ColdDrinkSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * The drinks subcategory "Šalti gėrimai" is called "Gaivieji gėrimai" on
     * the November menu.
     */
    public function up(): void
    {
        ColdDrinkSeeder::renameOldSubcategory();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
