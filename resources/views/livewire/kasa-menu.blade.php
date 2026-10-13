<div x-data x-init="initSwapSortable($el, $wire, 'updateOrder')"
     class="flex h-full flex-col gap-1.5">
    @foreach($categories as $categoryName)
        @php $isSelected = $categoryMenuClicked === $categoryName->component_name; @endphp
        <div data-sortable-id="{{ $categoryName->id }}" wire:key="category-{{ $categoryName->id }}"
             wire:click="categoryButtonClicked(@js($categoryName->component_name), {{ $categoryName->id }})"
             class="flex min-h-14 max-h-24 flex-1 touch-manipulation cursor-pointer select-none items-center rounded-2xl px-4 font-nunito
                    transition-all duration-100 active:scale-[0.97]
                    {{ $isSelected
                        ? 'bg-white text-emerald-900 shadow-lg shadow-emerald-950/30'
                        : 'text-white/85 hover:bg-white/10 active:bg-white/20' }}">
            <span class="line-clamp-2 text-lg font-extrabold leading-tight tracking-wide">{{ $categoryName->name }}</span>
        </div>
    @endforeach
</div>
