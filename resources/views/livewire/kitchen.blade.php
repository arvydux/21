@php
    /*
     * Kitchen display: oldest orders first. Tapping a preparing number marks it ready
     * (customers hear it and see it on the pickup screen); tapping a ready number moves
     * it back. Waiting time turns orange, then red, so late orders stand out. Each grid
     * picks the smallest column count at which all of its tiles fit on the screen.
     * The summary layout (chosen in the settings) puts the ready numbers in a strip under
     * the preparing ones and sums up on the right what all preparing orders still need.
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
            const siblings = [...panel.children].filter((child) => child !== grid).reduce((sum, child) => sum + child.offsetHeight, 0);
            const height = panel.clientHeight - siblings - parseFloat(style.paddingTop) - parseFloat(style.paddingBottom);
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

        @if($layout === \App\Livewire\Kitchen::LAYOUT_SUMMARY)
            <footer data-kitchen-ready class="mt-auto flex shrink-0 items-center gap-4 border-t border-stone-200 bg-[#f7f7f5] px-6 py-4">
                <div class="flex shrink-0 items-center gap-3">
                    <span class="flex size-[clamp(2.25rem,2.8vw,3rem)] items-center justify-center rounded-xl bg-[#169147] text-white">
                        <svg class="size-[55%]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <div class="leading-tight">
                        <p class="text-[clamp(1rem,1.3vw,1.4rem)] font-black uppercase tracking-wide text-[#17191b]">Paruošti <span class="tabular-nums text-[#169147]">{{ $readyOrders->count() }}</span></p>
                        <p class="text-[clamp(0.7rem,0.8vw,0.85rem)] font-semibold text-stone-500">Bakstelėkite, jei per klaidą</p>
                    </div>
                </div>

                @if($readyOrders->isEmpty())
                    <p class="text-[clamp(0.95rem,1.1vw,1.2rem)] font-bold text-stone-400">Paruoštų užsakymų nėra</p>
                @else
                    <div class="flex min-w-0 flex-1 gap-2.5 overflow-x-auto py-1">
                        @foreach($readyOrders as $order)
                            <button type="button" wire:key="ready-{{ $order->id }}"
                                    wire:click="makeOrderNotReady('{{ $order->number }}')" title="Grąžinti į gaminamus"
                                    class="flex h-[clamp(3rem,4.2vw,4.25rem)] shrink-0 touch-manipulation select-none items-center gap-2 rounded-2xl bg-[#169147] px-[clamp(0.9rem,1.2vw,1.25rem)] text-white transition-transform duration-100 active:scale-95">
                                @if($order->by_phone)
                                    <svg class="size-[clamp(1rem,1.2vw,1.25rem)] text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">{!! $phoneIcon !!}</svg>
                                @endif
                                <span class="text-[clamp(1.5rem,2.1vw,2.25rem)] font-black leading-none tabular-nums">{{ $order->number }}</span>
                                <svg class="size-[clamp(1rem,1.2vw,1.25rem)] text-white/60" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 0 11H11"/></svg>
                            </button>
                        @endforeach
                    </div>
                @endif
            </footer>
        @endif
    </section>

    @if($layout === \App\Livewire\Kitchen::LAYOUT_SUMMARY)
    {{-- Gaminti: sum-up of every preparing order --}}
    <section data-kitchen-summary class="{{ $panel }} flex-[2]">
        <header class="{{ $header }}">
            <span class="{{ $chip }} bg-white text-[#17191b]">
                <svg class="size-[50%]" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <h2 class="{{ $title }}">Gaminti</h2>
                <p class="{{ $subtitle }}">Iš visų gaminamų užsakymų</p>
            </div>
            <span class="{{ $count }} bg-[#fdbf2d] text-[#17191b]">{{ collect($summary)->sum('amount') }}</span>
        </header>

        @if($summary === [])
            <div class="flex flex-1 flex-col items-center justify-center gap-3 p-8 text-center text-[clamp(1.25rem,1.8vw,2rem)] font-bold text-stone-400">
                <svg class="size-[clamp(3rem,4vw,4.5rem)] text-stone-300" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17 21a1 1 0 0 0 1-1v-5.35c0-.457.316-.844.727-1.041a4 4 0 0 0-2.134-7.589 5 5 0 0 0-9.186 0 4 4 0 0 0-2.134 7.588c.411.198.727.585.727 1.041V20a1 1 0 0 0 1 1Z"/><path d="M6 17h12"/></svg>
                Nėra ką gaminti
            </div>
        @else
            <div class="flex min-h-0 flex-1 flex-col gap-2.5 overflow-y-auto p-4">
                @foreach($summary as $row)
                    <article wire:key="summary-{{ md5($row['name']) }}" data-kitchen-summary-row
                             class="flex items-center gap-4 rounded-2xl bg-[#f6f5f1] p-2.5 pr-5">
                        <span class="flex h-[clamp(2.75rem,3.8vw,4.25rem)] min-w-[clamp(2.75rem,3.8vw,4.25rem)] shrink-0 items-center justify-center rounded-xl bg-[#fdbf2d] px-2 text-[clamp(1.75rem,2.6vw,2.75rem)] font-black leading-none tabular-nums text-[#17191b]">{{ $row['amount'] }}</span>
                        <div class="flex min-w-0 flex-1 flex-col gap-1.5">
                            <p class="text-[clamp(1.15rem,1.6vw,1.75rem)] font-extrabold leading-tight text-[#17191b]">{{ $row['name'] }}</p>
                            @if($row['toppings'] !== [])
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($row['toppings'] as $topping => $amount)
                                        <span class="flex items-center gap-1.5 rounded-full bg-white py-1 pl-3 pr-1 text-[clamp(0.85rem,1vw,1.1rem)] font-bold text-stone-700 ring-1 ring-black/10">
                                            {{ $topping }}
                                            <span class="rounded-full bg-[#17191b] px-2 py-0.5 text-[0.85em] font-black tabular-nums text-white">×{{ $amount }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach

            </div>

            @if($wishes !== [])
                <div data-kitchen-wishes class="flex max-h-[40%] shrink-0 flex-col gap-2 overflow-y-auto border-t-2 border-red-200 bg-red-50 px-5 py-4">
                    <p class="flex items-center gap-2 text-[clamp(0.9rem,1.1vw,1.2rem)] font-black uppercase tracking-wider text-red-700">
                        <svg class="size-[1.2em]" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                        Pastabos
                    </p>
                    @foreach($wishes as $wish)
                        <p class="flex items-start gap-2.5 text-[clamp(0.95rem,1.15vw,1.25rem)] font-bold text-red-950">
                            <span class="shrink-0 rounded-lg bg-red-600 px-2 py-0.5 font-black tabular-nums text-white">{{ $wish['number'] }}</span>
                            <span class="min-w-0">{{ $wish['comments'] }}</span>
                        </p>
                    @endforeach
                </div>
            @endif
        @endif
    </section>
    @else
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
    @endif
</div>
