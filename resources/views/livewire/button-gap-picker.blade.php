<?php

use Livewire\Volt\Component;

new class extends Component {
    public int $gap = 4;

    public function mount(): void
    {
        $this->gap = (int) cache('pos_button_gap', 4);
    }

    public function updatedGap(): void
    {
        cache()->forever('pos_button_gap', $this->gap);
    }
}; ?>

<div class="grid grid-cols-5 gap-3">

    @foreach([1, 2, 3, 4, 5] as $value)
    <label class="cursor-pointer">
        <input type="radio" wire:model.live="gap" value="{{ $value }}" class="sr-only">
        <div class="rounded-xl overflow-hidden transition-all
            {{ $gap === $value ? 'ring-2 ring-white scale-105' : 'ring-1 ring-white/30 opacity-60' }}">
            <div class="w-full h-20 bg-white/10 flex flex-col p-2 justify-center" style="gap: {{ $value * 3 }}px;">
                @foreach([1, 2, 3] as $row)
                <div class="rounded w-full" style="height:0.9rem; background:rgba(255,255,255,0.2);"></div>
                @endforeach
            </div>
            <div class="flex items-center justify-center bg-white/10 text-white px-2 py-1.5 text-xs font-medium">
                {{ $value }}
                @if($gap === $value) <flux:icon.check-circle class="size-3 ml-1" /> @endif
            </div>
        </div>
    </label>
    @endforeach

</div>
