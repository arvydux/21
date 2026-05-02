<?php

namespace App\Livewire\Buttons;

use App\Models\Drink;
use App\Models\Order;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
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

        $this->dispatch('change-order', orderName: $productName);
    }

    public function updateLeft(int $id, ?int $left): void
    {
        $drink = Drink::find($id);
        if ($drink) {
            $drink->left = $left;
            $drink->save();
        }
    }

    public function toggleAttention(int $id, ?int $left = null): void
    {
        $drink = Drink::find($id);
        if ($drink) {
            if ($drink->attention) {
                $drink->attention = false;
                $drink->left = null;
            } else {
                $drink->attention = true;
                if ($left !== null) {
                    $drink->left = $left;
                }
            }
            $drink->save();
        }
    }

    #[On('reset-orders')]
    #[On('order-made')]
    public function refreshProducts(): void {}

    #[Renderless]
    public function updateOrder($list)
    {
        foreach ($list as $item) {
            Drink::where('id', $item['value'])->update(['position' => $item['order']]);
        }
    }

    public function render()
    {
        $products = Drink::orderBy('position')->paginate(10);
        $this->drinks = collect($products->items());

        return view('livewire.buttons.drinks');
    }
}
