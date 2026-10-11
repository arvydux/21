<?php

use App\Models\PotatoDish;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menu numbers of the dishes added by the "Bulviniai patiekalai" migration.
     *
     * @var array<string, int>
     */
    private array $menuNumbers = [
        'Žemaičių blynai su kiauliena' => 1,
        'Žemaičių blynai su vištiena' => 2,
        'Žemaičių blynai su pievagrybiais' => 3,
        'Bulvinukai su sūriu' => 4,
        'Bulviniai blynai su mėsa „Kėdainių“' => 5,
        'Traškūs bulviniai blynai be mėsos' => 6,
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('potato_dishes', function (Blueprint $table) {
            $table->unsignedSmallInteger('number')->nullable()->after('id');
        });

        foreach ($this->menuNumbers as $name => $number) {
            PotatoDish::where('name', $name)->whereNull('number')->update(['number' => $number]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('potato_dishes', function (Blueprint $table) {
            $table->dropColumn('number');
        });
    }
};
