<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kibinais', function (Blueprint $table) {
            $table->decimal('package')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kibinais', function (Blueprint $table) {
            $table->dropColumn('package'); // Remove the field if rolled back
        });
    }
};
