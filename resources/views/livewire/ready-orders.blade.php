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

<div class="fixed bottom-0 left-0 w-full text-center p-2">

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

    <div wire:poll.5s="checkAndPlaySound" class="grid md:grid-cols-10 auto-rows-min gap-1">
        @foreach(\App\Models\OrderNumbers::where('is_ready', true)->where('is_taken', false)->orderBy('updated_at', 'desc')->get() as $order)
            <div wire:click="makeOrderTaken('{{ $order->number }}')" class="text-center shadow-md p-3 hover:drop-shadow-2xl hover:shadow-md hover:scale-101
                    aspect-video overflow-hidden rounded-xl bg-emerald-800/80 dark:border-neutral-700">
                <div class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-xl w-full">
                    <div class="font-semibold text-6xl">{{ $order->number }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
