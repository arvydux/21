<?php

namespace App\Livewire;

use App\Models\OrderNumbers;
use Livewire\Component;

class Kitchen extends Component
{
    public function makeOrderReady($number): void
    {
        OrderNumbers::where('number', $number)->update(['is_ready' => true]);
        $this->skipRender();
    }

    public function makeOrderNotReady($number): void
    {
        OrderNumbers::where('number', $number)->update(['is_ready' => false]);
        $this->skipRender();
    }

    public function render()
    {
        return view('livewire.kitchen');
    }
}
