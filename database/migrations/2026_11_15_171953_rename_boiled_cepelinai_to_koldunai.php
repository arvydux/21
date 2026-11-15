<?php

use App\Models\OtherProduct;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * The boiled dishes added as "cepelinai" are koldūnai on the final menu.
     */
    public function up(): void
    {
        $names = [
            'Virti naminiai cepelinai su grietine' => 'Virti naminiai koldūnai su grietine',
            'Virti naminiai cepelinai su spirgučiais' => 'Virti naminiai koldūnai su spirgučiais',
        ];

        foreach ($names as $from => $to) {
            OtherProduct::where('name', $from)->update(['name' => $to]);
        }
    }

    public function down(): void
    {
        // Data backfill only.
    }
};
