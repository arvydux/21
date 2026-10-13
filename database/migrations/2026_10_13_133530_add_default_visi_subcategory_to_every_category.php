<?php

use Database\Seeders\DefaultSubcategorySeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * On an existing deployment, give every category back its default "Visi"
     * subcategory as the first tab. A fresh database is left to the seeders.
     */
    public function up(): void
    {
        (new DefaultSubcategorySeeder)->run();
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
