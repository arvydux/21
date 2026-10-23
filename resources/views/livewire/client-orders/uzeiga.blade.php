@php
    /*
     * Customer pickup screen in the colours of the online board and the mobile menu:
     * cream page, white cards, sand "Gaminami" tiles and deep green "Paruošti" tiles.
     * Tiles shrink to fit the screen the same way as in the Kasa template (see fit() below).
     */
    $tileCount = max($gaminamiOrders->count(), $paruostiOrders->count());
    $initialColumns = match (true) {
        $tileCount > 20 => 5,
        $tileCount > 12 => 4,
        default => 3,
    };

    $panel = 'flex min-w-0 flex-1 flex-col overflow-hidden rounded-[1.75rem] border border-[#E8E0D2] bg-white';
    $header = 'flex shrink-0 items-center gap-3 px-7 pt-6 pb-2';
    $dot = 'size-[clamp(0.8rem,1vw,1.1rem)] shrink-0 rounded-full';
    $title = 'flex-1 text-[clamp(1.4rem,2vw,2.2rem)] font-extrabold uppercase tracking-[0.08em]';
    $count = 'flex h-[clamp(2.4rem,3.2vw,3.4rem)] min-w-[clamp(2.4rem,3.2vw,3.4rem)] items-center justify-center rounded-full px-3 text-[clamp(1.1rem,1.6vw,1.7rem)] font-extrabold tabular-nums';
    $empty = 'flex flex-1 items-center justify-center text-center text-[clamp(1rem,1.4vw,1.5rem)] font-semibold text-[#7A7064]';
    $tile = '@container relative flex aspect-[4/3] items-center justify-center rounded-[1.4rem]';
    $number = 'text-[38cqw] font-extrabold leading-none tabular-nums';
    $phone = '<svg class="absolute right-[7cqw] top-[7cqw] size-[9cqw] opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>';
@endphp
<div data-pickup-screen data-pickup-uzeiga wire:ignore.self
     style="--pickup-cols: repeat({{ $initialColumns }}, minmax(0, 1fr))"
     x-data="{
         fit() {
             const grids = [...this.$root.querySelectorAll('[data-pickup-grid]')];
             if (grids.length === 0) return;
             const gap = parseFloat(getComputedStyle(grids[0]).rowGap) || 16;
             const areas = grids.map((grid) => {
                 const style = getComputedStyle(grid);
                 const panel = grid.parentElement;
                 return {
                     width: grid.clientWidth - parseFloat(style.paddingLeft) - parseFloat(style.paddingRight),
                     height: panel.clientHeight - panel.querySelector('header').offsetHeight - parseFloat(style.paddingTop) - parseFloat(style.paddingBottom),
                     tiles: grid.querySelectorAll('[data-pickup-tile]').length,
                 };
             });
             let columns = 3;
             for (; columns < 12; columns++) {
                 const fits = areas.every((area) => {
                     const tileHeight = (area.width - (columns - 1) * gap) / columns * 3 / 4;
                     const rows = Math.ceil(area.tiles / columns);
                     return rows * tileHeight + Math.max(rows - 1, 0) * gap <= area.height;
                 });
                 if (fits) break;
             }
             this.$root.style.setProperty('--pickup-cols', `repeat(${columns}, minmax(0, 1fr))`);
             this.$root.dataset.columns = columns;
         },
     }"
     x-init="fit(); new MutationObserver(() => fit()).observe($el, { childList: true, subtree: true })"
     x-on:resize.window.debounce.100ms="fit()"
     class="fixed inset-0 flex flex-col gap-5 overflow-hidden bg-[#FBF7F0] p-6 font-['Manrope',system-ui,sans-serif] text-[#2A2620] antialiased">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Playfair+Display:wght@700&display=swap">

    <header class="shrink-0 text-center">
        <h1 class="font-['Playfair_Display',Georgia,serif] text-[clamp(2rem,3.4vw,3.6rem)] leading-tight font-bold text-[#14664A]">Prusakovų užeiga</h1>
        <p class="text-[clamp(0.9rem,1.2vw,1.3rem)] font-semibold text-[#7A7064]">Užsakymų būsena</p>
    </header>

    <div class="flex min-h-0 flex-1 gap-5">
        {{-- Gaminami --}}
        <section data-pickup-preparing class="{{ $panel }}">
            <header class="{{ $header }} text-[#B5761B]">
                <span class="{{ $dot }} bg-[#B5761B]"></span>
                <h2 class="{{ $title }}">Gaminami</h2>
                <span class="{{ $count }} bg-[#F3ECDF] text-[#2A2620]">{{ $gaminamiOrders->count() }}</span>
            </header>

            @if($gaminamiOrders->isEmpty())
                <div class="{{ $empty }}">Šiuo metu nieko negaminame</div>
            @else
                <div data-pickup-grid class="grid grid-cols-(--pickup-cols) auto-rows-fr content-start gap-4 p-7 pt-4">
                    @foreach($gaminamiOrders as $order)
                        <div wire:key="preparing-{{ $order->id }}" data-pickup-tile
                             class="{{ $tile }} bg-[#F3ECDF] text-[#2A2620]">
                            @if($order->by_phone)
                                {!! $phone !!}
                            @endif
                            <span class="{{ $number }}">{{ $order->number }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Paruošti --}}
        <section data-pickup-ready class="{{ $panel }}">
            <header class="{{ $header }} text-[#14664A]">
                <span class="{{ $dot }} bg-[#14664A]"></span>
                <h2 class="{{ $title }}">Paruošti — atsiimkite</h2>
                <span class="{{ $count }} bg-[#14664A] text-white">{{ $paruostiOrders->count() }}</span>
            </header>

            @if($paruostiOrders->isEmpty())
                <div class="{{ $empty }}">Paruoštų užsakymų dar nėra</div>
            @else
                <div data-pickup-grid class="grid grid-cols-(--pickup-cols) auto-rows-fr content-start gap-4 p-7 pt-4">
                    @foreach($paruostiOrders as $order)
                        <div wire:key="ready-{{ $order->id }}" data-order-number="{{ $order->number }}" data-pickup-tile
                             class="{{ $tile }} bg-[#14664A] text-white">
                            @if($order->by_phone)
                                {!! $phone !!}
                            @endif
                            <span class="{{ $number }}">{{ $order->number }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
