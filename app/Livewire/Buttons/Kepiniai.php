<?php

namespace App\Livewire\Buttons;

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Kepiniai extends Component
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
        $sameOrder = Order::where('category', 2)
            ->where('takeaway', false)
            ->get()
            ->first(fn (Order $order) => json_decode($order->name, true) == $productWithPrice);
        if ($sameOrder) {
            Order::where('id', $sameOrder->id)->update([
                'amount' => $sameOrder->amount + 1,
            ]);
        } else {
            $product = \App\Models\Kepiniai::where('name', $productName)->first();
            Order::create([
                'name' => json_encode([[$productName => $productPrice]]),
                'order_price' => $productPrice,
                'package' => $product->package,
                'for_kitchen' => (bool) $product->for_kitchen,
                'category' => 2,
            ]);
        }

        $this->dispatch('change-order', orderName: $productName);
    }

    public function updateLeft(int $id, ?int $left): void
    {
        $kepinys = \App\Models\Kepiniai::find($id);
        if ($kepinys) {
            $kepinys->left = $left;
            $kepinys->save();
        }
    }

    public function toggleAttention(int $id, ?int $left = null): void
    {
        $kepinys = \App\Models\Kepiniai::find($id);
        if ($kepinys) {
            if ($kepinys->attention) {
                $kepinys->attention = false;
                $kepinys->left = null;
            } else {
                $kepinys->attention = true;
                if ($left !== null) {
                    $kepinys->left = $left;
                }
            }
            $kepinys->save();
        }
    }

    #[On('reset-orders')]
    #[On('order-made')]
    public function refreshProducts(): void {}

    #[Renderless]
    public function updateOrder($list)
    {
        foreach ($list as $item) {
            \App\Models\Kepiniai::where('id', $item['value'])->update(['position' => $item['order']]);
        }
    }

    public function render()
    {
        return view('livewire.buttons.kepiniai');
    }
}
