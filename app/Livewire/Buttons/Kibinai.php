<?php

namespace App\Livewire\Buttons;

use App\Models\Order;
use Illuminate\Support\Collection;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Kibinai extends Component
{
    public $productName;
    public $selectedProductName = '';
    public Collection $kibinai;

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

        $this->dispatch('change-order', orderName:$productName);
    }

    #[Renderless]
    public function updateOrder($list)
    {
        foreach ($list as $item) {
            \App\Models\Kibinai::where('id', $item['value'])->update(['position' => $item['order']]);
        }
    }

    public function render()
    {
        $products = \App\Models\Kibinai::orderBy('position')->paginate(10);
        $this->kibinai = collect($products->items());

        return view('livewire.buttons.kibinai');
    }
}
