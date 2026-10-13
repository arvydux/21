@php
    /*
     * Customer pickup screen: light page, white cards with a dark header, flat amber
     * "Gaminami" tiles with black numbers and flat green "Paruošti" tiles with white ones. The newest ready number gets
     * a wide card one row high. A number that has just become ready pulses for its first
     * 10 seconds (the negative delay keeps the animation in step across the 5 s polls).
     * Both panels share the header height and the column count, so rows line up.
     */
    $readyOrders = $paruostiOrders->values();
    $newestReady = $readyOrders->first();
    $otherReady = $readyOrders->slice(1);

    $tileCount = max($gaminamiOrders->count(), $otherReady->count() + 3);
    $columns = match (true) {
        $tileCount > 20 => 'grid-cols-5',
        $tileCount > 12 => 'grid-cols-4',
        default => 'grid-cols-3',
    };

    $panel = 'flex min-w-0 flex-1 flex-col overflow-hidden rounded-[1.75rem] bg-white shadow-[0_10px_30px_-12px_rgba(0,0,0,0.25)]';

    $flash = function ($order): array {
        $seconds = $order->updated_at ? abs($order->updated_at->diffInSeconds(now())) : null;

        return $seconds !== null && $seconds < 10
            ? ['class' => 'ready-flash z-10', 'style' => 'animation-delay: -'.number_format($seconds, 2, '.', '').'s']
            : ['class' => '', 'style' => ''];
    };

    $icons = [
        'chef' => '<path d="M17 21a1 1 0 0 0 1-1v-5.35c0-.457.316-.844.727-1.041a4 4 0 0 0-2.134-7.589 5 5 0 0 0-9.186 0 4 4 0 0 0-2.134 7.588c.411.198.727.585.727 1.041V20a1 1 0 0 0 1 1Z"/><path d="M6 17h12"/>',
        'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'checks' => '<path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'pickup' => '<path d="M7 7h10v10"/><path d="M7 17 17 7"/>',
        'phone' => '<path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>',
    ];
    $icon = fn (string $name, string $class) => '<svg class="'.$class.'" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">'.$icons[$name].'</svg>';

    $header = 'box-content flex h-[clamp(3.5rem,5vw,5rem)] shrink-0 items-center gap-4 bg-[#17191b] px-6 py-5';
    $chip = 'flex aspect-square h-full shrink-0 items-center justify-center rounded-2xl';
    $title = 'text-[clamp(1.75rem,2.6vw,2.75rem)] font-black uppercase leading-none tracking-wide';
    $subtitle = 'mt-1.5 text-[clamp(0.85rem,1vw,1.1rem)] font-semibold text-white/70';
    $count = 'flex aspect-square h-[75%] shrink-0 items-center justify-center rounded-full text-[clamp(1.25rem,1.7vw,1.9rem)] font-black tabular-nums';
    $tile = '@container relative aspect-[4/3] rounded-3xl';
    $tileBody = 'absolute inset-0 flex flex-col items-center justify-between p-[6cqw]';
    $label = 'text-[4.2cqw] font-extrabold uppercase tracking-[0.2em]';
    $number = 'text-[30cqw] font-black leading-none tabular-nums';
    $pill = 'flex items-center gap-[2cqw] rounded-full px-[5cqw] py-[1.8cqw] text-[4.4cqw] font-extrabold uppercase tracking-wider';
    $corner = 'absolute right-[6cqw] top-[5.2cqw] z-10 flex items-center gap-[1.5cqw]';
