<?php

use Database\Seeders\WineSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the "Vynas" drinks to the
     * "Alkoholis" category. A fresh database is left to the seeders.
     */
    public function up(): void
    {
        (new WineSeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
