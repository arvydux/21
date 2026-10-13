@props(['side', 'class' => ''])
<div class="{{ $class }} group relative flex w-4 shrink-0 touch-none cursor-col-resize items-center justify-center"
     title="Tempkite, kad pakeistumėte plotį. Dukart bakstelėkite – numatytasis plotis."
     data-panel-handle="{{ $side }}"
     x-on:pointerdown.prevent="start('{{ $side }}', $event)"
     x-on:pointermove="move($event)"
     x-on:pointerup="stop()"
     x-on:pointercancel="stop()">
    <span class="absolute inset-y-0 -inset-x-1"></span>
    <span class="pointer-events-none h-16 w-1 rounded-full bg-white/25 transition-all duration-150 group-hover:h-24 group-hover:bg-white/60"
          x-bind:class="dragging === '{{ $side }}' && '!h-32 !bg-amber-400 shadow-[0_0_12px_rgba(251,191,36,0.6)]'"></span>
</div>
