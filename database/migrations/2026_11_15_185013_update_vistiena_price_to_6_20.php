<?php

use App\Models\Ceburek;
use App\Models\Subcategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Vištiena ("Su mėsa") goes down to 6,20 €.
     */
    public function up(): void
    {
        $subcategory = Subcategory::where('name', 'Su mėsa')->first();

        if ($subcategory === null) {
            return;
        }

        Ceburek::where('subcategory_id', $subcategory->id)->where('name', 'Vištiena')->update(['price' => 6.20]);
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
