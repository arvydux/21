<div x-data x-init="initSwapSortable($el, $wire, 'updateOrder')" class="flex gap-3">
    @foreach($categories as $categoryName)
        <div data-sortable-id="{{ $categoryName->id }}" wire:key="category-{{ $categoryName->id }}"
             wire:click="categoryButtonClicked('{{ $categoryName->component_name }}')"
             class="relative flex-1 cursor-pointer rounded-2xl py-4 px-5
                    transition-all duration-300 hover:-translate-y-0.5 active:scale-[0.98]
                    {{ ($categoryMenuClicked === $categoryName->component_name)
                        ? 'bg-emerald-400/40 ring-2 ring-emerald-300/60'
                        : 'bg-emerald-600/30 hover:bg-emerald-500/35' }}"
             style="box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.2);">
            <div class="flex items-center justify-center text-white font-nunito">
                <span class="font-extrabold text-xl tracking-wide">{{ $categoryName->name }}</span>
            </div>
            @if($categoryMenuClicked === $categoryName->component_name)
                <div class="absolute bottom-2 left-1/2 -translate-x-1/2 w-10 h-0.5 bg-white/80 rounded-full"></div>
            @endif
        </div>
    @endforeach
</div>
