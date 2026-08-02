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
