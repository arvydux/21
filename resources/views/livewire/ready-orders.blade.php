@script
<script>
    $wire.on('play-sound', ({ numbers }) => {
        const queue = [];
        numbers.forEach(number => {
            queue.push('/ona/paruostas.mp3');
            queue.push(`/ona/${number}.mp3`);
        });

        function playNext() {
            if (queue.length === 0) return;
            const audio = new Audio(queue.shift());
            audio.addEventListener('ended', playNext);
            audio.addEventListener('error', playNext);
            audio.play();
        }

        playNext();
    });
</script>
@endscript

{{-- Ready numbers docked at the bottom of the middle cashier panel; tapping one marks it taken. --}}
<div wire:poll.5s="checkAndPlaySound" class="shrink-0 font-nunito">

    @if(! $soundEnabled)
        @teleport('#sound-activation-slot')
            <button wire:click="enableSound" onclick="new Audio('/14.mp3').play()" title="Aktyvuoti garsą"
                    class="relative flex h-11 shrink-0 touch-manipulation items-center gap-2 rounded-xl bg-amber-400 px-3 font-nunito text-sm font-extrabold text-emerald-950 shadow-lg shadow-amber-500/30 transition-all duration-100 hover:bg-amber-300 active:scale-[0.95]">
                <span class="absolute inset-0 rounded-xl bg-amber-400/70 motion-safe:animate-[ping_1.8s_cubic-bezier(0,0,0.2,1)_infinite]"></span>
                <svg class="relative size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M11 5L6 9H2v6h4l5 4V5z"></path>
                </svg>
                <span class="relative hidden whitespace-nowrap @[9.5rem]:inline">Aktyvuoti garsą</span>
            </button>
        @endteleport
    @endif

    @if($readyOrders->isNotEmpty())
        <div data-ready-strip class="flex flex-col gap-2.5 border-t border-white/10 bg-black/20 px-4 pb-4 pt-3">
            <div class="flex items-center gap-2.5">
                <span class="flex size-6 items-center justify-center rounded-full bg-[#169147] text-white">
                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
                <span class="kasa-meta uppercase tracking-widest text-white/75">Paruošti atsiimti</span>
                <span class="rounded-full bg-[#169147] px-2 py-0.5 text-xs font-black tabular-nums text-white">{{ $readyOrders->count() }}</span>
                <span class="kasa-meta ml-auto truncate text-white/40">Bakstelėkite numerį, kai užsakymas atsiimtas</span>
            </div>
            <div x-data="{ fadeEnd: false, check() { this.fadeEnd = this.$el.scrollLeft + this.$el.clientWidth < this.$el.scrollWidth - 4 } }"
                 x-init="check(); new ResizeObserver(() => check()).observe($el)" x-on:scroll.passive="check()"
                 x-bind:style="fadeEnd && '-webkit-mask-image: linear-gradient(to right, black 85%, transparent); mask-image: linear-gradient(to right, black 85%, transparent);'"
                 x-drag-scroll.x class="no-scrollbar -mx-1 flex touch-pan-x select-none gap-2.5 overflow-x-auto overscroll-contain px-1 py-1">
                @foreach($readyOrders as $order)
                    <button type="button" wire:key="ready-{{ $order->id }}" wire:click="makeOrderTaken('{{ $order->number }}')"
                            title="Pažymėti atsiimtu"
                            class="relative flex h-16 min-w-24 shrink-0 touch-manipulation items-center justify-center gap-2 rounded-2xl bg-[#169147] px-5 text-3xl font-black tabular-nums text-white shadow-lg shadow-black/20 ring-1 ring-white/20 transition-all duration-100 hover:brightness-110 active:scale-[0.95]">
                        {{ $order->number }}
                        @if($order->by_phone)
                            <svg class="size-4 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</div>
