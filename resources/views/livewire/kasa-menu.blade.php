<div x-data x-init="initSwapSortable($el, $wire, 'updateOrder')"
     class="grid gap-2.5"
     style="grid-template-columns: repeat({{ max($categories->count(), 1) }}, minmax(0, 1fr));">
    @foreach($categories as $categoryName)
        @php $isSelected = $categoryMenuClicked === $categoryName->component_name; @endphp
        <div data-sortable-id="{{ $categoryName->id }}" wire:key="category-{{ $categoryName->id }}"
             wire:click="categoryButtonClicked(@js($categoryName->component_name), {{ $categoryName->id }})"
             class="flex h-20 cursor-pointer select-none items-center justify-center rounded-2xl px-3 text-center font-nunito
                    transition-all duration-200 active:scale-[0.97]
                    {{ $isSelected
                        ? 'bg-white text-emerald-900 shadow-lg shadow-emerald-950/30'
                        : 'bg-white/10 text-white ring-1 ring-white/15 hover:bg-white/20' }}"
             @if(! $isSelected) style="box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.15);" @endif>
            <span class="line-clamp-2 text-lg font-extrabold leading-tight tracking-wide">{{ $categoryName->name }}</span>
        </div>
    @endforeach
</div>
