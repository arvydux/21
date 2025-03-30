<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class KasaCategorySection extends Component
{
    public string $buttonName;
    #[on('category-button-clicked')]
    public function showProductMenu($buttonName): void
    {
        $this->buttonName = $buttonName;
    }

    public function render()
    {
        return view('livewire.kasa-category-section');
    }
}
