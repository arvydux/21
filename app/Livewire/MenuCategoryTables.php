<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class MenuCategoryTables extends Component
{
    public string $buttonName;
    #[on('category-button-clicked')]
    public function getButtonName($buttonName): void
    {
        $this->buttonName = $buttonName;
    }

    public function render()
    {
        return view('livewire.menu-category-tables');
    }
}
