<?php

namespace App\Livewire;

use App\Models\Subcategory;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class KasaCategorySection extends Component
{
    public ?string $buttonName = null;

    public ?int $categoryId = null;

    public ?int $selectedSubcategoryId = null;

    public Collection $subcategories;

    public function mount(): void
    {
        $this->subcategories = collect();
    }

    #[On('category-button-clicked')]
    public function showProductMenu($buttonName, $categoryId = null): void
    {
        $this->buttonName = $buttonName;
        $this->categoryId = $categoryId;
        $this->selectedSubcategoryId = null;

        if ($categoryId && cache('subcategory_layout_mode', 'drilldown') === 'visi_default') {
            $this->selectedSubcategoryId = Subcategory::where('category_id', $categoryId)
                ->where('name', 'Visi')
                ->value('id');
        }
    }

    public function selectSubcategory(int $subcategoryId): void
    {
        $this->selectedSubcategoryId = $subcategoryId;
    }

    public function backToSubcategories(): void
    {
        $this->selectedSubcategoryId = null;
    }

    public function render()
    {
        $this->subcategories = $this->categoryId
            ? Subcategory::where('category_id', $this->categoryId)->orderBy('position')->get()
            : collect();

        $layoutMode = cache('subcategory_layout_mode', 'drilldown');
        $visiId = $this->subcategories->firstWhere('name', 'Visi')?->id;

        $showAll = $layoutMode === 'visi_default'
            && $this->selectedSubcategoryId !== null
            && $this->selectedSubcategoryId === $visiId;

        return view('livewire.kasa-category-section', [
            'layoutMode' => $layoutMode,
            'showAll' => $showAll,
        ]);
    }
}
