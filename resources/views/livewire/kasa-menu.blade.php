<div wire:sortable="updateOrder" class="grid md:grid-cols-4 auto-rows-min gap-4">
    @foreach(\App\Models\Category::orderBy('position')->get() as $categoryName)
        <div wire:sortable.item="{{ $categoryName->id }}" wire:key="{{ $loop->index }}" wire:click="categoryButtonClicked('{{ $categoryName->component_name }}')"
             class="relative text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
            aspect-video overflow-hidden rounded-xl  bg-gray-900/20 dark:border-neutral-700
             {{ ($categoryMenuClicked === $categoryName->component_name) ? 'bg-gray-900/30  scale-92' : null }}
            ">
            <div wire:sortable.handle class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-xl w-full">
                <div class="font-semibold text-lg">{{ $categoryName->name }}</div>
            </div>
        </div>
    @endforeach
</div>
