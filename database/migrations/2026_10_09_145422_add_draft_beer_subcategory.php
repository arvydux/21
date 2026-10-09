<?php

use Database\Seeders\DraftBeerSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the "Pilstomas alus" drinks to the
     * "Alkoholis" category. A fresh database is left to the seeders.
     */
    public function up(): void
    {
        (new DraftBeerSeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
