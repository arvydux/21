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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('toppings')->nullable();
            $table->integer('amount')->default(1);
            $table->decimal('order_price', 8, 2)->nullable();
            $table->boolean('takeaway')->default(false);
            $table->boolean('package')->default(false);
            $table->integer('category')->default(4);$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
