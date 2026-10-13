@props(['side', 'class' => ''])
<div class="{{ $class }} group relative z-10 flex w-4 shrink-0 touch-none cursor-col-resize items-center justify-center"
     style="background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);"
     title="Tempkite, kad pakeistumėte plotį. Dukart bakstelėkite – numatytasis plotis."
     data-panel-handle="{{ $side }}"
     x-on:pointerdown.prevent="start('{{ $side }}', $event)"
     x-on:pointermove="move($event)"
     x-on:pointerup="stop()"
     x-on:pointercancel="stop()">
    <span class="absolute inset-y-0 -inset-x-2"></span>
    <span class="pointer-events-none absolute inset-y-0 left-1/2 w-px -translate-x-1/2 bg-white/15 transition-colors duration-150 group-hover:bg-white/30"
          x-bind:class="dragging === '{{ $side }}' && '!bg-amber-400/80'"></span>
    <span data-panel-grip
          class="pointer-events-none relative flex h-14 w-3.5 flex-col items-center justify-center gap-1 rounded-full bg-emerald-900 ring-1 ring-white/25 shadow-md shadow-black/30 transition-all duration-150 group-hover:h-16 group-hover:ring-white/50"
          x-bind:class="dragging === '{{ $side }}' && '!h-20 !bg-amber-400 !ring-amber-200 shadow-[0_0_16px_rgba(251,191,36,0.6)]'">
        @foreach(range(1, 3) as $dot)
            <span class="size-1 rounded-full bg-white/70"
                  x-bind:class="dragging === '{{ $side }}' && '!bg-emerald-950'"></span>
        @endforeach
    </span>
</div>
