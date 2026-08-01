<div>
    @if(isset($buttonName))
        @if($selectedSubcategoryId === null)
            {{-- Step 2: choose a subcategory --}}
            <div class="grid md:grid-cols-4 auto-rows-min gap-3 mt-4">
                @forelse($subcategories as $subcategory)
                    <div wire:key="subcategory-{{ $subcategory->id }}"
                         wire:click="selectSubcategory({{ $subcategory->id }})"
                         class="relative cursor-pointer rounded-2xl py-6 px-5
                                bg-emerald-600/30 hover:bg-emerald-500/35
                                transition-all duration-300 hover:-translate-y-0.5 active:scale-[0.98]"
                         style="box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.2);">
                        <div class="flex items-center justify-center text-white font-nunito text-center">
                            <span class="font-extrabold text-lg tracking-wide">{{ $subcategory->name }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-white/70 font-nunito py-8">
                        Šioje kategorijoje subkategorijų nėra.
                    </div>
                @endforelse
            </div>
        @else
            {{-- Step 3: products of the selected subcategory --}}
            <div class="mt-4">
                <button wire:click="backToSubcategories"
                        class="inline-flex items-center gap-2 rounded-xl py-2 px-4 text-white font-nunito font-bold
                               bg-emerald-600/30 hover:bg-emerald-500/40 transition-all duration-200 active:scale-[0.98]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Atgal
                </button>
            </div>

            @if($buttonName === 'ceburekai')
                <livewire:dynamic-component :component="'buttons.ceburekai'" :subcategoryId="$selectedSubcategoryId" :key="'ceburekai-'.$selectedSubcategoryId" />
            @endif
            @if($buttonName === 'kibinai')
                <livewire:dynamic-component :component="'buttons.kibinai'" :subcategoryId="$selectedSubcategoryId" :key="'kibinai-'.$selectedSubcategoryId" />
            @endif
            @if($buttonName === 'drinks')
                <livewire:dynamic-component :component="'buttons.drinks'" :subcategoryId="$selectedSubcategoryId" :key="'drinks-'.$selectedSubcategoryId" />
            @endif
            @if($buttonName === 'other')
                <livewire:dynamic-component :component="'buttons.other-products'" :subcategoryId="$selectedSubcategoryId" :key="'other-'.$selectedSubcategoryId" />
            @endif
        @endif
    @endif{{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}

</div>
