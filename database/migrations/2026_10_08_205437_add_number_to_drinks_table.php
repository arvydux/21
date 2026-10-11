<?php

use Database\Seeders\HotDrinkSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the menu number column and, on an existing deployment, the numbered
     * "Karšti gėrimai" drinks. A fresh database is left to the seeders.
     */
    public function up(): void
    {
        Schema::table('drinks', function (Blueprint $table) {
            $table->unsignedSmallInteger('number')->nullable()->after('id');
        });

        (new HotDrinkSeeder)->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drinks', function (Blueprint $table) {
            $table->dropColumn('number');
        });
    }
};
