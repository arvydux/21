<div x-data x-init="initSwapSortable($el, $wire, 'updateOrder')"
     class="flex h-full flex-col gap-1.5">
    @foreach($categories as $categoryName)
        @php $isSelected = $categoryMenuClicked === $categoryName->component_name; @endphp
        <div data-sortable-id="{{ $categoryName->id }}" wire:key="category-{{ $categoryName->id }}"
             wire:click="categoryButtonClicked(@js($categoryName->component_name), {{ $categoryName->id }})"
             class="relative flex min-h-14 max-h-24 flex-1 touch-manipulation cursor-pointer select-none items-center rounded-2xl px-5 font-nunito
                    transition-all duration-100 active:scale-[0.97]
                    {{ $isSelected
                        ? 'bg-white/15 text-white ring-1 ring-white/20 shadow-lg shadow-emerald-950/20'
                        : 'text-white/70 hover:bg-white/10 hover:text-white/90 active:bg-white/20' }}">
            @if($isSelected)
                <span data-selected-category-bar class="absolute inset-y-3 left-0 w-1 rounded-full bg-amber-400 shadow-[0_0_12px_rgba(251,191,36,0.6)]"></span>
            @endif
            <span class="kasa-nav line-clamp-2">{{ $categoryName->name }}</span>
        </div>
    @endforeach
</div>
