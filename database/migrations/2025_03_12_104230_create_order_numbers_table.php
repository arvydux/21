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
        Schema::create('order_numbers', function (Blueprint $table) {
            $table->id();
            $table->integer('number')->default(0);
            $table->boolean('is_ready')->default(0);
            $table->boolean('by_phone')->default(0);
            $table->boolean('is_taken')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_numbers');
    }
};
