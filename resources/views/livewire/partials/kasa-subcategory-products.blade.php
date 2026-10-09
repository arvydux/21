@php $__showAll = $showAll ?? false; $__key = $selectedSubcategoryId.($__showAll ? '-all' : ''); @endphp
@if($buttonName === 'ceburekai')
    <livewire:dynamic-component :component="'buttons.ceburekai'" :subcategoryId="$selectedSubcategoryId" :showAll="$__showAll" :key="'ceburekai-'.$__key" />
@endif
@if($buttonName === 'kibinai')
    <livewire:dynamic-component :component="'buttons.kibinai'" :subcategoryId="$selectedSubcategoryId" :showAll="$__showAll" :key="'kibinai-'.$__key" />
@endif
@if($buttonName === 'drinks')
    <livewire:dynamic-component :component="'buttons.drinks'" :subcategoryId="$selectedSubcategoryId" :showAll="$__showAll" :key="'drinks-'.$__key" />
@endif
@if($buttonName === 'other')
    <livewire:dynamic-component :component="'buttons.other-products'" :subcategoryId="$selectedSubcategoryId" :showAll="$__showAll" :key="'other-'.$__key" />
@endif
@if($buttonName === 'bulviniai')
    <livewire:dynamic-component :component="'buttons.potato-dishes'" :subcategoryId="$selectedSubcategoryId" :showAll="$__showAll" :key="'bulviniai-'.$__key" />
@endif
@if($buttonName === 'koldunai')
    <livewire:dynamic-component :component="'buttons.koldunai-dishes'" :subcategoryId="$selectedSubcategoryId" :showAll="$__showAll" :key="'koldunai-'.$__key" />
@endif
