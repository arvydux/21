<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $mode = 'drilldown';

    public function mount(): void
    {
        $this->mode = cache('subcategory_layout_mode', 'drilldown');
    }

    public function updatedMode(): void
    {
        cache()->forever('subcategory_layout_mode', $this->mode);
    }
}; ?>

<div class="grid grid-cols-2 gap-4">

    {{-- Drilldown: subcategory buttons disappear, products + back button --}}
    <label class="cursor-pointer">
        <input type="radio" wire:model.live="mode" value="drilldown" class="sr-only">
        <div class="rounded-xl overflow-hidden transition-all
            {{ $mode === 'drilldown' ? 'ring-2 ring-white scale-105' : 'ring-1 ring-white/30 opacity-60' }}">
            <div class="w-full h-28 bg-white/10 flex flex-col gap-2 p-3 justify-center">
                <div class="flex items-center gap-1" style="height:1rem;">
                    <div class="flex items-center justify-center rounded" style="width:1.6rem;height:0.9rem;background:rgba(255,255,255,0.3);">
                        <div style="height:0.3rem;width:60%;background:rgba(255,255,255,0.7);border-radius:2px;"></div>
                    </div>
                    <div style="height:0.3rem;width:20%;background:rgba(255,255,255,0.35);border-radius:2px;"></div>
                </div>
                @foreach([1, 2] as $n)
                <div class="rounded" style="height:1.4rem;background:rgba(255,255,255,0.18);"></div>
                @endforeach
            </div>
            <div class="flex items-center justify-between bg-white/10 text-white px-3 py-2 text-sm">
                <span>{{ __('Su „Atgal“') }}</span>
                @if($mode === 'drilldown') <flux:icon.check-circle class="size-4" /> @endif
            </div>
        </div>
    </label>

    {{-- Persistent: subcategory buttons stay visible, no back button --}}
    <label class="cursor-pointer">
        <input type="radio" wire:model.live="mode" value="persistent" class="sr-only">
        <div class="rounded-xl overflow-hidden transition-all
            {{ $mode === 'persistent' ? 'ring-2 ring-white scale-105' : 'ring-1 ring-white/30 opacity-60' }}">
            <div class="w-full h-28 bg-white/10 flex flex-col gap-2 p-3 justify-center">
                <div class="flex gap-1" style="height:0.9rem;">
                    <div class="flex-1 rounded" style="background:rgba(255,255,255,0.5);"></div>
                    <div class="flex-1 rounded" style="background:rgba(255,255,255,0.2);"></div>
                    <div class="flex-1 rounded" style="background:rgba(255,255,255,0.2);"></div>
                </div>
                @foreach([1, 2] as $n)
                <div class="rounded" style="height:1.4rem;background:rgba(255,255,255,0.18);"></div>
                @endforeach
            </div>
            <div class="flex items-center justify-between bg-white/10 text-white px-3 py-2 text-sm">
                <span>{{ __('Be „Atgal“') }}</span>
                @if($mode === 'persistent') <flux:icon.check-circle class="size-4" /> @endif
            </div>
        </div>
    </label>

</div>
