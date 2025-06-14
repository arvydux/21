<div wire:sortable="updateOrder" class="grid md:grid-cols-3 auto-rows-min gap-4 mt-10 mt-4">
    @foreach(\App\Models\Kibinai::where('show', true)->orderBy('position')->get() as $productName)
        <div wire:sortable.item="{{ $productName->id }}" wire:key="{{ $loop->index }}" wire:click="addProduct('{{ $productName->name }}', '{{ $productName->price }}')" class="relative text-center  shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
            aspect-video overflow-hidden rounded-xl  dark:border-neutral-700">
            <div wire:sortable.handle class="flex grid content-center flex-col gap-2 h-full text-white rounded-xl w-full bg-white/20 ">
                <div class="font-semibold text-2xl tracking-tight">{{ $productName->name }}</div>
            </div>
        </div>
    @endforeach
</div>


