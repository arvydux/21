<?php

namespace App\Livewire;

use App\Jobs\SendOrderReadyPush;
use App\Models\KitchenOrderItem;
use App\Models\OrderNumbers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Kitchen extends Component
{
    /**
     * Minutes after which a preparing order is shown as late, then as very late.
     */
    public const LATE_AFTER_MINUTES = 15;

    public const VERY_LATE_AFTER_MINUTES = 25;

    /** Cache key of the kitchen screen layout chosen in the settings. */
    public const LAYOUT_CACHE_KEY = 'kitchen_layout';

    /** Preparing and ready orders side by side. */
    public const LAYOUT_NUMBERS = 'numbers';

    /** Preparing orders with the ready ones in a strip below, next to a sum-up of what to make. */
    public const LAYOUT_SUMMARY = 'summary';

    public static function layout(): string
    {
        $layout = Cache::get(self::LAYOUT_CACHE_KEY, self::LAYOUT_NUMBERS);

        return in_array($layout, [self::LAYOUT_NUMBERS, self::LAYOUT_SUMMARY], true) ? $layout : self::LAYOUT_NUMBERS;
    }

    /**
     * Every product still to be made across the preparing orders, with how many of each topping goes on them.
     * Kept in menu order so rows do not jump around while the kitchen works.
     *
     * @param  Collection<int, OrderNumbers>  $preparingOrders
     * @return list<array{name: string, amount: int, toppings: array<string, int>}>
     */
    public function summary(Collection $preparingOrders): array
    {
        return $preparingOrders
            ->flatMap(fn (OrderNumbers $order) => $order->kitchenItems)
            ->groupBy('name')
            ->map(fn ($items, string $name): array => [
                'name' => $name,
                'category' => $items->min('category'),
                'amount' => $items->sum('amount'),
                'toppings' => $items
                    ->flatMap(fn (KitchenOrderItem $item) => collect($item->toppings ?? [])->map(fn (string $topping) => ['name' => $topping, 'amount' => $item->amount]))
                    ->groupBy('name')
                    ->map(fn ($toppings) => $toppings->sum('amount'))
                    ->sortKeys()
                    ->all(),
            ])
            ->sortBy([['category', 'asc'], ['name', 'asc']])
            ->map(fn (array $row): array => collect($row)->except('category')->all())
            ->values()
            ->all();
    }

    /**
     * Wishes written at the till, with the order number they belong to.
     *
     * @param  Collection<int, OrderNumbers>  $preparingOrders
     * @return list<array{number: int, comments: string}>
     */
    public function wishes(Collection $preparingOrders): array
    {
        return $preparingOrders
            ->flatMap(fn (OrderNumbers $order) => $order->kitchenItems
                ->filter(fn (KitchenOrderItem $item) => filled($item->comments))
                ->map(fn (KitchenOrderItem $item) => ['number' => (int) $order->number, 'comments' => $item->name.': '.$item->comments]))
            ->values()
            ->all();
    }

    public function makeOrderReady($number): void
    {
        OrderNumbers::where('number', $number)->update(['is_ready' => true]);
        SendOrderReadyPush::dispatch((int) $number);
        $pending = Cache::get('pendingSounds', []);
        $pending[] = (int) $number;
        Cache::put('pendingSounds', $pending, 60 * 60 * 24);

        $animations = Cache::get('pendingAnimations', []);
        $animations[] = (int) $number;
        Cache::put('pendingAnimations', $animations, 60 * 60 * 24);
    }

    public function makeOrderNotReady($number): void
    {
        OrderNumbers::where('number', $number)->update(['is_ready' => false]);
    }

    public function render(): \Illuminate\View\View
    {
        $layout = self::layout();
        $preparingOrders = OrderNumbers::where('is_ready', false)
            ->when($layout === self::LAYOUT_SUMMARY, fn ($query) => $query->with('kitchenItems'))
            ->orderBy('created_at')
            ->get();

        return view('livewire.kitchen', [
            'layout' => $layout,
            'preparingOrders' => $preparingOrders,
            'readyOrders' => OrderNumbers::where('is_ready', true)->where('is_taken', false)->orderBy('updated_at', 'desc')->get(),
            'summary' => $layout === self::LAYOUT_SUMMARY ? $this->summary($preparingOrders) : [],
            'wishes' => $layout === self::LAYOUT_SUMMARY ? $this->wishes($preparingOrders) : [],
        ]);
    }
}
