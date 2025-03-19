<div>
<div class="bg-gray-100 ">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach(\App\Models\Product::all() as $product)
                <livewire:product :productName="$product->name"/>
            @endforeach

        </div>
    </div>
</div>
    <div id="crud-modal"  aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <livewire:product-modal/>
    </div>

</div>
