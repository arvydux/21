<?php

use Database\Seeders\CheeseCeburekSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the "Su sūriu" Čeburekai and move the
     * existing cheese Čeburekai into it at the new price. A fresh database is
     * left to the seeders.
     */
    public function up(): void
    {
        (new CheeseCeburekSeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
