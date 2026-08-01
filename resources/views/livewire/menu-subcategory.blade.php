<div>
    @if($categoryId)
        <div class="bg-white/80 rounded-xl px-5 py-4 backdrop-blur-[2px] mt-4">
            <h2 class="text-[#191919] text-base font-semibold mb-3">Subkategorijos</h2>

            <div class="flex gap-3 items-end flex-wrap mb-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block mb-1 text-sm font-medium text-gray-600">Nauja subkategorija</label>
                    <input wire:model="newName" wire:keydown.enter="create"
                           class="w-full px-3 py-2 rounded-lg bg-white border border-gray-200 shadow-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           placeholder="Įveskite pavadinimą..." />
                    @error('newName') <span class="text-sm text-red-500">Pavadinimas turi būti užpildytas</span> @enderror
                </div>
                <button wire:click="create"
                        class="px-6 py-2 bg-emerald-700 hover:bg-emerald-600 rounded-lg text-white font-semibold whitespace-nowrap transition-colors">
                    Pridėti
                </button>
            </div>

            <div x-data x-init="initSwapSortable($el, $wire, 'updateOrder')" class="flex flex-col gap-2">
                @forelse($subcategories as $subcategory)
                    <div data-sortable-id="{{ $subcategory->id }}" wire:key="subcategory-row-{{ $subcategory->id }}"
                         class="flex items-center gap-3 bg-white border border-gray-200 rounded-lg px-4 py-2 shadow-sm">
                        <svg class="w-5 h-5 text-gray-300 cursor-move shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                        </svg>

                        @if($editingId === $subcategory->id)
                            <input wire:model="editName" wire:keydown.enter="update"
                                   class="flex-1 px-3 py-1.5 rounded-lg bg-white border border-emerald-300 text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                            <flux:button wire:click="update" variant="primary">Išsaugoti</flux:button>
                            <flux:button wire:click="cancelEdit">Atšaukti</flux:button>
                        @else
                            <span class="flex-1 font-medium text-gray-700">{{ $subcategory->name }}</span>
                            <flux:button wire:click="editSubcategory({{ $subcategory->id }})" variant="primary">Pakeisti</flux:button>
                            <flux:button wire:click="deleteSubcategory({{ $subcategory->id }})">Pašalinti</flux:button>
                        @endif
                    </div>
                @empty
                    <div class="text-gray-500 text-sm py-2">Subkategorijų dar nėra.</div>
                @endforelse
            </div>
        </div>
    @endif
</div>
