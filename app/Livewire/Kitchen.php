<?php

namespace App\Livewire;

use App\Jobs\SendOrderReadyPush;
use App\Models\OrderNumbers;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Kitchen extends Component
{
    /**
     * Minutes after which a preparing order is shown as late, then as very late.
     */
    public const LATE_AFTER_MINUTES = 15;

    public const VERY_LATE_AFTER_MINUTES = 25;

    public function makeOrderReady($number): void
    {
        OrderNumbers::where('number', $number)->update(['is_ready' => true]);
        SendOrderReadyPush::dispatchAfterResponse((int) $number);
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
        return view('livewire.kitchen', [
            'preparingOrders' => OrderNumbers::where('is_ready', false)->orderBy('created_at')->get(),
            'readyOrders' => OrderNumbers::where('is_ready', true)->where('is_taken', false)->orderBy('updated_at', 'desc')->get(),
        ]);
    }
}
