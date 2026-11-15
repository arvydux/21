<?php

use Database\Seeders\CheeseCeburekSeeder;
use Database\Seeders\DessertSubcategorySeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, add the dishes from the November menu:
     * "Sūrio MIX'as su grybais" to the "Su sūriu" Čeburekai and the chocolate
     * and caramel Žagarėliai to "Desertai". A fresh database is left to the
     * seeders.
     */
    public function up(): void
    {
        (new CheeseCeburekSeeder)->run();
        (new DessertSubcategorySeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
