<?php

namespace App\Livewire\Buttons;

use App\Models\Order;
use App\Models\OtherProduct;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class OtherProducts extends Component
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
        $sameOrder = Order::where('category', 4)
            ->where('takeaway', false)
            ->get()
            ->first(fn (Order $order) => json_decode($order->name, true) == $productWithPrice);
        if ($sameOrder) {
            Order::where('id', $sameOrder->id)->update([
                'amount' => $sameOrder->amount + 1,
            ]);
        } else {
            $product = OtherProduct::where('name', $productName)->first();
            Order::create([
                'name' => json_encode([[$productName => $productPrice]]),
                'order_price' => $productPrice,
                'package' => $product->package,
                'for_kitchen' => (bool) $product->for_kitchen,
                'category' => 4,
            ]);
        }

        $this->dispatch('change-order', orderName: $productName);
    }

    public function updateLeft(int $id, ?int $left): void
    {
        $product = OtherProduct::find($id);
        if ($product) {
            $product->left = $left;
            $product->save();
        }
    }

    public function toggleAttention(int $id, ?int $left = null): void
    {
        $product = OtherProduct::find($id);
        if ($product) {
            if ($product->attention) {
                $product->attention = false;
                $product->left = null;
            } else {
                $product->attention = true;
                if ($left !== null) {
                    $product->left = $left;
                }
            }
            $product->save();
        }
    }

    #[On('reset-orders')]
    #[On('order-made')]
    public function refreshProducts(): void {}

    #[Renderless]
    public function updateOrder($list)
    {
        foreach ($list as $item) {
            OtherProduct::where('id', $item['value'])->update(['position' => $item['order']]);
        }
    }

    public function render()
    {
        return view('livewire.buttons.other-products');
    }
}
