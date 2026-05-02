<?php

namespace App\Livewire\Buttons;

use App\Models\Order;
use App\Models\OtherProduct;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class OtherProducts extends Component
{
    public $productName;

    public $selectedProductName = '';

    public Collection $otherProducts;

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
                'package' => OtherProduct::where('name', $productName)->first()->package,
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
        $products = OtherProduct::orderBy('position')->paginate(10);
        $this->otherProducts = collect($products->items());

        return view('livewire.buttons.other-products');
    }
}
