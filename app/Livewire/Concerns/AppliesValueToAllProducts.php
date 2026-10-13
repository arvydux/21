<?php

namespace App\Livewire\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;

trait AppliesValueToAllProducts
{
    use NormalizesMoneyInput;

    public string $bulkField = 'package';

    public $bulkValue = null;

    /**
     * The price columns that can be set for every product of the category at once.
     *
     * @return array<string, string>
     */
    abstract protected function bulkFields(): array;

    /**
     * @return class-string<Model>
     */
    abstract protected function bulkModel(): string;

    /**
     * @return array<string, string>
     */
    #[Computed]
    public function bulkFieldOptions(): array
    {
        return $this->bulkFields();
    }

    public function applyToAll(): void
    {
        $this->normalizeMoney('bulkValue');

        $this->validate([
            'bulkField' => ['required', Rule::in(array_keys($this->bulkFields()))],
            'bulkValue' => ['required', 'numeric', 'min:0'],
        ], $this->moneyMessages('bulkValue'));

        $this->bulkModel()::query()->update([$this->bulkField => $this->bulkValue]);

        $this->bulkValue = null;
        $this->editedProductNameId = 0;
        $this->mount();
    }
}
