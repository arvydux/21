<?php

namespace App\Livewire\Buttons;

use App\Models\Drink;
use App\Models\Order;
use Illuminate\Support\Collection;
use Livewire\Component;

class Drinks extends Component
{
    public $productName;
    public $selectedProductName = '';
    public Collection $drinks;
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
                'package' => Drink::where('name', $productName)->first()->package,

                'category' => 3,
            ]);
        }

        $this->dispatch('change-order', orderName:$productName);
    }

    public function updateOrder($list)
    {
        foreach ($list as $item) {
            $product = $this->drinks->firstWhere('id', $item['value']);

            if ($product['position'] != $item['order']) {
                Drink::where('id', $item['value'])->update(['position' => $item['order']]);
            }
        }
    }

    public function render()
    {
        $products = Drink::orderBy('position')->paginate(10);
        $this->drinks = collect($products->items());

        return view('livewire.buttons.drinks');
    }
}
