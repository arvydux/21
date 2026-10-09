<?php

use Database\Seeders\SpiritsSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the "Stiprieji alkoholiniai gėrimai" drinks to the
     * "Alkoholis" category. A fresh database is left to the seeders.
     */
    public function up(): void
    {
        (new SpiritsSeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
