<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $style = 'compact';

    public function mount(): void
    {
        $this->style = cache('pos_button_style', 'compact');
    }

    public function updatedStyle(): void
    {
        cache()->forever('pos_button_style', $this->style);
    }
}; ?>

<div class="grid grid-cols-2 gap-4">

    {{-- Compact --}}
    <label class="cursor-pointer">
        <input type="radio" wire:model.live="style" value="compact" class="sr-only">
        <div class="rounded-xl overflow-hidden transition-all
            {{ $style === 'compact' ? 'ring-2 ring-white scale-105' : 'ring-1 ring-white/30 opacity-60' }}">
            <div class="w-full h-28 bg-white/10 flex flex-col gap-2 p-3 justify-center">
                @foreach([1, 2, 3] as $n)
                <div class="flex rounded-lg overflow-hidden" style="height:1.4rem; background:rgba(255,255,255,0.18);">
                    <div class="flex-1 flex items-center px-2">
                        <div style="height:0.4rem; width:55%; background:rgba(255,255,255,0.55); border-radius:2px;"></div>
                    </div>
                    <div style="width:1px; background:rgba(255,255,255,0.25);"></div>
                    <div class="flex items-center justify-center px-2" style="width:1.8rem;">
                        <div style="height:0.5rem; width:70%; background:rgba(255,255,255,0.55); border-radius:2px;"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between bg-white/10 text-white px-3 py-2 text-sm">
                <span>Maži</span>
                @if($style === 'compact') <flux:icon.check-circle class="size-4" /> @endif
            </div>
        </div>
    </label>

    {{-- Large --}}
    <label class="cursor-pointer">
        <input type="radio" wire:model.live="style" value="large" class="sr-only">
        <div class="rounded-xl overflow-hidden transition-all
            {{ $style === 'large' ? 'ring-2 ring-white scale-105' : 'ring-1 ring-white/30 opacity-60' }}">
            <div class="w-full h-28 bg-white/10 flex flex-col gap-2 p-3 justify-center">
                @foreach([1, 2] as $n)
                <div class="flex rounded-lg overflow-hidden" style="height:2.4rem; background:rgba(255,255,255,0.18);">
                    <div class="flex-1 flex items-center px-2">
                        <div style="height:0.5rem; width:55%; background:rgba(255,255,255,0.55); border-radius:2px;"></div>
                    </div>
                    <div style="width:1px; background:rgba(255,255,255,0.25);"></div>
                    <div class="flex items-center justify-center px-2" style="width:2.2rem;">
                        <div style="height:0.65rem; width:70%; background:rgba(255,255,255,0.55); border-radius:2px;"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between bg-white/10 text-white px-3 py-2 text-sm">
                <span>Dideli</span>
                @if($style === 'large') <flux:icon.check-circle class="size-4" /> @endif
            </div>
        </div>
    </label>

</div>
