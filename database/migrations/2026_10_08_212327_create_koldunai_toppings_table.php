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
        Schema::create('koldunai_toppings', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->decimal('price', 8, 2);
            $table->text('sign');
            $table->boolean('show')->default(true);
            $table->boolean('is_default')->default(false);
            $table->decimal('package')->nullable();
            $table->integer('position')->nullable();
            $table->boolean('attention')->default(false);
            $table->integer('left')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('koldunai_toppings');
    }
};
