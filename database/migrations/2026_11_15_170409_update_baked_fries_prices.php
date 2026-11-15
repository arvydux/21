<?php

use App\Models\KoldunaiDish;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * New prices from the November menu for the baked fries in "Kiti
     * patiekalai": 4,90 € with cheese, 5,90 € with bacon and cheese.
     */
    public function up(): void
    {
        $prices = [
            'Užkeptos bulvytės „FRI“ su pikantišku padažu ir sūriu' => 4.90,
            'Užkeptos bulvytės „FRI“ su pikantišku padažu, kepta šonine ir sūriu' => 5.90,
        ];

        foreach ($prices as $name => $price) {
            KoldunaiDish::where('name', $name)->update(['price' => $price]);
        }
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
