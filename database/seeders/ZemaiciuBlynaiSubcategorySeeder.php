<?php

namespace Database\Seeders;

class ZemaiciuBlynaiSubcategorySeeder extends PotatoDishSubcategorySeeder
{
    protected function subcategoryName(): string
    {
        return 'Žemaičių blynai';
    }

    public function dishes(): array
    {
        return [
            'Žemaičių blynai su kiauliena' => 1,
            'Žemaičių blynai su vištiena' => 2,
            'Žemaičių blynai su pievagrybiais' => 3,
        ];
    }
}
