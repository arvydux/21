@php
    /*
     * Customer pickup screen: light page, white cards with a dark header, amber
     * "Gaminami" tiles with black numbers and green "Paruošti" tiles with white ones.
     * All tiles are the same size; the browser shrinks them when there are too many orders
     * to fit on the screen (see fit() below). The "kasa-gradient" template renders this
     * view with $gradient = true for shaded tiles instead of flat ones.
     */
    $gradient ??= false;
    $amber = $gradient
        ? ['fill' => 'bg-linear-to-br from-[#ffd35c] to-[#eba521] ring-1 ring-[#ffe08a]/70', 'tile' => 'shadow-[0_10px_24px_-12px_rgba(140,90,10,0.55)]']
        : ['fill' => 'bg-[#fdbf2d]', 'tile' => ''];
    $green = $gradient
        ? ['fill' => 'bg-linear-to-br from-[#23805a] to-[#145a3e] ring-1 ring-[#3fbf86]/50', 'tile' => 'shadow-[0_10px_24px_-12px_rgba(6,60,40,0.6)]']
        : ['fill' => 'bg-[#169147]', 'tile' => ''];

    // First guess for the column count; the browser then picks the smallest count at
    // which every tile fits on the screen and keeps it up to date.
    $tileCount = max($gaminamiOrders->count(), $paruostiOrders->count());
    $initialColumns = match (true) {
        $tileCount > 20 => 5,
        $tileCount > 12 => 4,
        default => 3,
    };

    $panel = 'flex min-w-0 flex-1 flex-col overflow-hidden rounded-[1.75rem] bg-white shadow-[0_10px_30px_-12px_rgba(0,0,0,0.25)]';


    $icons = [
        'chef' => '<path d="M17 21a1 1 0 0 0 1-1v-5.35c0-.457.316-.844.727-1.041a4 4 0 0 0-2.134-7.589 5 5 0 0 0-9.186 0 4 4 0 0 0-2.134 7.588c.411.198.727.585.727 1.041V20a1 1 0 0 0 1 1Z"/><path d="M6 17h12"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'checks' => '<path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
    ];
    $icon = fn (string $name, string $class) => '<svg class="'.$class.'" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">'.$icons[$name].'</svg>';

    $header = 'box-content flex h-[clamp(3.5rem,5vw,5rem)] shrink-0 items-center gap-4 bg-[#17191b] px-6 py-5';
    $chip = 'flex aspect-square h-full shrink-0 items-center justify-center rounded-2xl';
    $title = 'text-[clamp(1.75rem,2.6vw,2.75rem)] font-black uppercase leading-none tracking-wide';
    $subtitle = 'mt-1.5 text-[clamp(0.85rem,1vw,1.1rem)] font-semibold text-white/70';
    $count = 'flex aspect-square h-[75%] shrink-0 items-center justify-center rounded-full text-[clamp(1.25rem,1.7vw,1.9rem)] font-black tabular-nums';
    $tile = '@container relative aspect-[4/3] rounded-3xl';
    $tileBody = 'absolute inset-0 flex items-center justify-center p-[6cqw]';
    $number = 'text-[46cqw] font-black leading-none tabular-nums';
    $corner = 'absolute right-[6cqw] top-[5.2cqw] z-10 flex items-center gap-[1.5cqw]';
@endphp
<div data-pickup-screen wire:ignore.self
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
     class="fixed inset-0 flex gap-5 overflow-hidden bg-[#f3f3f1] p-5 font-nunito text-white antialiased">
    {{-- Gaminami --}}
    <section data-pickup-preparing class="{{ $panel }}">
        <header class="{{ $header }}">
            <span class="{{ $chip }} {{ $amber['fill'] }} text-[#17191b]">
                {!! $icon('chef', 'size-[50%]') !!}
            </span>
            <div class="min-w-0 flex-1">
                <h2 class="{{ $title }}">Gaminami</h2>
                <p class="{{ $subtitle }}">Ruošiame jūsų užsakymą</p>
            </div>
            <span class="{{ $count }} bg-white text-[#17191b]">{{ $gaminamiOrders->count() }}</span>
        </header>

        @if($gaminamiOrders->isEmpty())
            <div class="flex flex-1 items-center justify-center text-center text-[clamp(1rem,1.4vw,1.5rem)] font-bold text-stone-400">Šiuo metu nieko negaminame</div>
        @else
            <div data-pickup-grid class="grid grid-cols-(--pickup-cols) auto-rows-fr content-start gap-4 p-6">
                @foreach($gaminamiOrders as $order)
                    <div wire:key="preparing-{{ $order->id }}" data-pickup-tile
                         class="{{ $tile }} {{ $amber['fill'] }} {{ $amber['tile'] }} text-[#151515]">
                        <span class="{{ $corner }} text-[#151515]/50">
                            {!! $icon('clock', 'size-[5.5cqw]') !!}
                        </span>
                        <div class="{{ $tileBody }}">
                            <span class="{{ $number }}">{{ $order->number }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Paruošti --}}
    <section data-pickup-ready class="{{ $panel }}">
        <header class="{{ $header }}">
            <span class="{{ $chip }} {{ $green['fill'] }} text-white">
                {!! $icon('check', 'size-[50%]') !!}
            </span>
            <div class="min-w-0 flex-1">
                <h2 class="{{ $title }}">Paruošti</h2>
                <p class="{{ $subtitle }}">Atsiimkite prie kasos</p>
            </div>
            <span class="{{ $count }} {{ $green['fill'] }} text-white">{{ $paruostiOrders->count() }}</span>
        </header>

        @if($paruostiOrders->isEmpty())
            <div class="flex flex-1 items-center justify-center text-center text-[clamp(1rem,1.4vw,1.5rem)] font-bold text-stone-400">Paruoštų užsakymų dar nėra</div>
        @else
            <div data-pickup-grid class="grid grid-cols-(--pickup-cols) auto-rows-fr content-start gap-4 p-6">
                @foreach($paruostiOrders as $order)
                    <div wire:key="ready-{{ $order->id }}" data-order-number="{{ $order->number }}" data-pickup-tile
                         class="{{ $tile }} {{ $green['fill'] }} {{ $green['tile'] }} text-white">
                        <span class="{{ $corner }}">
                            <span class="flex size-[7cqw] items-center justify-center rounded-full bg-black/20 text-white">
                                {!! $icon('check', 'size-[60%]') !!}
                            </span>
                        </span>
                        <div class="{{ $tileBody }}">
                            <span class="{{ $number }}">{{ $order->number }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
