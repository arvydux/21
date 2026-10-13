@php
    /*
     * Kitchen display: oldest orders first. Tapping a preparing number marks it ready
     * (customers hear it and see it on the pickup screen); tapping a ready number moves
     * it back. Waiting time turns orange, then red, so late orders stand out. Each grid
     * picks the smallest column count at which all of its tiles fit on the screen.
     */
    $panel = 'flex min-w-0 flex-col overflow-hidden rounded-[1.75rem] bg-white shadow-[0_10px_30px_-12px_rgba(0,0,0,0.25)]';
    $header = 'flex shrink-0 items-center gap-4 bg-[#17191b] px-6 py-5';
    $chip = 'flex size-[clamp(3rem,4.2vw,4.5rem)] shrink-0 items-center justify-center rounded-2xl';
    $title = 'text-[clamp(1.75rem,2.6vw,2.75rem)] font-black uppercase leading-none tracking-wide text-white';
    $subtitle = 'mt-1.5 text-[clamp(0.85rem,1vw,1.1rem)] font-semibold text-white/65';
    $count = 'flex size-[clamp(2.75rem,3.6vw,3.75rem)] shrink-0 items-center justify-center rounded-full text-[clamp(1.25rem,1.7vw,1.9rem)] font-black tabular-nums';
    $tile = '@container relative aspect-[4/3] w-full touch-manipulation select-none rounded-3xl text-left transition-transform duration-100 active:scale-[0.97]';
    $fit = "{
        fit() {
            const grid = this.\$el;
            const style = getComputedStyle(grid);
            const gap = parseFloat(style.rowGap) || 16;
            const panel = grid.parentElement;
            const width = grid.clientWidth - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight);
            const height = panel.clientHeight - panel.querySelector('header').offsetHeight - parseFloat(style.paddingTop) - parseFloat(style.paddingBottom);
            const tiles = grid.querySelectorAll('[data-kitchen-tile]').length;
            let columns = Number(grid.dataset.minColumns);
            for (; columns < 12; columns++) {
                const tileHeight = (width - (columns - 1) * gap) / columns * 3 / 4;
                const rows = Math.ceil(tiles / columns);
                if (rows * tileHeight + Math.max(rows - 1, 0) * gap <= height) break;
            }
            grid.style.setProperty('--kitchen-cols', `repeat(\${columns}, minmax(0, 1fr))`);
        },
    }";
    $phoneIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>';
