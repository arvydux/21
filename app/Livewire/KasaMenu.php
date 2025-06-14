<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Support\Collection;
use Livewire\Component;

class KasaMenu extends Component
{
    public $categoryMenuClicked;
    public Collection $categories;

    public function categoryButtonClicked($buttonName): void
    {
        $this->categoryMenuClicked = $buttonName;
        $this->dispatch('category-button-clicked', $buttonName);
    }

    public function updateOrder($list)
    {
        foreach ($list as $item) {
            $product = $this->categories->firstWhere('id', $item['value']);

            if ($product['position'] != $item['order']) {
                Category::where('id', $item['value'])->update(['position' => $item['order']]);
            }
        }
    }


    public function render()
    {
        $products = Category::orderBy('position')->paginate(10);
        $this->categories = collect($products->items());

        return view('livewire.kasa-menu');
    }
}
