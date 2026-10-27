<?php

namespace App\Livewire\Buttons;

use App\Models\AlcoholicDrink;
use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class AlcoholicDrinks extends Component
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
        $sameOrder = Order::where('category', AlcoholicDrink::ORDER_CATEGORY)
            ->where('takeaway', false)
            ->get()
            ->first(fn (Order $order) => json_decode($order->name, true) == $productWithPrice);
        if ($sameOrder) {
            Order::where('id', $sameOrder->id)->update([
                'amount' => $sameOrder->amount + 1,
            ]);
        } else {
            $product = AlcoholicDrink::where('name', $productName)->first();
            Order::create([
                'name' => json_encode([[$productName => $productPrice]]),
                'order_price' => $productPrice,
                'package' => $product->package,
                'for_kitchen' => (bool) $product->for_kitchen,
                'category' => AlcoholicDrink::ORDER_CATEGORY,
            ]);
        }

        $this->dispatch('change-order', orderName: $productName);
    }

    public function updateLeft(int $id, ?int $left): void
    {
        $alcoholicDrink = AlcoholicDrink::find($id);
        if ($alcoholicDrink) {
            $alcoholicDrink->left = $left;
            $alcoholicDrink->save();
        }
    }

    public function toggleAttention(int $id, ?int $left = null): void
    {
        $alcoholicDrink = AlcoholicDrink::find($id);
        if ($alcoholicDrink) {
            if ($alcoholicDrink->attention) {
                $alcoholicDrink->attention = false;
                $alcoholicDrink->left = null;
            } else {
                $alcoholicDrink->attention = true;
                if ($left !== null) {
                    $alcoholicDrink->left = $left;
                }
            }
            $alcoholicDrink->save();
        }
    }

    #[On('reset-orders')]
    #[On('order-made')]
    public function refreshProducts(): void {}

    #[Renderless]
    public function updateOrder($list)
    {
        foreach ($list as $item) {
            AlcoholicDrink::where('id', $item['value'])->update(['position' => $item['order']]);
        }
    }

    public function render()
    {
        return view('livewire.buttons.alcoholic-drinks');
    }
}
