<?php

use Database\Seeders\ColdDrinkSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the numbered "Šalti gėrimai" drinks.
     * A fresh database is left to the seeders.
     */
    public function up(): void
    {
        (new ColdDrinkSeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
