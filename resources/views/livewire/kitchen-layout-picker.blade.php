<?php

use App\Livewire\Kitchen;
use Livewire\Volt\Component;

new class extends Component {
    public string $layout = Kitchen::LAYOUT_NUMBERS;

    public function mount(): void
    {
        $this->layout = Kitchen::layout();
    }

    public function updatedLayout(): void
    {
        if (! in_array($this->layout, [Kitchen::LAYOUT_NUMBERS, Kitchen::LAYOUT_SUMMARY], true)) {
            $this->layout = Kitchen::layout();

            return;
        }

        cache()->forever(Kitchen::LAYOUT_CACHE_KEY, $this->layout);
    }
}; ?>

<div class="grid grid-cols-2 gap-4">

    {{-- Numeriai --}}
    <label class="cursor-pointer">
        <input type="radio" wire:model.live="layout" value="{{ Kitchen::LAYOUT_NUMBERS }}" class="sr-only">
        <div class="rounded-xl overflow-hidden transition-all
            {{ $layout === Kitchen::LAYOUT_NUMBERS ? 'ring-2 ring-white scale-105' : 'ring-1 ring-white/30 opacity-60' }}">
            <div class="flex h-36 w-full gap-1.5 overflow-hidden bg-[#f3f3f1] p-2 font-nunito">
                <div class="flex flex-[3] flex-col overflow-hidden rounded-lg bg-white">
                    <div class="bg-[#17191b] px-1.5 py-1 text-[0.45rem] font-black uppercase text-white">Gaminami</div>
                    <div class="grid grid-cols-3 gap-1 p-1.5">
                        @foreach([54, 55, 58, 59, 60, 61] as $n)
                            <div class="flex aspect-[4/3] items-center justify-center rounded bg-[#fdbf2d] text-[0.55rem] font-black text-[#151515]">{{ $n }}</div>
                        @endforeach
                    </div>
                </div>
                <div class="flex flex-[2] flex-col overflow-hidden rounded-lg bg-white">
                    <div class="bg-[#17191b] px-1.5 py-1 text-[0.45rem] font-black uppercase text-white">Paruošti</div>
                    <div class="grid grid-cols-2 gap-1 p-1.5">
                        @foreach([52, 62, 57, 63] as $n)
                            <div class="flex aspect-[4/3] items-center justify-center rounded bg-[#169147] text-[0.55rem] font-black text-white">{{ $n }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between bg-white/10 text-white px-3 py-2 text-sm">
                <span>Numeriai</span>
                @if($layout === Kitchen::LAYOUT_NUMBERS) <flux:icon.check-circle class="size-4" /> @endif
            </div>
        </div>
    </label>

    {{-- Suvestinė --}}
    <label class="cursor-pointer">
        <input type="radio" wire:model.live="layout" value="{{ Kitchen::LAYOUT_SUMMARY }}" class="sr-only">
        <div class="rounded-xl overflow-hidden transition-all
            {{ $layout === Kitchen::LAYOUT_SUMMARY ? 'ring-2 ring-white scale-105' : 'ring-1 ring-white/30 opacity-60' }}">
            <div class="flex h-36 w-full gap-1.5 overflow-hidden bg-[#f3f3f1] p-2 font-nunito">
                <div class="flex flex-[3] flex-col overflow-hidden rounded-lg bg-white">
                    <div class="bg-[#17191b] px-1.5 py-1 text-[0.45rem] font-black uppercase text-white">Gaminami</div>
                    <div class="grid grid-cols-3 gap-1 p-1.5">
                        @foreach([54, 55, 58, 59, 60, 61] as $n)
                            <div class="flex aspect-[4/3] items-center justify-center rounded bg-[#fdbf2d] text-[0.55rem] font-black text-[#151515]">{{ $n }}</div>
                        @endforeach
                    </div>
                    <div class="mt-auto flex items-center gap-1 border-t border-stone-200 bg-[#f7f7f5] px-1.5 py-1">
                        <span class="text-[0.4rem] font-black uppercase text-[#17191b]">Paruošti</span>
                        @foreach([52, 62, 57] as $n)
                            <span class="rounded-sm bg-[#169147] px-1 text-[0.45rem] font-black text-white">{{ $n }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="flex flex-[2] flex-col overflow-hidden rounded-lg bg-white">
                    <div class="bg-[#17191b] px-1.5 py-1 text-[0.45rem] font-black uppercase text-white">Gaminti</div>
                    <div class="flex flex-col gap-1 p-1.5">
                        @foreach(['Kibinas su mėsa' => 7, 'Čeburekas' => 4, 'Cepelinai' => 2] as $name => $amount)
                            <div class="flex items-center gap-1 rounded bg-[#f6f5f1] p-0.5">
                                <span class="flex size-3.5 items-center justify-center rounded-sm bg-[#fdbf2d] text-[0.45rem] font-black text-[#151515]">{{ $amount }}</span>
                                <span class="truncate text-[0.42rem] font-extrabold text-[#17191b]">{{ $name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between bg-white/10 text-white px-3 py-2 text-sm">
                <span>Suvestinė</span>
                @if($layout === Kitchen::LAYOUT_SUMMARY) <flux:icon.check-circle class="size-4" /> @endif
            </div>
        </div>
    </label>

</div>
