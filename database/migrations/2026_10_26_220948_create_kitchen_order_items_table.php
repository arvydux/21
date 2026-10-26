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
        Schema::create('kitchen_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_number_id')->constrained('order_numbers')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('amount')->default(1);
            $table->json('toppings')->nullable();
            $table->unsignedTinyInteger('category')->default(4);
            $table->boolean('takeaway')->default(false);
            $table->string('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_order_items');
    }
};
