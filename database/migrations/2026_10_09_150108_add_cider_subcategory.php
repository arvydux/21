<?php

use Database\Seeders\CiderSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the "Sidras" drinks to the
     * "Alkoholis" category. A fresh database is left to the seeders.
     */
    public function up(): void
    {
        (new CiderSeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
