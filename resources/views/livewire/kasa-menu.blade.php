<div x-data x-init="initSwapSortable($el, $wire, 'updateOrder')" class="grid md:grid-cols-4 auto-rows-min gap-4">
    @foreach(\App\Models\Category::orderBy('position')->get() as $categoryName)
        <div data-sortable-id="{{ $categoryName->id }}" wire:key="category-{{ $categoryName->id }}" wire:click="categoryButtonClicked('{{ $categoryName->component_name }}')"
             class="relative text-center aspect-video overflow-hidden rounded-2xl cursor-pointer
                bg-gray-900/20 border border-white/25
                hover:scale-[1.03] hover:-translate-y-0.5
                active:scale-[0.98]
                transition-all duration-300
                {{ ($categoryMenuClicked === $categoryName->component_name) ? 'bg-gray-900/30 scale-92' : '' }}"
             style="box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.25);">
            <div class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-2xl w-full">
                <div class="font-extrabold text-lg tracking-wide antialiased" style="text-shadow: 0 0 20px rgba(255,255,255,0.15), 0 2px 4px rgba(0,0,0,0.3);">{{ $categoryName->name }}</div>
            </div>
        </div>
    @endforeach
</div>
