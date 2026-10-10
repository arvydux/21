<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename the "Kibinai" category to "Kepiniai": its product table and the
     * category row (display name and component name). Orders keep category 2.
     */
    public function up(): void
    {
        Schema::rename('kibinais', 'kepiniai');

        DB::table('categories')
            ->where('component_name', 'kibinai')
            ->update(['name' => 'Kepiniai', 'component_name' => 'kepiniai']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')
            ->where('component_name', 'kepiniai')
            ->update(['name' => 'Kibinai', 'component_name' => 'kibinai']);

        Schema::rename('kepiniai', 'kibinais');
    }
};
