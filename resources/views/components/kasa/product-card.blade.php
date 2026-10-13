@props([
    'product',
    'price' => null,
    'buttonStyle' => 'compact',
    'padding' => 3,
    'disabled' => false,
])
@php
    $stateClasses = match (true) {
        (bool) $product->attention => 'bg-red-500/70 ring-red-300/50',
        ! ($product->show ?? true) => 'bg-red-400/80 ring-red-200/50',
        default => 'bg-white/10 ring-white/15 hover:bg-white/15',
    };
    $paddingClass = match ((int) $padding) {
        1, 2 => 'p-3',
        4 => 'p-5',
        5 => 'p-6',
        default => 'p-4',
    };
    $productNameJs = \Illuminate\Support\Js::from($product->name);
@endphp
<div @class([
        'relative flex h-full flex-col gap-1 overflow-hidden rounded-2xl font-nunito text-white ring-1 backdrop-blur-lg transition-all duration-200',
        $buttonStyle === 'large' ? 'min-h-40' : 'min-h-32',
        $paddingClass,
        $stateClasses,
        'pointer-events-none' => $disabled,
        'cursor-pointer select-none active:scale-[0.98]' => ! $disabled,
     ])
     @unless($disabled)
     x-bind:class="$store.cart.count({{ $productNameJs }}) > 0 && 'outline-2 outline-white/70'"
     @endunless
     style="box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.2);">
    @unless($disabled)
        <div class="flex min-h-7 items-start justify-between gap-2">
            <span class="text-sm font-bold tabular-nums text-white/55">{{ $product->number }}</span>
            <span x-cloak
                  x-show="$store.cart.count({{ $productNameJs }}) > 0"
                  x-text="$store.cart.count({{ $productNameJs }})"
                  class="flex size-7 shrink-0 items-center justify-center rounded-full bg-white text-sm font-black text-emerald-800 shadow-lg shadow-emerald-950/30"></span>
        </div>

        <div class="line-clamp-3 flex-1 font-bold leading-snug antialiased {{ $buttonStyle === 'large' ? 'text-lg' : 'text-base' }}"
             style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);">
            {{ $product->name }}
        </div>

        <div class="mt-2 flex items-end justify-between gap-2">
            <div class="flex flex-wrap items-baseline gap-x-2">
                @if($price !== null)
                    <span class="font-extrabold text-amber-300 {{ $buttonStyle === 'large' ? 'text-xl' : 'text-lg' }}" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);">{{ $price }}</span>
                @endif
                @if($product->left !== null)
                    <span class="text-sm font-bold text-white/80">
                        <span x-show="editingProductId === {{ $product->id }} && editingLeft !== ''" x-text="editingLeft"></span><span x-show="!(editingProductId === {{ $product->id }} && editingLeft !== '')">{{ $product->left }}</span> vnt.
                    </span>
                @else
                    <span x-cloak x-show="editingProductId === {{ $product->id }} && editingLeft !== ''" class="text-sm font-bold text-white/80">
                        <span x-text="editingLeft"></span> vnt.
                    </span>
                @endif
            </div>
            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-white/15 text-white ring-1 ring-white/20">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
                </svg>
            </span>
        </div>
    @endunless
</div>
