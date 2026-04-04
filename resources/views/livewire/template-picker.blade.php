<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $template = 'green';

    public function mount(): void
    {
        $this->template = cache('pos_template', 'green');
    }

    public function updatedTemplate(): void
    {
        cache()->forever('pos_template', $this->template);
    }
}; ?>

<div class="grid grid-cols-2 gap-4">

    {{-- Original --}}
    <label class="cursor-pointer">
        <input type="radio" wire:model.live="template" value="original" class="sr-only">
        <div class="rounded-xl overflow-hidden transition-all
            {{ $template === 'original' ? 'ring-2 ring-white scale-105' : 'ring-1 ring-white/30 opacity-60' }}">
            <div class="w-full h-36 overflow-hidden flex gap-0 p-2" style="background: linear-gradient(to top right, #4d7c0f, #059669, #0f766e);">
                <div class="flex-1 flex flex-col gap-1 p-1">
                    <div style="font-size:0.45rem; font-weight:600; color:rgba(255,255,255,0.75);">Gaminami:</div>
                    <div class="grid gap-1" style="grid-template-columns:repeat(3,1fr);">
                        @foreach([1,2,3,4,5,6] as $n)
                        <div class="flex items-center justify-center rounded" style="aspect-ratio:16/9; background:linear-gradient(135deg,#f7971e,#ffd200); box-shadow:0 2px 6px rgba(255,210,0,0.4);">
                            <span style="font-size:0.45rem; font-weight:900; color:#1a0a00;">{{ $n }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex-1 flex flex-col gap-1 p-1">
                    <div style="font-size:0.45rem; font-weight:600; color:rgba(255,255,255,0.75);">Paruošti:</div>
                    <div class="grid gap-1" style="grid-template-columns:repeat(3,1fr);">
                        @foreach([1,2,3] as $n)
                        <div class="flex items-center justify-center rounded" style="aspect-ratio:16/9; background:rgba(255,255,255,0.13); border:1px solid rgba(255,255,255,0.28); box-shadow:0 2px 8px rgba(48,207,208,0.2);">
                            <span style="font-size:0.45rem; font-weight:900; color:#fff;">{{ $n }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between bg-white/10 text-white px-3 py-2 text-sm">
                <span>Original</span>
                @if($template === 'original') <flux:icon.check-circle class="size-4" /> @endif
            </div>
        </div>
    </label>

    {{-- Green --}}
    <label class="cursor-pointer">
        <input type="radio" wire:model.live="template" value="green" class="sr-only">
        <div class="rounded-xl overflow-hidden transition-all
            {{ $template === 'green' ? 'ring-2 ring-white scale-105' : 'ring-1 ring-white/30 opacity-60' }}">
            <div class="w-full h-36 overflow-hidden flex" style="background: radial-gradient(circle at 50% 50%, rgba(226,232,240,0.15) 0%, rgba(226,232,240,0.05) 35%, transparent 55%), #000;">
                <div class="flex-1 flex flex-col gap-1 p-2">
                    <div style="font-size:0.45rem; font-weight:800; color:#e2e8f0; letter-spacing:0.04em;">GAMINAMI</div>
                    <div class="grid gap-1" style="grid-template-columns:repeat(3,1fr);">
                        @foreach([1,2,3,4,5,6] as $n)
                        <div class="flex items-center justify-center rounded-lg" style="aspect-ratio:4/3; background:#f5a520; box-shadow:0 2px 6px rgba(0,0,0,0.3);">
                            <span style="font-size:0.5rem; font-weight:900; color:#fff;">{{ $n }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex-1 flex flex-col gap-1 p-2">
                    <div style="font-size:0.45rem; font-weight:800; color:#e2e8f0; letter-spacing:0.04em;">PARUOŠTI</div>
                    <div class="grid gap-1" style="grid-template-columns:repeat(3,1fr);">
                        @foreach([1,2,3,4] as $n)
                        <div class="flex items-center justify-center rounded-lg" style="aspect-ratio:4/3; background:#48a855; box-shadow:0 2px 6px rgba(0,0,0,0.3);">
                            <span style="font-size:0.5rem; font-weight:900; color:#fff;">{{ $n }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between bg-white/10 text-white px-3 py-2 text-sm">
                <span>Green</span>
                @if($template === 'green') <flux:icon.check-circle class="size-4" /> @endif
            </div>
        </div>
    </label>

    {{-- Light --}}
    <label class="cursor-pointer">
        <input type="radio" wire:model.live="template" value="light" class="sr-only">
        <div class="rounded-xl overflow-hidden transition-all
            {{ $template === 'light' ? 'ring-2 ring-white scale-105' : 'ring-1 ring-white/30 opacity-60' }}">
            <div class="w-full h-36 overflow-hidden flex" style="background:#f1f5f9;">
                <div class="flex-1 flex flex-col gap-1 p-2">
                    <div style="font-size:0.45rem; font-weight:800; color:#0f172a; letter-spacing:0.04em;">GAMINAMI</div>
                    <div class="grid gap-1" style="grid-template-columns:repeat(3,1fr);">
                        @foreach([1,2,3,4,5,6] as $n)
                        <div class="flex items-center justify-center rounded-lg" style="aspect-ratio:4/3; background:#f5a520; box-shadow:0 1px 4px rgba(0,0,0,0.1);">
                            <span style="font-size:0.5rem; font-weight:900; color:#fff;">{{ $n }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex-1 flex flex-col gap-1 p-2">
                    <div style="font-size:0.45rem; font-weight:800; color:#0f172a; letter-spacing:0.04em;">PARUOŠTI</div>
                    <div class="grid gap-1" style="grid-template-columns:repeat(3,1fr);">
                        @foreach([1,2,3,4] as $n)
                        <div class="flex items-center justify-center rounded-lg" style="aspect-ratio:4/3; background:#48a855; box-shadow:0 1px 4px rgba(0,0,0,0.1);">
                            <span style="font-size:0.5rem; font-weight:900; color:#fff;">{{ $n }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between bg-white/10 text-white px-3 py-2 text-sm">
                <span>Light</span>
                @if($template === 'light') <flux:icon.check-circle class="size-4" /> @endif
            </div>
        </div>
    </label>

    {{-- Modern --}}
    <label class="cursor-pointer">
        <input type="radio" wire:model.live="template" value="modern" class="sr-only">
        <div class="rounded-xl overflow-hidden transition-all
            {{ $template === 'modern' ? 'ring-2 ring-white scale-105' : 'ring-1 ring-white/30 opacity-60' }}">
            <div class="w-full h-36 overflow-hidden flex gap-0" style="background:#eef0f5;">
                <div class="flex-1 flex flex-col gap-1 p-2">
                    <div style="font-size:0.45rem; font-weight:800; color:#0f172a; letter-spacing:0.02em;">GAMINAMI</div>
                    <div class="grid gap-1" style="grid-template-columns:repeat(3,1fr);">
                        @foreach([1,2,3,4,5,6] as $n)
                        <div class="flex items-center justify-center rounded overflow-hidden" style="aspect-ratio:4/3; background:#fff; border-left:2px solid #f5622d; box-shadow:0 1px 3px rgba(0,0,0,0.07);">
                            <span style="font-size:0.5rem; font-weight:900; color:#0f172a;">{{ $n }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="self-stretch w-px my-2" style="background:#d1d5db;"></div>
                <div class="flex-1 flex flex-col gap-1 p-2">
                    <div style="font-size:0.45rem; font-weight:800; color:#0f172a; letter-spacing:0.02em;">PARUOŠTI</div>
                    <div class="grid gap-1" style="grid-template-columns:repeat(3,1fr);">
                        @foreach([1,2,3,4] as $n)
                        <div class="flex items-center justify-center rounded-lg" style="aspect-ratio:4/3; background:#3da85a; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                            <span style="font-size:0.5rem; font-weight:900; color:#fff;">{{ $n }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between bg-white/10 text-white px-3 py-2 text-sm">
                <span>Modern</span>
                @if($template === 'modern') <flux:icon.check-circle class="size-4" /> @endif
            </div>
        </div>
    </label>

</div>
