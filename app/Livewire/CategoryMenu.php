<?php

namespace App\Livewire;

use Livewire\Component;

class CategoryMenu extends Component
{
    public $categoryMenuClicked;
    public function categoryButtonClicked($buttonName): void
    {
        $this->categoryMenuClicked = $buttonName;
        $this->dispatch('category-button-clicked', $buttonName);
    }
    public function render()
    {
        return view('livewire.category-menu');
    }
}
