<?php

namespace App\Livewire;

use App\Models\OrderNumbers;
use Livewire\Component;

class ReadyOrders extends Component
{
    public bool $firstTime = true;

    public function setFirstTime(): void
    {
        $this->firstTime = false;
    }

    public function notFirstTime(): void
    {
        $this->firstTime = false;
    }

    public function notNotFirstTime(): void
    {
        // intentionally empty — used for the auto-sound button after first interaction
    }

    public function makeOrderTaken($number): void
    {
        OrderNumbers::where('number', $number)->update(['is_taken' => true]);
    }

    public function render()
    {
        return view('livewire.ready-orders');
    }
}
