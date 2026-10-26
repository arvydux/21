<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Product tables and whether their products went to the kitchen before this setting existed.
     *
     * @var array<string, bool>
     */
    private const PRODUCT_TABLES = [
        'cebureks' => true,
        'other_products' => true,
        'potato_dishes' => true,
        'koldunai_dishes' => true,
        'kepiniai' => false,
        'drinks' => false,
        'alcoholic_drinks' => false,
    ];

    /**
     * Order categories that were hardcoded as kitchen categories.
     *
     * @var list<int>
     */
    private const KITCHEN_ORDER_CATEGORIES = [1, 4, 5, 6];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::PRODUCT_TABLES as $tableName => $forKitchen) {
            Schema::table($tableName, function (Blueprint $table) use ($forKitchen) {
                $table->boolean('for_kitchen')->default($forKitchen);
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('for_kitchen')->default(false);
        });

        DB::table('orders')->whereIn('category', self::KITCHEN_ORDER_CATEGORIES)->update(['for_kitchen' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ([...array_keys(self::PRODUCT_TABLES), 'orders'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('for_kitchen');
            });
        }
    }
};