@endphp
<div wire:poll.5s data-kitchen-screen class="fixed inset-0 flex gap-5 overflow-hidden bg-[#f3f3f1] p-5 font-nunito antialiased">
    {{-- Gaminami --}}
    <section data-kitchen-preparing class="{{ $panel }} flex-[3]">
        <header class="{{ $header }}">
            <span class="{{ $chip }} bg-[#fdbf2d] text-[#17191b]">
                <svg class="size-[52%]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17 21a1 1 0 0 0 1-1v-5.35c0-.457.316-.844.727-1.041a4 4 0 0 0-2.134-7.589 5 5 0 0 0-9.186 0 4 4 0 0 0-2.134 7.588c.411.198.727.585.727 1.041V20a1 1 0 0 0 1 1Z"/><path d="M6 17h12"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <h2 class="{{ $title }}">Gaminami</h2>
                <p class="{{ $subtitle }}">Bakstelėkite numerį, kai užsakymas paruoštas</p>
            </div>
            <span class="{{ $count }} bg-white text-[#17191b]">{{ $preparingOrders->count() }}</span>
        </header>

        @if($preparingOrders->isEmpty())
            <div class="flex flex-1 items-center justify-center p-8 text-center text-[clamp(1.25rem,1.8vw,2rem)] font-bold text-stone-400">Visi užsakymai paruošti</div>
        @else
            <div x-data="{{ $fit }}" x-init="fit(); new MutationObserver(() => fit()).observe($el, { childList: true })" x-on:resize.window.debounce.100ms="fit()" wire:ignore.self
                 data-min-columns="3" style="--kitchen-cols: repeat(3, minmax(0, 1fr))"
                 class="grid grid-cols-(--kitchen-cols) auto-rows-fr content-start gap-4 p-6">
                @foreach($preparingOrders as $order)
                    @php
                        $minutes = (int) $order->minutes_since_created;
                        $lateness = match (true) {
                            $minutes >= \App\Livewire\Kitchen::VERY_LATE_AFTER_MINUTES => 'very-late',
                            $minutes >= \App\Livewire\Kitchen::LATE_AFTER_MINUTES => 'late',
                            default => 'on-time',
                        };
                    @endphp
                    <button type="button" wire:key="preparing-{{ $order->id }}" data-kitchen-tile data-lateness="{{ $lateness }}"
                            wire:click="makeOrderReady('{{ $order->number }}')" title="Pažymėti paruoštu"
                            @class([
                                $tile,
                                'bg-[#fdbf2d] text-[#151515]',
                                'ring-[5px] ring-red-600 ring-offset-2' => $lateness === 'very-late',
                            ])>
                        <span class="absolute inset-0 flex flex-col items-center justify-between p-[6cqw]">
                            <span class="flex w-full items-center justify-between">
                                @if($order->by_phone)
                                    <span class="flex items-center gap-[1.5cqw] rounded-full bg-[#151515] px-[3.5cqw] py-[1.2cqw] text-[4.2cqw] font-extrabold uppercase tracking-wider text-[#fdbf2d]">
                                        <svg class="size-[4.6cqw]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">{!! $phoneIcon !!}</svg>
                                        Telefonu
                                    </span>
                                @else
                                    <span class="text-[4.2cqw] font-extrabold uppercase tracking-[0.2em] text-[#151515]/55">Užsakymas</span>
                                @endif
                                <span class="flex size-[9cqw] items-center justify-center rounded-full bg-[#151515]/10 text-[#151515]/60" aria-hidden="true">
                                    <svg class="size-[60%]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </span>
                            <span class="text-[30cqw] font-black leading-none tabular-nums">{{ $order->number }}</span>
                            <span @class([
                                'flex items-center gap-[1.8cqw] rounded-full px-[4.5cqw] py-[1.6cqw] text-[5.5cqw] font-black tabular-nums',
                                'bg-[#151515] text-white' => $lateness === 'on-time',
                                'bg-orange-600 text-white' => $lateness === 'late',
                                'bg-red-600 text-white' => $lateness === 'very-late',
                            ])>
                                <svg class="size-[5.5cqw]" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                {{ $minutes }} min.
                            </span>
                        </span>
                    </button>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Paruošti --}}
    <section data-kitchen-ready class="{{ $panel }} flex-[2]">
        <header class="{{ $header }}">
            <span class="{{ $chip }} bg-[#169147] text-white">
                <svg class="size-[52%]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <h2 class="{{ $title }}">Paruošti</h2>
                <p class="{{ $subtitle }}">Bakstelėkite, jei pažymėjote per klaidą</p>
            </div>
            <span class="{{ $count }} bg-[#169147] text-white">{{ $readyOrders->count() }}</span>
        </header>

        @if($readyOrders->isEmpty())
            <div class="flex flex-1 items-center justify-center p-8 text-center text-[clamp(1.25rem,1.8vw,2rem)] font-bold text-stone-400">Paruoštų užsakymų nėra</div>
        @else
            <div x-data="{{ $fit }}" x-init="fit(); new MutationObserver(() => fit()).observe($el, { childList: true })" x-on:resize.window.debounce.100ms="fit()" wire:ignore.self
                 data-min-columns="2" style="--kitchen-cols: repeat(2, minmax(0, 1fr))"
                 class="grid grid-cols-(--kitchen-cols) auto-rows-fr content-start gap-4 p-6">
                @foreach($readyOrders as $order)
                    <button type="button" wire:key="ready-{{ $order->id }}" data-kitchen-tile
                            wire:click="makeOrderNotReady('{{ $order->number }}')" title="Grąžinti į gaminamus"
                            class="{{ $tile }} bg-[#169147] text-white">
                        <span class="absolute inset-0 flex flex-col items-center justify-between p-[6cqw]">
                            <span class="flex w-full items-center justify-between">
                                <span class="text-[4.2cqw] font-extrabold uppercase tracking-[0.2em] text-white/70">Užsakymas</span>
                                <span class="flex size-[9cqw] items-center justify-center rounded-full bg-black/20 text-white/80" aria-hidden="true">
                                    <svg class="size-[58%]" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 0 11H11"/></svg>
                                </span>
                            </span>
                            <span class="text-[30cqw] font-black leading-none tabular-nums">{{ $order->number }}</span>
                            <span class="flex items-center gap-[1.5cqw] rounded-full bg-white px-[4.5cqw] py-[1.6cqw] text-[4.6cqw] font-extrabold uppercase tracking-wider text-[#169147]">
                                @if($order->by_phone)
                                    <svg class="size-[4.6cqw]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">{!! $phoneIcon !!}</svg>
                                @endif
                                Paruošta
                            </span>
                        </span>
                    </button>
                @endforeach
            </div>
        @endif
    </section>
</div>
