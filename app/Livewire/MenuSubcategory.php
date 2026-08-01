<?php

namespace App\Livewire;

use App\Models\Ceburek;
use App\Models\Drink;
use App\Models\Kibinai;
use App\Models\OtherProduct;
use App\Models\Subcategory;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class MenuSubcategory extends Component
{
    /**
     * Map each category's component_name to its product model class.
     *
     * @var array<string, class-string<\Illuminate\Database\Eloquent\Model>>
     */
    private array $componentModels = [
        'ceburekai' => Ceburek::class,
        'kibinai' => Kibinai::class,
        'drinks' => Drink::class,
        'other' => OtherProduct::class,
    ];

    public ?int $categoryId = null;

    public ?string $componentName = null;

    public string $newName = '';

    public int $editingId = 0;

    public string $editName = '';

    public Collection $subcategories;

    public function mount(): void
    {
        $this->subcategories = collect();
    }

    #[On('category-button-clicked')]
    public function selectCategory($buttonName, $categoryId = null): void
    {
        $this->componentName = $buttonName;
        $this->categoryId = $categoryId;
        $this->reset('newName', 'editingId', 'editName');
    }

    public function create(): void
    {
        if ($this->categoryId === null) {
            return;
        }

        $this->validate(['newName' => 'required|string|max:255']);

        $position = Subcategory::where('category_id', $this->categoryId)->max('position') + 1;

        Subcategory::create([
            'name' => $this->newName,
            'category_id' => $this->categoryId,
            'position' => $position,
        ]);

        $this->newName = '';
        $this->dispatch('subcategoryChanged');
    }

    public function editSubcategory(int $id): void
    {
        $subcategory = Subcategory::findOrFail($id);
        $this->editingId = $subcategory->id;
        $this->editName = $subcategory->name;
    }

    public function update(): void
    {
        $this->validate(['editName' => 'required|string|max:255']);

        $subcategory = Subcategory::findOrFail($this->editingId);
        $subcategory->update(['name' => $this->editName]);

        $this->editingId = 0;
        $this->editName = '';
        $this->dispatch('subcategoryChanged');
    }

    public function cancelEdit(): void
    {
        $this->editingId = 0;
        $this->editName = '';
    }

    public function deleteSubcategory(int $id): void
    {
        $subcategory = Subcategory::findOrFail($id);

        $modelClass = $this->componentModels[$this->componentName] ?? null;

        if ($modelClass !== null) {
            $fallback = Subcategory::firstOrCreate(
                ['category_id' => $subcategory->category_id, 'name' => 'Visi'],
                ['position' => 0],
            );

            if ($fallback->id === $subcategory->id) {
                return;
            }

            $modelClass::query()
                ->where('subcategory_id', $subcategory->id)
                ->update(['subcategory_id' => $fallback->id]);
        }

        $subcategory->delete();
        $this->dispatch('subcategoryChanged');
    }

    #[Renderless]
    public function updateOrder($list): void
    {
        foreach ($list as $item) {
            Subcategory::where('id', $item['value'])->update(['position' => $item['order']]);
        }
    }

    public function render()
    {
        $this->subcategories = $this->categoryId
            ? Subcategory::where('category_id', $this->categoryId)->orderBy('position')->get()
            : collect();

        return view('livewire.menu-subcategory');
    }
}
