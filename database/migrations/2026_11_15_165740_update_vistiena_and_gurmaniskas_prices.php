<?php

use App\Models\Ceburek;
use App\Models\Subcategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * New prices from the November menu: Vištiena 6,50 € ("Su mėsa") and
     * Gurmaniškas 7,50 € ("Firminis").
     */
    public function up(): void
    {
        $prices = [
            'Su mėsa' => ['Vištiena' => 6.50],
            'Firminis' => ['Gurmaniškas' => 7.50],
        ];

        foreach ($prices as $subcategoryName => $products) {
            $subcategory = Subcategory::where('name', $subcategoryName)->first();

            if ($subcategory === null) {
                continue;
            }

            foreach ($products as $name => $price) {
                Ceburek::where('subcategory_id', $subcategory->id)->where('name', $name)->update(['price' => $price]);
            }
        }
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
