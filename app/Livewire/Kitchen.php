<?php

namespace App\Livewire;

use App\Models\OrderNumbers;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Kitchen extends Component
{
    public function makeOrderReady($number): void
    {
        OrderNumbers::where('number', $number)->update(['is_ready' => true]);
        Cache::put('playSound', (int) Cache::get('playSound', 0) + 1, 60 * 60 * 24);
    }

    public function makeOrderNotReady($number): void
    {
        OrderNumbers::where('number', $number)->update(['is_ready' => false]);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.kitchen');
    }
}
