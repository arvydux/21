<div class="relative mt-6 text-white/80">
    <livewire:menu.create-form.drinks-create-form/>

    <div class="mt-4 overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left">
            <thead class="text-xs uppercase bg-emerald-700/70 text-white/80">
                <tr>
                    <th scope="col" class="px-6 py-3">Pavadinimas</th>
                    <th scope="col" class="px-6 py-3 w-32">Kaina</th>
                    <th scope="col" class="px-6 py-3 w-28">Rodyti</th>
                    <th scope="col" class="px-6 py-3 w-36">Pakuotės kaina</th>
                    <th scope="col" class="px-6 py-3 w-44">Veiksmai</th>
                </tr>
            </thead>
            <tbody>
            @foreach($simpleProducts as $productName)
                <tr class="@if($editedProductNameId === $productName->id) hidden @endif border-b border-green-500/30 bg-white/10">
                    <td class="px-6 py-4 font-medium text-white">{{ $productName->name }}</td>
                    <td class="px-6 py-4">{{ number_format($productName->price, 2) }} €</td>
                    <td class="px-6 py-4">
                        @if($productName->show)
                            <div class="flex items-center"><div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div> Taip</div>
                        @else
                            <div class="flex items-center"><div class="h-2.5 w-2.5 rounded-full bg-gray-300 me-2"></div> Ne</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ number_format($productName->package ?? 0, 2) }} €</td>
                    <td class="px-4 py-4">
                        <div class="flex gap-2">
                            <flux:button wire:click="editProduct({{ $productName->id }})" variant="primary">Pakeisti</flux:button>
                            <flux:button wire:click="deleteProduct({{ $productName->id }})" variant="primary">Pašalinti</flux:button>
                        </div>
                    </td>
                </tr>
                <tr class="@if($editedProductNameId !== $productName->id) hidden @endif border-b-2 border-emerald-400/50 bg-emerald-900/30">
                    <td colspan="5" class="px-4 py-4">
                        <div class="flex gap-4 items-end flex-wrap">
                            <div class="flex-1 min-w-[200px]">
                                <label class="block mb-1 text-xs font-medium text-white/70">Pavadinimas</label>
                                <input type="text" wire:model.live.debounce="name" class="w-full bg-white/90 px-3 py-2 rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400" />
                                @error('name') <span class="text-xs text-red-300">Pavadinimas turi būti užpildytas</span> @enderror
                            </div>
                            <div class="w-32">
                                <label class="block mb-1 text-xs font-medium text-white/70">Kaina</label>
                                <input type="number" wire:model="price" class="w-full bg-white/90 px-3 py-2 rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400" />
                                @error('price') <span class="text-xs text-red-300">Gali būti tik skaičiai</span> @enderror
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-medium text-white/70">Rodyti</label>
                                <label class="relative cursor-pointer block">
                                    <input type="checkbox" wire:model="show" class="sr-only peer" />
                                    <div class="w-[53px] h-7 flex items-center bg-gray-300 rounded-full text-[9px] peer-checked:text-emerald-700 text-gray-300 font-extrabold after:flex after:items-center after:justify-center peer after:content-['Ne'] peer-checked:after:content-['Taip'] peer-checked:after:translate-x-full after:absolute after:left-[2px] peer-checked:after:border-white after:bg-white after:border after:border-gray-300 after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-600"></div>
                                </label>
                            </div>
                            <div class="w-32">
                                <label class="block mb-1 text-xs font-medium text-white/70">Pakuotės kaina</label>
                                <input type="number" min="0" wire:model="package" id="package-price" class="w-full bg-white/90 px-3 py-2 rounded-lg text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400" />
                                @error('package-price') <span class="text-xs text-red-300">Pakuotės kaina turi būti užpildytas</span> @enderror
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <flux:button wire:click="save({{ $productName->id }})" variant="primary">Išsaugoti</flux:button>
                                <flux:button wire:click.prevent="cancel" variant="primary">Atšaukti</flux:button>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
