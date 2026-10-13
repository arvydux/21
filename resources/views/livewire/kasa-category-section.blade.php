<div class="flex h-full flex-col font-nunito">
    @if(isset($buttonName))
        @php
            $persistent = in_array($layoutMode, ['persistent', 'visi_default'], true);
            $showTabs = $persistent || $selectedSubcategoryId === null;
        @endphp

        <div class="border-b border-white/10 px-5 pt-4">
            <h2 class="kasa-title">{{ $categoryName }}</h2>

            @if($showTabs)
                @include('livewire.partials.kasa-subcategory-chips')
            @else
                {{-- Drilldown mode: products of the chosen subcategory, with a way back to the subcategories --}}
                <div class="flex items-center gap-3 py-2">
                    <button wire:click="backToSubcategories"
                            class="inline-flex h-12 touch-manipulation items-center gap-2 rounded-xl bg-white/10 px-4 text-base font-bold text-white ring-1 ring-white/15 transition-all duration-100 hover:bg-white/20 active:scale-[0.97]">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Atgal
                    </button>
                    <span class="kasa-nav text-amber-300">{{ $subcategories->firstWhere('id', $selectedSubcategoryId)?->name }}</span>
                </div>
            @endif
        </div>

        <div x-data x-drag-scroll x-scroll-fade data-drag-scroll class="no-scrollbar @container min-h-0 flex-1 touch-pan-y select-none overflow-y-auto overscroll-contain p-4">
            @if($selectedSubcategoryId !== null)
                @include('livewire.partials.kasa-subcategory-products')
            @elseif($subcategories->isNotEmpty())
                <div class="flex h-full items-center justify-center text-lg font-semibold text-white/50">Pasirinkite subkategoriją</div>
            @endif
        </div>
    @else
        <div class="flex h-full flex-col items-center justify-center gap-2 text-white/50">
            <svg class="size-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h7"/>
            </svg>
            <span class="text-lg font-semibold">Pasirinkite kategoriją</span>
        </div>
    @endif
</div>
