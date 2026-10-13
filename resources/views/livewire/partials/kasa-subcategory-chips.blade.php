<div class="mt-4 flex flex-wrap gap-2">
    @forelse($subcategories as $subcategory)
        <div wire:key="subcategory-{{ $subcategory->id }}"
             wire:click="selectSubcategory({{ $subcategory->id }})"
             class="flex h-12 cursor-pointer select-none items-center rounded-full px-6 font-nunito text-base font-bold tracking-wide
                    transition-all duration-200 active:scale-[0.97]
                    {{ $selectedSubcategoryId === $subcategory->id
                        ? 'bg-amber-400 text-emerald-950 shadow-lg shadow-amber-500/25'
                        : 'bg-white/10 text-white ring-1 ring-white/15 hover:bg-white/20' }}">
            {{ $subcategory->name }}
        </div>
    @empty
        <div class="w-full py-8 text-center font-nunito text-white/70">
            Šioje kategorijoje subkategorijų nėra.
        </div>
    @endforelse
</div>
