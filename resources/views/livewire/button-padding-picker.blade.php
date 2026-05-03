<?php

use Livewire\Volt\Component;

new class extends Component {
    public int $padding = 3;

    public function mount(): void
    {
        $this->padding = (int) cache('pos_button_padding', 3);
    }

    public function updatedPadding(): void
    {
        cache()->forever('pos_button_padding', $this->padding);
    }
}; ?>

<div class="grid grid-cols-5 gap-3">

    @foreach([1, 2, 3, 4, 5] as $value)
    <label class="cursor-pointer">
        <input type="radio" wire:model.live="padding" value="{{ $value }}" class="sr-only">
        <div class="rounded-xl overflow-hidden transition-all
            {{ $padding === $value ? 'ring-2 ring-white scale-105' : 'ring-1 ring-white/30 opacity-60' }}">
            <div class="w-full h-20 bg-white/10 flex flex-col gap-2 p-2 justify-center">
                @foreach([1, 2] as $row)
                <div class="rounded-lg bg-white/15 flex items-center justify-center" style="height: 1.6rem; padding: {{ $value * 2 }}px {{ $value * 3 }}px;">
                    <div class="rounded bg-white/50 w-full" style="height: 0.4rem;"></div>
                </div>
                @endforeach
            </div>
            <div class="flex items-center justify-center bg-white/10 text-white px-2 py-1.5 text-xs font-medium">
                {{ $value }}
                @if($padding === $value) <flux:icon.check-circle class="size-3 ml-1" /> @endif
            </div>
        </div>
    </label>
    @endforeach

</div>
