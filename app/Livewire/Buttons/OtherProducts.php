<?php

namespace App\Livewire\Buttons;

use App\Models\Order;
use App\Models\OtherProduct;
use Illuminate\Support\Collection;
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

        $this->dispatch('change-order', orderName:$productName);
    }

    public function updateOrder($list)
    {
        foreach ($list as $item) {
            $product = $this->otherProducts->firstWhere('id', $item['value']);

            if ($product['position'] != $item['order']) {
                OtherProduct::where('id', $item['value'])->update(['position' => $item['order']]);
            }
        }
    }

    public function render()
    {
        $products = OtherProduct::orderBy('position')->paginate(10);
        $this->otherProducts = collect($products->items());

        return view('livewire.buttons.other-products');
    }
}
