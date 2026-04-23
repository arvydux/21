<?php

namespace App\Livewire;

use App\Events\OrderReady;
use App\Events\OrdersUpdated;
use App\Models\OrderNumbers;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class Kitchen extends Component
{
    #[On('echo:orders,OrdersUpdated')]
    public function handleOrdersUpdated(): void {}

    public function makeOrderReady($number): void
    {
        OrderNumbers::where('number', $number)->update(['is_ready' => true]);
        Cache::put('playSound', 1, 60 * 60 * 24);
        OrdersUpdated::dispatch();
        OrderReady::dispatch((int) $number);
    }

    public function makeOrderNotReady($number): void
    {
        OrderNumbers::where('number', $number)->update(['is_ready' => false]);
        OrdersUpdated::dispatch();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.kitchen');
    }
}
