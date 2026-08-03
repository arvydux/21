<div class="flex flex-wrap gap-2 mt-4">
    @forelse($subcategories as $subcategory)
        <div wire:key="subcategory-{{ $subcategory->id }}"
             wire:click="selectSubcategory({{ $subcategory->id }})"
             class="cursor-pointer select-none rounded-full px-5 py-2.5 font-nunito font-bold text-base tracking-wide
                    transition-all duration-200 active:scale-[0.97]
                    {{ $selectedSubcategoryId === $subcategory->id
                        ? 'bg-amber-400 text-emerald-950 shadow-lg shadow-amber-500/25'
                        : 'bg-white/10 text-white/80 hover:bg-white/20 ring-1 ring-white/15' }}">
            {{ $subcategory->name }}
        </div>
    @empty
        <div class="w-full text-center text-white/70 font-nunito py-8">
            Šioje kategorijoje subkategorijų nėra.
        </div>
    @endforelse
</div>
