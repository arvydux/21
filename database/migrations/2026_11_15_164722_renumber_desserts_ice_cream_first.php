<?php

use App\Models\OtherProduct;
use App\Models\Subcategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Ice creams come first on the November menu: renumber "Desertai" to
     * 1–3 for the ice creams and 4 for "Žagarėliai su ledais ir padažu", and
     * reorder the buttons the same way. A dessert renumbered by hand is left
     * alone.
     */
    public function up(): void
    {
        $desserts = Subcategory::where('name', 'Desertai')->first();

        if ($desserts === null) {
            return;
        }

        $renumbering = [
            'Žagarėliai su ledais ir padažu' => [1, 4],
            'Ledai su vyšnių užpilu' => [2, 1],
            'Ledai su šokolado užpilu' => [3, 2],
            'Ledai su karamelės užpilu' => [4, 3],
        ];

        $products = OtherProduct::where('subcategory_id', $desserts->id)->get();

        foreach ($products as $product) {
            [$from, $to] = $renumbering[$product->name] ?? [null, null];

            if ($from !== null && $product->number === $from) {
                $product->number = $to;
            }
        }

        $positions = $products->pluck('position')->sort()->values();

        $products->sortBy(fn (OtherProduct $product) => [$product->number ?? PHP_INT_MAX, $product->position])
            ->values()
            ->each(fn (OtherProduct $product, int $index) => $product->forceFill(['position' => $positions[$index]])->save());
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
