<div class="relative gap-3 mt-10 overflow-x-auto shadow-md sm:rounded-lg text-white/80">
    <div class="flex h-full w-full flex-1  gap-4 rounded-xl">
        <div class="auto-rows-min gap-4 w-3/4" data-tabs-toggle="#default-tab-content" role="tablist">
            <table class="w-full text-sm text-left  dark:text-gray-400 table-fixed">
                <thead class="text-xs uppercase bg-emerald-700/70">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Pavadinimas
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Kaina
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Rodyti
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Veiksmai
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($simpleProducts as $productName)
                    <tr class="border-b border-green-500/30 bg-white/10">
                        <th scope="row" class="@if($editedProductNameId === $productName->id) hidden @endif px-6 py-4 font-medium   dark:text-white">
                            {{ $productName->name }}
                        </th>
                        <td class="@if($editedProductNameId !== $productName->id) hidden @endif px-4 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                            <input type="text" wire:model.live.debounce="name" id="name" class="bg-white/80 px-4 py-1.5  pl-4 rounded-lg  sm:text-base " >
                            @error('name')
                            <span class="text-md text-red-500">Pavadinimas turi būti užpildytas</span>
                            @enderror
                        </td>
                        <th scope="row" class="@if($editedProductNameId === $productName->id) hidden @endif px-6 py-4 font-medium   dark:text-white">
                            {{ number_format($productName->price, 2) }} €
                        </th>
                        <td class="@if($editedProductNameId !== $productName->id) hidden @endif px-4 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                            <input type="text" wire:model.live.debounce="price" id="price" class="bg-white/80 px-4 py-1.5  pl-4  rounded-lg  sm:text-base " >
                            @error('price')
                            <span class="text-md text-red-500">Gali būti tik skaičiai</span>
                            @enderror
                        </td>
                        <th scope="row" class="@if($editedProductNameId === $productName->id) hidden @endif px-6 py-4 font-medium   dark:text-white">
                            @if($productName->show)
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div> Taip
                                </div>
                            @else
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-gray-300 me-2"></div> Ne
                                </div>
                            @endif
                        </th>
                        <td class="@if($editedProductNameId !== $productName->id) hidden @endif px-4 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                            <div class="flex items-center">
                                <label class="relative cursor-pointer">
                                    <input type="checkbox"  wire:model="show" class="sr-only peer" />
                                    <div
                                        class="w-[53px] h-7 flex items-center bg-gray-300 rounded-full text-[9px] peer-checked:text-emerald-700  text-gray-300 font-extrabold after:flex after:items-center after:justify-center peer after:content-['Ne'] peer-checked:after:content-['Taip'] peer-checked:after:translate-x-full after:absolute after:left-[2px] peer-checked:after:border-white after:bg-white after:border after:border-gray-300 after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-700 ">
                                    </div>
                                </label>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($editedProductNameId === $productName->id)
                                <flux:button wire:click="save({{ $productName->id }})" variant="primary" class="">Išsaugoti</flux:button>
                                <flux:button wire:click.prevent="cancel" variant="primary">Atšaukti</flux:button>
                            @else
                                <flux:button wire:click="editProduct({{ $productName->id }})" variant="primary">Pakeisti</flux:button>
                                <flux:button wire:click="deleteProduct({{ $productName->id }})" variant="primary">Pašalinti</flux:button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <livewire:menu.create-form.drinks-create-form/>
    </div>
</div>
