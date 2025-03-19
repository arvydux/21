<div class="grid md:grid-cols-4 auto-rows-min gap-4">
    @foreach(\App\Models\Category::all() as $categoryName)
        <div wire:click="categoryButtonClicked('{{ $categoryName->component_name }}')"
             class="relative text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
            aspect-video overflow-hidden rounded-xl  bg-gray-900/20 dark:border-neutral-700
             {{ ($categoryMenuClicked === $categoryName->component_name) ? 'shadow-4xl bg-gray-900/30 scale-107' : null }}
            ">
            <div class="flex grid content-center flex-col gap-2 h-full text-white rounded-xl w-full">
                <div class="font-semibold text-lg">{{ $categoryName->name }}</div>
            </div>
        </div>
    @endforeach
</div>
