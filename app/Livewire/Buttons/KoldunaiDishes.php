<?php

namespace App\Livewire\Buttons;

use App\Models\KoldunaiDish;
use App\Models\KoldunaiTopping;
use App\Models\Order;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class KoldunaiDishes extends Component
{
    public ?int $productId = null;

    /**
     * @var list<string>
     */
    public array $toppings = [];

    public $takeaway = true;

    public ?int $subcategoryId = null;

    public bool $showAll = false;

    public function mount(?int $subcategoryId = null, bool $showAll = false): void
    {
        $this->subcategoryId = $subcategoryId;
        $this->showAll = $showAll;
    }

    public function chooseProduct(int $productId): void
    {
        $this->productId = $productId;
        $this->resetChoices();
    }

    #[On('reset-orders')]
    #[On('order-made')]
    public function refreshProducts(): void {}

    public function addOrder(): void
    {
        $product = KoldunaiDish::find($this->productId);
        if ($product === null) {
            return;
        }

        $toppingPrices = KoldunaiTopping::whereIn('name', $this->toppings)->pluck('price', 'name');
        $productWithPrice = [[$product->name => (float) $product->price]];
        $orderPrice = (float) $product->price;
        $toppingsWithPrices = [];

        $selectedToppings = $this->toppings;
        sort($selectedToppings);
        foreach ($selectedToppings as $topping) {
            if (! $toppingPrices->has($topping)) {
                continue;
            }
            $orderPrice += (float) $toppingPrices[$topping];
            $toppingsWithPrices[] = [$topping => (float) $toppingPrices[$topping]];
        }

        $sameOrder = Order::where('category', KoldunaiDish::ORDER_CATEGORY)
            ->where('takeaway', (bool) $this->takeaway)
            ->get()
            ->first(fn (Order $order) => json_decode($order->name, true) == $productWithPrice
                && (json_decode($order->toppings, true) ?? []) == $toppingsWithPrices);

        if ($sameOrder) {
            $sameOrder->update(['amount' => $sameOrder->amount + 1]);
        } else {
            Order::create([
                'name' => json_encode($productWithPrice),
                'toppings' => json_encode($toppingsWithPrices),
                'takeaway' => $this->takeaway,
                'package' => $product->package,
                'order_price' => $orderPrice,
                'category' => KoldunaiDish::ORDER_CATEGORY,
            ]);
        }

        $this->dispatch('change-order');
        $this->resetChoices();
    }

    /**
     * Start with only the default toppings selected.
     */
    private function resetChoices(): void
    {
        $this->toppings = KoldunaiTopping::where('show', true)
            ->where('is_default', true)
            ->pluck('name')
            ->all();
    }

    public function updateToppingLeft(int $id, ?int $left): void
    {
        KoldunaiTopping::whereKey($id)->update(['left' => $left]);
    }

    public function toggleToppingAttention(int $id, ?int $left = null): void
    {
        $topping = KoldunaiTopping::find($id);
        if ($topping) {
            $this->toggleAttentionOn($topping, $left);
        }
    }

    public function updateLeft(int $id, ?int $left): void
    {
        KoldunaiDish::whereKey($id)->update(['left' => $left]);
    }

    public function toggleAttention(int $id, ?int $left = null): void
    {
        $product = KoldunaiDish::find($id);
        if ($product) {
            $this->toggleAttentionOn($product, $left);
        }
    }

    private function toggleAttentionOn(KoldunaiDish|KoldunaiTopping $model, ?int $left): void
    {
        if ($model->attention) {
            $model->attention = false;
            $model->left = null;
        } else {
            $model->attention = true;
            if ($left !== null) {
                $model->left = $left;
            }
        }
        $model->save();
    }

    #[Renderless]
    public function updateOrder($list): void
    {
        foreach ($list as $item) {
            KoldunaiDish::where('id', $item['value'])->update(['position' => $item['order']]);
        }
    }

    #[Renderless]
    public function updateToppingOrder($list): void
    {
        foreach ($list as $item) {
            KoldunaiTopping::where('id', $item['value'])->update(['position' => $item['order']]);
        }
    }

    public function render(): View
    {
        return view('livewire.buttons.koldunai-dishes', [
            'products' => KoldunaiDish::when(! $this->showAll, fn ($query) => $query->where('subcategory_id', $this->subcategoryId))
                ->orderBy('position')
                ->get(),
            'koldunaiToppings' => KoldunaiTopping::where('show', true)->orderBy('position')->get(),
            'selectedProduct' => $this->productId ? KoldunaiDish::find($this->productId) : null,
        ]);
    }
}
