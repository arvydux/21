<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Support\Collection;
use Livewire\Attributes\Renderless;
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

    #[Renderless]
    public function updateOrder($list)
    {
        foreach ($list as $item) {
            Category::where('id', $item['value'])->update(['position' => $item['order']]);
        }
    }

    public function render()
    {
        $this->categories = Category::orderBy('position')->get();

        return view('livewire.kasa-menu');
    }
}
