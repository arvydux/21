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
        Schema::create('potato_dishes', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->decimal('price', 8, 2);
            $table->decimal('half_price', 8, 2);
            $table->text('sign');
            $table->boolean('show')->default(true);
            $table->decimal('package')->nullable();
            $table->integer('position')->nullable();
            $table->boolean('attention')->default(false);
            $table->integer('left')->nullable();
            $table->foreignId('subcategory_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('potato_dishes');
    }
};
