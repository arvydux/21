<?php

namespace Database\Seeders;

class BulviniaiBlynaiSubcategorySeeder extends PotatoDishSubcategorySeeder
{
    protected function subcategoryName(): string
    {
        return 'Bulviniai blynai';
    }

    public function dishes(): array
    {
        return [
            'Bulvinukai su sūriu' => 1,
            'Bulviniai blynai su mėsa „Kėdainių“' => 2,
            'Traškūs bulviniai blynai be mėsos' => 3,
        ];
    }
}
