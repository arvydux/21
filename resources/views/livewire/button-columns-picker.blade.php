<?php

use Livewire\Volt\Component;

new class extends Component {
    public int $columns = 4;

    public function mount(): void
    {
        $this->columns = (int) cache('pos_button_columns', 4);
    }

    public function updatedColumns(): void
    {
        cache()->forever('pos_button_columns', $this->columns);
    }
}; ?>

<div class="grid grid-cols-3 gap-4">

    @foreach([3, 4, 5] as $count)
    <label class="cursor-pointer">
        <input type="radio" wire:model.live="columns" value="{{ $count }}" class="sr-only">
        <div class="rounded-xl overflow-hidden transition-all
            {{ $columns === $count ? 'ring-2 ring-white scale-105' : 'ring-1 ring-white/30 opacity-60' }}">
            <div class="w-full h-24 bg-white/10 flex flex-col gap-1.5 p-2.5 justify-center">
                @foreach([1, 2] as $row)
                <div class="grid gap-1" style="grid-template-columns: repeat({{ $count }}, 1fr);">
                    @for($i = 0; $i < $count; $i++)
                    <div class="rounded" style="height:1.2rem; background:rgba(255,255,255,0.2);"></div>
                    @endfor
                </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between bg-white/10 text-white px-3 py-2 text-sm">
                <span>{{ $count }} stulp.</span>
                @if($columns === $count) <flux:icon.check-circle class="size-4" /> @endif
            </div>
        </div>
    </label>
    @endforeach

</div>
