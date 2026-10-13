<div x-data="{ fadeEnd: false, check() { this.fadeEnd = this.$el.scrollLeft + this.$el.clientWidth < this.$el.scrollWidth - 4 } }"
     x-init="check()" x-on:scroll.passive="check()" x-on:resize.window="check()"
     x-bind:style="fadeEnd && '-webkit-mask-image: linear-gradient(to right, black 85%, transparent); mask-image: linear-gradient(to right, black 85%, transparent);'"
     x-drag-scroll.x
     class="-mx-2 mt-1 flex touch-pan-x select-none overflow-x-auto overscroll-contain" style="scrollbar-width: none;">
    @forelse($subcategories as $subcategory)
        @php $isSelected = $selectedSubcategoryId === $subcategory->id; @endphp
        <div wire:key="subcategory-{{ $subcategory->id }}"
             wire:click="selectSubcategory({{ $subcategory->id }})"
             class="relative flex h-14 shrink-0 touch-manipulation cursor-pointer select-none items-center rounded-t-xl px-4 kasa-nav
                    transition-colors duration-100
                    {{ $isSelected ? 'text-white' : 'text-white/55 hover:text-white/85 active:bg-white/10' }}">
            {{ $subcategory->name }}
            @if($isSelected)
                <span class="absolute inset-x-3 bottom-0 h-1 rounded-full bg-amber-400 shadow-[0_0_12px_rgba(251,191,36,0.6)]"></span>
            @endif
        </div>
    @empty
        <div class="px-2 py-4 text-white/60">
            Šioje kategorijoje subkategorijų nėra.
        </div>
    @endforelse
</div>
