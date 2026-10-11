<div>
    @if(isset($buttonName))
        @php $persistent = in_array($layoutMode, ['persistent', 'visi_default'], true); @endphp

        @if($persistent)
            {{-- Persistent mode: subcategory chips stay visible, no "Atgal" button --}}
            @include('livewire.partials.kasa-subcategory-chips')

            @if($selectedSubcategoryId !== null)
                @include('livewire.partials.kasa-subcategory-products')
            @endif
        @else
            {{-- Drilldown mode --}}
            @if($selectedSubcategoryId === null)
                {{-- Step 2: choose a subcategory --}}
                @include('livewire.partials.kasa-subcategory-chips')
            @else
                {{-- Step 3: products of the selected subcategory --}}
                <div class="mt-3">
                    <button wire:click="backToSubcategories"
                            class="inline-flex h-12 items-center gap-2 rounded-full px-5 font-nunito text-base font-bold text-white
                                   bg-white/10 ring-1 ring-white/15 hover:bg-white/20 transition-all duration-200 active:scale-[0.97]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Atgal
                    </button>
                </div>

                @include('livewire.partials.kasa-subcategory-products')
            @endif
        @endif
    @endif{{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}

</div>
