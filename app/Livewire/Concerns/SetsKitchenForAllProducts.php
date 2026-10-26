<?php

namespace App\Livewire\Concerns;

use Illuminate\Database\Eloquent\Model;

trait SetsKitchenForAllProducts
{
    public bool $for_kitchen = false;

    /**
     * @return class-string<Model>
     */
    abstract protected function bulkModel(): string;

    /**
     * Decide for every product of the category at once whether it is printed on the kitchen receipt.
     */
    public function setKitchenForAll(bool $forKitchen): void
    {
        $this->bulkModel()::query()->update(['for_kitchen' => $forKitchen]);

        $this->editedProductNameId = 0;
        $this->mount();
    }
}
