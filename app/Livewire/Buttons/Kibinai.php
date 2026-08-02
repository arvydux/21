<?php

namespace App\Livewire\Buttons;

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Kibinai extends Component
{
    public $productName;

    public $selectedProductName = '';

    public ?int $subcategoryId = null;

    public bool $showAll = false;

    public function mount(?int $subcategoryId = null, bool $showAll = false): void
    {
        $this->subcategoryId = $subcategoryId;
        $this->showAll = $showAll;
    }

    public function addProduct($productName, $productPrice)
    {
        $this->selectedProductName = $productName;
        $productWithPrice[] = [$productName => $productPrice];
        $sameOrder = Order::whereJsonContains('name', $productWithPrice)->first() ?? null;
        if ($sameOrder) {
            Order::where('id', $sameOrder->id)->update([
                'amount' => $sameOrder->amount + 1,
            ]);
        } else {
            Order::create([
                'name' => json_encode([[$productName => $productPrice]]),
                'order_price' => $productPrice,
                'package' => \App\Models\Kibinai::where('name', $productName)->first()->package,
                'category' => 2,
            ]);
        }

        $this->dispatch('change-order', orderName: $productName);
    }

    public function updateLeft(int $id, ?int $left): void
    {
        $kibinai = \App\Models\Kibinai::find($id);
        if ($kibinai) {
            $kibinai->left = $left;
            $kibinai->save();
        }
    }

    public function toggleAttention(int $id, ?int $left = null): void
    {
        $kibinai = \App\Models\Kibinai::find($id);
        if ($kibinai) {
            if ($kibinai->attention) {
                $kibinai->attention = false;
                $kibinai->left = null;
            } else {
                $kibinai->attention = true;
                if ($left !== null) {
                    $kibinai->left = $left;
                }
            }
            $kibinai->save();
        }
    }

    #[On('reset-orders')]
    #[On('order-made')]
    public function refreshProducts(): void {}

    #[Renderless]
    public function updateOrder($list)
    {
        foreach ($list as $item) {
            \App\Models\Kibinai::where('id', $item['value'])->update(['position' => $item['order']]);
        }
    }

    public function render()
    {
        return view('livewire.buttons.kibinai');
    }
}