@endphp
<div data-pickup-screen class="fixed inset-0 flex gap-5 overflow-hidden bg-[#f3f3f1] p-5 font-nunito text-white antialiased">
    {{-- Gaminami --}}
    <section data-pickup-preparing class="{{ $panel }}">
        <header class="{{ $header }}">
            <span class="{{ $chip }} bg-[#fdbf2d] text-[#17191b]">
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
            <div class="grid {{ $columns }} auto-rows-fr content-start gap-4 p-6">
                @foreach($gaminamiOrders as $order)
                    <div wire:key="preparing-{{ $order->id }}"
                         class="{{ $tile }} bg-[#fdbf2d] text-[#151515]">
                        <span class="{{ $corner }} text-[#151515]/50">
                            @if($order->by_phone)
                                {!! $icon('phone', 'size-[5.5cqw]') !!}
                            @endif
                            {!! $icon('clock', 'size-[5.5cqw]') !!}
                        </span>
                        <div class="{{ $tileBody }}">
                            <span class="{{ $label }} text-[#151515]/55">Užsakymas</span>
                            <span class="{{ $number }}">{{ $order->number }}</span>
                            <span class="{{ $pill }} bg-[#151515] text-[#fdbf2d]">
                                {!! $icon('chef', 'size-[5cqw]') !!}
                                Gaminamas
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Paruošti --}}
    <section data-pickup-ready class="{{ $panel }}">
        <header class="{{ $header }}">
            <span class="{{ $chip }} bg-[#169147] text-white">
                {!! $icon('check', 'size-[50%]') !!}
            </span>
            <div class="min-w-0 flex-1">
                <h2 class="{{ $title }}">Paruošti</h2>
                <p class="{{ $subtitle }}">Atsiimkite prie kasos</p>
            </div>
            <span class="{{ $count }} bg-[#169147] text-white">{{ $paruostiOrders->count() }}</span>
        </header>

        @if($newestReady === null)
            <div class="flex flex-1 items-center justify-center text-center text-[clamp(1rem,1.4vw,1.5rem)] font-bold text-stone-400">Paruoštų užsakymų dar nėra</div>
        @else
            <div class="grid {{ $columns }} auto-rows-fr content-start gap-4 p-6">
                @php $newestFlash = $flash($newestReady); @endphp
                <div wire:key="ready-{{ $newestReady->id }}" data-order-number="{{ $newestReady->number }}" data-newest-ready
                     class="@container relative col-span-full {{ $otherReady->isEmpty() ? 'aspect-[4/1]' : 'h-full' }} rounded-3xl bg-[#169147] text-white {{ $newestFlash['class'] }}"
                     @if($newestFlash['style']) style="{{ $newestFlash['style'] }}" @endif>
                    <div class="absolute inset-0 flex items-center justify-between gap-[3cqw] px-[3.5cqw]">
                    <div class="flex h-full flex-col items-start justify-center gap-[1.2cqw]">
                        <span class="flex items-center gap-[0.8cqw] text-[1.35cqw] font-extrabold uppercase tracking-[0.2em] text-white/80">
                            <span class="size-[0.75cqw] rounded-full bg-[#fdbf2d] motion-safe:animate-pulse"></span>
                            Paruoštas atsiimti
                        </span>
                        <span class="text-[11cqw] font-black leading-[0.85] tabular-nums">{{ $newestReady->number }}</span>
                        <span class="flex items-center gap-[0.8cqw] rounded-full bg-white px-[1.8cqw] py-[0.6cqw] text-[1.6cqw] font-extrabold uppercase tracking-wider text-[#169147]">
                            {!! $icon('pickup', 'size-[1.8cqw]') !!}
                            Atsiėmimui
                        </span>
                    </div>
                    <div class="flex items-center gap-[2cqw]">
                        @if($newestReady->by_phone)
                            <span class="flex size-[5cqw] items-center justify-center rounded-full bg-white text-[#151515]">{!! $icon('phone', 'size-[52%]') !!}</span>
                        @endif
                        <span class="flex size-[8.5cqw] items-center justify-center rounded-full bg-black/20 text-white">
                            {!! $icon('check', 'size-[50%]') !!}
                        </span>
                    </div>
                    </div>
                </div>

                @foreach($otherReady as $order)
                    @php $orderFlash = $flash($order); @endphp
                    <div wire:key="ready-{{ $order->id }}" data-order-number="{{ $order->number }}"
                         class="{{ $tile }} bg-[#169147] text-white {{ $orderFlash['class'] }}"
                         @if($orderFlash['style']) style="{{ $orderFlash['style'] }}" @endif>
                        <span class="{{ $corner }}">
                            @if($order->by_phone)
                                <span class="flex size-[7cqw] items-center justify-center rounded-full bg-white text-[#151515]">{!! $icon('phone', 'size-[55%]') !!}</span>
                            @endif
                            <span class="flex size-[7cqw] items-center justify-center rounded-full bg-black/20 text-white">
                                {!! $icon('check', 'size-[60%]') !!}
                            </span>
                        </span>
                        <div class="{{ $tileBody }}">
                            <span class="{{ $label }} text-white/70">Užsakymas</span>
                            <span class="{{ $number }}">{{ $order->number }}</span>
                            <span class="{{ $pill }} bg-white text-[#169147]">
                                {!! $icon('pickup', 'size-[5cqw]') !!}
                                Atsiėmimui
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
